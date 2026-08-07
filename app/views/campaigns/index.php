<?php
// المسار: app/views/campaigns/index.php
$campaigns = $data['campaigns'] ?? [];
$isAdmin = $data['is_admin'] ?? false;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-bullhorn text-primary"></i> الحملات التسويقية (Campaigns)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة المبادرات التسويقية ومتابعة استقطاب العملاء المحتملين.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/campaign/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إنشاء حملة جديدة
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>اسم الحملة</th>
                        <th>النوع والجمهور</th>
                        <th>التاريخ (من - إلى)</th>
                        <th>الميزانية (ر.س)</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($campaigns)): foreach($campaigns as $camp): 
                        $statusClass = match($camp->status) {
                            'planned' => 'badge-secondary',
                            'active' => 'badge-success',
                            'completed' => 'badge-info',
                            'cancelled' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($camp->status) {
                            'planned' => 'مخطط لها', 'active' => 'نشطة حالياً', 'completed' => 'مكتملة', 'cancelled' => 'ملغاة', default => $camp->status
                        };
                        $typeIcon = match($camp->type) {
                            'email' => 'fa-envelope text-accent',
                            'sms' => 'fa-comment-sms text-info',
                            'social' => 'fa-hashtag text-primary',
                            'print' => 'fa-print text-muted',
                            default => 'fa-bullhorn text-purple'
                        };
                    ?>
                    <tr>
                        <td class="fw-bold text-dark">
                            <?php echo htmlspecialchars($camp->name); ?>
                            <div class="text-muted" style="font-size: 11px; margin-top: 4px;"><i class="fas fa-user-pen"></i> أُنشئت بواسطة: <?php echo htmlspecialchars($camp->created_by_name ?? 'النظام'); ?></div>
                        </td>
                        <td>
                            <div class="fw-bold text-body"><i class="fas <?php echo $typeIcon; ?> me-1"></i> <?php echo ucfirst($camp->type); ?></div>
                            <div class="text-muted fs-6"><i class="fas fa-users"></i> <?php echo htmlspecialchars($camp->target_audience ?? 'عام'); ?></div>
                        </td>
                        <td class="font-monospace text-muted fs-6">
                            <?php echo date('M d, Y', strtotime($camp->start_date)); ?> <br>
                            <?php echo date('M d, Y', strtotime($camp->end_date)); ?>
                        </td>
                        <td class="font-monospace fw-bold text-success fs-5" style="direction:ltr; text-align:right;">
                            <?php echo number_format($camp->budget, 2); ?>
                        </td>
                        <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/campaign/edit/<?php echo $camp->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if($isAdmin): ?>
                                <form action="<?php echo URLROOT; ?>/campaign/delete/<?php echo $camp->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد الحذف نهائياً؟ هذا الإجراء لا يمكن التراجع عنه.');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted" style="padding: 60px;"><i class="fas fa-bullhorn fs-1 mb-3 opacity-50 d-block"></i> لا توجد حملات تسويقية مسجلة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>