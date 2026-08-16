<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Path: resources/views/sales/receipt.php -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Receipt - Nour Trust</title>
    <style>
        /* Standalone Print CSS optimized for 80mm Thermal Printers */
        @import url('https://fonts.googleapis.com/css2?family=Courier+Prime:ital,wght@0,400;0,700;1,400&family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .receipt-container {
            width: 80mm;
            background: white;
            padding: 5mm;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: 700;
        }

        .text-xs {
            font-size: 11px;
        }

        .text-sm {
            font-size: 13px;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mt-2 {
            margin-top: 8px;
        }

        .border-b {
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }

        .border-t {
            border-top: 1px dashed #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            text-align: left;
            padding: 2px 0;
            font-size: 12px;
        }

        th {
            font-weight: 600;
        }

        .qty {
            width: 15%;
            text-align: center;
        }

        .price {
            width: 25%;
            text-align: right;
        }

        .total {
            width: 25%;
            text-align: right;
        }

        .logo {
            font-size: 24px;
            font-weight: 900;
            font-family: 'Courier Prime', monospace;
            margin-bottom: 5px;
        }

        .qr-code {
            margin: 15px auto;
            width: 100px;
            height: 100px;
            background-color: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #000;
        }

        .qr-code img {
            max-width: 100%;
            height: auto;
            mix-blend-multiply: multiply;
        }

        .no-print {
            position: fixed;
            top: 20px;
            left: 20px;
            padding: 10px 20px;
            background: #0a1930;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        @media print {
            body {
                background: none;
                padding: 0;
                display: block;
            }

            .receipt-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                width: 100%;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <button class="no-print" onclick="window.print()">Print Receipt</button>

    <div class="receipt-container">
        <!-- Header -->
        <div class="text-center border-b">
            <div class="logo">NOUR TRUST</div>
            <div class="text-xs">Riyadh Branch, King Fahd Rd.</div>
            <div class="text-xs">VAT Reg: 300123456789003</div>
            <div class="text-xs mt-2">Tel: +966 50 123 4567</div>
        </div>

        <!-- Meta -->
        <div class="text-xs mb-2 border-b">
            <div><span class="font-bold">Date:</span> 2026-08-15 14:30:22</div>
            <div><span class="font-bold">Receipt:</span> POS-260815-042</div>
            <div><span class="font-bold">Cashier:</span> Ahmed H. (TERM-01)</div>
        </div>

        <!-- Items -->
        <table class="border-b">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="qty">Qty</th>
                    <th class="price">Price</th>
                    <th class="total">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Wireless Mouse</td>
                    <td class="qty">2</td>
                    <td class="price">25.00</td>
                    <td class="total">50.00</td>
                </tr>
                <tr>
                    <td>Keyboard Pro</td>
                    <td class="qty">1</td>
                    <td class="price">85.50</td>
                    <td class="total">85.50</td>
                </tr>
                <tr>
                    <td>HDMI Cable 2m</td>
                    <td class="qty">3</td>
                    <td class="price">15.00</td>
                    <td class="total">45.00</td>
                </tr>
            </tbody>
        </table>

        <!-- Totals -->
        <div class="text-sm">
            <div style="display: flex; justify-content: space-between;">
                <span>Subtotal:</span>
                <span>180.50</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>VAT (15%):</span>
                <span>27.08</span>
            </div>
            <div class="border-t font-bold mt-2" style="display: flex; justify-content: space-between; font-size: 16px;">
                <span>TOTAL:</span>
                <span>207.58 SAR</span>
            </div>
        </div>

        <!-- Payment -->
        <div class="border-t text-xs mt-2 pt-2">
            <div style="display: flex; justify-content: space-between;">
                <span>Paid by:</span>
                <span class="font-bold">Credit Card (VISA)</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>Auth Code:</span>
                <span>09912A</span>
            </div>
        </div>

        <!-- QR Code ZATCA -->
        <div class="qr-code">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=AQVab3VyIFRydXN0Ag8zMDAxMjM0NTY3ODkwMDM=" alt="QR">
        </div>

        <!-- Footer -->
        <div class="text-center text-xs mt-2 font-bold">
            Thank you for your business!<br>
            Please keep this receipt for returns.
        </div>
    </div>

</body>

</html>