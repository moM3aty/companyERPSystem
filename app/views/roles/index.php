<?php
// app/views/roles/index.php
$title = $data['title'] ?? 'دليل الصلاحيات';

// تعريف الصلاحيات لغرض العرض في المصفوفة
$roles = [
    'admin' => ['name' => 'مدير المؤسسة', 'color' => 'var(--danger)', 'desc' => 'صلاحيات مطلقة داخل الشركة'],
    'manager' => ['name' => 'مدير قسم', 'color' => 'var(--warning)', 'desc' => 'إدارة العمليات والاعتمادات'],
    'accountant' => ['name' => 'محاسب', 'color' => 'var(--info)', 'desc' => 'إدارة الشؤون المالية والقيود'],
    'sales_rep' => ['name' => 'مندوب مبيعات', 'color' => 'var(--success)', 'desc' => 'إدارة نقاط البيع وعروض الأسعار'],
    'hr' => ['name' => 'شؤون الموظفين', 'color' => 'var(--purple)', 'desc' => 'إدارة الموارد البشرية والرواتب'],
    'editor' => ['name' => 'مُدخل بيانات', 'color' => 'var(--primary)', 'desc' => 'إدخال وتعديل السجلات الأساسية'],
    'viewer' => ['name' => 'مُشاهد (للاطلاع)', 'color' => 'var(--slate-500)', 'desc' => 'قراءة التقارير وعرض البيانات فقط']
];

$features = [
    'الوصول للوحة القيادة والتقارير' => ['admin', 'manager', 'accountant', 'sales_rep', 'hr', 'viewer'],
    'إدارة إعدادات الشركة (اللوجو، الرقم الضريبي)' => ['admin'],
    'إضافة وحذف المستخدمين' => ['admin'],
    'حذف السجلات (فواتير، عملاء، منتجات)' => ['admin', 'manager'],
    'تسجيل وتعديل المصروفات وسندات الصرف' => ['admin', 'manager', 'accountant'],
    'اعتماد مسيرات الرواتب وإنشاء القيود' => ['admin', 'accountant', 'hr'],
    'إدارة الصندوق والحسابات البنكية' => ['admin', 'accountant'],
    'إصدار فواتير مبيعات ونقاط البيع (POS)' => ['admin', 'manager', 'sales_rep', 'editor'],
    'إنشاء عروض أسعار للعملاء' => ['admin', 'manager', 'sales_rep'],
    'تسجيل حضور وانصراف، إجازات، وسلف' => ['admin', 'hr', 'manager'],
    'إضافة منتجات، عملاء، موردين' => ['admin', 'manager', 'accountant', 'sales_rep', 'editor', 'hr'],
    'فتح والرد على تذاكر الدعم الفني' => ['admin', 'manager', 'sales_rep', 'hr', 'accountant', 'editor', 'viewer']
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-user-shield text-primary"></i> <?php echo htmlspecialchars($title); ?></h3>
        <p class="text-muted mt-1" style="font-size: 13px;">دليل مرجعي لصلاحيات النظام الافتراضية. يساعدك في اختيار الدور المناسب قبل إنشاء مستخدم جديد.</p>
    </div>
    <div>
        <a href="<?php echo URLROOT; ?>/user/create" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
        </a>
    </div>
</div>

<!-- بطاقات تعريف الأدوار -->
<div class="form-grid mb-4" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
    <?php foreach($roles as $key => $role): ?>
    <div class="card mb-0 border-0 shadow-sm" style="border-top: 4px solid <?php echo $role['color']; ?> !important;">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div style="width: 40px; height: 40px; border-radius: 8px; background: <?php echo $role['color']; ?>22; color: <?php echo $role['color']; ?>; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold" style="font-size: 15px; color: var(--text-dark);"><?php echo $role['name']; ?></h5>
                    <span class="font-monospace text-muted" style="font-size: 11px;">[<?php echo $key; ?>]</span>
                </div>
            </div>
            <p class="text-muted m-0" style="font-size: 12px;"><?php echo $role['desc']; ?></p>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- مصفوفة الصلاحيات (Matrix) -->
<div class="card">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-table text-info"></i> مصفوفة توزيع الصلاحيات (Permissions Matrix)</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover table-bordered" style="border: 1px solid var(--border-color);">
                <thead style="background: var(--slate-900); color: #fff;">
                    <tr>
                        <th style="width: 30%; color: #fff; border-bottom: none;">الوحدة / الإجراء</th>
                        <?php foreach($roles as $role): ?>
                            <th class="text-center" style="font-size: 12px; color: #fff; border-bottom: none; border-right: 1px solid rgba(255,255,255,0.1);">
                                <?php echo $role['name']; ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($features as $feature => $allowedRoles): ?>
                    <tr>
                        <td class="fw-bold text-dark" style="font-size: 13px; background: var(--slate-50); border-left: 1px solid var(--border-color);">
                            <i class="fas fa-caret-left text-muted me-2"></i> <?php echo $feature; ?>
                        </td>
                        <?php foreach($roles as $key => $role): 
                            $isAllowed = in_array($key, $allowedRoles);
                        ?>
                            <td class="text-center align-middle" style="border-left: 1px solid var(--border-color);">
                                <?php if($isAllowed): ?>
                                    <i class="fas fa-check-circle" style="color: var(--success); font-size: 18px;"></i>
                                <?php else: ?>
                                    <i class="fas fa-minus" style="color: var(--slate-300); font-size: 12px;"></i>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-light text-muted" style="font-size: 13px;">
        <i class="fas fa-info-circle text-info"></i> <strong>ملاحظة للمالك (Super Admin):</strong> إذا كنت ترغب مستقبلاً في تخصيص كل زر وشاشة بشكل دقيق لكل مستخدم (نظام RBAC ديناميكي)، يمكننا ترقية النظام لدعم الصلاحيات المتقدمة المخصصة.
    </div>
</div>