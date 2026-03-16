<?php

namespace App\Http\Controllers;

use App\Mail\SaleReceipt;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SalesController extends Controller
{
    public function index()
    {
        $today = now()->format('Y-m-d');
        $todaySales = Sale::whereDate('created_at', $today)->sum('total_amount');
        $todayTransactions = Sale::whereDate('created_at', $today)->count();
        
        return view('sales.index', compact('todaySales', 'todayTransactions'));
    }

    public function create()
    {
        $products = Product::where('stock_quantity', '>', 0)->get();
        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        // Decode the JSON string from the hidden input field
        $itemsJson = $request->input('items');
        $items = json_decode($itemsJson, true);
        
        // Validate that items is a valid array
        if (!is_array($items) || empty($items)) {
            return back()->with('error', 'Please add at least one item to the cart.');
        }
        
        // Validate each item has required fields
        foreach ($items as $item) {
            if (!isset($item['product_id']) || !isset($item['quantity'])) {
                return back()->with('error', 'Invalid item data. Please try again.');
            }
        }
        
        // Merge decoded items back to request for further validation
        $request->merge(['items' => $items]);
        
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string|in:cash,card,bank_transfer',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $saleItems = [];

            foreach ($data['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock_quantity < $item['quantity']) {
                    return back()->with('error', "Insufficient stock for {$product->name}. Available: {$product->stock_quantity}");
                }

                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                // Auto-deduct stock
                $product->stock_quantity -= $item['quantity'];
                $product->save();

                $saleItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            $sale = Sale::create([
                'transaction_id' => 'TXN-' . date('Ymd') . '-' . str_pad(time() % 1000000, 6, '0', STR_PAD_LEFT),
                'total_amount' => $totalAmount,
                'payment_method' => $data['payment_method'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_phone' => $data['customer_phone'],
                'items' => $saleItems, // Let the model cast handle JSON encoding
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            // Send email receipt if customer email is provided
            if (!empty($data['customer_email'])) {
                try {
                    Mail::to($data['customer_email'])->send(new SaleReceipt($sale, $saleItems));
                } catch (\Exception $e) {
                    // Log error but don't fail the transaction
                    \Log::error('Failed to send receipt email: ' . $e->getMessage());
                }
            }

            // Determine the correct receipt route based on user role
            $receiptRoute = 'sales.receipt'; // default
            if (auth()->user()->isSuperAdmin()) {
                $receiptRoute = 'superadmin.sales.receipt';
            } elseif (auth()->user()->isAdmin()) {
                $receiptRoute = 'admin.sales.receipt';
            } elseif (auth()->user()->isCashier()) {
                $receiptRoute = 'cashier.sales.receipt';
            } elseif (auth()->user()->isManager() || auth()->user()->isEmployee()) {
                $receiptRoute = 'employee.sales.receipt';
            }

            return redirect()->route($receiptRoute, $sale)->with('success', 'Sale completed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'An error occurred while processing the sale: ' . $e->getMessage());
        }
    }

public function reports()
    {
        // Daily Sales - SQLite compatible (DATE function works)
        $dailySales = Sale::selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as transactions')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();

        // Weekly Sales - SQLite compatible using strftime
        $weeklySales = Sale::selectRaw("strftime('%Y-%W', created_at) as week, SUM(total_amount) as total, COUNT(*) as transactions")
            ->groupBy('week')
            ->orderBy('week', 'desc')
            ->limit(12)
            ->get();

        // Monthly Sales - SQLite compatible using strftime
        $monthlySales = Sale::selectRaw("strftime('%Y', created_at) as year, strftime('%m', created_at) as month, SUM(total_amount) as total, COUNT(*) as transactions")
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return view('sales.reports', compact('dailySales', 'weeklySales', 'monthlySales'));
    }

    public function history()
    {
        $user = auth()->user();
        
        // Cashiers can only see their own sales
        if ($user->isCashier()) {
            $sales = Sale::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        } else {
            // Admins and other roles can see all sales
            $sales = Sale::with('user')->orderBy('created_at', 'desc')->paginate(20);
        }
        
        return view('sales.history', compact('sales'));
    }

    public function receipt(Sale $sale)
    {
        $user = auth()->user();
        
        // Cashiers can only view their own sales receipts
        if ($user->isCashier() && $sale->user_id !== $user->id) {
            abort(403, 'Access denied. You can only view your own sales receipts.');
        }
        
        $sale->load('user');
        // Items may already be an array due to model cast, or a JSON string
        $items = is_array($sale->items) ? $sale->items : json_decode($sale->items, true);
        return view('sales.receipt', compact('sale', 'items'));
    }

    public function resendReceipt(Sale $sale)
    {
        $user = auth()->user();
        
        // Cashiers can only resend their own sales receipts
        if ($user->isCashier() && $sale->user_id !== $user->id) {
            abort(403, 'Access denied. You can only resend your own sales receipts.');
        }
        
        $sale->load('user');
        // Items may already be an array due to model cast, or a JSON string
        $items = is_array($sale->items) ? $sale->items : json_decode($sale->items, true);
        
        if (empty($sale->customer_email)) {
            return back()->with('error', 'No customer email address on file.');
        }
        
        try {
            Mail::to($sale->customer_email)->send(new SaleReceipt($sale, $items));
            return back()->with('success', 'Receipt email sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    public function printReceipt(Sale $sale)
    {
        $sale->load('user');
        // Items may already be an array due to model cast, or a JSON string
        $items = is_array($sale->items) ? $sale->items : json_decode($sale->items, true);
        return view('sales.print-receipt', compact('sale', 'items'));
    }

    public function destroy(Sale $sale)
    {
        try {
            // Restore stock quantities before deleting
            if (!empty($sale->items)) {
                $items = is_array($sale->items) ? $sale->items : json_decode($sale->items, true);
                foreach ($items as $item) {
                    if (isset($item['product_id']) && isset($item['quantity'])) {
                        $product = Product::find($item['product_id']);
                        if ($product) {
                            $product->stock_quantity += $item['quantity'];
                            $product->save();
                        }
                    }
                }
            }
            
            // Determine the correct history route based on user role
            $historyRoute = 'sales.history'; // default
            if (auth()->user()->isSuperAdmin()) {
                $historyRoute = 'superadmin.sales.history';
            } elseif (auth()->user()->isAdmin()) {
                $historyRoute = 'admin.sales.history';
            } elseif (auth()->user()->isCashier()) {
                $historyRoute = 'cashier.sales.history';
            } elseif (auth()->user()->isManager() || auth()->user()->isEmployee()) {
                $historyRoute = 'employee.sales.history';
            }
            
            $sale->delete();
            return redirect()->route($historyRoute)->with('success', 'Sale deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete sale: ' . $e->getMessage());
        }
    }
}
