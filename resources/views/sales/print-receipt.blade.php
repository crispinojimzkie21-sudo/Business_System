<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Receipt - {{ config('app.name') }}</title>
    <style>
        @media print {
            body { 
                background: white !important; 
                color: black !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            .receipt-container { 
                border: none !important; 
                box-shadow: none !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 5px !important;
            }
            .text-black { color: black !important; }
            .border-black { border-color: black !important; }
            .bg-white { background: white !important; }
        }
        
        body {
            font-family: 'Courier New', monospace;
            background: white;
            color: #000000 !important;
            margin: 0;
            padding: 5px;
            font-size: 11px;
            line-height: 1.2;
        }
        
        .receipt-container {
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
            border: 2px solid #000000;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #000000 !important;
            margin: 0;
            letter-spacing: 2px;
        }
        
        .receipt-title {
            font-size: 12px;
            font-weight: bold;
            color: #000000 !important;
            margin: 3px 0;
            letter-spacing: 1px;
        }
        
        .transaction-info {
            margin-bottom: 10px;
            font-size: 10px;
        }
        
        .transaction-info div {
            margin: 2px 0;
            color: #000000 !important;
        }
        
        .transaction-info strong {
            color: #000000 !important;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            font-size: 10px;
        }
        
        .items-table th {
            text-align: left;
            border-bottom: 2px solid #000000;
            padding: 3px 1px;
            font-weight: bold;
            color: #000000 !important;
            background: transparent;
            font-size: 9px;
        }
        
        .items-table td {
            padding: 2px 1px;
            border-bottom: 1px solid #000000;
            color: #000000 !important;
            font-size: 10px;
        }
        
        .total-section {
            margin: 10px 0;
            text-align: right;
            border-top: 2px solid #000000;
            padding-top: 5px;
        }
        
        .total-amount {
            font-size: 14px;
            font-weight: bold;
            color: #000000 !important;
        }
        
        .total-amount strong {
            color: #000000 !important;
        }
        
        .payment-info {
            border-top: 2px solid #000000;
            padding-top: 8px;
            margin-bottom: 10px;
            font-size: 10px;
        }
        
        .payment-info div {
            color: #000000 !important;
        }
        
        .payment-info strong {
            color: #000000 !important;
        }
        
        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 2px solid #000000;
            font-size: 9px;
            color: #000000 !important;
        }
        
        .footer p {
            color: #000000 !important;
        }
        
        .footer strong {
            color: #000000 !important;
        }
        
        .dashed-border {
            border-bottom: 2px solid #000000;
            margin: 5px 0;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="receipt-container">
        <!-- Business Header -->
        <div class="header">
            <h2 class="company-name">MANLIQUID STORE</h2>
            <p class="receipt-title">*** SALES RECEIPT ***</p>
            <p style="margin: 3px 0; font-size: 9px; color: #000000 !important;">Thank you for your purchase!</p>
        </div>

        <!-- Transaction Info -->
        <div class="transaction-info">
            <div><strong style="color: #000000 !important;">Receipt #:</strong> {{ $sale->transaction_id ?? 'N/A' }}</div>
            <div><strong style="color: #000000 !important;">Date:</strong> {{ $sale->created_at->format('m/d/Y H:i:s') }}</div>
            <div><strong style="color: #000000 !important;">Cashier:</strong> {{ $sale->user->name ?? 'Unknown' }}</div>
            @if($sale->customer_name)
            <div><strong style="color: #000000 !important;">Customer:</strong> {{ $sale->customer_name }}</div>
            @endif
            <div><strong style="color: #000000 !important;">Payment:</strong> {{ ucfirst($sale->payment_method) }}</div>
        </div>

        <div class="dashed-border"></div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 45%; color: #000000 !important;">ITEM</th>
                    <th style="width: 15%; text-align: center; color: #000000 !important;">QTY</th>
                    <th style="width: 20%; text-align: right; color: #000000 !important;">PRICE</th>
                    <th style="width: 20%; text-align: right; color: #000000 !important;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @if(is_array($items) || is_object($items))
                    @foreach($items as $item)
                        <tr>
                            <td style="color: #000000 !important;">{{ Str::limit($item['product_name'] ?? $item['name'] ?? 'Unknown', 25) }}</td>
                            <td style="text-align: center; color: #000000 !important;">{{ $item['quantity'] }}</td>
                            <td style="text-align: right; color: #000000 !important;">{{ number_format($item['unit_price'], 2) }}</td>
                            <td style="text-align: right; color: #000000 !important;">{{ number_format($item['subtotal'], 2) }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        <div class="dashed-border"></div>

        <!-- Total -->
        <div class="total-section">
            <div class="total-amount">
                <strong style="color: #000000 !important;">TOTAL: ₱{{ number_format($sale->total_amount, 2) }}</strong>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="payment-info">
            <div><strong style="color: #000000 !important;">Payment Method:</strong> {{ ucfirst($sale->payment_method) }}</div>
            @if($sale->cash_amount && $sale->change_amount)
            <div><strong style="color: #000000 !important;">Cash Received:</strong> ₱{{ number_format($sale->cash_amount, 2) }}</div>
            <div><strong style="color: #000000 !important;">Change:</strong> ₱{{ number_format($sale->change_amount, 2) }}</div>
            @endif
        </div>

        <div class="dashed-border"></div>

        <!-- Footer -->
        <div class="footer">
            <p style="color: #000000 !important;"><strong style="color: #000000 !important;">Thank you for doing business with us!</strong></p>
            <p style="color: #000000 !important;">MANLIQUID STORE</p>
            <p style="margin-top: 3px; font-size: 8px; color: #000000 !important;">*** OFFICIAL RECEIPT ***</p>
            <p style="font-size: 8px; color: #000000 !important;">{{ config('app.url') }}</p>
        </div>

        <!-- Back Button (No Print) -->
        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <a href="{{ route('sales.receipt', $sale->id) }}" 
               style="padding: 8px 16px; background: black; color: white; text-decoration: none; border-radius: 4px;">
                ← Back to Receipt
            </a>
        </div>
    </div>
</body>
</html>

