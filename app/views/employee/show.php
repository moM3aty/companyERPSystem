<?php
// app/views/employee/show.php
$employee = $data['employee'] ?? null;
$documents = $data['documents'] ?? [];
$assets = $data['assets'] ?? [];
$leaves = $data['leaves'] ?? [];

// توافق كامل مع جميع إصدارات PHP
$statusBadge = 'badge-secondary';
$statusLabel = $employee->employment_status;
if ($employee->employment_status === 'Active') {
    $statusBadge = 'badge-success';
    $statusLabel = 'نشط (Active)';
} elseif ($employee->employment_status === 'Exit Process') {
    $statusBadge = 'badge-warning';
    $statusLabel = 'إخلاء طرف (Exit Process)';
} elseif ($employee->employment_status === 'Terminated') {
    $statusBadge = 'badge-danger';
    $statusLabel = 'منهي خدماته (Terminated)';
}
?>

<!-- 🌟 شريط الهيدر العلوي لملف الموظف 🌟 -->
<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div class="d-flex align-items-center gap-3">
        <div style="width: 80px; height: 80px; border-radius: 16px; background: linear-gradient(135deg, var(--primary), var(--info)); color: white; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 900; box-shadow: 0 4px 15px rgba(14,165,233,0.3); text-transform: uppercase;">
            <?php echo mb_substr($employee->name_ar ?: $employee->full_name, 0, 1); ?>
        </div>
        <div>
            <h2 class="mb-0 text-dark fw-black"><?php echo htmlspecialchars($employee->name_ar ?: $employee->full_name); ?></h2>
            <div class="text-muted mt-2 font-monospace" style="font-size: 14px;">
                <span class="bg-light px-2 py-1 rounded border"><i class="fas fa-id-badge text-primary"></i> <?php echo htmlspecialchars($employee->employee_number); ?></span>
                <span class="bg-light px-2 py-1 rounded border ms-2"><i class="fas fa-briefcase text-success"></i> <?php echo htmlspecialchars($employee->position ?: $employee->job_title); ?></span>
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 flex-column align-items-end">
        <span class="badge <?php echo $statusBadge; ?> fs-5 px-4 py-2 mb-2 shadow-sm"><i class="fas fa-circle" style="font-size:10px; vertical-align:middle; margin-left:6px;"></i> <?php echo $statusLabel; ?></span>
        <div class="d-flex gap-2">
            <a href="<?php echo URLROOT; ?>/employee/edit/<?php echo $employee->id; ?>" class="btn btn-secondary btn-sm"><i class="fas fa-pen"></i> تعديل البيانات</a>
            <button class="btn btn-dark btn-sm" onclick="window.print()"><i class="fas fa-print"></i> طباعة الملف الكامل</button>
        </div>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<!-- 🌟 نظام التبويبات الشامل (Tabs) 🌟 -->
<style>
    .emp-tabs { display: flex; gap: 5px; border-bottom: 2px solid var(--border-color); margin-bottom: 25px; overflow-x: auto; scrollbar-width: none; }
    .emp-tab { padding: 12px 20px; font-size: 14px; font-weight: 800; color: var(--slate-500); cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.2s ease; white-space: nowrap; }
    .emp-tab:hover { color: var(--primary); background: var(--slate-50); border-radius: 8px 8px 0 0; }
    .emp-tab.active { color: var(--primary); border-bottom-color: var(--primary); background: var(--primary-light); border-radius: 8px 8px 0 0; }
    .emp-panel { display: none; animation: fadeIn 0.3s; }
    .emp-panel.active { display: block; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    
    @media print {
        .emp-tabs { display: none; }
        .emp-panel { display: block !important; margin-bottom: 40px; page-break-inside: avoid; }
        .card { border: 1px solid #ccc !important; box-shadow: none !important; }
        .card-header { background-color: #f8f9fa !important; -webkit-print-color-adjust: exact; }
    }
</style>

<div class="emp-tabs d-print-none">
    <div class="emp-tab active" data-target="basic"><i class="fas fa-address-card"></i> البيانات الأساسية</div>
    <div class="emp-tab" data-target="job"><i class="fas fa-briefcase"></i> البيانات الوظيفية</div>
    <div class="emp-tab" data-target="finance"><i class="fas fa-money-check-dollar"></i> البنك والرواتب</div>
    <div class="emp-tab" data-target="docs"><i class="fas fa-passport"></i> الوثائق الرسمية <span class="badge badge-danger ms-1"><?php echo count($documents); ?></span></div>
    <div class="emp-tab" data-target="assets"><i class="fas fa-laptop"></i> العهد والأصول <span class="badge badge-warning text-dark ms-1"><?php echo count($assets); ?></span></div>
    <div class="emp-tab" data-target="leave"><i class="fas fa-calendar-minus"></i> سجل الإجازات</div>
</div>

<!-- 1️⃣ التبويب الأول: البيانات الأساسية والطوارئ -->
<div class="emp-panel active" id="panel-basic">
    <div class="row d-flex gap-4 mb-4">
        <div class="card flex-1 mb-0 border-0 shadow-sm">
            <div class="card-header bg-light border-bottom-0"><h3 class="card-title fs-6 text-primary"><i class="fas fa-user"></i> المعلومات الشخصية (Personal Info)</h3></div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr><td class="text-muted fw-bold w-40">الاسم الكامل:</td><td class="text-dark fw-bold"><?php echo htmlspecialchars($employee->full_name); ?></td></tr>
                    <tr><td class="text-muted fw-bold">الاسم بالعربية:</td><td class="text-dark"><?php echo htmlspecialchars($employee->name_ar ?? '—'); ?></td></tr>
                    <tr><td class="text-muted fw-bold">الاسم بالإنجليزية:</td><td class="text-dark" style="direction:ltr; text-align:right;"><?php echo htmlspecialchars($employee->name_en ?? '—'); ?></td></tr>
                    <tr><td class="text-muted fw-bold">الجنس (Gender):</td><td class="text-dark"><?php echo htmlspecialchars($employee->gender ?? '—'); ?></td></tr>
                    <tr><td class="text-muted fw-bold">تاريخ الميلاد:</td><td class="text-dark font-monospace"><?php echo $employee->dob ?? '—'; ?></td></tr>
                    <tr><td class="text-muted fw-bold">الجنسية:</td><td class="text-dark"><?php echo htmlspecialchars($employee->nationality ?? '—'); ?></td></tr>
                    <tr><td class="text-muted fw-bold">الحالة الاجتماعية:</td><td class="text-dark"><?php echo htmlspecialchars($employee->marital_status ?? '—'); ?></td></tr>
                    <tr><td class="text-muted fw-bold">فصيلة الدم:</td><td class="text-danger fw-bold font-monospace"><?php echo htmlspecialchars($employee->blood_group ?? '—'); ?></td></tr>
                </table>
            </div>
        </div>

        <div class="card flex-1 mb-0 border-0 shadow-sm">
            <div class="card-header bg-light border-bottom-0"><h3 class="card-title fs-6 text-info"><i class="fas fa-phone-alt"></i> بيانات التواصل والطوارئ (Contact Info)</h3></div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-4">
                    <tr><td class="text-muted fw-bold w-40">الجوال الشخصي:</td><td class="text-dark font-monospace fw-bold" style="direction:ltr; text-align:right;"><?php echo htmlspecialchars($employee->personal_mobile ?? '—'); ?></td></tr>
                    <tr><td class="text-muted fw-bold">البريد الإلكتروني:</td><td class="text-primary font-monospace" style="direction:ltr; text-align:right;"><?php echo htmlspecialchars($employee->personal_email ?? '—'); ?></td></tr>
                </table>
                
                <div class="p-3 bg-danger-light border border-danger rounded">
                    <h6 class="text-danger fw-bold mb-3"><i class="fas fa-truck-medical"></i> جهة اتصال للطوارئ (Emergency Contact)</h6>
                    <table class="table table-borderless table-sm mb-0">
                        <tr><td class="text-danger-dark fw-bold w-40">اسم الشخص:</td><td class="text-dark fw-bold"><?php echo htmlspecialchars($employee->emergency_contact ?? '—'); ?></td></tr>
                        <tr><td class="text-danger-dark fw-bold">صلة القرابة:</td><td class="text-dark"><?php echo htmlspecialchars($employee->emergency_relation ?? '—'); ?></td></tr>
                        <tr><td class="text-danger-dark fw-bold">رقم الجوال:</td><td class="text-dark font-monospace fw-bold" style="direction:ltr; text-align:right;"><i class="fas fa-phone text-danger fs-7"></i> <?php echo htmlspecialchars($employee->emergency_phone ?? '—'); ?></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2️⃣ التبويب الثاني: البيانات الوظيفية -->
<div class="emp-panel" id="panel-job">
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-light border-bottom-0"><h3 class="card-title fs-6 text-success"><i class="fas fa-briefcase"></i> التفاصيل الوظيفية (Employment Info)</h3></div>
        <div class="card-body">
            <div class="row d-flex gap-4">
                <div class="flex-1">
                    <table class="table table-borderless table-sm mb-0">
                        <tr><td class="text-muted fw-bold w-40">المسمى الوظيفي:</td><td class="text-dark fw-bold"><?php echo htmlspecialchars($employee->job_title ?? '—'); ?></td></tr>
                        <tr><td class="text-muted fw-bold">المنصب (Position):</td><td class="text-primary fw-bold"><?php echo htmlspecialchars($employee->position ?? '—'); ?></td></tr>
                        <tr><td class="text-muted fw-bold">فئة الموظف:</td><td class="text-dark"><?php echo htmlspecialchars($employee->employee_category ?? '—'); ?></td></tr>
                        <tr><td class="text-muted fw-bold">نوع الدوام:</td><td class="text-dark"><span class="badge badge-secondary"><?php echo htmlspecialchars($employee->employment_type ?? '—'); ?></span></td></tr>
                        <tr><td class="text-muted fw-bold">الدرجة الوظيفية:</td><td class="text-info fw-bold font-monospace"><?php echo htmlspecialchars($employee->employee_grade ?? '—'); ?></td></tr>
                    </table>
                </div>
                <div class="flex-1">
                    <table class="table table-borderless table-sm mb-0">
                        <tr><td class="text-muted fw-bold w-40">تاريخ الالتحاق:</td><td class="text-success fw-bold font-monospace"><?php echo $employee->date_of_joining ?? '—'; ?></td></tr>
                        <tr><td class="text-muted fw-bold">موقع العمل:</td><td class="text-dark"><?php echo htmlspecialchars($employee->work_location ?? '—'); ?></td></tr>
                        <tr><td class="text-muted fw-bold">مركز التكلفة:</td><td class="text-dark font-monospace"><?php echo htmlspecialchars($employee->cost_center ?? '—'); ?></td></tr>
                        <tr><td class="text-muted fw-bold">المشروع/التكليف:</td><td class="text-dark"><?php echo htmlspecialchars($employee->project_assignment ?? '—'); ?></td></tr>
                    </table>
                </div>
            </div>
            
            <div class="mt-4 p-3 bg-warning-light border border-warning rounded d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-warning-dark fw-bold mb-1"><i class="fas fa-stopwatch"></i> فترة التجربة (Probation Period)</h6>
                    <div class="text-muted" style="font-size:13px;">تاريخ بداية ونهاية التقييم التجريبي للموظف.</div>
                </div>
                <div class="text-end">
                    <div class="font-monospace fw-bold text-dark"><span class="text-muted fs-7 me-2">من:</span> <?php echo $employee->probation_start_date ?? '—'; ?></div>
                    <div class="font-monospace fw-bold text-danger mt-1"><span class="text-muted fs-7 me-2">إلى:</span> <?php echo $employee->probation_end_date ?? '—'; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3️⃣ التبويب الثالث: البيانات المالية والرواتب -->
<div class="emp-panel" id="panel-finance">
    <div class="row d-flex gap-4">
        <!-- الراتب الأساسي والبدلات -->
        <div class="card flex-1 mb-0 border-0 shadow-sm bg-light">
            <div class="card-header bg-transparent border-bottom"><h3 class="card-title fs-6 text-success"><i class="fas fa-money-bill-wave"></i> الراتب والبدلات (Payroll Details)</h3></div>
            <div class="card-body p-4 text-center">
                <div class="text-muted fw-bold mb-2">الراتب الأساسي (Basic Salary)</div>
                <div class="font-monospace fw-bold text-success mb-4" style="font-size: 3rem; direction:ltr;"><?php echo number_format((float)$employee->basic_salary, 2); ?> <span class="fs-6 text-muted">SAR</span></div>
                
                <div class="d-flex justify-content-between gap-2 text-start">
                    <div class="p-2 border rounded bg-white flex-1 shadow-sm">
                        <div class="text-muted" style="font-size:11px;">بدل السكن</div>
                        <div class="font-monospace fw-bold text-primary fs-5" style="direction:ltr;"><?php echo number_format((float)$employee->housing_allowance, 2); ?></div>
                    </div>
                    <div class="p-2 border rounded bg-white flex-1 shadow-sm">
                        <div class="text-muted" style="font-size:11px;">بدل المواصلات</div>
                        <div class="font-monospace fw-bold text-info fs-5" style="direction:ltr;"><?php echo number_format((float)$employee->transport_allowance, 2); ?></div>
                    </div>
                    <div class="p-2 border rounded bg-white flex-1 shadow-sm">
                        <div class="text-muted" style="font-size:11px;">بدلات أخرى</div>
                        <div class="font-monospace fw-bold text-secondary fs-5" style="direction:ltr;"><?php echo number_format((float)$employee->other_allowances, 2); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- تفاصيل الحساب البنكي -->
        <div class="card flex-1 mb-0 border-0 shadow-sm">
            <div class="card-header bg-light border-bottom-0"><h3 class="card-title fs-6 text-warning-dark"><i class="fas fa-university"></i> معلومات البنك (Bank Details)</h3></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted fw-bold w-40">طريقة الدفع:</td><td class="text-dark fw-bold"><span class="badge badge-dark"><?php echo htmlspecialchars($employee->salary_payment_method ?? 'Bank Transfer'); ?></span></td></tr>
                    <tr><td class="text-muted fw-bold">اسم البنك:</td><td class="text-primary fw-bold"><?php echo htmlspecialchars($employee->bank_name ?? '—'); ?></td></tr>
                    <tr><td class="text-muted fw-bold">صاحب الحساب:</td><td class="text-dark"><?php echo htmlspecialchars($employee->account_holder ?? '—'); ?></td></tr>
                    <tr>
                        <td colspan="2" class="pt-4">
                            <div class="text-muted fw-bold mb-2"><i class="fas fa-barcode"></i> رقم الآيبان (IBAN):</div>
                            <div class="p-3 bg-light border border-secondary rounded font-monospace fw-bold text-center" style="font-size: 22px; letter-spacing: 3px; direction:ltr;">
                                <?php echo htmlspecialchars($employee->iban ?? 'لم يتم الإدخال'); ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 4️⃣ التبويب الرابع: الوثائق الرسمية -->
<div class="emp-panel" id="panel-docs">
    <div class="card mb-0 shadow-sm border-0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h3 class="card-title fs-6 text-primary mb-0"><i class="fas fa-passport"></i> الوثائق، الإقامات، والرخص المرفوعة</h3>
            <a href="<?php echo URLROOT; ?>/hrDocument/create" class="btn btn-sm btn-primary d-print-none"><i class="fas fa-upload"></i> أرشفة وثيقة جديدة</a>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0 table-hover">
                <thead class="bg-white"><tr><th>نوع الوثيقة</th><th>رقم الوثيقة</th><th class="text-center">جهة الإصدار</th><th class="text-center">تاريخ الإصدار</th><th class="text-center">تاريخ الانتهاء</th><th class="text-center">الحالة (تنبيه)</th></tr></thead>
                <tbody>
                    <?php foreach($documents as $doc): 
                        $days = $doc->days_to_expire;
                        $alertClass = 'badge-success'; $alertText = '🟢 سارية';
                        if ($days !== null) {
                            if ($days < 0) { $alertClass = 'badge-danger'; $alertText = '🔴 منتهية!'; }
                            elseif ($days <= 30) { $alertClass = 'badge-danger'; $alertText = "🔴 تنتهي خلال $days يوم"; }
                            elseif ($days <= 60) { $alertClass = 'badge-warning'; $alertText = "🟠 تنتهي خلال $days يوم"; }
                        }
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($doc->doc_type); ?></td>
                        <td class="font-monospace text-muted fw-bold"><?php echo htmlspecialchars($doc->doc_number); ?></td>
                        <td class="text-center"><?php echo htmlspecialchars($doc->issuing_authority ?? '—'); ?></td>
                        <td class="font-monospace text-center text-muted"><?php echo $doc->issue_date ?? '—'; ?></td>
                        <td class="font-monospace fw-bold text-center text-dark"><?php echo $doc->expiry_date ?? '—'; ?></td>
                        <td class="text-center"><span class="badge <?php echo $alertClass; ?>"><?php echo $alertText; ?></span></td>
                    </tr>
                    <?php endforeach; if(empty($documents)): ?>
                        <tr><td colspan="6" class="text-center text-muted p-5"><i class="fas fa-folder-open fs-1 opacity-25 mb-3 d-block"></i>لا توجد وثائق مؤرشفة للموظف حتى الآن.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 5️⃣ التبويب الخامس: العهد والأصول -->
<div class="emp-panel" id="panel-assets">
    <div class="card mb-0 shadow-sm border-0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h3 class="card-title fs-6 text-warning-dark mb-0"><i class="fas fa-laptop"></i> العهد الموكلة للموظف (Assigned Assets)</h3>
            <a href="<?php echo URLROOT; ?>/assetAssignment/create" class="btn btn-sm btn-warning d-print-none"><i class="fas fa-plus"></i> تسليم عهدة جديدة</a>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0 table-hover">
                <thead class="bg-white"><tr><th>نوع العهدة (Asset Type)</th><th>السيريال / الكود (ID)</th><th class="text-center">تاريخ التسليم</th><th class="text-center">الحالة عند التسليم</th><th class="text-center">تاريخ الإرجاع</th><th class="text-center">الحالة الحالية</th></tr></thead>
                <tbody>
                    <?php foreach($assets as $asset): 
                        $statusClass = 'badge-secondary';
                        $statusLabel = $asset->status;
                        if ($asset->status === 'Assigned') { $statusClass = 'badge-primary'; $statusLabel = 'مُسلمة للموظف'; }
                        elseif ($asset->status === 'Returned') { $statusClass = 'badge-success'; $statusLabel = 'تم استرجاعها'; }
                        elseif ($asset->status === 'Lost') { $statusClass = 'badge-danger'; $statusLabel = 'مفقودة/تالفة'; }
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><i class="fas fa-box text-muted me-1"></i> <?php echo htmlspecialchars($asset->asset_type); ?></td>
                        <td class="font-monospace text-muted fw-bold"><?php echo htmlspecialchars($asset->asset_id); ?></td>
                        <td class="text-center font-monospace fs-6"><?php echo $asset->issue_date; ?></td>
                        <td class="text-center"><span class="badge badge-light border text-muted"><?php echo htmlspecialchars($asset->condition_given ?? '—'); ?></span></td>
                        <td class="text-center font-monospace fs-6 text-danger fw-bold"><?php echo $asset->return_date ?? '—'; ?></td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                    </tr>
                    <?php endforeach; if(empty($assets)): ?>
                        <tr><td colspan="6" class="text-center text-muted p-5"><i class="fas fa-laptop-house fs-1 opacity-25 mb-3 d-block"></i>لا توجد عهد أو أصول مسلمة للموظف.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 6️⃣ التبويب السادس: سجل الإجازات -->
<div class="emp-panel" id="panel-leave">
    <div class="card mb-0 shadow-sm border-0">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h3 class="card-title fs-6 text-dark mb-0"><i class="fas fa-calendar-minus text-danger"></i> سجل الإجازات والمغادرات (Leave History)</h3>
            <a href="<?php echo URLROOT; ?>/leave/create" class="btn btn-sm btn-dark d-print-none"><i class="fas fa-paper-plane"></i> تسجيل إجازة</a>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0 table-hover">
                <thead class="bg-white"><tr><th>نوع الإجازة</th><th class="text-center">من تاريخ (Start)</th><th class="text-center">إلى تاريخ (End)</th><th class="text-center">المدة المخصومة</th><th class="text-center">حالة الطلب</th></tr></thead>
                <tbody>
                    <?php foreach($leaves as $l): 
                        $statusClass = 'badge-secondary';
                        $statusLabel = 'غير محدد';
                        if ($l->status === 'pending') { $statusClass = 'badge-warning'; $statusLabel = 'قيد الانتظار'; }
                        elseif ($l->status === 'approved') { $statusClass = 'badge-success'; $statusLabel = 'تمت الموافقة'; }
                        elseif ($l->status === 'rejected') { $statusClass = 'badge-danger'; $statusLabel = 'مرفوضة'; }
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><span class="badge badge-secondary fs-6"><i class="fas fa-plane-departure me-1"></i> <?php echo htmlspecialchars($l->leave_type); ?></span></td>
                        <td class="text-center font-monospace text-muted fw-bold"><?php echo $l->start_date; ?></td>
                        <td class="text-center font-monospace text-muted fw-bold"><?php echo $l->end_date; ?></td>
                        <td class="text-center font-monospace fw-black text-danger fs-5"><?php echo $l->total_days; ?> <small class="text-muted fs-7">أيام</small></td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                    </tr>
                    <?php endforeach; if(empty($leaves)): ?>
                        <tr><td colspan="5" class="text-center text-muted p-5"><i class="fas fa-umbrella-beach fs-1 opacity-25 mb-3 d-block"></i>لم يتم تسجيل إجازات سابقة للموظف.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- سكربت التحكم في التبويبات -->
<script>
    document.querySelectorAll('.emp-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            // إزالة التفعيل من جميع التبويبات والمحتوى
            document.querySelectorAll('.emp-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.emp-panel').forEach(p => p.classList.remove('active'));
            
            // تفعيل التبويب والمحتوى المختار
            tab.classList.add('active');
            document.getElementById('panel-' + tab.dataset.target).classList.add('active');
        });
    });
</script>