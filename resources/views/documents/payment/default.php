<?php
// Path: resources/views/documents/payment/default.php
// Standalone Print Layout - Payment Voucher (Cash Out)
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Voucher - PV-2026-045</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #525659; margin: 0; padding: 2rem 0; display: flex; justify-content: center; color: #1f2937; }
        .receipt-page { width: 148mm; min-height: 210mm; /* A5 Size */ padding: 15mm; margin: 0 auto; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;}
        @media print { @page { size: A5; margin: 0; } body { background: none; padding: 0; } .receipt-page { box-shadow: none; border: none; padding: 10mm; margin: 0; width: 100%; } .no-print { display: none !important; } }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print fixed top-4 right-4 bg-gray-800 text-white px-5 py-2.5 rounded-lg shadow-xl font-medium z-50">
        Print Voucher
    </button>

    <div class="receipt-page bg-red-50/10">
        <div class="bg-white p-6 rounded-xl border-2 border-gray-300 shadow-sm relative">
            
            <div class="text-center mb-6 border-b border-gray-200 pb-4">
                <h1 class="text-xl font-black uppercase tracking-widest text-gray-900">Payment Voucher</h1>
                <p class="text-xs font-bold text-gray-500 mt-1">Nour Trust Enterprise</p>
            </div>

            <div class="flex justify-between items-center mb-6">
                <div>
                    <p class="text-[10px] text-gray-500 font-bold uppercase">Voucher No.</p>
                    <p class="text-lg font-black text-gray-900 font-mono">PV-2026-045</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-gray-500 font-bold uppercase">Date</p>
                    <p class="text-sm font-bold text-gray-900">August 16, 2026</p>
                </div>
            </div>

            <div class="space-y-4 text-sm mb-8">
                <div class="flex border-b border-dashed border-gray-200 pb-2">
                    <span class="w-1/3 font-bold text-gray-700">Paid To:</span>
                    <span class="w-2/3 font-medium text-gray-900">Alpha Manufacturing</span>
                </div>
                <div class="flex border-b border-dashed border-gray-200 pb-2 items-center">
                    <span class="w-1/3 font-bold text-gray-700">Amount:</span>
                    <span class="w-2/3 font-black text-xl text-gray-900">$ 12,400.00</span>
                </div>
                <div class="flex border-b border-dashed border-gray-200 pb-2">
                    <span class="w-1/3 font-bold text-gray-700">Purpose / Desc:</span>
                    <span class="w-2/3 font-medium text-gray-800">Settlement for Bill #BILL-2026-0801</span>
                </div>
                <div class="flex border-b border-dashed border-gray-200 pb-2">
                    <span class="w-1/3 font-bold text-gray-700">From Account:</span>
                    <span class="w-2/3 font-medium text-gray-600 font-mono">1010 - Al Rajhi Bank</span>
                </div>
            </div>

            <div class="mt-12 flex justify-between items-end pt-4">
                <div class="text-center w-1/3">
                    <div class="border-b border-gray-800 mb-1"></div>
                    <p class="text-[10px] font-bold text-gray-600 uppercase">Authorized By</p>
                </div>
                <div class="text-center w-1/3">
                    <div class="border-b border-gray-800 mb-1"></div>
                    <p class="text-[10px] font-bold text-gray-600 uppercase">Received By (Signature)</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>