<?php
// Path: resources/views/documents/credit-note/default.php
// Standalone Print Layout - Credit Note
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Credit Note CN-2026-005</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #525659; margin: 0; padding: 20px; display: flex; justify-content: center; }
        .page { background: white; width: 210mm; min-height: 297mm; padding: 20mm; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        @media print { body { background: none; padding: 0; } .page { box-shadow: none; padding: 0; width: 100%; } .no-print { display: none; } }
    </style>
</head>
<body>
    <button class="no-print fixed top-4 right-4 bg-red-600 text-white px-4 py-2 rounded" onclick="window.print()">Print Note</button>
    <div class="page text-sm text-gray-800">
        
        <div class="flex justify-between items-start border-b-2 border-red-200 pb-6 mb-8">
            <h1 class="text-2xl font-black uppercase tracking-tight text-gray-900">Nour Trust</h1>
            <div class="text-right">
                <h2 class="text-3xl font-black text-red-600 uppercase tracking-widest">Credit Note</h2>
                <p class="text-lg font-bold text-gray-900 mt-1">CN-2026-005</p>
            </div>
        </div>

        <div class="flex justify-between mb-8 p-4 bg-red-50/50 rounded border border-red-100">
            <div class="w-1/2">
                <h3 class="text-xs font-bold text-gray-400 uppercase mb-1">Customer:</h3>
                <p class="font-bold text-lg text-gray-900">Alpha Tech Solutions</p>
                <p class="text-gray-600">Jeddah, Saudi Arabia</p>
            </div>
            <div class="w-1/3">
                <table class="w-full text-sm">
                    <tr><td class="py-1 text-gray-500 font-medium">Date:</td><td class="py-1 font-bold text-right">Aug 16, 2026</td></tr>
                    <tr><td class="py-1 text-gray-500 font-medium">Linked Invoice:</td><td class="py-1 font-bold text-right text-red-600">INV-2026-089</td></tr>
                    <tr><td class="py-1 text-gray-500 font-medium">Reason:</td><td class="py-1 font-bold text-right">Items Damaged</td></tr>
                </table>
            </div>
        </div>

        <table class="w-full text-left border-collapse mb-8 border border-red-200">
            <thead>
                <tr class="bg-red-50 text-red-800 text-xs uppercase">
                    <th class="p-3 border-r border-red-200">Returned Item Description</th>
                    <th class="p-3 border-r border-red-200 text-center w-20">Qty</th>
                    <th class="p-3 border-r border-red-200 text-right w-28">Unit Price</th>
                    <th class="p-3 text-right w-32">Credit Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-red-100">
                <tr>
                    <td class="p-3 font-bold text-gray-900">Dell PowerEdge R740 Server</td>
                    <td class="p-3 text-center font-medium">1</td>
                    <td class="p-3 text-right text-gray-700">5,000.00</td>
                    <td class="p-3 text-right font-bold text-red-600">5,000.00</td>
                </tr>
            </tbody>
        </table>

        <div class="flex justify-end mb-12">
            <div class="w-1/2">
                <table class="w-full text-right text-sm">
                    <tr><td class="py-2 text-gray-600">Subtotal:</td><td class="py-2 font-bold">5,000.00</td></tr>
                    <tr class="border-b border-gray-300"><td class="py-2 text-gray-600">VAT (15%):</td><td class="py-2 font-bold">750.00</td></tr>
                    <tr class="text-lg"><td class="py-3 font-bold text-gray-900">Total Credit:</td><td class="py-3 font-black text-red-600">5,750.00 SAR</td></tr>
                </table>
            </div>
        </div>
        
        <div class="text-xs text-gray-500 border-t border-gray-200 pt-4 text-center">
            This credit note amount will be deducted from your outstanding balance or kept on your account for future purchases.
        </div>
    </div>
</body>
</html>