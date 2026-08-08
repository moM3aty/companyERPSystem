<?php
// app/views/contract/show.php
$contract = $data['contract'] ?? null;

if (!$contract) {
    echo "<div class='alert alert-danger m-4'><i class='fas fa-exclamation-triangle'></i> خطأ: بيانات العقد غير متوفرة أو تم حذفها.</div>";
    return;
}

// --- جلب إعدادات الشركة ديناميكياً للترويسة بذكاء ---
$db = Database::getInstance();
$cid = Session::get('company_id') ?: 1;
$db->query("SELECT setting_key, setting_value FROM settings WHERE company_id = :cid OR company_id IS NULL");
$db->bind(':cid', $cid);
$sysSettings = $db->resultSet();

$companyName = 'اسم المؤسسة غير محدد';
$vatNumber = 'غير مسجل';
$companyEmail = '';

foreach ($sysSettings as $s) {
    $val = trim($s->setting_value ?? '');
    if (empty($val)) continue;

    // تم إضافة عدة احتمالات لمفاتيح الإعدادات لتعمل أياً كان الاسم في الداتابيز
    if (in_array($s->setting_key, ['company_name', 'name'])) {
        $companyName = $val;
    }
    if (in_array($s->setting_key, ['tax_number', 'vat_number', 'tax_id', 'vat'])) {
        $vatNumber = $val;
    }
    if (in_array($s->setting_key, ['company_email', 'email', 'contact_email'])) {
        $companyEmail = $val;
    }
}

// حساب حالة العقد (ساري، منتهي)
$isExpired = false;
$daysRemaining = 0;
if (!empty($contract->end_date)) {
    $diff = (strtotime($contract->end_date) - time()) / 86400;
    $daysRemaining = ceil($diff);
    if ($daysRemaining < 0) {
        $isExpired = true;
    }
}
?>

<div class="card" style="max-width: 900px; margin: 0 auto; border: none; box-shadow: var(--shadow-md);">
    
    <!-- شريط الأزرار (يختفي عند الطباعة) -->
    <div class="card-header bg-light d-print-none d-flex justify-content-between align-items-center" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <div class="d-flex align-items-center gap-3">
            <h3 class="card-title text-dark mb-0"><i class="fas fa-file-contract text-primary"></i> تفاصيل العقد: <?php echo htmlspecialchars($contract->title ?? 'بدون عنوان'); ?></h3>
            <?php 
                $statusVal = strtolower($contract->status ?? 'draft');
                $statusBadge = match($statusVal) {
                    'active' => 'badge-success', 'expired' => 'badge-danger', 'terminated' => 'badge-dark', 'draft' => 'badge-secondary', 'pending' => 'badge-warning', default => 'badge-secondary'
                };
                $statusLabel = match($statusVal) {
                    'active' => 'ساري المفعول', 'expired' => 'منتهي', 'terminated' => 'مفسوخ / ملغي', 'draft' => 'مسودة', 'pending' => 'قيد الانتظار', default => $statusVal
                };
            ?>
            <span class="badge <?php echo $statusBadge; ?> fs-6"><?php echo $statusLabel; ?></span>
            
            <?php if(!$isExpired && $daysRemaining > 0 && $daysRemaining <= 30 && $statusVal == 'active'): ?>
                <span class="badge badge-warning fs-6"><i class="fas fa-clock"></i> ينتهي قريباً (<?php echo $daysRemaining; ?> يوم)</span>
            <?php endif; ?>
        </div>
        
        <div class="d-flex gap-2">
            <a href="<?php echo URLROOT; ?>/contract/edit/<?php echo $contract->id; ?>" class="btn btn-warning btn-sm text-white"><i class="fas fa-pen"></i> تعديل</a>
            <button class="btn btn-dark btn-sm" onclick="window.print()"><i class="fas fa-print"></i> طباعة / حفظ PDF</button>
            <a href="<?php echo URLROOT; ?>/contract/index" class="btn btn-secondary btn-sm">رجوع للقائمة</a>
        </div>
    </div>

    <!-- ورقة العقد (للطباعة) -->
    <div class="card-body p-5 bg-white" style="border-radius: 0 0 var(--radius-md) var(--radius-md);">
        
        <!-- الترويسة -->
        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-5">
            <div>
                <h1 style="font-size: 28px; font-weight: 900; color: var(--primary-dark); margin-bottom: 5px;">وثيقة عقد (Contract)</h1>
                <div class="text-muted font-monospace fw-bold" style="font-size: 16px;">
                    رقم المرجع: <?php echo htmlspecialchars($contract->contract_number ?? $contract->reference ?? 'CTR-' . $contract->id); ?>
                </div>
            </div>
            
            <div class="text-left" style="direction: ltr; text-align: left;">
                <h2 style="font-size: 22px; font-weight: 900; color: var(--text-dark); margin-bottom: 5px;"><?php echo htmlspecialchars($companyName); ?></h2>
                <div class="text-muted fs-6">
                    الرقم الضريبي: <span class="font-monospace"><?php echo htmlspecialchars($vatNumber); ?></span><br>
                    <?php if($companyEmail): ?><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($companyEmail); ?><?php endif; ?>
                </div>
            </div>
        </div>

        <!-- الأطراف (الطرف الأول والثاني) -->
        <div class="row mb-5" style="display: flex; gap: 30px;">
            <div style="flex: 1; background: var(--slate-50); padding: 20px; border-radius: 8px; border-right: 4px solid var(--primary);">
                <div class="text-primary fw-bold mb-2">الطرف الأول (مقدم الخدمة / البائع):</div>
                <div class="fs-5 fw-bold text-dark mb-1"><?php echo htmlspecialchars($companyName); ?></div>
                <div class="text-muted fs-6">ويمثلها في هذا العقد الإدارة المختصة.</div>
            </div>
            
            <div style="flex: 1; background: var(--slate-50); padding: 20px; border-radius: 8px; border-right: 4px solid var(--success);">
                <div class="text-success-dark fw-bold mb-2">الطرف الثاني (العميل / المشتري):</div>
                <div class="fs-5 fw-bold text-dark mb-1"><i class="fas fa-user-tie text-muted"></i> <?php echo htmlspecialchars($contract->customer_name ?? $contract->client_name ?? 'غير محدد'); ?></div>
                <div class="text-muted fs-6">أقر بأهليته المعتبرة شرعاً ونظاماً للتعاقد.</div>
            </div>
        </div>

        <!-- تفاصيل العقد الأساسية -->
        <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">البند الأول: تفاصيل العقد</h4>
        <table class="table table-bordered mb-5" style="border: 1px solid var(--border-color);">
            <tbody>
                <tr>
                    <td class="bg-light fw-bold" style="width: 25%;">موضوع العقد</td>
                    <td colspan="3" class="text-dark fw-bold"><?php echo htmlspecialchars($contract->title ?? ''); ?></td>
                </tr>
                <tr>
                    <td class="bg-light fw-bold" style="width: 25%;">تاريخ البداية</td>
                    <td class="font-monospace text-dark" style="width: 25%;"><i class="far fa-calendar-check text-success"></i> <?php echo !empty($contract->start_date) ? date('Y-m-d', strtotime($contract->start_date)) : '—'; ?></td>
                    <td class="bg-light fw-bold" style="width: 25%;">تاريخ الانتهاء</td>
                    <td class="font-monospace text-dark" style="width: 25%;">
                        <i class="far fa-calendar-times text-danger"></i> <?php echo !empty($contract->end_date) ? date('Y-m-d', strtotime($contract->end_date)) : '—'; ?>
                    </td>
                </tr>
                <tr>
                    <td class="bg-light fw-bold" style="width: 25%;">القيمة المالية (الميزانية)</td>
                    <td colspan="3" class="font-monospace fw-bold text-primary fs-5" style="direction: ltr; text-align: right;">
                        <?php echo number_format((float)($contract->value ?? $contract->amount ?? 0), 2); ?> <span class="fs-6 text-muted font-sans">ر.س</span>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- الشروط والأحكام / نص العقد -->
        <h4 class="fw-bold text-dark mb-3 border-bottom pb-2">البند الثاني: الشروط والأحكام</h4>
        <div class="p-4 mb-5" style="background: #fff; border: 1px solid var(--slate-200); border-radius: 8px; min-height: 200px; line-height: 1.8; color: var(--slate-700); font-size: 15px;">
            <?php 
                $content = $contract->description ?? $contract->terms ?? $contract->content ?? '';
                if (!empty($content)) {
                    echo nl2br(htmlspecialchars($content));
                } else {
                    echo '<div class="text-muted text-center py-5"><i class="fas fa-file-signature fa-2x mb-3 opacity-50"></i><br>لم يتم إدخال نص أو شروط لهذا العقد.</div>';
                }
            ?>
        </div>

        <!-- التوقيعات -->
        <div class="mt-5 pt-4" style="page-break-inside: avoid;">
            <div class="row" style="display: flex; justify-content: space-between; text-align: center;">
                <div style="flex: 1; padding: 20px;">
                    <div class="fw-bold text-dark mb-4">الطرف الأول (الشركة)</div>
                    <div style="border-bottom: 1px dashed var(--slate-400); width: 80%; margin: 0 auto 10px;"></div>
                    <div class="text-muted fs-6">الاسم / التوقيع / الختم</div>
                </div>
                
                <div style="flex: 1; padding: 20px;">
                    <div class="fw-bold text-dark mb-4">الطرف الثاني (العميل)</div>
                    <div style="border-bottom: 1px dashed var(--slate-400); width: 80%; margin: 0 auto 10px;"></div>
                    <div class="text-muted fs-6">الاسم / التوقيع / الختم</div>
                </div>
            </div>
            
            <div class="text-center mt-4 text-muted" style="font-size: 12px;">
                تاريخ الاعتماد: .......................................
            </div>
        </div>

    </div>
</div>

<style>
    /* تحسينات الطباعة (Print Styles) */
    @media print {
        body { background: #fff !important; }
        .d-print-none, .sidebar, .topbar { display: none !important; }
        .main-content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .card { box-shadow: none !important; border: none !important; max-width: 100% !important; margin: 0 !important; }
        .card-body { padding: 0 !important; }
        .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
        .bg-light { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>