<?php
// app/views/employee/index.php
$employees = $data['employees'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-users text-primary"></i> دليل الموظفين (HR)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة بيانات موظفي الشركة والهيكل الوظيفي.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/employee/create" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> إضافة موظف جديد
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>الموظف / التواصل</th>
                        <th>المسمى الوظيفي</th>
                        <th>تاريخ التعيين</th>
                        <th>الراتب الأساسي</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($employees)): foreach($employees as $emp): ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                <i class="fas fa-user-circle text-muted fs-5"></i> 
                                <?php echo htmlspecialchars($emp->name); ?>
                            </div>
                            <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">
                                <i class="fas fa-phone"></i> <?php echo htmlspecialchars($emp->phone ?? '—'); ?> | <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($emp->email ?? '—'); ?>
                            </div>
                        </td>
                        <td class="fw-bold text-body"><?php echo htmlspecialchars($emp->position); ?></td>
                        <td class="text-muted fs-6"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($emp->hire_date)); ?></td>
                        <td class="font-monospace fw-bold text-success" style="direction:ltr; text-align:right;">
                            <?php echo number_format($emp->salary, 2); ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/employee/edit/<?php echo $emp->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if (Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/employee/delete/<?php echo $emp->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف الموظف نهائياً؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding:40px;">
                            <i class="fas fa-users-slash fs-1 text-muted mb-3 d-block"></i>
                            لا يوجد موظفين مسجلين
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>