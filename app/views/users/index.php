<?php
// app/views/users/index.php
$users = $data['users'] ?? [];

// قاموس لترجمة وتلوين الصلاحيات بشكل احترافي
$roleMap = [
    'admin'      => ['label' => 'مدير المؤسسة', 'class' => 'badge-danger', 'icon' => 'fa-user-cog'],
    'manager'    => ['label' => 'مدير قسم', 'class' => 'badge-warning', 'icon' => 'fa-user-shield'],
    'hr'         => ['label' => 'شؤون الموظفين', 'class' => 'badge-purple', 'icon' => 'fa-user-tie'],
    'accountant' => ['label' => 'محاسب', 'class' => 'badge-primary', 'icon' => 'fa-file-invoice-dollar'],
    'sales_rep'  => ['label' => 'مندوب مبيعات', 'class' => 'badge-success', 'icon' => 'fa-user-tag'],
    'editor'     => ['label' => 'مُدخل بيانات', 'class' => 'badge-info', 'icon' => 'fa-pen-to-square'],
    'viewer'     => ['label' => 'مُشاهد للاطلاع', 'class' => 'badge-secondary', 'icon' => 'fa-eye'],
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-users-gear text-primary"></i> إدارة المستخدمين والصلاحيات</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تحديد من يمكنه الدخول للنظام والتحكم بصلاحياته.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/user/create" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
    </a>
</div>

<?php 
    $flash = Session::getFlash();
    if ($flash): 
?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>">
        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>اسم المستخدم</th>
                        <th>البريد الإلكتروني (تسجيل الدخول)</th>
                        <th class="text-center">الدور والصلاحية (Role)</th>
                        <th class="text-center">تاريخ الإنشاء</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($users)): foreach($users as $user): 
                        $r = $user->role ?? 'viewer';
                        $rData = $roleMap[$r] ?? $roleMap['viewer'];
                    ?>
                    <tr>
                        <td class="text-center">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--slate-100); color: var(--slate-600); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px;">
                                <?php echo mb_substr($user->name, 0, 1); ?>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6"><?php echo htmlspecialchars($user->name); ?></div>
                        </td>
                        <td class="font-monospace text-muted" style="direction:ltr; text-align:right;">
                            <i class="fas fa-envelope fs-7 me-1"></i> <?php echo htmlspecialchars($user->email); ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $rData['class']; ?> py-2 px-3" style="font-size: 12px;">
                                <i class="fas <?php echo $rData['icon']; ?> me-1"></i> <?php echo $rData['label']; ?>
                            </span>
                        </td>
                        <td class="text-center text-muted font-monospace" style="font-size: 12px;">
                            <?php echo date('Y-m-d', strtotime($user->created_at)); ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/user/edit/<?php echo $user->id; ?>" class="btn-icon edit" title="تعديل المستخدم والصلاحية"><i class="fas fa-pen"></i></a>
                                
                                <?php if($user->id !== Session::getUserId()): ?>
                                <form action="<?php echo URLROOT; ?>/user/delete/<?php echo $user->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف هذا المستخدم نهائياً ومنعه من الدخول؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف حساب المستخدم"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php else: ?>
                                    <button class="btn-icon" style="opacity: 0.3; cursor: not-allowed;" title="لا يمكنك حذف حسابك الشخصي"><i class="fas fa-ban"></i></button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted p-5"><i class="fas fa-users-slash fa-3x mb-3 opacity-50 d-block"></i> لا يوجد مستخدمين مسجلين في النظام.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>