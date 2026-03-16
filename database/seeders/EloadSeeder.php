<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EloadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data to avoid duplicates
        DB::table('eload_numbers')->delete();
        DB::table('eloads')->delete();
        DB::table('eload_categories')->delete();

        // Create Categories
        $categories = [
            ['name' => 'Mobile Load', 'description' => 'Regular mobile phone load for calls and texts', 'status' => 'active'],
            ['name' => 'Data Load', 'description' => 'Internet data packages for mobile browsing', 'status' => 'active'],
            ['name' => 'Gaming Load', 'description' => 'Load for mobile gaming and in-app purchases', 'status' => 'active'],
            ['name' => 'Promo Bundle', 'description' => 'Combined call, text, and data promos', 'status' => 'active'],
        ];

        foreach ($categories as $category) {
            DB::table('eload_categories')->insert($category);
        }

        // Get the inserted category IDs
        $categoryIds = DB::table('eload_categories')->pluck('id')->toArray();

        // Create E-Load Products
        $eloads = [
            // Smart
            ['name' => 'GO+99', 'network' => 'Smart', 'price' => 99.00, 'category_id' => $categoryIds[0] ?? 1, 'status' => 'active'],
            ['name' => 'GO+150', 'network' => 'Smart', 'price' => 150.00, 'category_id' => $categoryIds[0] ?? 1, 'status' => 'active'],
            ['name' => 'GO+300', 'network' => 'Smart', 'price' => 300.00, 'category_id' => $categoryIds[0] ?? 1, 'status' => 'active'],
            ['name' => 'Smart 299 Data', 'network' => 'Smart', 'price' => 299.00, 'category_id' => $categoryIds[1] ?? 2, 'status' => 'active'],
            ['name' => 'Smart Data 599', 'network' => 'Smart', 'price' => 599.00, 'category_id' => $categoryIds[1] ?? 2, 'status' => 'active'],
            ['name' => 'ML Diamond', 'network' => 'Smart', 'price' => 50.00, 'category_id' => $categoryIds[2] ?? 3, 'status' => 'active'],
            ['name' => 'ML Ruby', 'network' => 'Smart', 'price' => 100.00, 'category_id' => $categoryIds[2] ?? 3, 'status' => 'active'],
            
            // Globe
            ['name' => 'GoTXT 30', 'network' => 'Globe', 'price' => 30.00, 'category_id' => $categoryIds[0] ?? 1, 'status' => 'active'],
            ['name' => 'GoTXT 60', 'network' => 'Globe', 'price' => 60.00, 'category_id' => $categoryIds[0] ?? 1, 'status' => 'active'],
            ['name' => 'GoTXT 100', 'network' => 'Globe', 'price' => 100.00, 'category_id' => $categoryIds[0] ?? 1, 'status' => 'active'],
            ['name' => 'Globe Data 299', 'network' => 'Globe', 'price' => 299.00, 'category_id' => $categoryIds[1] ?? 2, 'status' => 'active'],
            ['name' => 'Globe Data 599', 'network' => 'Globe', 'price' => 599.00, 'category_id' => $categoryIds[1] ?? 2, 'status' => 'active'],
            ['name' => 'GCash 50', 'network' => 'Globe', 'price' => 50.00, 'category_id' => $categoryIds[2] ?? 3, 'status' => 'active'],
            ['name' => 'GCash 100', 'network' => 'Globe', 'price' => 100.00, 'category_id' => $categoryIds[2] ?? 3, 'status' => 'active'],
            
            // DITO
            ['name' => 'DITO 30', 'network' => 'DITO', 'price' => 30.00, 'category_id' => $categoryIds[0] ?? 1, 'status' => 'active'],
            ['name' => 'DITO 50', 'network' => 'DITO', 'price' => 50.00, 'category_id' => $categoryIds[0] ?? 1, 'status' => 'active'],
            ['name' => 'DITO 100', 'network' => 'DITO', 'price' => 100.00, 'category_id' => $categoryIds[0] ?? 1, 'status' => 'active'],
            ['name' => 'DITO Data 199', 'network' => 'DITO', 'price' => 199.00, 'category_id' => $categoryIds[1] ?? 2, 'status' => 'active'],
            ['name' => 'DITO Data 399', 'network' => 'DITO', 'price' => 399.00, 'category_id' => $categoryIds[1] ?? 2, 'status' => 'active'],
        ];

        foreach ($eloads as $eload) {
            DB::table('eloads')->insert($eload);
        }

        // Create E-Load Numbers (Gateway Numbers)
        $eloadNumbers = [
            // Smart Numbers
            ['number' => '09123456701', 'network' => 'Smart', 'status' => 'active', 'description' => 'Smart Main Gateway'],
            ['number' => '09123456702', 'network' => 'Smart', 'status' => 'active', 'description' => 'Smart Secondary Gateway'],
            ['number' => '09123456703', 'network' => 'Smart', 'status' => 'active', 'description' => 'Smart Backup Gateway'],
            
            // Globe Numbers
            ['number' => '09151234567', 'network' => 'Globe', 'status' => 'active', 'description' => 'Globe Main Gateway'],
            ['number' => '09151234568', 'network' => 'Globe', 'status' => 'active', 'description' => 'Globe Secondary Gateway'],
            ['number' => '09151234569', 'network' => 'Globe', 'status' => 'active', 'description' => 'Glome Backup Gateway'],
            
            // DITO Numbers
            ['number' => '09911234567', 'network' => 'DITO', 'status' => 'active', 'description' => 'DITO Main Gateway'],
            ['number' => '09911234568', 'network' => 'DITO', 'status' => 'active', 'description' => 'DITO Secondary Gateway'],
            ['number' => '09911234569', 'network' => 'DITO', 'status' => 'active', 'description' => 'DITO Backup Gateway'],
        ];

        foreach ($eloadNumbers as $number) {
            DB::table('eload_numbers')->insert($number);
        }

        $this->command->info('E-Load categories, products, and numbers seeded successfully!');
    }
}

