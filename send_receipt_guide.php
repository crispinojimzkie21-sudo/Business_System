<?php

echo "=== How to Send Receipts via Gmail SMTP ===\n\n";

echo "=== METHOD 1: Automatic Receipt (After Sale) ===\n";
echo "This happens automatically when you create a sale:\n\n";

echo "1. Create a Sale with Customer Email\n";
echo "   - Go to: /sales/create\n";
echo "   - Fill sale details\n";
echo "   - Enter customer email in 'Customer Email' field\n";
echo "   - Click 'Complete Sale'\n\n";

echo "2. Receipt is Sent Automatically\n";
echo "   - System sends receipt to customer email\n";
echo "   - Uses Gmail SMTP (Manliquid Store)\n";
echo "   - Customer receives professional receipt\n\n";

echo "=== METHOD 2: Manual Receipt Sending ===\n";
echo "Send receipt to any email address:\n\n";

echo "1. Go to Receipt Page\n";
echo "   - Find sale in sales history\n";
echo "   - Click 'View Receipt'\n\n";

echo "2. Send Email Button\n";
echo "   - Click '📧 Send Receipt' button\n";
echo "   - Enter customer email address\n";
echo "   - Enter customer name (optional)\n";
echo "   - Click 'Send'\n\n";

echo "=== METHOD 3: Resend Receipt ===\n";
echo "Resend to existing customer email:\n\n";

echo "1. From Receipt Page\n";
echo "   - Go to sale receipt\n";
echo "   - Click '🔄 Resend Receipt'\n";
echo "   - Email sent to original customer email\n\n";

echo "=== METHOD 4: Test Email Function ===\n";
echo "Test if Gmail SMTP is working:\n\n";

echo "1. Add Test Button to Any Page\n";
echo '<button onclick="testEmailConfig()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
    🔧 Test Email Config
</button>

<script>
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
}
</script>';

echo "\n=== METHOD 5: Send Receipt via Code ===\n";
echo "Programmatically send receipt:\n\n";

echo '<?php
// Send receipt to customer
use Illuminate\Support\Facades\Mail;
use App\Mail\SaleReceipt;

$sale = Sale::find(1); // Get sale
$items = $sale->items; // Get sale items
$customerEmail = "customer@example.com";

try {
    Mail::to($customerEmail)->send(new SaleReceipt($sale, $items));
    echo "✅ Receipt sent to " . $customerEmail;
} catch (Exception $e) {
    echo "❌ Failed: " . $e->getMessage();
}
';

echo "\n=== What Happens When Receipt is Sent ===\n";
echo "1. Email is queued using Gmail SMTP\n";
echo "2. Sent from: Manliquid Store <your-gmail@gmail.com>\n";
echo "3. Subject: Sale Receipt - Manliquid Store\n";
echo "4. Content: Professional receipt with:\n";
echo "   - Sale details\n";
echo "   - Item list and prices\n";
echo "   - Total amount\n";
echo "   - Payment method\n";
echo "   - Transaction date\n\n";

echo "=== Gmail SMTP Process ===\n";
echo "1. Laravel queues the email\n";
echo "2. Connects to smtp.gmail.com:587\n";
echo "3. Uses TLS encryption\n";
echo "4. Authenticates with App Password\n";
echo "5. Sends email to customer\n";
echo "6. Logs success/failure\n\n";

echo "=== Troubleshooting Receipt Sending ===\n\n";

echo "❌ Email not received?\n";
echo "✅ Check:\n";
echo "   - Customer email address is correct\n";
echo "   - Check spam/junk folder\n";
echo "   - Gmail SMTP is configured\n";
echo "   - App Password is correct\n\n";

echo "❌ Authentication error?\n";
echo "✅ Check:\n";
echo "   - App Password: jycm lnkq qpkg gaom\n";
echo "   - 2-Step Verification enabled\n";
echo "   - Gmail address is correct\n\n";

echo "❌ Connection timeout?\n";
echo "✅ Check:\n";
echo "   - Port 587 is open\n";
echo "   - TLS encryption is enabled\n";
echo "   - Firewall allows SMTP\n\n";

echo "=== Quick Test Steps ===\n\n";
echo "1. Create test sale with your email\n";
echo "2. Check if you receive receipt\n";
echo "3. If not, check error logs\n";
echo "4. Run test email function\n";
echo "5. Verify Gmail SMTP settings\n\n";

echo "=== Success Indicators ===\n";
echo "✅ Email sent successfully message\n";
echo "✅ Customer receives professional receipt\n";
echo "✅ From: Manliquid Store\n";
echo "✅ Subject: Sale Receipt - Manliquid Store\n";
echo "✅ Content: Complete sale details\n\n";

echo "=== Done! Receipt sending ready! ===\n";
