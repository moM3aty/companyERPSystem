<?php
// app/views/tickets/view.php
$ticket = $data['ticket'] ?? null;
$comments = $data['comments'] ?? [];

$priorityBadge = match($ticket->priority) {
    'urgent' => 'badge-danger', 'high' => 'badge-warning', 'medium' => 'badge-info', 'low' => 'badge-secondary', default => 'badge-secondary'
};
$priorityLabel = match($ticket->priority) {
    'urgent' => 'عاجل جداً', 'high' => 'مرتفعة', 'medium' => 'متوسطة', 'low' => 'منخفضة', default => $ticket->priority
};

$statusBadge = match($ticket->status) {
    'open' => 'badge-danger', 'in_progress' => 'badge-info', 'resolved' => 'badge-success', 'closed' => 'badge-secondary', default => 'badge-secondary'
};
$statusLabel = match($ticket->status) {
    'open' => 'مفتوحة (بانتظار الرد)', 'in_progress' => 'قيد المعالجة', 'resolved' => 'محلولة', 'closed' => 'مغلقة نهائياً', default => $ticket->status
};

// حساب الوقت المنقضي
$createdTime = strtotime($ticket->created_at);
$now = time();
$hoursElapsed = round(($now - $createdTime) / 3600);
$isLate = ($ticket->status == 'open' || $ticket->status == 'in_progress') && $hoursElapsed > 48;
?>

<div class="form-grid" style="grid-template-columns: 1fr 2fr; gap: 24px; align-items: start;">
    
    <!-- الجانب الأيمن: بيانات التذكرة والإجراءات -->
    <div class="d-flex flex-column gap-4">
        <div class="card mb-0">
            <div class="card-header bg-light">
                <h3 class="card-title text-dark"><i class="fas fa-ticket text-primary"></i> بيانات التذكرة</h3>
            </div>
            <div class="card-body">
                <div class="mb-3 pb-3 border-bottom">
                    <div class="text-muted fs-6 text-uppercase fw-bold mb-1">رقم التذكرة</div>
                    <div class="font-monospace fs-5 fw-bold text-dark"><?php echo htmlspecialchars($ticket->ticket_number); ?></div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="text-muted fs-6 text-uppercase fw-bold mb-1">العميل / المؤسسة</div>
                    <div class="fw-bold text-dark"><i class="fas fa-building text-muted"></i> <?php echo htmlspecialchars($ticket->customer_name ?? 'تذكرة عامة/داخلية'); ?></div>
                </div>
                <div class="mb-3 pb-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted fs-6 text-uppercase fw-bold mb-1">الأولوية</div>
                        <span class="badge <?php echo $priorityBadge; ?>"><?php echo $priorityLabel; ?></span>
                    </div>
                    <div>
                        <div class="text-muted fs-6 text-uppercase fw-bold mb-1">الحالة</div>
                        <span class="badge <?php echo $statusBadge; ?>"><?php echo $statusLabel; ?></span>
                    </div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="text-muted fs-6 text-uppercase fw-bold mb-1">الموظف المعين للمتابعة</div>
                    <div class="fw-bold text-body"><i class="fas fa-user-tie text-info"></i> <?php echo htmlspecialchars($ticket->assigned_to_name ?? 'غير معين'); ?></div>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <div class="text-muted fs-6 text-uppercase fw-bold mb-1">تاريخ الفتح وSLA</div>
                    <div class="text-dark fs-6"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d h:i A', strtotime($ticket->created_at)); ?></div>
                    <?php if ($isLate): ?>
                        <div class="text-danger mt-1 fs-6 fw-bold"><i class="fas fa-exclamation-triangle"></i> التذكرة متأخرة عن الوقت المعياري (مر <?php echo $hoursElapsed; ?> ساعة)</div>
                    <?php endif; ?>
                </div>

                <!-- تغيير الحالة -->
                <div class="mt-4">
                    <div class="text-muted fs-6 text-uppercase fw-bold mb-2">تحديث حالة التذكرة:</div>
                    <form action="<?php echo URLROOT; ?>/ticket/changeStatus/<?php echo $ticket->id; ?>" method="POST" class="d-flex gap-2">
                        <select name="status" class="form-control" style="flex: 1;">
                            <option value="open" <?php echo $ticket->status == 'open' ? 'selected' : ''; ?>>مفتوحة</option>
                            <option value="in_progress" <?php echo $ticket->status == 'in_progress' ? 'selected' : ''; ?>>قيد المعالجة</option>
                            <option value="resolved" <?php echo $ticket->status == 'resolved' ? 'selected' : ''; ?>>محلولة (Resolved)</option>
                            <option value="closed" <?php echo $ticket->status == 'closed' ? 'selected' : ''; ?>>مغلقة نهائياً</option>
                        </select>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i></button>
                    </form>
                </div>
            </div>
        </div>
        
        <a href="<?php echo URLROOT; ?>/ticket/index" class="btn btn-secondary w-100"><i class="fas fa-arrow-right"></i> العودة للقائمة</a>
    </div>

    <!-- الجانب الأيسر: الوصف والتعليقات -->
    <div class="d-flex flex-column gap-4">
        <!-- وصف المشكلة الأساسي -->
        <div class="card mb-0">
            <div class="card-header border-bottom-0 pb-0">
                <h3 class="card-title text-dark fs-4"><?php echo htmlspecialchars($ticket->subject); ?></h3>
            </div>
            <div class="card-body">
                <div class="bg-light p-4 rounded border" style="line-height: 1.8; color: var(--text-dark); font-size: 15px;">
                    <?php echo nl2br(htmlspecialchars($ticket->description)); ?>
                </div>
            </div>
        </div>

        <!-- سلسلة التعليقات (Thread) -->
        <div class="card mb-0" style="background: #f8fafc;">
            <div class="card-header bg-transparent">
                <h3 class="card-title"><i class="fas fa-comments text-info"></i> سجل المتابعة والردود</h3>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <?php if (empty($comments)): ?>
                    <div class="text-center text-muted p-4">
                        <i class="fas fa-comment-dots fs-1 mb-2 opacity-50"></i>
                        <p>لا توجد تعليقات أو ردود على هذه التذكرة بعد.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($comments as $comment): 
                            // تحديد ما إذا كان التعليق من المستخدم الحالي لتلوينه بلون مختلف
                            $isMine = $comment->user_id == Session::getUserId();
                            $bgColor = $isMine ? 'bg-primary-light border-primary' : 'bg-white border';
                            $textColor = $isMine ? 'text-primary-dark' : 'text-dark';
                        ?>
                            <div class="p-3 rounded shadow-sm <?php echo $bgColor; ?>" style="border-width: 1px; border-style: solid;">
                                <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                                    <strong class="<?php echo $textColor; ?>"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($comment->user_name); ?></strong>
                                    <span class="text-muted font-monospace" style="font-size: 11px;"><?php echo date('Y-m-d h:i A', strtotime($comment->created_at)); ?></span>
                                </div>
                                <div style="font-size: 14px; line-height: 1.6; color: var(--text-body);">
                                    <?php echo nl2br(htmlspecialchars($comment->comment)); ?>
                                </div>
                                <?php if(!empty($comment->attachment_path)): ?>
                                    <div class="mt-3">
                                        <a href="<?php echo URLROOT . $comment->attachment_path; ?>" target="_blank" class="badge badge-secondary" style="text-decoration:none;"><i class="fas fa-paperclip"></i> تحميل المرفق</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- نموذج إضافة رد -->
            <?php if ($ticket->status !== 'closed'): ?>
            <div class="card-footer bg-white border-top p-4">
                <form action="<?php echo URLROOT; ?>/ticket/addComment/<?php echo $ticket->id; ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group mb-3">
                        <label class="form-label text-muted">إضافة رد أو ملاحظة داخلية:</label>
                        <textarea name="comment" class="form-control" rows="3" required placeholder="اكتب ردك هنا لتوثيق الإجراء الذي تم..."></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <label class="btn btn-secondary mb-0" style="cursor: pointer;">
                            <i class="fas fa-paperclip"></i> إرفاق ملف
                            <input type="file" name="attachment" style="display:none;" onchange="alert('تم إرفاق: ' + this.files[0].name)">
                        </label>
                        <button type="submit" class="btn btn-info text-white"><i class="fas fa-paper-plane"></i> إرسال الرد</button>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <div class="card-footer bg-light border-top p-3 text-center text-muted fw-bold">
                <i class="fas fa-lock text-danger"></i> التذكرة مغلقة، لا يمكن إضافة ردود جديدة.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>