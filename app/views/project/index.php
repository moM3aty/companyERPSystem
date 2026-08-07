<?php
// app/views/project/index.php
$projects = $projects ?? ($data['projects'] ?? []);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-diagram-project text-primary"></i> إدارة المشاريع والمهام</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تخطيط ومتابعة ميزانية ومهام المشاريع.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/project/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة مشروع جديد
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>كود المشروع</th>
                        <th>اسم المشروع / العميل</th>
                        <th>الميزانية</th>
                        <th>تاريخ التسليم</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($projects)): foreach($projects as $p): 
                        $statusClass = match($p->status) {
                            'planning' => 'badge-info',
                            'active' => 'badge-primary',
                            'completed' => 'badge-success',
                            'on_hold' => 'badge-warning',
                            'cancelled' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($p->status) {
                            'planning' => 'تخطيط', 'active' => 'نشط', 'completed' => 'مكتمل', 'on_hold' => 'معلق', 'cancelled' => 'ملغي', default => $p->status
                        };
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($p->code); ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($p->name); ?></div>
                            <div class="text-muted" style="font-size:11px;"><i class="fas fa-building"></i> <?php echo htmlspecialchars($p->customer_name ?? 'بدون عميل'); ?></div>
                        </td>
                        <td class="font-monospace fw-bold text-success" style="direction:ltr; text-align:right;"><?php echo number_format($p->budget, 2); ?></td>
                        <td class="text-muted fs-6"><i class="far fa-calendar-check"></i> <?php echo $p->end_date ? date('Y-m-d', strtotime($p->end_date)) : 'غير محدد'; ?></td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/project/show/<?php echo $p->id; ?>" class="btn-icon view" title="المهام وجانت"><i class="fas fa-chart-gantt"></i></a>
                                <a href="<?php echo URLROOT; ?>/project/edit/<?php echo $p->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/project/delete/<?php echo $p->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف المشروع بالكامل؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted" style="padding:40px;">لا توجد مشاريع مسجلة بعد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>