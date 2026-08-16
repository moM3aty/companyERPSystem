<?php
// Path: resources/views/documents/purchase-order/default.php
// Standalone Print Layout - Purchase Order
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - PO-2026-042</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        nour: { dark: '#0a1930', primary: '#005eb8', light: '#21a1f1' }
                    }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #525659; margin: 0; padding: 2rem 0; display: flex; justify-content: center; color: #1f2937; }
        .a4-page { width: 210mm; min-height: 297mm; padding: 15mm; margin: 0 auto; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); position: relative; }
        @media print {
            @page { size: A4; margin: 0; }
            body { background: none; padding: 0; }
            .a4-page { box-shadow: none; padding: 10mm; margin: 0; width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print fixed top-4 right-4 bg-nour-dark text-white px-5 py-2.5 rounded-lg shadow-xl hover:bg-black font-medium z-50 flex items-center gap-2 transition-colors">
        <i class="fas fa-print"></i> Print PO
    </button>

    <div class="a4-page bg-white text-sm">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-8 border-b-4 border-nour-primary pb-6">
            <div class="flex gap-4 items-center">
                <div class="w-16 h-16 rounded bg-nour-dark flex items-center justify-center text-white font-black text-2xl">
                    NT
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 tracking-tight uppercase">Nour Trust Enterprise</h1>
                    <p class="text-gray-500 text-xs mt-0.5">Procurement Department</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-black uppercase text-nour-primary tracking-wider">Purchase Order</h2>
                <p class="font-bold text-gray-800 text-lg mt-1">#PO-2026-042</p>
            </div>
        </div>

        <div class="flex justify-between mb-8 gap-8">
            <!-- Vendor Info -->
            <div class="w-1/2">
                <div class="bg-gray-100 px-3 py-1 mb-2 border-l-4 border-gray-400 font-bold text-xs uppercase tracking-wider text-gray-600">Vendor (To)</div>
                <p class="font-bold text-gray-900 text-base">Global Tech Suppliers Inc.</p>
                <p class="text-gray-600 mt-1 text-sm">100 Silicon Valley Way</p>
                <p class="text-gray-600 text-sm">San Jose, CA 94088, USA</p>
                <p class="text-gray-500 text-xs mt-2 font-medium">Contact: sales@globaltech.com</p>
            </div>

            <!-- Ship To & Details -->
            <div class="w-1/2 space-y-4">
                <div>
                    <div class="bg-blue-50 px-3 py-1 mb-2 border-l-4 border-nour-primary font-bold text-xs uppercase tracking-wider text-nour-primary">Ship To</div>
                    <p class="font-bold text-gray-900 text-sm">Nour Trust - Main Warehouse</p>
                    <p class="text-gray-600 text-sm mt-0.5">Industrial Zone, Riyadh, KSA</p>
                </div>
                
                <table class="w-full text-xs text-left border border-gray-200">
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="py-1.5 px-2 text-gray-500 font-medium">Order Date</th>
                        <td class="py-1.5 px-2 font-bold">Aug 15, 2026</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <th class="py-1.5 px-2 text-gray-500 font-medium bg-gray-50">Expected By</th>
                        <td class="py-1.5 px-2 font-bold text-nour-primary">Aug 25, 2026</td>
                    </tr>
                    <tr>
                        <th class="py-1.5 px-2 text-gray-500 font-medium bg-gray-50">Terms</th>
                        <td class="py-1.5 px-2 font-bold">Net 30</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-8 border border-gray-800">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white text-xs uppercase tracking-wider">
                        <th class="py-2 px-3 w-12 text-center border-r border-gray-600">Item</th>
                        <th class="py-2 px-3 border-r border-gray-600">Description / SKU</th>
                        <th class="py-2 px-3 w-20 text-center border-r border-gray-600">Qty</th>
                        <th class="py-2 px-3 w-24 text-right border-r border-gray-600">Unit Price</th>
                        <th class="py-2 px-3 w-32 text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    <tr>
                        <td class="py-3 px-3 text-center text-gray-500 border-r border-gray-200">1</td>
                        <td class="py-3 px-3 border-r border-gray-200">
                            <p class="font-bold text-gray-900">Processor Alpha-Core X9</p>
                            <p class="text-[10px] text-gray-500 font-mono mt-0.5">SKU: CHIP-ALPH-X9</p>
                        </td>
                        <td class="py-3 px-3 text-center font-bold border-r border-gray-200">100</td>
                        <td class="py-3 px-3 text-right text-gray-700 border-r border-gray-200">$350.00</td>
                        <td class="py-3 px-3 text-right font-bold text-gray-900">$35,000.00</td>
                    </tr>
                    <tr class="bg-gray-50/50">
                        <td class="py-3 px-3 text-center text-gray-500 border-r border-gray-200">2</td>
                        <td class="py-3 px-3 border-r border-gray-200">
                            <p class="font-bold text-gray-900">Copper Cable 5mm (Spool)</p>
                            <p class="text-[10px] text-gray-500 font-mono mt-0.5">SKU: CBL-COP-001</p>
                        </td>
                        <td class="py-3 px-3 text-center font-bold border-r border-gray-200">20</td>
                        <td class="py-3 px-3 text-right text-gray-700 border-r border-gray-200">$510.00</td>
                        <td class="py-3 px-3 text-right font-bold text-gray-900">$10,200.00</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Totals & Authorization -->
        <div class="flex justify-between items-end">
            <!-- Notes -->
            <div class="w-1/2 text-xs text-gray-500 pr-8">
                <p class="font-bold text-gray-800 mb-1">Special Instructions:</p>
                <p>Please deliver between 8:00 AM and 4:00 PM. All packages must include packing slips referencing this PO number.</p>
                
                <div class="mt-8 pt-8 border-t border-gray-300 w-48 text-center">
                    <p class="font-bold text-gray-800 mb-1">Authorized Signature</p>
                    <p class="text-[10px]">Procurement Manager</p>
                </div>
            </div>

            <!-- Totals -->
            <div class="w-2/5">
                <table class="w-full text-right text-sm border-collapse border border-gray-300">
                    <tbody>
                        <tr class="border-b border-gray-200">
                            <td class="py-2 px-3 text-gray-600 bg-gray-50 font-medium">Subtotal</td>
                            <td class="py-2 px-3 font-semibold text-gray-900">$45,200.00</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <td class="py-2 px-3 text-gray-600 bg-gray-50 font-medium">Tax / VAT</td>
                            <td class="py-2 px-3 font-semibold text-gray-900">$0.00</td>
                        </tr>
                        <tr class="text-base bg-gray-800 text-white">
                            <td class="py-3 px-3 font-bold uppercase tracking-wider">Total</td>
                            <td class="py-3 px-3 font-black">$45,200.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>