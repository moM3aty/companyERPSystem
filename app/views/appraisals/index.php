<?php
// المسار: app/views/appraisals/index.php
$appraisals = $data['appraisals'] ?? [];
$isAdmin = $data['is_admin'] ?? false;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-star-half-stroke text-primary"></i> سجل تقييمات الأداء</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">متابعة وتقييم كفاءة أداء فريق العمل.</p>
    </div>
    <?php if ($isAdmin) : ?>
    <a href="<?php echo URLROOT; ?>/appraisal/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> تقييم جديد
    </a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>تاريخ التقييم</th>
                        <th>الموظف</th>
                        <th class="text-center">النسبة</th>
                        <th class="text-center">التقدير العام</th>
                        <th>ملاحظات المقيم</th>
                        <th>بواسطة</th>
                        <?php if ($isAdmin) : ?><th class="text-center">إجراء</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($appraisals)): foreach ($appraisals as $app) : 
                        $badgeClass = match(true) {
                            $app->total_score >= 90 => 'badge-success',
                            $app->total_score >= 80 => 'badge-info',
                            $app->total_score >= 70 => 'badge-primary',
                            $app->total_score >= 60 => 'badge-warning',
                            default => 'badge-danger'
                        };
                    ?>
                    <tr>
                        <td class="text-muted fs-6"><i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($app->evaluation_date)); ?></td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($app->employee_name ?? '—'); ?></div>
                            <div class="text-muted" style="font-size:11px;"><?php echo htmlspecialchars($app->position); ?></div>
                        </td>
                        <td class="text-center font-monospace fw-bold fs-5 text-dark">
                            <?php echo round($app->total_score); ?>%
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $badgeClass; ?>">
                                <?php echo htmlspecialchars($app->grade); ?>
                            </span>
                        </td>
                        <td class="text-muted" style="font-size: 13px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?php echo htmlspecialchars($app->comments); ?>
                        </td>
                        <td class="text-muted fs-6"><i class="fas fa-user-gear"></i> <?php echo htmlspecialchars($app->evaluator_name); ?></td>
                        
                        <?php if ($isAdmin) : ?>
                        <td class="text-center">
                            <form method="POST" action="<?php echo URLROOT; ?>/appraisal/delete/<?php echo $app->id; ?>" style="display:inline;" onsubmit="return confirm('تأكيد حذف التقييم؟');">
                                <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="<?php echo $isAdmin ? '7' : '6'; ?>" class="text-center text-muted" style="padding:40px;">لا توجد تقييمات مسجلة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>