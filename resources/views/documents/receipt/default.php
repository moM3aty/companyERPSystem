<?php
// Path: resources/views/documents/receipt/default.php
// Standalone Print Layout - Official Receipt (Cash In)
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt - RC-2026-105</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #525659; margin: 0; padding: 2rem 0; display: flex; justify-content: center; color: #1f2937; }
        .receipt-page { width: 148mm; min-height: 210mm; /* A5 Size */ padding: 15mm; margin: 0 auto; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); position: relative; border: 1px solid #e5e7eb;}
        @media print { @page { size: A5; margin: 0; } body { background: none; padding: 0; } .receipt-page { box-shadow: none; border: none; padding: 10mm; margin: 0; width: 100%; } .no-print { display: none !important; } }
        .pattern-bg { background-image: radial-gradient(#e5e7eb 1px, transparent 1px); background-size: 20px 20px; }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print fixed top-4 right-4 bg-gray-800 text-white px-5 py-2.5 rounded-lg shadow-xl hover:bg-black font-medium z-50 flex items-center gap-2 transition-colors">
        <i class="fas fa-print"></i> Print Receipt
    </button>

    <div class="receipt-page pattern-bg">
        <div class="bg-white p-6 rounded-xl border-2 border-gray-800 shadow-sm relative">
            
            <!-- Watermark -->
            <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none z-0">
                <span class="text-8xl font-black transform -rotate-45">PAID</span>
            </div>

            <div class="relative z-10">
                <div class="text-center mb-6 border-b-2 border-gray-800 pb-4">
                    <h1 class="text-2xl font-black uppercase tracking-widest text-gray-900">Official Receipt</h1>
                    <p class="text-sm font-bold text-gray-500 mt-1">Nour Trust Enterprise</p>
                </div>

                <div class="flex justify-between items-center mb-6">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase">Receipt No.</p>
                        <p class="text-lg font-black text-gray-900 font-mono">RC-2026-105</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 font-bold uppercase">Date</p>
                        <p class="text-sm font-bold text-gray-900">August 15, 2026</p>
                    </div>
                </div>

                <div class="space-y-4 text-sm mb-8">
                    <div class="flex border-b border-dashed border-gray-300 pb-2">
                        <span class="w-1/3 font-bold text-gray-700">Received From:</span>
                        <span class="w-2/3 font-medium text-gray-900">Alpha Tech Solutions</span>
                    </div>
                    <div class="flex border-b border-dashed border-gray-300 pb-2 items-center">
                        <span class="w-1/3 font-bold text-gray-700">Amount:</span>
                        <span class="w-2/3 font-black text-2xl text-gray-900">$ 28,175.00</span>
                    </div>
                    <div class="flex border-b border-dashed border-gray-300 pb-2">
                        <span class="w-1/3 font-bold text-gray-700">The Sum of:</span>
                        <span class="w-2/3 font-medium text-gray-600 italic uppercase">Twenty Eight Thousand One Hundred Seventy Five Dollars Only.</span>
                    </div>
                    <div class="flex border-b border-dashed border-gray-300 pb-2">
                        <span class="w-1/3 font-bold text-gray-700">Payment For:</span>
                        <span class="w-2/3 font-medium text-gray-900">Settlement of Sales Invoice #INV-2026-089</span>
                    </div>
                    <div class="flex border-b border-dashed border-gray-300 pb-2">
                        <span class="w-1/3 font-bold text-gray-700">Payment Method:</span>
                        <span class="w-2/3 font-medium text-gray-900">Bank Transfer (Ref: TRF-998811)</span>
                    </div>
                </div>

                <div class="mt-12 flex justify-between items-end pt-4">
                    <div class="text-center w-1/3">
                        <div class="border-b border-gray-800 mb-1"></div>
                        <p class="text-xs font-bold text-gray-600 uppercase">Received By</p>
                    </div>
                    <div class="w-16 h-16 border-4 border-double border-green-600 text-green-600 rounded-full flex items-center justify-center font-black text-xs transform -rotate-12">
                        POSTED
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>