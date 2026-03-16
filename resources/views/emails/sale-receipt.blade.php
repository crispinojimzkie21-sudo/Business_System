<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sale Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
        }
        .receipt-info {
            background-color: #f3f4f6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .receipt-info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .totals {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }
        .total-amount {
            color: #16a34a;
            font-size: 24px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Manliquid Store</div>
        <p>Thank you for your purchase!</p>
    </div>

    <div class="receipt-info">
        <p><strong>Transaction ID:</strong> {{ $sale->transaction_id ?? 'N/A' }}</p>
        <p><strong>Date:</strong> {{ $sale->created_at->format('F j, Y g:i A') }}</p>
        <p><strong>Payment Method:</strong> {{ ucfirst($sale->payment_method) }}</p>
        @if($sale->customer_name)
        <p><strong>Customer:</strong> {{ $sale->customer_name }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['product_name'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>₱{{ number_format($item['unit_price'], 2) }}</td>
                <td>₱{{ number_format($item['subtotal'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p class="total-amount">Total: ₱{{ number_format($sale->total_amount, 2) }}</p>
    </div>

    <div style="text-align: center;">
        <a href="{{ route('sales.receipt', $sale->id) }}" class="btn">View Full Receipt</a>
    </div>

    <div class="footer">
        <p>Thank you for doing business with us!</p>
        <p>Manliquid Store - {{ config('app.url') }}</p>
    </div>
</body>
</html>

