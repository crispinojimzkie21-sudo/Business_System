<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Eload;
use App\Models\EloadNumber;
use App\Models\EloadTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EloadController extends Controller
{
    /**
     * Display dashboard stats.
     */
    public function dashboardStats()
    {
        $totalCategories = Category::count();
        $totalEloads = Eload::count();
        $totalTransactions = EloadTransaction::count();
        $todayTransactions = EloadTransaction::whereDate('created_at', now()->format('Y-m-d'))->count();
        $todaySales = EloadTransaction::whereDate('created_at', now()->format('Y-m-d'))
            ->where('status', 'completed')
            ->sum('price');
        
        return compact('totalCategories', 'totalEloads', 'totalTransactions', 'todayTransactions', 'todaySales');
    }

    // ==================== CATEGORY MANAGEMENT ====================

    /**
     * Display category list (Super Admin only).
     */
    public function categoriesIndex()
    {
        $categories = Category::orderBy('created_at', 'desc')->get();
        return view('eload.categories.index', compact('categories'));
    }

    /**
     * Show create category form (Super Admin only).
     */
    public function categoriesCreate()
    {
        return view('eload.categories.create');
    }

    /**
     * Store new category (Super Admin only).
     */
    public function categoriesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:eload_categories,name',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Category::create($request->all());

        return redirect()->route('eload.categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show edit category form (Super Admin only).
     */
    public function categoriesEdit(Category $category)
    {
        return view('eload.categories.edit', compact('category'));
    }

    /**
     * Update category (Super Admin only).
     */
    public function categoriesUpdate(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:eload_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $category->update($request->all());

        return redirect()->route('eload.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Delete category (Super Admin only).
     */
    public function categoriesDestroy(Category $category)
    {
        $category->delete();
        return redirect()->route('eload.categories.index')
            ->with('success', 'Category deleted successfully!');
    }

    // ==================== ELOAD MANAGEMENT ====================

    /**
     * Display eload list (Super Admin & Admin).
     */
    public function index()
    {
        $eloads = Eload::with('category')->orderBy('created_at', 'desc')->get();
        return view('eload.index', compact('eloads'));
    }

    /**
     * Show create eload form (Super Admin & Admin).
     */
    public function create()
    {
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        return view('eload.create', compact('categories'));
    }

    /**
     * Store new eload (Super Admin & Admin).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'network' => 'required|in:Smart,Globe,DITO',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:eload_categories,id',
            'status' => 'required|in:active,inactive',
        ]);

        Eload::create($request->all());

        return redirect()->route('eload.index')
            ->with('success', 'E-Load product created successfully!');
    }

    /**
     * Show edit eload form (Super Admin & Admin).
     */
    public function edit(Eload $eload)
    {
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        return view('eload.edit', compact('eload', 'categories'));
    }

    /**
     * Update eload (Super Admin & Admin).
     */
    public function update(Request $request, Eload $eload)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'network' => 'required|in:Smart,Globe,DITO',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:eload_categories,id',
            'status' => 'required|in:active,inactive',
        ]);

        $eload->update($request->all());

        return redirect()->route('eload.index')
            ->with('success', 'E-Load product updated successfully!');
    }

    /**
     * Delete eload (Super Admin only).
     */
    public function destroy(Eload $eload)
    {
        $eload->delete();
        return redirect()->route('eload.index')
            ->with('success', 'E-Load product deleted successfully!');
    }

    // ==================== ELOAD NUMBERS (VIEW ONLY - SUPER ADMIN) ====================

    /**
     * Display eload numbers list (Super Admin only - View Only).
     */
    public function numbersIndex(Request $request)
    {
        $query = EloadNumber::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('network', 'like', "%{$search}%");
            });
        }

        // Filter by network
        if ($request->has('network') && $request->network) {
            $query->where('network', $request->network);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $eloadNumbers = $query->orderBy('created_at', 'desc')->get();
        $networks = ['Smart', 'Globe', 'DITO'];

        return view('eload.numbers.index', compact('eloadNumbers', 'networks'));
    }

    // ==================== ADD LOAD (TRANSACTION) ====================

    /**
     * Show add load form (Admin & Super Admin).
     */
    public function addLoad()
    {
        $eloads = Eload::where('status', 'active')->with('category')->orderBy('name')->get();
        $eloadNumbers = EloadNumber::where('status', 'active')->orderBy('network')->get();
        
        // Group eload numbers by network
        $numbersByNetwork = $eloadNumbers->groupBy('network');
        
        return view('eload.add-load', compact('eloads', 'eloadNumbers', 'numbersByNetwork'));
    }

    /**
     * Show add multiple loads form (Admin & Super Admin).
     */
    public function addLoadMultiple()
    {
        return view('eload.add-load-multiple');
    }

    /**
     * Process add load transaction (Admin & Super Admin).
     */
    public function processLoad(Request $request)
    {
        $request->validate([
            'network' => 'required|string|max:255',
            'eload_number' => 'required|string|max:20',
            'price' => 'required|numeric|min:0.01',
            'status' => 'required|in:completed,not_completed',
        ]);

        // Clean and format the mobile number
        $eloadNumber = preg_replace('/[^0-9]/', '', $request->eload_number);
        
        // Ensure it starts with 0 for Philippine numbers
        if (strlen($eloadNumber) === 10 && !str_starts_with($eloadNumber, '0')) {
            $eloadNumber = '0' . $eloadNumber;
        }

        // Get or create a valid category
        $category = Category::first();
        if (!$category) {
            $category = Category::create([
                'name' => 'Default',
                'description' => 'Default category for custom loads',
                'status' => 'active'
            ]);
        }

        // Create a temporary eload record or use a default one
        $eload = Eload::firstOrCreate([
            'name' => 'Custom Load',
            'network' => $request->network,
            'price' => $request->price,
            'category_id' => $category->id,
            'status' => 'active'
        ], [
            'name' => 'Custom Load - ' . $request->network,
            'network' => $request->network,
            'price' => $request->price,
            'category_id' => $category->id,
            'status' => 'active'
        ]);

        // Create a temporary eload number record or use a default one
        $eloadNumberRecord = EloadNumber::where('number', $eloadNumber)->first();
        if (!$eloadNumberRecord) {
            $eloadNumberRecord = EloadNumber::create([
                'number' => $eloadNumber,
                'network' => $request->network,
                'status' => 'active'
            ]);
        }

        $transaction = EloadTransaction::create([
            'eload_id' => $eload->id,
            'eload_number_id' => $eloadNumberRecord->id,
            'user_id' => Auth::id(),
            'eload_number' => $eloadNumber,
            'price' => $request->price,
            'status' => $request->status,
            'transaction_id' => EloadTransaction::generateTransactionId(),
        ]);

        return redirect()->route('eload.transactions.history')
            ->with('success', 'Load transaction completed! Transaction ID: ' . $transaction->transaction_id);
    }

    /**
     * Process multiple load transactions (Admin & Super Admin).
     */
    public function processMultipleLoads(Request $request)
    {
        $request->validate([
            'loads' => 'required|array|min:1',
            'loads.*.network' => 'required|string|max:255',
            'loads.*.eload_number' => 'required|string|max:20',
            'loads.*.price' => 'required|numeric|min:0.01',
            'loads.*.status' => 'required|in:completed,not_completed',
        ]);

        // Get or create a valid category
        $category = Category::first();
        if (!$category) {
            $category = Category::create([
                'name' => 'Default',
                'description' => 'Default category for custom loads',
                'status' => 'active'
            ]);
        }

        $transactions = [];
        $totalAmount = 0;

        foreach ($request->loads as $loadData) {
            // Clean and format the mobile number
            $formattedNumber = preg_replace('/[^0-9]/', '', $loadData['eload_number']);
            
            // Ensure it starts with 0 for Philippine numbers
            if (strlen($formattedNumber) === 10 && !str_starts_with($formattedNumber, '0')) {
                $formattedNumber = '0' . $formattedNumber;
            }

            // Create a temporary eload record or use a default one
            $eload = Eload::firstOrCreate([
                'name' => 'Custom Load',
                'network' => $loadData['network'],
                'price' => $loadData['price'],
                'category_id' => $category->id,
                'status' => 'active'
            ], [
                'name' => 'Custom Load - ' . $loadData['network'],
                'network' => $loadData['network'],
                'price' => $loadData['price'],
                'category_id' => $category->id,
                'status' => 'active'
            ]);

            // Create a temporary eload number record or use a default one
            $eloadNumberRecord = EloadNumber::where('number', $formattedNumber)->first();
            if (!$eloadNumberRecord) {
                $eloadNumberRecord = EloadNumber::create([
                    'number' => $formattedNumber,
                    'network' => $loadData['network'],
                    'status' => 'active'
                ]);
            }

            $transaction = EloadTransaction::create([
                'eload_id' => $eload->id,
                'eload_number_id' => $eloadNumberRecord->id,
                'user_id' => Auth::id(),
                'eload_number' => $formattedNumber,
                'price' => $loadData['price'],
                'status' => $loadData['status'],
                'transaction_id' => EloadTransaction::generateTransactionId(),
            ]);

            $transactions[] = $transaction->transaction_id;
            $totalAmount += $loadData['price'];
        }

        return redirect()->route('eload.transactions.history')
            ->with('success', count($transactions) . ' load transactions processed successfully! Total: ₱' . number_format($totalAmount, 2));
    }

    // ==================== TRANSACTION HISTORY ====================

    /**
     * Display transaction history (Admin & Super Admin).
     */
    public function transactionsHistory(Request $request)
    {
        $query = EloadTransaction::with(['eload', 'eloadNumber', 'user']);

        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by network
        if ($request->has('network') && $request->network) {
            $query->whereHas('eload', function($q) use ($request) {
                $q->where('network', $request->network);
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();
        $networks = ['Smart', 'Globe', 'DITO'];

        return view('eload.transactions.history', compact('transactions', 'networks'));
    }

    /**
     * Update transaction status (Admin & Super Admin).
     */
    public function updateTransactionStatus(Request $request, EloadTransaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:completed,not_completed',
        ]);

        $transaction->update(['status' => $request->status]);

        return redirect()->route('eload.transactions.history')
            ->with('success', 'Transaction status updated successfully!');
    }
}

