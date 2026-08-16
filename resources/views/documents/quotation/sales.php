<?php
// Path: resources/views/documents/quotation/sales.php
// Standalone Print Layout - Sales Quotation
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation - QT-2026-089</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    colors: {
                        nour: {
                            dark: '#0a1930',
                            primary: '#005eb8',
                            light: '#21a1f1'
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #525659;
            margin: 0;
            padding: 2rem 0;
            display: flex;
            justify-content: center;
            color: #1f2937;
        }

        .a4-page {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        @media print {
            @page {
                size: A4;
                margin: 0;
            }

            body {
                background: none;
                padding: 0;
            }

            .a4-page {
                box-shadow: none;
                padding: 10mm;
                margin: 0;
                width: 100%;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

    <button onclick="window.print()" class="no-print fixed top-4 right-4 bg-nour-primary text-white px-5 py-2.5 rounded-lg shadow-xl hover:bg-nour-dark font-medium z-50 flex items-center gap-2 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
        </svg>
        Print Quotation
    </button>

    <div class="a4-page bg-white text-sm">

        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div class="flex gap-4 items-center">
                <div class="w-20 h-20 rounded-xl bg-gradient-to-tr from-nour-dark to-nour-primary flex items-center justify-center text-white font-black text-4xl shadow-md">
                    NT
                </div>
                <div>
                    <h1 class="text-2xl font-black text-nour-dark tracking-tight uppercase">Nour Trust Enterprise</h1>
                    <p class="text-gray-500 text-xs mt-1">King Fahd Road, Riyadh, KSA</p>
                    <p class="text-gray-500 text-xs">Email: sales@nourtrust.com | Tel: +966 50 123 4567</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-3xl font-black uppercase text-gray-300 tracking-wider">Quotation</h2>
                <p class="font-bold text-nour-primary text-lg mt-1">#QT-2026-089</p>
            </div>
        </div>

        <div class="flex justify-between mb-8 pb-6 border-b border-gray-200">
            <!-- Prepared For -->
            <div class="w-1/2">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Prepared For:</h3>
                <p class="font-bold text-gray-900 text-base">Global Pharma Group</p>
                <p class="text-gray-600 mt-1">Attn: Dr. Ahmed Yassin</p>
                <p class="text-gray-600">Jeddah, Saudi Arabia</p>
            </div>
            <!-- Meta -->
            <div class="w-1/3 space-y-2">
                <div class="flex justify-between border-b border-gray-100 pb-1">
                    <span class="text-gray-500 font-medium">Date:</span>
                    <span class="font-semibold text-gray-900">Aug 15, 2026</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-1">
                    <span class="text-gray-500 font-medium">Valid Until:</span>
                    <span class="font-semibold text-nour-primary">Sep 15, 2026</span>
                </div>
                <div class="flex justify-between border-b border-gray-100 pb-1">
                    <span class="text-gray-500 font-medium">Currency:</span>
                    <span class="font-semibold text-gray-900">USD ($)</span>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="mb-8 rounded-lg overflow-hidden border border-gray-300">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="py-3 px-4 font-bold border-r border-gray-300">Description</th>
                        <th class="py-3 px-4 w-20 text-center font-bold border-r border-gray-300">Qty</th>
                        <th class="py-3 px-4 w-28 text-right font-bold border-r border-gray-300">Unit Price</th>
                        <th class="py-3 px-4 w-28 text-right font-bold border-r border-gray-300">Discount</th>
                        <th class="py-3 px-4 w-32 text-right font-bold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="py-4 px-4 border-r border-gray-200">
                            <p class="font-bold text-gray-900">Enterprise Cloud Hosting (Annual)</p>
                            <p class="text-xs text-gray-500 mt-1">Dedicated cluster with 99.9% SLA guarantee.</p>
                        </td>
                        <td class="py-4 px-4 text-center font-medium border-r border-gray-200">1</td>
                        <td class="py-4 px-4 text-right text-gray-700 border-r border-gray-200">12,000.00</td>
                        <td class="py-4 px-4 text-right text-gray-700 border-r border-gray-200">1,000.00</td>
                        <td class="py-4 px-4 text-right font-bold text-gray-900">11,000.00</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="py-4 px-4 border-r border-gray-200">
                            <p class="font-bold text-gray-900">Setup & Migration Services</p>
                        </td>
                        <td class="py-4 px-4 text-center font-medium border-r border-gray-200">1</td>
                        <td class="py-4 px-4 text-right text-gray-700 border-r border-gray-200">2,500.00</td>
                        <td class="py-4 px-4 text-right text-gray-700 border-r border-gray-200">-</td>
                        <td class="py-4 px-4 text-right font-bold text-gray-900">2,500.00</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end mb-12">
            <div class="w-1/2">
                <table class="w-full text-right text-sm border-collapse">
                    <tbody>
                        <tr>
                            <td class="py-2 px-3 text-gray-500 font-medium">Subtotal:</td>
                            <td class="py-2 px-3 font-semibold text-gray-900">14,500.00</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 text-gray-500 font-medium">Total Discount:</td>
                            <td class="py-2 px-3 font-semibold text-red-600">-1,000.00</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 text-gray-500 font-medium border-b border-gray-200 pb-3">VAT (15%):</td>
                            <td class="py-2 px-3 font-semibold text-gray-900 border-b border-gray-200 pb-3">2,025.00</td>
                        </tr>
                        <tr class="text-lg">
                            <td class="py-3 px-3 font-bold text-nour-dark">Grand Total:</td>
                            <td class="py-3 px-3 font-black text-nour-primary">15,525.00 USD</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Terms -->
        <div class="text-xs text-gray-500 border-t border-gray-200 pt-6">
            <h4 class="font-bold text-gray-800 mb-2">Terms & Conditions:</h4>
            <ol class="list-decimal pl-4 space-y-1">
                <li>This quotation is valid for 30 days from the date of issue.</li>
                <li>Payment terms: 50% advance upon PO, 50% upon completion of migration.</li>
                <li>Prices exclude any additional third-party software licenses not mentioned above.</li>
            </ol>
        </div>

    </div>
</body>

</html>