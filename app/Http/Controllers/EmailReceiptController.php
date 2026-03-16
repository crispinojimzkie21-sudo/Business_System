<?php

namespace App\Http\Controllers;

use App\Mail\SaleReceipt;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailReceiptController extends Controller
{
    /**
     * Send receipt via email with enhanced error handling
     */
    public function sendReceiptEmail(Sale $sale, Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'customer_name' => 'nullable|string|max:255',
        ]);

        try {
            // Load sale with items
            $sale->load('user');
            $items = is_array($sale->items) ? $sale->items : json_decode($sale->items, true);

            // Generate sales report data
            $salesReport = $this->generateSalesReport();

            // Update sale record with customer email if provided
            if ($request->customer_name) {
                $sale->update([
                    'customer_email' => $request->email,
                    'customer_name' => $request->customer_name,
                ]);
            }

            // Send email with sales report
            Mail::to($request->email)->send(new SaleReceipt($sale, $items, $salesReport));

            // Log successful email
            Log::info('Receipt email sent successfully with sales report', [
                'sale_id' => $sale->id,
                'customer_email' => $request->email,
                'amount' => $sale->total_amount,
                'includes_sales_report' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Receipt with sales report sent successfully to ' . $request->email
            ]);

        } catch (\Exception $e) {
            // Log detailed error
            Log::error('Failed to send receipt email', [
                'sale_id' => $sale->id,
                'customer_email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test email configuration
     */
    public function testEmailConfig()
    {
        try {
            Mail::raw("This is a test email from Business System at " . now()->format('Y-m-d H:i:s'), function($message) {
                $message->to('test@example.com')
                       ->subject('SMTP Test - Business System')
                       ->from(config('mail.from.address'), config('mail.from.name'));
            });

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully!',
                'config' => [
                    'mailer' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption') ?? 'none',
                    'from_address' => config('mail.from.address'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test email failed: ' . $e->getMessage(),
                'config' => [
                    'mailer' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption') ?? 'none',
                    'from_address' => config('mail.from.address'),
                ]
            ], 500);
        }
    }

    /**
     * Resend receipt with retry logic
     */
    public function resendReceiptWithRetry(Sale $sale, Request $request)
    {
        $maxRetries = 3;
        $retryDelay = 1000; // 1 second

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $sale->load('user');
                $items = is_array($sale->items) ? $sale->items : json_decode($sale->items, true);

                Mail::to($sale->customer_email)->send(new SaleReceipt($sale, $items));

                Log::info('Receipt resent successfully', [
                    'sale_id' => $sale->id,
                    'attempt' => $attempt,
                    'customer_email' => $sale->customer_email
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Receipt resent successfully!',
                    'attempt' => $attempt
                ]);

            } catch (\Exception $e) {
                Log::warning("Receipt resend attempt $attempt failed", [
                    'sale_id' => $sale->id,
                    'attempt' => $attempt,
                    'error' => $e->getMessage()
                ]);

                if ($attempt < $maxRetries) {
                    usleep($retryDelay * 1000); // Convert to microseconds
                    $retryDelay *= 2; // Exponential backoff
                }
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to resend receipt after ' . $maxRetries . ' attempts',
            'last_error' => $e->getMessage()
        ], 500);
    }

    /**
     * Generate sales report data for email receipts
     */
    private function generateSalesReport()
    {
        // Get today's sales
        $todaySales = Sale::selectRaw('DATE(created_at) as date, SUM(total_amount) as total, COUNT(*) as transactions')
            ->whereDate('created_at', today())
            ->groupBy('date')
            ->first();

        // Get this week's sales
        $weeklySales = Sale::selectRaw("strftime('%Y-%W', created_at) as week, SUM(total_amount) as total, COUNT(*) as transactions")
            ->where('created_at', '>=', now()->startOfWeek())
            ->groupBy('week')
            ->first();

        // Get this month's sales
        $monthlySales = Sale::selectRaw("strftime('%Y', created_at) as year, strftime('%m', created_at) as month, SUM(total_amount) as total, COUNT(*) as transactions")
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->groupBy('year', 'month')
            ->first();

        // Get top 5 products sold today
        $topProductsToday = Sale::select('items')
            ->whereDate('created_at', today())
            ->get()
            ->flatMap(function ($sale) {
                $items = json_decode($sale->items, true) ?: [];
                return collect($items)->map(function ($item) {
                    return [
                        'product_name' => $item['product_name'] ?? 'Unknown',
                        'quantity' => $item['quantity'] ?? 0,
                        'total' => ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0)
                    ];
                });
            })
            ->groupBy('product_name')
            ->map(function ($group) {
                return [
                    'name' => $group->first()['product_name'],
                    'total_quantity' => $group->sum('quantity'),
                    'total_amount' => $group->sum('total')
                ];
            })
            ->sortByDesc('total_quantity')
            ->take(5)
            ->values();

        return [
            'today' => $todaySales,
            'weekly' => $weeklySales,
            'monthly' => $monthlySales,
            'top_products' => $topProductsToday,
            'generated_at' => now()->format('F j, Y g:i A')
        ];
    }
}
