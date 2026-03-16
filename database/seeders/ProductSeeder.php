<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop Computer',
                'sku' => 'ELEC-001',
                'description' => 'High-performance laptop for office work',
                'price' => 25000.00,
                'cost' => 18000.00,
                'stock_quantity' => 5,
                'min_stock_level' => 3,
                'category' => 'Electronics',
            ],
            [
                'name' => 'Office Chair',
                'sku' => 'FURN-001',
                'description' => 'Ergonomic office chair with lumbar support',
                'price' => 3500.00,
                'cost' => 2200.00,
                'stock_quantity' => 12,
                'min_stock_level' => 5,
                'category' => 'Other',
            ],
            [
                'name' => 'Wireless Mouse',
                'sku' => 'ELEC-002',
                'description' => 'Ergonomic wireless mouse',
                'price' => 450.00,
                'cost' => 280.00,
                'stock_quantity' => 25,
                'min_stock_level' => 10,
                'category' => 'Electronics',
            ],
            [
                'name' => 'Coffee Beans',
                'sku' => 'FOOD-001',
                'description' => 'Premium arabica coffee beans 1kg',
                'price' => 850.00,
                'cost' => 550.00,
                'stock_quantity' => 2,  // Low stock
                'min_stock_level' => 5,
                'category' => 'Food',
            ],
            [
                'name' => 'Bottled Water',
                'sku' => 'BEV-001',
                'description' => 'Mineral water 500ml bottle',
                'price' => 25.00,
                'cost' => 15.00,
                'stock_quantity' => 0,  // Out of stock
                'min_stock_level' => 20,
                'category' => 'Beverages',
            ],
            [
                'name' => 'Office Shirt',
                'sku' => 'CLOTH-001',
                'description' => 'Professional office shirt white',
                'price' => 1200.00,
                'cost' => 750.00,
                'stock_quantity' => 15,
                'min_stock_level' => 8,
                'category' => 'Clothing',
            ],
            [
                'name' => 'Desk Lamp',
                'sku' => 'ELEC-003',
                'description' => 'LED desk lamp with adjustable brightness',
                'price' => 890.00,
                'cost' => 620.00,
                'stock_quantity' => 8,
                'min_stock_level' => 4,
                'category' => 'Electronics',
            ],
            [
                'name' => 'Notebook Set',
                'sku' => 'STAT-001',
                'description' => 'Set of 5 notebooks for office use',
                'price' => 150.00,
                'cost' => 95.00,
                'stock_quantity' => 30,
                'min_stock_level' => 15,
                'category' => 'Other',
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['sku' => $product['sku']], $product);
        }

        $this->command->info('Sample products created successfully!');
    }
}
