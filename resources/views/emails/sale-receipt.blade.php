<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sale Receipt</title>
    <style>
        @media print {
            body { 
                background: white !important; 
                color: black !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .header { border-bottom: 2px solid black !important; }
            .company-name { color: black !important; }
            .receipt-info { 
                background-color: #f9f9f9 !important; 
                color: black !important;
                border: 1px solid black !important;
            }
            table { color: black !important; }
            th, td { 
                border-bottom: 1px solid black !important; 
                color: black !important;
            }
            th { 
                background-color: #f0f0f0 !important; 
                color: black !important;
            }
            .total-amount { color: black !important; }
            .footer { 
                border-top: 1px solid black !important; 
                color: black !important;
            }
            .btn { display: none !important; }
        }
        
        body {
            font-family: 'Courier New', monospace;
            line-height: 1.4;
            color: black;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid black;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: black;
        }
        .receipt-info {
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .receipt-info p {
            margin: 5px 0;
            color: black;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            color: black;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid black;
            color: black;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            color: black;
        }
        .totals {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: black;
        }
        .total-amount {
            color: black;
            font-size: 24px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid black;
            color: black;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">MANLIQUID STORE</div>
        <p style="color: black; font-weight: bold;">SALES RECEIPT</p>
        <p style="color: black; font-size: 12px;">Thank you for your purchase!</p>
    </div>

    <div class="receipt-info">
        <p><strong>Transaction ID:</strong> {{ $sale->transaction_id ?? 'N/A' }}</p>
        <p><strong>Date:</strong> {{ $sale->created_at->format('F j, Y g:i A') }}</p>
        <p><strong>Payment Method:</strong> {{ ucfirst($sale->payment_method) }}</p>
        @if($sale->customer_name)
        <p><strong>Customer:</strong> {{ $sale->customer_name }}</p>
        @endif
        @if($sale->user && $sale->user->name)
        <p><strong>Cashier:</strong> {{ $sale->user->name }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>PRODUCT</th>
                <th>QTY</th>
                <th>PRICE</th>
                <th>SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['product_name'] ?? $item['name'] ?? 'Unknown' }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>₱{{ number_format($item['unit_price'], 2) }}</td>
                <td>₱{{ number_format($item['subtotal'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p class="total-amount">TOTAL: ₱{{ number_format($sale->total_amount, 2) }}</p>
    </div>

    <div style="text-align: center;">
        <a href="{{ route('sales.receipt', $sale->id) }}" class="btn">View Full Receipt</a>
    </div>

    <div class="footer">
        <p><strong>Thank you for doing business with us!</strong></p>
        <p>MANLIQUID STORE - {{ config('app.url') }}</p>
        <p style="font-size: 11px; margin-top: 10px;">This is an official receipt from Manliquid Store</p>
    </div>
</body>
</html>

