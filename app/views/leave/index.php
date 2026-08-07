<?php
// app/views/leave/index.php
$requests = $requests ?? ($data['requests'] ?? []);
$isAdmin = $isAdmin ?? ($data['is_admin'] ?? false);
?>

<div class="d-flex justify-content-between align-items-center" style="margin-bottom: 24px;">
    <div>
        <h3 class="card-title mb-0"><i class="fas fa-calendar-minus text-primary"></i> إدارة طلبات الإجازات</h3>
        <p class="text-muted mt-0">متابعة واعتماد إجازات الموظفين بمختلف أنواعها.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <a href="<?php echo URLROOT; ?>/leave/create" class="btn btn-primary"><i class="fas fa-plus"></i> تقديم طلب إجازة</a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>نوع الإجازة</th>
                        <th>المدة وتاريخها</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">الاعتماد والإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($requests)): foreach ($requests as $req) : 
                        $statusClass = match($req->status) {
                            'pending' => 'badge-warning',
                            'approved' => 'badge-success',
                            'rejected' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($req->status) {
                            'pending' => '<i class="fas fa-clock"></i> قيد المراجعة',
                            'approved' => '<i class="fas fa-check-double"></i> تمت الموافقة',
                            'rejected' => '<i class="fas fa-xmark"></i> مرفوض',
                            default => $req->status
                        };
                        
                        // حساب الأيام
                        $start = new DateTime($req->start_date);
                        $end = new DateTime($req->end_date);
                        $diff = $start->diff($end)->days + 1; // +1 لحساب اليوم نفسه
                    ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($req->employee_name); ?></div>
                            <div class="text-muted" style="font-size:11px;"><?php echo htmlspecialchars(mb_substr($req->reason, 0, 30)) . '...'; ?></div>
                        </td>
                        <td><span class="badge badge-info"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($req->leave_type_name); ?></span></td>
                        <td>
                            <div class="font-monospace text-dark fw-bold"><?php echo $diff; ?> أيام</div>
                            <div class="text-muted" style="font-size:11px;"><?php echo date('M d', strtotime($req->start_date)); ?> إلى <?php echo date('M d', strtotime($req->end_date)); ?></div>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                            <?php if ($req->approved_by_name): ?>
                                <div style="font-size:10px; color:var(--text-muted); margin-top:4px;">بواسطة: <?php echo htmlspecialchars($req->approved_by_name); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <?php if ($req->status === 'pending') : ?>
                                    
                                    <?php if ($isAdmin) : ?>
                                        <form method="POST" action="<?php echo URLROOT; ?>/leave/approve/<?php echo $req->id; ?>" style="display:inline;" onsubmit="return confirm('تأكيد الموافقة على الإجازة؟');">
                                            <button type="submit" class="btn-icon view" title="موافقة"><i class="fas fa-check text-success"></i></button>
                                        </form>
                                        <form method="POST" action="<?php echo URLROOT; ?>/leave/reject/<?php echo $req->id; ?>" style="display:inline;" onsubmit="return confirm('تأكيد رفض الإجازة؟');">
                                            <button type="submit" class="btn-icon delete" title="رفض"><i class="fas fa-times text-danger"></i></button>
                                        </form>
                                    <?php endif; ?>

                                    <!-- التعديل والحذف متاح لصاحب الطلب أو الإدارة مادام معلقاً -->
                                    <a href="<?php echo URLROOT; ?>/leave/edit/<?php echo $req->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                    <form action="<?php echo URLROOT; ?>/leave/delete/<?php echo $req->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من إلغاء وحذف الطلب؟');">
                                        <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size:12px;"><i class="fas fa-lock"></i> مقفل</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding: 40px;">
                            <i class="fas fa-calendar-xmark" style="font-size: 40px; opacity:0.3; margin-bottom:10px; display:block;"></i>
                            لا توجد طلبات إجازة مسجلة في النظام.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>