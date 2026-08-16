<?php
// Path: resources/views/documents/invoice/purchase.php
// Standalone Print Layout - Purchase Invoice (Bill)
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Bill - BILL-2026-0801</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { nour: { dark: '#0a1930', primary: '#005eb8', light: '#21a1f1' } }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #525659; margin: 0; padding: 2rem 0; display: flex; justify-content: center; color: #1f2937; }
        .a4-page { width: 210mm; min-height: 297mm; padding: 15mm; margin: 0 auto; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); position: relative; }
        @media print { @page { size: A4; margin: 0; } body { background: none; padding: 0; } .a4-page { box-shadow: none; padding: 10mm; margin: 0; width: 100%; } .no-print { display: none !important; } }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print fixed top-4 right-4 bg-nour-dark text-white px-5 py-2.5 rounded-lg shadow-xl hover:bg-black font-medium z-50 flex items-center gap-2 transition-colors">
        <i class="fas fa-print"></i> Print Bill
    </button>

    <div class="a4-page bg-white text-sm">
        
        <!-- Header -->
        <div class="flex justify-between items-center mb-8 border-b-4 border-gray-200 pb-6">
            <div class="text-left">
                <h2 class="text-3xl font-black uppercase text-gray-800 tracking-wider">Purchase Bill</h2>
                <p class="font-bold text-nour-primary text-lg mt-1">#BILL-2026-0801</p>
                <p class="text-gray-500 text-xs mt-1">Supplier Ref: <span class="font-bold text-gray-800">INV-SUP-998</span></p>
            </div>
            <div class="flex gap-4 items-center text-right">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 tracking-tight uppercase">Nour Trust Enterprise</h1>
                    <p class="text-gray-500 text-xs mt-0.5">Accounts Payable Department</p>
                </div>
                 <div class="w-16 h-16 rounded bg-gray-100 flex items-center justify-center text-gray-400 font-black text-2xl border border-gray-300">
                    NT
                </div>
            </div>
        </div>

        <div class="flex justify-between mb-8 gap-8">
            <!-- Vendor Info -->
            <div class="w-1/2">
                <div class="bg-gray-100 px-3 py-1 mb-2 border-l-4 border-gray-400 font-bold text-xs uppercase tracking-wider text-gray-600">Billed From (Supplier)</div>
                <p class="font-bold text-gray-900 text-base">Global Tech Suppliers Inc.</p>
                <p class="text-gray-600 mt-1 text-sm">100 Silicon Valley Way</p>
                <p class="text-gray-600 text-sm">San Jose, CA 94088, USA</p>
                <p class="text-gray-500 text-xs mt-2 font-medium">VAT: 998877665544</p>
            </div>

            <!-- Details -->
            <div class="w-1/3 space-y-2">
                <div class="flex justify-between border-b border-gray-100 pb-1">
                    <span class="text-gray-500 font-medium text-xs uppercase">Bill Date:</span>
                    <span class="font-semibold text-gray-900">Aug 20, 2026</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-1">
                    <span class="text-gray-500 font-medium text-xs uppercase">Due Date:</span>
                    <span class="font-bold text-red-600">Sep 20, 2026</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-1">
                    <span class="text-gray-500 font-medium text-xs uppercase">Linked PO:</span>
                    <span class="font-semibold text-nour-primary">PO-2026-042</span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-8 border border-gray-300 rounded overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 text-xs uppercase tracking-wider">
                        <th class="py-2 px-3 border-r border-gray-300">Description / Product</th>
                        <th class="py-2 px-3 w-20 text-center border-r border-gray-300">Qty</th>
                        <th class="py-2 px-3 w-24 text-right border-r border-gray-300">Unit Cost</th>
                        <th class="py-2 px-3 w-32 text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-200">
                    <tr>
                        <td class="py-3 px-3 border-r border-gray-200">
                            <p class="font-bold text-gray-900">Processor Alpha-Core X9</p>
                            <p class="text-[10px] text-gray-500 font-mono mt-0.5">SKU: CHIP-ALPH-X9</p>
                        </td>
                        <td class="py-3 px-3 text-center font-medium border-r border-gray-200">100</td>
                        <td class="py-3 px-3 text-right text-gray-700 border-r border-gray-200">$350.00</td>
                        <td class="py-3 px-3 text-right font-bold text-gray-900">$35,000.00</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="flex justify-end">
            <div class="w-2/5">
                <table class="w-full text-right text-sm border-collapse border border-gray-300">
                    <tbody>
                        <tr class="border-b border-gray-200">
                            <td class="py-2 px-3 text-gray-600 bg-gray-50 font-medium">Subtotal</td>
                            <td class="py-2 px-3 font-semibold text-gray-900">$35,000.00</td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <td class="py-2 px-3 text-gray-600 bg-gray-50 font-medium">Tax / VAT</td>
                            <td class="py-2 px-3 font-semibold text-gray-900">$0.00</td>
                        </tr>
                        <tr class="text-base bg-gray-100 text-gray-900 border-t-2 border-gray-400">
                            <td class="py-3 px-3 font-bold uppercase tracking-wider">Total Due</td>
                            <td class="py-3 px-3 font-black">$35,000.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-16 text-center text-xs text-gray-400 font-medium uppercase tracking-wider">
            <p>Internal AP Document - Do Not Send to Supplier</p>
        </div>

    </div>
</body>
</html>