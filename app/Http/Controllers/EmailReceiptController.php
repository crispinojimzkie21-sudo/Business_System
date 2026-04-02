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
     * Send receipt via email with enhanced Gmail compatibility
     */
    public function sendReceiptEmail(Sale $sale, Request $request)
    {
        $request->validate([
            "email" => "required|email|max:255",
            "customer_name" => "nullable|string|max:255",
        ]);

        try {
            // Load sale with relationships
            $sale->load("user");
            $items = is_array($sale->items) ? $sale->items : json_decode($sale->items, true);

            // Ensure items is properly formatted
            if (!$items) {
                $items = [];
            }

            // Update sale record with customer info
            $sale->update([
                "customer_email" => $request->email,
                "customer_name" => $request->customer_name ?? $sale->customer_name,
            ]);

            // Create email with proper configuration
            $email = new SaleReceipt($sale, $items);
            
            // Set proper email headers for Gmail compatibility
            $email->from(config("mail.from.address"), config("mail.from.name"));
            $email->subject("Receipt #" . $sale->transaction_id . " - Manliquid Store");
            
            // Send email
            Mail::to($request->email)->send($email);

            // Log success
            Log::info("Receipt email sent successfully", [
                "sale_id" => $sale->id,
                "customer_email" => $request->email,
                "amount" => $sale->total_amount,
                "transaction_id" => $sale->transaction_id
            ]);

            return response()->json([
                "success" => true,
                "message" => "Receipt sent successfully to " . $request->email,
                "transaction_id" => $sale->transaction_id
            ]);

        } catch (\Exception $e) {
            // Log detailed error for debugging
            Log::error("Failed to send receipt email", [
                "sale_id" => $sale->id,
                "customer_email" => $request->email,
                "error" => $e->getMessage(),
                "trace" => $e->getTraceAsString()
            ]);

            return response()->json([
                "success" => false,
                "message" => "Failed to send email: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Quick send receipt (simplified version)
     */
    public function quickSendReceipt(Sale $sale, Request $request)
    {
        $email = $request->email ?? $sale->customer_email;
        
        if (!$email) {
            return response()->json([
                "success" => false,
                "message" => "No email address provided"
            ], 400);
        }

        try {
            $sale->load("user");
            $items = is_array($sale->items) ? $sale->items : json_decode($sale->items, true);
            
            Mail::to($email)->send(new SaleReceipt($sale, $items));
            
            return response()->json([
                "success" => true,
                "message" => "Receipt sent to $email"
            ]);
            
        } catch (\Exception $e) {
            Log::error("Quick send failed", [
                "sale_id" => $sale->id,
                "email" => $email,
                "error" => $e->getMessage()
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Send failed: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test email configuration
     */
    public function testEmailConfig()
    {
        try {
            Mail::raw("Test Email from Manliquid Store
            
This is a test to verify your email configuration is working.
Time: " . now()->format("Y-m-d H:i:s") . "
System: Business System

If you receive this, your email receipt system is working perfectly!", function($message) {
                $message->to(config("mail.from.address"))
                       ->subject("✅ Email Test Successful - Manliquid Store")
                       ->from(config("mail.from.address"), config("mail.from.name"));
            });

            return response()->json([
                "success" => true,
                "message" => "Test email sent successfully!",
                "config" => [
                    "mailer" => config("mail.default"),
                    "host" => config("mail.mailers.smtp.host"),
                    "port" => config("mail.mailers.smtp.port"),
                    "encryption" => config("mail.mailers.smtp.encryption") ?? "none",
                    "from_address" => config("mail.from.address"),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "success" => false,
                "message" => "Test failed: " . $e->getMessage(),
                "troubleshooting" => [
                    "Check Gmail App Password",
                    "Verify 2-Factor Authentication",
                    "Confirm SMTP settings",
                    "Check internet connection"
                ]
            ], 500);
        }
    }
}