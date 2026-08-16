<?php
// Path: resources/views/documents/delivery/default.php
// Standalone Print Layout - Delivery Note (No Financials)
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Note - DEL-2026-042</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #525659; margin: 0; padding: 2rem 0; display: flex; justify-content: center; color: #1f2937; }
        .a4-page { width: 210mm; min-height: 297mm; padding: 15mm; margin: 0 auto; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        @media print { @page { size: A4; margin: 0; } body { background: none; padding: 0; } .a4-page { box-shadow: none; padding: 10mm; margin: 0; width: 100%; } .no-print { display: none !important; } }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print fixed top-4 right-4 bg-gray-800 text-white px-5 py-2.5 rounded-lg shadow-xl font-medium z-50">
        Print Delivery Note
    </button>

    <div class="a4-page text-sm">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-8 border-b-4 border-gray-800 pb-6">
            <div>
                <h1 class="text-2xl font-black text-gray-900 uppercase">Nour Trust Enterprise</h1>
                <p class="text-gray-500 text-xs mt-1">Logistics & Warehousing Dept.</p>
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-black uppercase text-gray-300 tracking-wider">Delivery Note</h2>
                <p class="font-bold text-gray-800 text-lg mt-1">#DEL-2026-042</p>
            </div>
        </div>

        <div class="flex justify-between mb-8">
            <div class="w-1/2">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Deliver To:</h3>
                <p class="font-bold text-gray-900 text-base">Global Pharma Group</p>
                <p class="text-gray-600 mt-1">Warehouse Gate 4, Industrial City</p>
                <p class="text-gray-600">Jeddah, Saudi Arabia</p>
                <p class="text-gray-600 mt-1 font-medium">Attn: Receiving Manager</p>
            </div>
            <div class="w-1/3">
                <table class="w-full text-left text-sm">
                    <tr><th class="py-1 text-gray-500 font-medium">Date:</th><td class="py-1 font-bold">Aug 16, 2026</td></tr>
                    <tr><th class="py-1 text-gray-500 font-medium">Sales Order:</th><td class="py-1 font-bold">SO-2026-015</td></tr>
                    <tr><th class="py-1 text-gray-500 font-medium">Dispatch WH:</th><td class="py-1 font-bold">Main Riyadh</td></tr>
                </table>
            </div>
        </div>

        <!-- Items Table (NO PRICES) -->
        <div class="mb-12 border border-gray-800">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white text-xs uppercase tracking-wider">
                        <th class="py-2 px-3 w-12 text-center border-r border-gray-600">No.</th>
                        <th class="py-2 px-3 border-r border-gray-600">Item Description</th>
                        <th class="py-2 px-3 w-32 text-center border-r border-gray-600">Ordered Qty</th>
                        <th class="py-2 px-3 w-32 text-center">Delivered Qty</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-300">
                    <tr>
                        <td class="py-3 px-3 text-center text-gray-500 border-r border-gray-300">1</td>
                        <td class="py-3 px-3 border-r border-gray-300">
                            <p class="font-bold text-gray-900">Enterprise Cloud Server</p>
                            <p class="text-[10px] text-gray-500 font-mono mt-0.5">SKU: SRV-ENT-001</p>
                        </td>
                        <td class="py-3 px-3 text-center text-gray-500 border-r border-gray-300">5 Units</td>
                        <td class="py-3 px-3 text-center font-black text-gray-900 text-lg">5 Units</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="py-3 px-3 text-center text-gray-500 border-r border-gray-300">2</td>
                        <td class="py-3 px-3 border-r border-gray-300">
                            <p class="font-bold text-gray-900">Network Switch 24-Port</p>
                            <p class="text-[10px] text-gray-500 font-mono mt-0.5">SKU: NT-SW-024</p>
                        </td>
                        <td class="py-3 px-3 text-center text-gray-500 border-r border-gray-300">10 Units</td>
                        <td class="py-3 px-3 text-center font-black text-gray-900 text-lg">10 Units</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-end mt-16 pt-8 border-t border-gray-300">
            <div class="text-center w-1/3">
                <div class="border-b border-gray-800 mb-1"></div>
                <p class="text-xs font-bold text-gray-600 uppercase">Dispatched By (Warehouse)</p>
            </div>
            <div class="text-center w-1/3">
                <div class="border-b border-gray-800 mb-1"></div>
                <p class="text-xs font-bold text-gray-600 uppercase">Received By (Customer)</p>
                <p class="text-[10px] text-gray-400 mt-1">Sign & Stamp</p>
            </div>
        </div>

    </div>
</body>
</html>