<?php
// Path: resources/views/documents/payslip/default.php
// Standalone Print Layout - Employee Payslip
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - August 2026 - Ahmed Hassan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: { colors: { nour: { primary: '#005eb8' } } }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #525659; margin: 0; padding: 2rem 0; display: flex; justify-content: center; }
        .a4-page { width: 210mm; min-height: 297mm; padding: 15mm; margin: 0 auto; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        @media print { @page { size: A4; margin: 0; } body { background: none; padding: 0; } .a4-page { box-shadow: none; padding: 10mm; margin: 0; width: 100%; } .no-print { display: none !important; } }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print fixed top-4 right-4 bg-gray-800 text-white px-5 py-2.5 rounded-lg shadow-xl font-medium z-50">
        Print Payslip
    </button>

    <div class="a4-page text-sm text-gray-800">
        
        <div class="text-center mb-8 border-b-2 border-gray-800 pb-4">
            <h1 class="text-2xl font-black uppercase tracking-widest text-gray-900">Nour Trust Enterprise</h1>
            <h2 class="text-xl font-bold text-gray-500 mt-1">Payslip for the month of <span class="text-nour-primary">August 2026</span></h2>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-8">
            <div>
                <table class="w-full text-left text-sm">
                    <tr><th class="py-1 w-1/3 text-gray-500">Employee Name:</th><td class="py-1 font-bold">Ahmed Hassan</td></tr>
                    <tr><th class="py-1 text-gray-500">Employee ID:</th><td class="py-1 font-mono">EMP-2022-045</td></tr>
                    <tr><th class="py-1 text-gray-500">Designation:</th><td class="py-1">Senior Software Engineer</td></tr>
                </table>
            </div>
            <div>
                <table class="w-full text-left text-sm">
                    <tr><th class="py-1 w-1/3 text-gray-500">Department:</th><td class="py-1">Information Technology</td></tr>
                    <tr><th class="py-1 text-gray-500">Date of Join:</th><td class="py-1">Mar 15, 2022</td></tr>
                    <tr><th class="py-1 text-gray-500">Bank Account:</th><td class="py-1 font-mono">SA01800...1234</td></tr>
                </table>
            </div>
        </div>

        <div class="flex gap-6 mb-8">
            <!-- Earnings -->
            <div class="w-1/2 border border-gray-300 rounded overflow-hidden">
                <div class="bg-gray-100 p-2 border-b border-gray-300 font-bold text-center uppercase tracking-wider text-green-700">Earnings</div>
                <table class="w-full text-left text-sm">
                    <tbody class="divide-y divide-gray-100">
                        <tr><td class="py-2 px-3">Basic Salary</td><td class="py-2 px-3 text-right font-medium">15,000.00 SAR</td></tr>
                        <tr><td class="py-2 px-3">Housing Allowance</td><td class="py-2 px-3 text-right font-medium">2,500.00 SAR</td></tr>
                        <tr><td class="py-2 px-3">Transportation</td><td class="py-2 px-3 text-right font-medium">1,250.00 SAR</td></tr>
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold border-t border-gray-300">
                        <tr><td class="py-2 px-3">Gross Earnings (A)</td><td class="py-2 px-3 text-right">18,750.00 SAR</td></tr>
                    </tfoot>
                </table>
            </div>

            <!-- Deductions -->
            <div class="w-1/2 border border-gray-300 rounded overflow-hidden">
                <div class="bg-gray-100 p-2 border-b border-gray-300 font-bold text-center uppercase tracking-wider text-red-700">Deductions</div>
                <table class="w-full text-left text-sm">
                    <tbody class="divide-y divide-gray-100">
                        <tr><td class="py-2 px-3">GOSI (Social Insurance)</td><td class="py-2 px-3 text-right font-medium">1,462.50 SAR</td></tr>
                        <tr><td class="py-2 px-3 text-gray-400">Absence Penalty</td><td class="py-2 px-3 text-right text-gray-400">0.00 SAR</td></tr>
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold border-t border-gray-300">
                        <tr><td class="py-2 px-3">Total Deductions (B)</td><td class="py-2 px-3 text-right">1,462.50 SAR</td></tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Net Pay -->
        <div class="border-2 border-gray-800 p-4 rounded text-center mb-12 bg-gray-50">
            <p class="text-gray-600 font-bold uppercase tracking-wider mb-1">Net Pay (A - B)</p>
            <p class="text-3xl font-black text-gray-900">17,287.50 SAR</p>
        </div>

        <div class="text-center text-xs text-gray-500 mt-16 pt-4 border-t border-gray-200">
            <p>This is a computer generated document and does not require a signature.</p>
        </div>
    </div>
</body>
</html>