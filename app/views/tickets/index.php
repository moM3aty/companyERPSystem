<?php
// app/views/tickets/index.php
$tickets = $data['tickets'] ?? [];
$isAdmin = in_array(Session::getUserRole(), ['admin', 'super_admin']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-headset text-primary"></i> مركز الدعم والتذاكر</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تواصل مع الدعم الفني للإبلاغ عن مشكلة أو طلب مساعدة.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/ticket/create" class="btn btn-primary">
        <i class="fas fa-ticket"></i> فتح تذكرة جديدة
    </a>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 80px;">رقم</th>
                        <th>الموضوع</th>
                        <th>المستخدم</th>
                        <th class="text-center">الأهمية</th>
                        <th class="text-center">الحالة</th>
                        <?php if($isAdmin): ?><th class="text-center">تحديث</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($tickets as $t): 
                        $priClass = match($t->priority) { 'high' => 'text-danger', 'low' => 'text-info', default => 'text-warning' };
                        $stClass = match($t->status) { 'closed' => 'badge-success', 'in_progress' => 'badge-primary', default => 'badge-secondary' };
                        $stLabel = match($t->status) { 'closed' => 'مغلقة', 'in_progress' => 'قيد المعالجة', default => 'مفتوحة' };
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-muted">#<?php echo $t->id; ?></td>
                        <td>
                            <strong class="text-dark"><?php echo htmlspecialchars($t->subject); ?></strong>
                            <div class="text-muted" style="font-size:11px;"><?php echo date('Y-m-d H:i', strtotime($t->created_at)); ?></div>
                        </td>
                        <td><i class="fas fa-user text-muted"></i> <?php echo htmlspecialchars($t->user_name); ?></td>
                        <td class="text-center fw-bold <?php echo $priClass; ?>"><i class="fas fa-circle" style="font-size:10px;"></i> <?php echo strtoupper($t->priority); ?></td>
                        <td class="text-center"><span class="badge <?php echo $stClass; ?>"><?php echo $stLabel; ?></span></td>
                        
                        <?php if($isAdmin): ?>
                        <td class="text-center">
                        <div class="d-flex align-items-center justify-content-center gap-2">
                            <!-- زر التعديل يظهر لصاحب التذكرة أو الأدمن -->
                            <a href="<?php echo URLROOT; ?>/ticket/edit/<?php echo $t->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                            
                            <?php if($isAdmin): ?>
                            <!-- تغيير الحالة للآدمن فقط -->
                            <form action="<?php echo URLROOT; ?>/ticket/updateStatus/<?php echo $t->id; ?>" method="POST" class="d-inline-block">
                                <select name="status" class="form-control form-control-sm font-monospace" onchange="this.form.submit()" style="width:100px; padding: 2px 10px;">
                                    <option value="open" <?php echo $t->status=='open'?'selected':'';?>>مفتوحة</option>
                                    <option value="in_progress" <?php echo $t->status=='in_progress'?'selected':'';?>>معالجة</option>
                                    <option value="closed" <?php echo $t->status=='closed'?'selected':'';?>>إغلاق</option>
                                </select>
                            </form>
                            <form action="<?php echo URLROOT; ?>/ticket/delete/<?php echo $t->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد مسح التذكرة؟');">
                                <button type="submit" class="btn-icon delete"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>