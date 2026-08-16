<?php
// Path: resources/views/documents/quotation/purchase.php
// Standalone Print Layout - Request For Quotation (RFQ)
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request for Quotation - RFQ-2026-005</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] }, colors: { nour: { dark: '#0a1930', primary: '#005eb8' } } } } }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #525659; margin: 0; padding: 2rem 0; display: flex; justify-content: center; color: #1f2937; }
        .a4-page { width: 210mm; min-height: 297mm; padding: 15mm; margin: 0 auto; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        @media print { @page { size: A4; margin: 0; } body { background: none; padding: 0; } .a4-page { box-shadow: none; padding: 10mm; margin: 0; width: 100%; } .no-print { display: none !important; } }
    </style>
</head>
<body>
    <button onclick="window.print()" class="no-print fixed top-4 right-4 bg-nour-primary text-white px-5 py-2.5 rounded-lg shadow-xl font-medium z-50">Print RFQ</button>

    <div class="a4-page text-sm">
        
        <?php require_once __DIR__ . '/../common/company-info.php'; ?>

        <div class="text-center mb-8 border-b-4 border-gray-800 pb-4">
            <h2 class="text-3xl font-black uppercase text-gray-300 tracking-widest">Request For Quotation</h2>
            <p class="font-bold text-nour-primary text-xl mt-1">#RFQ-2026-005</p>
        </div>

        <div class="flex justify-between mb-8">
            <div class="w-1/2">
                <?php require_once __DIR__ . '/../common/supplier-info.php'; ?>
            </div>
            <div class="w-1/3 space-y-2 text-right">
                <p><span class="text-gray-500 font-bold uppercase text-xs mr-2">Issue Date:</span> <span class="font-semibold text-gray-900">Aug 16, 2026</span></p>
                <p><span class="text-gray-500 font-bold uppercase text-xs mr-2">Submission Deadline:</span> <span class="font-bold text-red-600">Aug 25, 2026</span></p>
                <p><span class="text-gray-500 font-bold uppercase text-xs mr-2">Expected Delivery:</span> <span class="font-semibold text-gray-900">Sep 01, 2026</span></p>
            </div>
        </div>

        <div class="mb-6">
            <p class="text-sm text-gray-700 mb-2">Dear Vendor, please provide your best pricing and delivery terms for the following items:</p>
            <table class="w-full text-left border-collapse border border-gray-800">
                <thead>
                    <tr class="bg-gray-800 text-white text-xs uppercase tracking-wider">
                        <th class="py-2 px-3 border-r border-gray-600 w-10 text-center">#</th>
                        <th class="py-2 px-3 border-r border-gray-600">Description / Specifications</th>
                        <th class="py-2 px-3 w-24 text-center border-r border-gray-600">Required Qty</th>
                        <th class="py-2 px-3 w-32 text-center border-r border-gray-600">Unit Price (To Fill)</th>
                        <th class="py-2 px-3 w-32 text-center">Total (To Fill)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-300 text-sm">
                    <tr>
                        <td class="py-4 px-3 text-center text-gray-500 border-r border-gray-300">1</td>
                        <td class="py-4 px-3 border-r border-gray-300 font-bold text-gray-900">Industrial Forklift Tires (Solid)</td>
                        <td class="py-4 px-3 text-center font-bold border-r border-gray-300 text-lg">16 Pcs</td>
                        <td class="py-4 px-3 border-r border-gray-300"></td>
                        <td class="py-4 px-3"></td>
                    </tr>
                    <tr>
                        <td class="py-4 px-3 text-center text-gray-500 border-r border-gray-300">2</td>
                        <td class="py-4 px-3 border-r border-gray-300 font-bold text-gray-900">Heavy Duty Engine Oil (Barrel)</td>
                        <td class="py-4 px-3 text-center font-bold border-r border-gray-300 text-lg">5 Brl</td>
                        <td class="py-4 px-3 border-r border-gray-300"></td>
                        <td class="py-4 px-3"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 p-4 border border-gray-200 rounded mb-12 text-xs text-gray-600 space-y-2">
            <h4 class="font-bold text-gray-800 uppercase tracking-wider mb-2">Terms & Instructions:</h4>
            <p>1. Please quote your prices strictly in USD or SAR.</p>
            <p>2. Prices must be inclusive of delivery to our Main Warehouse in Riyadh.</p>
            <p>3. Submit this document signed and stamped to procurement@nourtrust.com before the deadline.</p>
        </div>

        <?php require_once __DIR__ . '/../common/signatures.php'; ?>

    </div>
</body>
</html>