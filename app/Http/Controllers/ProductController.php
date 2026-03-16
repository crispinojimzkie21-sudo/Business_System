<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
public function index()
    {
        $products = Product::paginate(15);
        
        // Calculate statistics - SQLite compatible
        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->where('stock_quantity', '>', 0)
            ->count();
        $outOfStockCount = Product::where('stock_quantity', 0)->count();
        $totalValue = Product::all()->sum(function($p) { return $p->stock_quantity * $p->price; });
        
        return view('products.index', compact('products', 'lowStockCount', 'outOfStockCount', 'totalValue'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'sku' => 'required|string|max:100|unique:products',
            'category' => 'nullable|string|max:255',
        ]);

        Log::info('Creating product with data:', $validatedData);

        try {
            $product = Product::create($validatedData);
            Log::info('Product created successfully:', ['product_id' => $product->id, 'name' => $product->name]);
            return redirect()->route('products.index')->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to create product:', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to create product: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'min_stock_level' => 'required|integer|min:0',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
            'category' => 'nullable|string|max:255',
        ]);

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    }

    public function inventory()
    {
        // SQLite compatible query
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'min_stock_level')
            ->where('stock_quantity', '>', 0)
            ->get();
        $outOfStockProducts = Product::where('stock_quantity', 0)->get();
        $allProducts = Product::orderBy('stock_quantity', 'asc')->paginate(20);
        $totalValue = Product::all()->sum(function($p) { return $p->stock_quantity * $p->price; });

        return view('inventory.index', compact('lowStockProducts', 'outOfStockProducts', 'allProducts', 'totalValue'));
    }

    public function updateStock(Request $request, Product $product)
    {
        $data = $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'operation' => 'required|in:set,add,subtract',
        ]);

        switch ($data['operation']) {
            case 'set':
                $product->stock_quantity = $data['stock_quantity'];
                break;
            case 'add':
                $product->stock_quantity += $data['stock_quantity'];
                break;
            case 'subtract':
                $product->stock_quantity = max(0, $product->stock_quantity - $data['stock_quantity']);
                break;
        }

        $product->save();

        return back()->with('success', 'Stock updated successfully!');
    }
}
