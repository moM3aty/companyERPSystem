<?php
// app/views/tickets/index.php
$tickets = $data['tickets'] ?? [];
$stats = $data['stats'] ?? ['open_tickets'=>0, 'in_progress_tickets'=>0, 'closed_tickets'=>0, 'urgent_tickets'=>0];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-headset text-primary"></i> نظام تذاكر الدعم الفني</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">متابعة شكاوى واستفسارات العملاء والالتزام بمستوى الخدمة (SLA)</p>
    </div>
    <a href="<?php echo URLROOT; ?>/ticket/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> تذكرة جديدة
    </a>
</div>

<!-- لوحة إحصائيات التذاكر (SLA Dashboard) -->
<div class="form-grid mb-4" style="grid-template-columns: repeat(4, 1fr);">
    <div class="card mb-0">
        <div class="card-body text-center p-3">
            <h4 class="text-danger fw-bold fs-3 mb-1 font-monospace"><?php echo $stats['urgent_tickets']; ?></h4>
            <div class="text-muted" style="font-size: 12px; font-weight: 700;"><i class="fas fa-fire text-danger"></i> تذاكر طارئة وعاجلة</div>
        </div>
    </div>
    <div class="card mb-0">
        <div class="card-body text-center p-3">
            <h4 class="text-warning fw-bold fs-3 mb-1 font-monospace"><?php echo $stats['open_tickets']; ?></h4>
            <div class="text-muted" style="font-size: 12px; font-weight: 700;"><i class="fas fa-envelope-open text-warning"></i> تذاكر مفتوحة للتو</div>
        </div>
    </div>
    <div class="card mb-0">
        <div class="card-body text-center p-3">
            <h4 class="text-info fw-bold fs-3 mb-1 font-monospace"><?php echo $stats['in_progress_tickets']; ?></h4>
            <div class="text-muted" style="font-size: 12px; font-weight: 700;"><i class="fas fa-spinner text-info"></i> تذاكر قيد المعالجة</div>
        </div>
    </div>
    <div class="card mb-0">
        <div class="card-body text-center p-3">
            <h4 class="text-success fw-bold fs-3 mb-1 font-monospace"><?php echo $stats['closed_tickets']; ?></h4>
            <div class="text-muted" style="font-size: 12px; font-weight: 700;"><i class="fas fa-check-double text-success"></i> تذاكر مغلقة/محلولة</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>رقم التذكرة</th>
                        <th>العنوان / العميل</th>
                        <th class="text-center">الأولوية</th>
                        <th>المسؤول (الموظف)</th>
                        <th>مؤشر الوقت (SLA)</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراء السريع</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($tickets)): foreach($tickets as $t): 
                        $priorityBadge = match($t->priority) {
                            'urgent' => 'badge-danger', 'high' => 'badge-warning', 'medium' => 'badge-info', 'low' => 'badge-secondary', default => 'badge-secondary'
                        };
                        $priorityLabel = match($t->priority) {
                            'urgent' => 'عاجل جداً', 'high' => 'مرتفعة', 'medium' => 'متوسطة', 'low' => 'منخفضة', default => $t->priority
                        };
                        $statusBadge = match($t->status) {
                            'open' => 'badge-danger', 'in_progress' => 'badge-info', 'resolved' => 'badge-success', 'closed' => 'badge-secondary', default => 'badge-secondary'
                        };
                        $statusLabel = match($t->status) {
                            'open' => 'مفتوحة', 'in_progress' => 'قيد المعالجة', 'resolved' => 'محلولة', 'closed' => 'مغلقة', default => $t->status
                        };
                        
                        // حساب وقت فتح التذكرة SLA
                        $createdTime = strtotime($t->created_at);
                        $now = time();
                        $hoursElapsed = round(($now - $createdTime) / 3600);
                        
                        if (in_array($t->status, ['resolved', 'closed'])) {
                            $slaHtml = '<span class="text-success" style="font-size:11px; font-weight:bold;"><i class="fas fa-check"></i> تم إغلاقها</span>';
                        } else {
                            if ($hoursElapsed > 48) {
                                $slaHtml = '<span class="text-danger" style="font-size:11px; font-weight:bold;"><i class="fas fa-exclamation-circle"></i> متأخرة (' . $hoursElapsed . ' ساعة)</span>';
                            } else {
                                $slaHtml = '<span class="text-info" style="font-size:11px; font-weight:bold;"><i class="far fa-clock"></i> مفتوحة منذ ' . $hoursElapsed . ' ساعة</span>';
                            }
                        }
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><span class="badge badge-secondary"><?php echo htmlspecialchars($t->ticket_number); ?></span></td>
                        <td>
                            <div class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($t->subject); ?></div>
                            <div class="text-muted" style="font-size: 11px;"><i class="fas fa-user"></i> <?php echo htmlspecialchars($t->customer_name ?? 'بدون عميل مرتبط'); ?></div>
                        </td>
                        <td class="text-center"><span class="badge <?php echo $priorityBadge; ?>"><?php echo $priorityLabel; ?></span></td>
                        <td class="text-muted fw-bold fs-6">
                            <?php if($t->assigned_to_name): ?>
                                <i class="fas fa-user-tie text-primary"></i> <?php echo htmlspecialchars($t->assigned_to_name); ?>
                            <?php else: ?>
                                <span class="text-danger" style="font-size:12px;"><i class="fas fa-exclamation-triangle"></i> غير معين</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $slaHtml; ?></td>
                        <td class="text-center"><span class="badge <?php echo $statusBadge; ?>"><?php echo $statusLabel; ?></span></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <?php if ($t->status === 'open'): ?>
                                    <form action="<?php echo URLROOT; ?>/ticket/changeStatus/<?php echo $t->id; ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="status" value="in_progress">
                                        <button type="submit" class="btn-icon view" title="بدء العمل"><i class="fas fa-play"></i></button>
                                    </form>
                                <?php elseif ($t->status === 'in_progress'): ?>
                                    <form action="<?php echo URLROOT; ?>/ticket/changeStatus/<?php echo $t->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('هل تم حل المشكلة فعلياً؟');">
                                        <input type="hidden" name="status" value="resolved">
                                        <button type="submit" class="btn-icon" style="color:var(--success); background:var(--success-light); border:none;" title="تحديد كمحلولة"><i class="fas fa-check-double"></i></button>
                                    </form>
                                <?php else: ?>
                                     <span class="text-muted fs-6"><i class="fas fa-lock"></i> مقفلة</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="7" class="text-center text-muted p-5">لا توجد تذاكر دعم فني.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>