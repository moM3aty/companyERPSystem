<?php
// المسار: app/views/purchase_returns/index.php
$returns = $data['returns'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-truck-ramp-box text-danger"></i> مرتجعات المشتريات</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">توثيق البضائع المعادة للموردين والخصم من مستحقاتهم.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/purchaseReturn/create" class="btn btn-danger">
        <i class="fas fa-plus"></i> تسجيل مرتجع جديد
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>رقم المرتجع</th>
                        <th>المورد</th>
                        <th>رقم الـ PO</th>
                        <th>التاريخ</th>
                        <th>القيمة المستردة</th>
                        <th>بواسطة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($returns)): foreach($returns as $ret): ?>
                    <tr>
                        <td class="font-monospace fw-bold text-danger"><?php echo htmlspecialchars($ret->return_number); ?></td>
                        <td class="fw-bold"><i class="fas fa-truck-field text-muted"></i> <?php echo htmlspecialchars($ret->supplier_name); ?></td>
                        <td class="text-muted font-monospace fs-6"><?php echo $ret->po_number ? htmlspecialchars($ret->po_number) : '—'; ?></td>
                        <td class="text-muted font-monospace fs-6"><?php echo date('Y-m-d', strtotime($ret->created_at)); ?></td>
                        <td class="font-monospace fw-bold text-danger" style="direction: ltr;">-<?php echo number_format($ret->total_refund, 2); ?></td>
                        <td class="text-muted fs-6"><i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($ret->created_by_name ?? 'النظام'); ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted" style="padding: 40px;">لا توجد مرتجعات مسجلة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>