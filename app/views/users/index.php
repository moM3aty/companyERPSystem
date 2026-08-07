<?php
// المسار: app/views/users/index.php
$users = $users ?? ($data['users'] ?? []);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-users-gear text-primary"></i> إدارة النظام والمستخدمين</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">التحكم في الصلاحيات وحسابات الوصول للنظام.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/user/create" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> مستخدم جديد
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المستخدم</th>
                        <th class="text-center">الصلاحية (الدور)</th>
                        <th>الهاتف</th>
                        <th>تاريخ الانضمام</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($users)): foreach($users as $user): 
                        $roleBadge = match($user->role) {
                            'admin' => '<span class="badge badge-danger"><i class="fas fa-crown"></i> مدير عام</span>',
                            'manager' => '<span class="badge badge-warning"><i class="fas fa-user-tie"></i> مدير قسم</span>',
                            'editor' => '<span class="badge badge-success"><i class="fas fa-pen"></i> محرر</span>',
                            default => '<span class="badge badge-info"><i class="fas fa-eye"></i> موظف/عارض</span>',
                        };
                    ?>
                    <tr>
                        <td class="font-monospace text-muted fw-bold"><?php echo $user->id; ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($user->name); ?></div>
                            <div class="text-muted font-monospace" style="font-size:12px; direction:ltr; text-align:right;"><?php echo htmlspecialchars($user->email); ?></div>
                        </td>
                        <td class="text-center"><?php echo $roleBadge; ?></td>
                        <td class="font-monospace text-dark" style="direction:ltr; text-align:right;"><?php echo htmlspecialchars($user->phone ?? '—'); ?></td>
                        <td class="text-muted fs-6"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($user->created_at)); ?></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/user/edit/<?php echo $user->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if($user->id !== Session::getUserId()): ?>
                                    <form action="<?php echo URLROOT; ?>/user/delete/<?php echo $user->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف هذا المستخدم نهائياً؟');">
                                        <button type="submit" class="btn-icon delete" title="حذف החساب"><i class="fas fa-trash"></i></button>
                                    </form>
                                <?php else: ?>
                                    <span class="badge badge-success">حسابك</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted" style="padding: 40px;">لا يوجد مستخدمين لعرضهم.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>