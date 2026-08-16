<?php
// Path: resources/views/documents/sales-order/default.php
// Standalone Print Layout - Sales Order Confirmation
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Order - SO-2026-042</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] }, colors: { nour: { dark: '#0a1930', primary: '#005eb8', light: '#21a1f1' } } } } }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #525659; margin: 0; padding: 2rem 0; display: flex; justify-content: center; color: #1f2937; }
        .a4-page { width: 210mm; min-height: 297mm; padding: 15mm; margin: 0 auto; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        @media print { @page { size: A4; margin: 0; } body { background: none; padding: 0; } .a4-page { box-shadow: none; padding: 10mm; margin: 0; width: 100%; } .no-print { display: none !important; } }
    </style>
</head>
<body>
    <button onclick="window.print()" class="no-print fixed top-4 right-4 bg-nour-primary text-white px-5 py-2.5 rounded-lg shadow-xl font-medium z-50">Print Order</button>

    <div class="a4-page text-sm">
        
        <!-- Included Header Partial conceptually -->
        <?php require_once __DIR__ . '/../common/header.php'; ?>

        <div class="flex justify-between mb-8 pb-6 border-b border-gray-200">
            <?php require_once __DIR__ . '/../common/customer-info.php'; ?>
            
            <div class="w-1/3 space-y-2">
                <div class="flex justify-between border-b border-gray-100 pb-1">
                    <span class="text-gray-500 font-medium">Order Date:</span><span class="font-semibold text-gray-900">Aug 14, 2026</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-1">
                    <span class="text-gray-500 font-medium">Expected Delivery:</span><span class="font-semibold text-nour-primary">Aug 20, 2026</span>
                </div>
            </div>
        </div>

        <div class="mb-8 rounded-lg overflow-hidden border border-gray-300">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-3 px-4 font-bold border-r border-gray-300">Description</th>
                        <th class="py-3 px-4 w-20 text-center font-bold border-r border-gray-300">Qty</th>
                        <th class="py-3 px-4 w-28 text-right font-bold border-r border-gray-300">Unit Price</th>
                        <th class="py-3 px-4 w-32 text-right font-bold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="py-4 px-4 border-r border-gray-200 font-bold text-gray-900">Dell PowerEdge R740 Server</td>
                        <td class="py-4 px-4 text-center font-medium border-r border-gray-200">2</td>
                        <td class="py-4 px-4 text-right text-gray-700 border-r border-gray-200">5,000.00</td>
                        <td class="py-4 px-4 text-right font-bold text-gray-900">10,000.00</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end mb-12">
            <!-- Conceptually including totals partial -->
            <?php 
                $subtotal = 10000.00; $vatPercentage = 15; $vatAmount = 1500.00; $grandTotal = 11500.00; $currency = 'SAR';
                require __DIR__ . '/../common/totals.php'; 
            ?>
        </div>
        
        <div class="text-xs text-gray-500 border-t border-gray-200 pt-6 text-center">
            This is an order confirmation. A tax invoice will be generated upon delivery.
        </div>
    </div>
</body>
</html>