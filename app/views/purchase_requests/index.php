<?php
// المسار: app/views/purchase_requests/index.php
$requests = $requests ?? ($data['requests'] ?? []);
$isAdmin = $isAdmin ?? ($data['is_admin'] ?? false);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-signature text-primary"></i> طلبات الشراء الداخلية (PR)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">توثيق الاحتياجات واعتمادها من الإدارة قبل إرسال أمر الشراء للمورد</p>
    </div>
    <a href="<?php echo URLROOT; ?>/purchaseRequest/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> رفع طلب شراء للإدارة
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>تاريخ الطلب</th>
                        <th>الموظف الطالب</th>
                        <th>المعتمد بواسطة</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">تفاصيل واعتماد</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($requests)): foreach($requests as $req): 
                        $statusClass = match($req->status) {
                            'pending' => 'badge-warning',
                            'approved' => 'badge-success',
                            'rejected' => 'badge-danger',
                            'ordered' => 'badge-primary',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($req->status) {
                            'pending' => '<i class="fas fa-clock"></i> قيد المراجعة',
                            'approved' => '<i class="fas fa-check-double"></i> معتمد للإصدار',
                            'rejected' => '<i class="fas fa-xmark"></i> مرفوض',
                            'ordered' => '<i class="fas fa-file-invoice"></i> تم أمر الشراء',
                            default => $req->status
                        };
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($req->request_number); ?></td>
                        <td class="text-muted fs-6"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($req->request_date)); ?></td>
                        <td class="fw-bold text-dark"><i class="fas fa-user-tie text-muted me-1"></i> <?php echo htmlspecialchars($req->requested_by_name); ?></td>
                        <td class="text-muted fs-6">
                            <?php echo $req->approved_by_name ? '<i class="fas fa-user-shield text-success"></i> ' . htmlspecialchars($req->approved_by_name) : '—'; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo URLROOT; ?>/purchaseRequest/show/<?php echo $req->id; ?>" class="btn-icon view" title="التفاصيل"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding: 40px;">لا توجد طلبات شراء داخلية مسجلة بعد.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>