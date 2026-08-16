<?php
// Path: resources/views/documents/invoice/sales.php
// Standalone Print Layout - ZATCA Phase 2 Compliant (Bilingual B2B/B2C)
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice | فاتورة ضريبية - INV-2026-0089</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Cairo', 'Inter', 'sans-serif'] },
                    colors: {
                        nour: { dark: '#0a1930', primary: '#005eb8', light: '#21a1f1' }
                    }
                }
            }
        }
    </script>
    
    <style>
        body { font-family: 'Cairo', 'Inter', sans-serif; background-color: #525659; margin: 0; padding: 2rem 0; display: flex; justify-content: center; color: #1f2937; }
        .a4-page { width: 210mm; min-height: 297mm; padding: 15mm; margin: 0 auto; background: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); position: relative; }
        @media print {
            @page { size: A4; margin: 0; }
            body { background: none; padding: 0; }
            .a4-page { box-shadow: none; padding: 10mm; margin: 0; width: 100%; }
            .no-print { display: none !important; }
        }
        .bilingual-label { display: flex; flex-direction: column; line-height: 1.2; }
        .bilingual-label span.ar { font-weight: 700; font-size: 0.85rem; color: #0a1930; }
        .bilingual-label span.en { font-family: 'Inter', sans-serif; font-size: 0.65rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
    </style>
</head>
<body>

    <button onclick="window.print()" class="no-print fixed top-4 left-4 bg-nour-dark text-white px-5 py-2.5 rounded-lg shadow-xl hover:bg-black font-medium z-50 flex items-center gap-2 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        طباعة الفاتورة
    </button>

    <div class="a4-page bg-white text-sm" dir="ltr">
        
        <div class="flex justify-between items-start mb-6 border-b-2 border-nour-primary pb-4">
            
            <!-- Company Details -->
            <div class="flex gap-4 items-center w-1/3">
                <div class="w-20 h-20 rounded-xl bg-gradient-to-tr from-nour-dark to-nour-primary flex items-center justify-center text-white font-black text-4xl shadow-md">
                    NT
                </div>
                <div>
                    <h1 class="text-xl font-bold text-nour-dark tracking-tight">Nour Trust Enterprise</h1>
                    <p class="text-gray-600 text-xs mt-1 font-medium">مؤسسة نور ترست التجارية</p>
                    <p class="text-gray-500 text-xs mt-1">Riyadh 12345, Saudi Arabia</p>
                    <div class="mt-2 text-xs font-semibold text-gray-700 bg-gray-50 p-1.5 rounded border border-gray-100 inline-block">
                        VAT No: 300123456789003
                    </div>
                </div>
            </div>

            <!-- Invoice Title -->
            <div class="text-center w-1/3 pt-2">
                <h2 class="text-2xl font-black uppercase text-nour-primary tracking-wider">Tax Invoice</h2>
                <h3 class="text-xl font-bold text-gray-700 mt-1 font-['Cairo']">فاتورة ضريبية</h3>
            </div>

            <!-- ZATCA Phase 2 QR Code -->
            <div class="w-1/3 flex justify-end">
                <div class="w-28 h-28 border-2 border-gray-200 p-1 rounded-lg bg-white flex flex-col items-center justify-center shadow-sm">
                    <!-- Base64 TLV QR Code Placeholder -->
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=AQVab3VyIFRydXN0Ag8zMDAxMjM0NTY3ODkwMDMDHDIwMjYtMDgtMTVUMTU6MzA6MDBaBAQxMTUwAAUFMTUwAA==" alt="ZATCA Phase 2 QR Code" class="max-w-full mix-blend-multiply">
                </div>
            </div>
        </div>

        <div class="flex justify-between mb-8 gap-6">
            
            <!-- Customer Details (Bill To) -->
            <div class="flex-1 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <div class="bilingual-label mb-3 border-b border-gray-200 pb-1">
                    <span class="ar text-nour-primary">بيانات العميل</span>
                    <span class="en">Buyer Details</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Alpha Tech Solutions LLC</h3>
                <p class="text-gray-600 text-xs font-['Cairo'] mb-2">شركة ألفا تك للحلول التقنية ذ.م.م</p>
                <p class="text-gray-600 text-xs mb-1">456 Innovation Park, Jeddah 54321, KSA</p>
                <div class="mt-3 text-xs space-y-1.5">
                    <p class="flex justify-between border-b border-gray-100 pb-1">
                        <span class="font-semibold text-gray-500">VAT Number (الرقم الضريبي):</span> 
                        <span class="font-bold text-gray-800">300987654321003</span>
                    </p>
                    <p class="flex justify-between">
                        <span class="font-semibold text-gray-500">Contact (للتواصل):</span> 
                        <span class="font-medium text-gray-800">+966 50 123 4567</span>
                    </p>
                </div>
            </div>

            <!-- Invoice Meta Data -->
            <div class="flex-1 bg-gray-50 p-4 rounded-xl border border-gray-200">
                <div class="bilingual-label mb-3 border-b border-gray-200 pb-1">
                    <span class="ar text-nour-primary">تفاصيل الفاتورة</span>
                    <span class="en">Invoice Details</span>
                </div>
                <div class="space-y-2.5">
                    <div class="flex justify-between items-center">
                        <div class="bilingual-label">
                            <span class="ar">رقم الفاتورة</span><span class="en">Invoice No.</span>
                        </div>
                        <p class="font-bold text-nour-dark text-base">INV-2026-0089</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="bilingual-label">
                            <span class="ar">تاريخ الإصدار</span><span class="en">Issue Date</span>
                        </div>
                        <p class="font-semibold text-gray-800 text-xs">2026-08-15 14:30:00</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="bilingual-label">
                            <span class="ar">تاريخ التوريد</span><span class="en">Supply Date</span>
                        </div>
                        <p class="font-semibold text-gray-800 text-xs">2026-08-15</p>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="bilingual-label">
                            <span class="ar">الرقم المرجعي (UUID)</span><span class="en">Invoice UUID</span>
                        </div>
                        <p class="font-mono text-[9px] text-gray-500">123e4567-e89b-12d3-a456-426614174000</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6 rounded-lg overflow-hidden border border-gray-300">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-nour-dark text-white">
                        <th class="py-3 px-3 w-10 text-center border-r border-gray-600">
                            <div class="bilingual-label"><span class="ar text-white">#</span></div>
                        </th>
                        <th class="py-3 px-3 border-r border-gray-600">
                            <div class="bilingual-label"><span class="ar text-white">وصف الصنف</span><span class="en text-gray-300">Item Description</span></div>
                        </th>
                        <th class="py-3 px-3 w-20 text-center border-r border-gray-600">
                            <div class="bilingual-label"><span class="ar text-white">الكمية</span><span class="en text-gray-300">Qty</span></div>
                        </th>
                        <th class="py-3 px-3 w-24 text-right border-r border-gray-600">
                            <div class="bilingual-label"><span class="ar text-white">سعر الوحدة</span><span class="en text-gray-300">Unit Price</span></div>
                        </th>
                        <th class="py-3 px-3 w-20 text-right border-r border-gray-600">
                            <div class="bilingual-label"><span class="ar text-white">الخصم</span><span class="en text-gray-300">Discount</span></div>
                        </th>
                        <th class="py-3 px-3 w-24 text-right border-r border-gray-600">
                            <div class="bilingual-label"><span class="ar text-white">الضريبة 15%</span><span class="en text-gray-300">VAT 15%</span></div>
                        </th>
                        <th class="py-3 px-3 w-28 text-right">
                            <div class="bilingual-label"><span class="ar text-white">المجموع</span><span class="en text-gray-300">Total (SAR)</span></div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-xs">
                    <tr>
                        <td class="py-3 px-3 text-center text-gray-500 font-mono border-r border-gray-200">1</td>
                        <td class="py-3 px-3 border-r border-gray-200">
                            <p class="font-bold text-gray-900">Dell PowerEdge R740 Server</p>
                            <p class="text-gray-500 mt-0.5 font-['Cairo']">سيرفر ديل بآور إيدج R740</p>
                            <p class="text-[10px] text-gray-400 font-mono mt-1">SKU: HW-SRV-001</p>
                        </td>
                        <td class="py-3 px-3 text-center font-medium border-r border-gray-200">2.00</td>
                        <td class="py-3 px-3 text-right text-gray-700 border-r border-gray-200">10,000.00</td>
                        <td class="py-3 px-3 text-right text-gray-700 border-r border-gray-200">0.00</td>
                        <td class="py-3 px-3 text-right text-gray-700 border-r border-gray-200">3,000.00</td>
                        <td class="py-3 px-3 text-right font-bold text-gray-900">23,000.00</td>
                    </tr>
                    <tr class="bg-gray-50/50">
                        <td class="py-3 px-3 text-center text-gray-500 font-mono border-r border-gray-200">2</td>
                        <td class="py-3 px-3 border-r border-gray-200">
                            <p class="font-bold text-gray-900">Software Implementation Service</p>
                            <p class="text-gray-500 mt-0.5 font-['Cairo']">خدمة تنفيذ وبرمجة النظام</p>
                            <p class="text-[10px] text-gray-400 font-mono mt-1">SKU: SV-IMP-001</p>
                        </td>
                        <td class="py-3 px-3 text-center font-medium border-r border-gray-200">1.00</td>
                        <td class="py-3 px-3 text-right text-gray-700 border-r border-gray-200">5,000.00</td>
                        <td class="py-3 px-3 text-right text-gray-700 border-r border-gray-200">500.00</td>
                        <td class="py-3 px-3 text-right text-gray-700 border-r border-gray-200">675.00</td>
                        <td class="py-3 px-3 text-right font-bold text-gray-900">5,175.00</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-between items-start mb-10">
            <!-- Payment Instructions -->
            <div class="w-1/2 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="bilingual-label mb-2 border-b border-gray-200 pb-1">
                    <span class="ar text-nour-dark">تفاصيل الدفع البنكي</span>
                    <span class="en">Bank Payment Details</span>
                </div>
                <div class="text-xs text-gray-700 space-y-1.5 font-medium">
                    <p><span class="text-gray-500">Bank (البنك):</span> Al Rajhi Bank</p>
                    <p><span class="text-gray-500">Beneficiary (المستفيد):</span> Nour Trust Enterprise</p>
                    <p><span class="text-gray-500">IBAN (الآيبان):</span> SA01 8000 0000 1234 5678 90</p>
                    <p class="text-[10px] text-red-500 mt-2 font-bold">* Please include Invoice No. in transfer remarks.</p>
                </div>
            </div>

            <!-- Totals Calculation -->
            <div class="w-5/12">
                <table class="w-full text-right text-sm border-collapse">
                    <tbody>
                        <tr>
                            <td class="py-2 px-3 text-gray-600 border-b border-gray-100">
                                <div class="bilingual-label items-end"><span class="ar">الإجمالي قبل الضريبة</span><span class="en">Subtotal (Excl. VAT)</span></div>
                            </td>
                            <td class="py-2 px-3 font-semibold text-gray-900 border-b border-gray-100">25,000.00 SAR</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 text-gray-600 border-b border-gray-100">
                                <div class="bilingual-label items-end"><span class="ar">إجمالي الخصومات</span><span class="en">Total Discount</span></div>
                            </td>
                            <td class="py-2 px-3 font-semibold text-red-600 border-b border-gray-100">-500.00 SAR</td>
                        </tr>
                        <tr>
                            <td class="py-2 px-3 text-gray-600 border-b border-gray-300">
                                <div class="bilingual-label items-end"><span class="ar">إجمالي ضريبة القيمة المضافة (15%)</span><span class="en">Total VAT (15%)</span></div>
                            </td>
                            <td class="py-2 px-3 font-semibold text-gray-900 border-b border-gray-300">3,675.00 SAR</td>
                        </tr>
                        <tr class="bg-nour-dark text-white">
                            <td class="py-3 px-3 rounded-l-lg">
                                <div class="bilingual-label items-end"><span class="ar text-white text-base">الإجمالي المستحق</span><span class="en text-nour-light">Grand Total Due</span></div>
                            </td>
                            <td class="py-3 px-3 font-black text-xl rounded-r-lg">28,175.00 SAR</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6 flex justify-between items-end mt-auto">
            <div class="w-1/3">
                <div class="bilingual-label mb-8 text-center"><span class="ar">توقيع العميل / المستلم</span><span class="en">Customer / Receiver Signature</span></div>
                <div class="border-b border-gray-400 mx-8"></div>
            </div>
            
            <div class="w-1/3 text-center">
                <!-- Company Stamp Placeholder -->
                <div class="w-24 h-24 rounded-full border-2 border-nour-light/30 mx-auto mb-2 flex items-center justify-center transform -rotate-12 opacity-80">
                    <div class="text-center text-nour-primary font-bold text-[10px] leading-tight border border-nour-primary p-2 rounded-full">
                        NOUR TRUST<br>APPROVED<br>ZATCA
                    </div>
                </div>
                <div class="bilingual-label"><span class="ar">الختم المعتمد</span><span class="en">Authorized Stamp</span></div>
            </div>
        </div>
        
        <!-- Document Footer Note -->
        <div class="text-center mt-8 text-[10px] text-gray-400 border-t border-gray-100 pt-3">
            <p>تم إصدار هذه الفاتورة إلكترونياً من خلال نظام Nour Trust ERP المتوافق مع متطلبات هيئة الزكاة والضريبة والجمارك (ZATCA).</p>
            <p class="font-mono mt-1">Generated by Nour Trust ERP System v2.0 - Phase 2 Compliant</p>
        </div>

    </div>

</body>
</html>