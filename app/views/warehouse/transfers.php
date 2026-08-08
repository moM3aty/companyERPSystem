<?php
// app/views/warehouse/transfers.php
$transfers = $data['transfers'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-truck-ramp-box text-info"></i> سجل حركة نقل المخزون</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع تحويلات ونقل المنتجات بين فروع ومستودعات الشركة.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/warehouse/createTransfer" class="btn btn-info text-white">
        <i class="fas fa-plus"></i> إنشاء أمر نقل جديد
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>المرجع</th>
                        <th>المنتج المُنقول</th>
                        <th>مسار النقل (من ⟵ إلى)</th>
                        <th class="text-center">الكمية</th>
                        <th class="text-center">الحالة</th>
                        <th>التاريخ والمسؤول</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transfers as $tr) : 
                        $statusClass = match($tr->status) {
                            'completed' => 'badge-success',
                            'pending', 'approved' => 'badge-warning',
                            'cancelled' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($tr->status) {
                            'completed' => '<i class="fas fa-check-double"></i> مكتمل',
                            'pending' => '<i class="fas fa-clock"></i> قيد الانتظار',
                            'approved' => '<i class="fas fa-check"></i> معتمد',
                            'cancelled' => '<i class="fas fa-times"></i> ملغي',
                            default => $tr->status
                        };
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($tr->transfer_number); ?></td>
                        <td>
                            <div class="fw-bold text-dark"><i class="fas fa-box text-muted"></i> <?php echo htmlspecialchars($tr->product_name ?? '—'); ?></div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-secondary" title="المصدر"><?php echo htmlspecialchars($tr->from_warehouse_name ?? '—'); ?></span>
                                <i class="fas fa-arrow-left text-muted" style="font-size:10px;"></i>
                                <span class="badge badge-info text-dark" title="الوجهة"><?php echo htmlspecialchars($tr->to_warehouse_name ?? '—'); ?></span>
                            </div>
                        </td>
                        <td class="text-center font-monospace fs-5 fw-bold text-primary"><?php echo $tr->quantity; ?></td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo $statusLabel; ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-size:12px; color:var(--text-muted);"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($tr->created_at)); ?></div>
                            <div style="font-size:11px; color:var(--text-dark); margin-top:4px; font-weight:600;"><i class="fas fa-user-gear text-primary"></i> <?php echo htmlspecialchars($tr->requested_by_name ?? 'النظام'); ?></div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($transfers)) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-5">
                            <i class="fas fa-arrows-left-right fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد عمليات نقل مخزون مسجلة.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>