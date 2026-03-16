<?php

// Add these routes to your existing routes/web.php file

// Email Receipt Routes
Route::middleware(['web', 'auth'])->group(function () {
    // Send receipt via email
    Route::post('/sales/{sale}/send-email', [EmailReceiptController::class, 'sendReceiptEmail'])
        ->name('sales.send.email');
    
    // Test email configuration
    Route::post('/test-email', [EmailReceiptController::class, 'testEmailConfig'])
        ->name('test.email');
    
    // Resend receipt with retry
    Route::post('/sales/{sale}/resend-email', [EmailReceiptController::class, 'resendReceiptWithRetry'])
        ->name('sales.resend.email');
});

// Add these to your existing sales routes section:

// Enhanced Sales Routes with Email
Route::middleware(['web', 'auth'])->prefix('sales')->group(function () {
    Route::get('/', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/create', [SalesController::class, 'create'])->name('sales.create');
    Route::post('/', [SalesController::class, 'store'])->name('sales.store');
    Route::get('/reports', [SalesController::class, 'reports'])->name('sales.reports');
    Route::get('/history', [SalesController::class, 'history'])->name('sales.history');
    Route::get('/{sale}/receipt', [SalesController::class, 'receipt'])->name('sales.receipt');
    Route::get('/{sale}/print', [SalesController::class, 'printReceipt'])->name('sales.print');
    Route::post('/{sale}/resend-receipt', [SalesController::class, 'resendReceipt'])->name('sales.resend.receipt');
    Route::post('/{sale}/send-email', [EmailReceiptController::class, 'sendReceiptEmail'])->name('sales.send.email');
    Route::delete('/{sale}', [SalesController::class, 'destroy'])->name('sales.destroy');
});

echo "=== Email Receipt Routes ===\n";
echo "Add these routes to your routes/web.php file:\n\n";
echo "use App\Http\Controllers\EmailReceiptController;\n\n";
echo "// Email Receipt Routes\n";
echo "Route::middleware(['web', 'auth'])->group(function () {\n";
echo "    Route::post('/sales/{sale}/send-email', [EmailReceiptController::class, 'sendReceiptEmail'])\n";
echo "        ->name('sales.send.email');\n";
echo "    \n";
echo "    Route::post('/test-email', [EmailReceiptController::class, 'testEmailConfig'])\n";
echo "        ->name('test.email');\n";
echo "    \n";
echo "    Route::post('/sales/{sale}/resend-email', [EmailReceiptController::class, 'resendReceiptWithRetry'])\n";
echo "        ->name('sales.resend.email');\n";
echo "});\n\n";

echo "\n=== Frontend JavaScript for Email Sending ===\n";
echo "Add this to your receipt view:\n\n";
echo '// JavaScript functions for email sending
function sendReceiptEmail(saleId) {
    const email = prompt("Enter customer email address:");
    if (!email) return;
    
    const customerName = prompt("Enter customer name (optional):");
    
    fetch(`/sales/${saleId}/send-email`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute(\'content\')
        },
        body: JSON.stringify({
            email: email,
            customer_name: customerName || ""
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("✅ " + data.message);
        } else {
            alert("❌ " + data.message);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("❌ Failed to send email");
    });
}

function testEmailConfig() {
    fetch("/test-email", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector(\'meta[name="csrf-token"]\').getAttribute(\'content\')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("✅ " + data.message);
            console.log("Email Config:", data.config);
        } else {
            alert("❌ " + data.message);
            console.error("Email Config:", data.config);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("❌ Failed to test email");
    });
}';

echo "\n// Add these buttons to your receipt view:\n";
echo '<button onclick="sendReceiptEmail({{ $sale->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">' . "\n";
echo '    📧 Send Receipt' . "\n";
echo '</button>' . "\n";
echo '<button onclick="testEmailConfig()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">' . "\n";
echo '    🔧 Test Email Config' . "\n";
echo '</button>' . "\n";

echo "\n=== Done! ===\n";
