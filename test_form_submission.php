<?php

/**
 * Test Form Submission
 * This script will simulate the actual form submission to identify the error
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RM Manliquid Business System - Test Form Submission ===\n\n";

try {
    echo "Testing actual form submission...\n";
    echo str_repeat("=", 50) . "\n";
    
    // Simulate the exact form data that would be submitted
    $formData = [
        'network' => 'Globe',
        'eload_number' => '09123456789',
        'price' => '100.00',
        'status' => 'completed',
        '_token' => 'test-token',
    ];
    
    echo "Form data to be submitted:\n";
    foreach ($formData as $key => $value) {
        echo "- {$key}: {$value}\n";
    }
    
    echo "\nTesting validation rules...\n";
    echo str_repeat("-", 30) . "\n";
    
    // Test validation rules
    $rules = [
        'network' => 'required|string|max:255',
        'eload_number' => 'required|string|max:20',
        'price' => 'required|numeric|min:0.01',
        'status' => 'required|in:completed,not_completed',
    ];
    
    foreach ($rules as $field => $rule) {
        $value = $formData[$field] ?? null;
        echo "- {$field}: '{$value}' - Rule: {$rule}\n";
        
        // Simple validation checks
        if (str_contains($rule, 'required') && empty($value)) {
            echo "  ERROR: {$field} is required but empty\n";
        }
        if (str_contains($rule, 'max:20') && strlen($value) > 20) {
            echo "  ERROR: {$field} exceeds max length\n";
        }
        if (str_contains($rule, 'numeric') && !is_numeric($value)) {
            echo "  ERROR: {$field} must be numeric\n";
        }
        if (str_contains($rule, 'min:0.01') && is_numeric($value) && $value < 0.01) {
            echo "  ERROR: {$field} must be at least 0.01\n";
        }
        if (str_contains($rule, 'in:completed,not_completed') && !in_array($value, ['completed', 'not_completed'])) {
            echo "  ERROR: {$field} must be completed or not_completed\n";
        }
    }
    
    echo "\nTesting controller processLoad method...\n";
    echo str_repeat("-", 30) . "\n";
    
    // Create a proper request
    $request = new \Illuminate\Http\Request();
    $request->merge($formData);
    
    // Mock Auth
    if (!class_exists('Illuminate\Support\Facades\Auth')) {
        class MockAuth {
            public static function id() { return 1; }
            public static function user() { 
                $user = new stdClass();
                $user->id = 1;
                $user->role = 'super_admin';
                return $user;
            }
        }
        
        // Create an alias for our mock
        class_alias('MockAuth', 'Illuminate\Support\Facades\Auth');
    }
    
    // Create controller instance
    $controller = new \App\Http\Controllers\EloadController();
    
    echo "Controller created successfully\n";
    
    // Test the processLoad method step by step
    echo "\nStep-by-step execution:\n";
    
    try {
        // Step 1: Validation
        echo "1. Validating request...\n";
        $request->validate([
            'network' => 'required|string|max:255',
            'eload_number' => 'required|string|max:20',
            'price' => 'required|numeric|min:0.01',
            'status' => 'required|in:completed,not_completed',
        ]);
        echo "   Validation passed\n";
        
        // Step 2: Clean mobile number
        echo "2. Cleaning mobile number...\n";
        $eloadNumber = preg_replace('/[^0-9]/', '', $request->eload_number);
        echo "   Original: " . $request->eload_number . "\n";
        echo "   Cleaned: " . $eloadNumber . "\n";
        
        // Step 3: Format mobile number
        echo "3. Formatting mobile number...\n";
        if (strlen($eloadNumber) === 10 && !str_starts_with($eloadNumber, '0')) {
            $eloadNumber = '0' . $eloadNumber;
            echo "   Formatted: " . $eloadNumber . "\n";
        } else {
            echo "   No formatting needed: " . $eloadNumber . "\n";
        }
        
        // Step 4: Get category
        echo "4. Getting category...\n";
        $category = \App\Models\Category::first();
        if (!$category) {
            echo "   Creating default category...\n";
            $category = \App\Models\Category::create([
                'name' => 'Default',
                'description' => 'Default category for custom loads',
                'status' => 'active'
            ]);
        }
        echo "   Category: " . $category->name . " (ID: " . $category->id . ")\n";
        
        // Step 5: Create/find eload
        echo "5. Creating/finding eload...\n";
        $eload = \App\Models\Eload::firstOrCreate([
            'name' => 'Custom Load',
            'network' => $request->network,
            'price' => $request->price,
            'category_id' => $category->id,
            'status' => 'active'
        ], [
            'name' => 'Custom Load - ' . $request->network,
            'network' => $request->network,
            'provider' => $request->network . ' Telecom',
            'service_type' => 'Mobile Load',
            'code' => strtoupper(str_replace(' ', '', $request->network)),
            'description' => 'Custom load for ' . $request->network,
            'validity' => '30 days',
            'price' => $request->price,
            'category_id' => $category->id,
            'status' => 'active'
        ]);
        echo "   E-Load: " . $eload->name . " (ID: " . $eload->id . ")\n";
        
        // Step 6: Create/find eload number
        echo "6. Creating/finding eload number...\n";
        $eloadNumberRecord = \App\Models\EloadNumber::where('number', $eloadNumber)->first();
        if (!$eloadNumberRecord) {
            $eloadNumberRecord = \App\Models\EloadNumber::create([
                'number' => $eloadNumber,
                'network' => $request->network,
                'provider' => $request->network . ' Telecom',
                'number_type' => 'Mobile',
                'description' => $request->network . ' mobile number',
                'is_active' => 1,
                'priority' => 1,
                'status' => 'active'
            ]);
        }
        echo "   E-Load Number: " . $eloadNumberRecord->number . " (ID: " . $eloadNumberRecord->id . ")\n";
        
        // Step 7: Convert status
        echo "7. Converting status...\n";
        $status = $request->status === 'completed' ? 'completed' : 'pending';
        echo "   Status: " . $request->status . " -> " . $status . "\n";
        
        // Step 8: Create transaction
        echo "8. Creating transaction...\n";
        $transaction = \App\Models\EloadTransaction::create([
            'eload_id' => $eload->id,
            'eload_number_id' => $eloadNumberRecord->id,
            'user_id' => 1,
            'eload_number' => $eloadNumber,
            'price' => $request->price,
            'original_price' => $request->price,
            'amount' => $request->price,
            'customer_name' => 'Walk-in Customer',
            'customer_mobile' => $eloadNumber,
            'network' => $eload->network ?? $request->network,
            'provider' => $eload->provider ?? $request->network . ' Telecom',
            'status' => $status,
            'transaction_id' => 'EL-' . strtoupper(uniqid()) . '-' . rand(1000, 9999),
            'reference_number' => 'REF-' . date('YmdHis') . '-' . rand(100, 999),
            'processed_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        echo "   Transaction created! ID: " . $transaction->id . "\n";
        echo "   Transaction ID: " . $transaction->transaction_id . "\n";
        
        echo "\nSUCCESS: Form submission test completed successfully!\n";
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        echo "   Validation ERROR: " . $e->getMessage() . "\n";
        echo "   Errors: " . implode(', ', $e->errors()->all()) . "\n";
    } catch (Exception $e) {
        echo "   ERROR: " . $e->getMessage() . "\n";
        echo "   File: " . $e->getFile() . "\n";
        echo "   Line: " . $e->getLine() . "\n";
    }
    
} catch (Exception $e) {
    echo "Critical error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
