<?php $requests = $data['requests'] ?? []; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-clipboard-list text-primary"></i> طلبات الاحتياج الداخلية (PR)</h3>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/purchaseRequest/create" class="btn btn-primary fw-bold"><i class="fas fa-plus"></i> رفع طلب جديد</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?> mb-3"><i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 align-middle text-center table-hover">
            <thead class="bg-light">
                <tr>
                    <th>رقم الطلب</th>
                    <th>التاريخ</th>
                    <th>مقدم الطلب</th>
                    <th>القسم</th>
                    <th class="text-left">التكلفة التقديرية</th>
                    <th>الحالة</th>
                    <th class="d-print-none">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($requests as $r): 
                    $badge = $r->status == 'Approved' ? 'bg-success' : ($r->status == 'Rejected' ? 'bg-danger' : 'bg-warning text-dark');
                ?>
                <tr>
                    <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($r->request_number); ?></td>
                    <td class="font-monospace text-muted"><?php echo $r->request_date; ?></td>
                    <td class="fw-bold"><i class="fas fa-user text-muted me-1"></i> <?php echo htmlspecialchars($r->user_name); ?></td>
                    <td><?php echo htmlspecialchars($r->department ?: '—'); ?></td>
                    <td class="font-monospace fw-black text-primary fs-5 text-left" style="direction:ltr;"><?php echo number_format($r->total_estimated, 2); ?></td>
                    <td><span class="badge <?php echo $badge; ?>"><?php echo htmlspecialchars($r->status); ?></span></td>
                    <td class="d-print-none">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="<?php echo URLROOT; ?>/purchaseRequest/show/<?php echo $r->id; ?>" class="btn-icon view text-primary"><i class="fas fa-eye"></i></a>
                            <?php if(Session::hasRole('admin') || Session::hasRole('super_admin')): ?>
                            <form action="<?php echo URLROOT; ?>/purchaseRequest/delete/<?php echo $r->id; ?>" method="POST" onsubmit="return confirm('تأكيد حذف الطلب؟');">
                                <button type="submit" class="btn-icon delete text-danger" style="border:none; background:none;"><i class="fas fa-trash"></i></button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; if(empty($requests)): ?>
                    <tr><td colspan="7" class="text-center text-muted p-5"><i class="fas fa-clipboard-list fs-1 opacity-25 mb-3 d-block"></i>لا توجد طلبات احتياج.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>