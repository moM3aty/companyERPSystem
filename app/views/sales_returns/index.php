<?php
// المسار: app/views/sales_returns/index.php
$returns = $data['returns'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-arrow-rotate-left text-danger"></i> سجل البضائع المرتجعة</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">توثيق مرتجعات المبيعات وإعادة إدراجها في المخزون المتاح</p>
    </div>
    <a href="<?php echo URLROOT; ?>/saleReturn/create" class="btn btn-danger">
        <i class="fas fa-plus"></i> تسجيل مرتجع جديد
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>رقم المرتجع</th>
                        <th>الفاتورة الأصلية</th>
                        <th>العميل</th>
                        <th class="text-left">المبلغ المردود</th>
                        <th>السبب</th>
                        <th>تاريخ الإرجاع</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($returns)): foreach($returns as $ret): ?>
                    <tr>
                        <td class="font-monospace fw-bold text-danger"><?php echo htmlspecialchars($ret->return_number); ?></td>
                        <td class="font-monospace fw-bold text-primary"><?php echo htmlspecialchars($ret->invoice_number); ?></td>
                        <td class="fw-bold text-dark"><i class="fas fa-user-circle text-muted"></i> <?php echo htmlspecialchars($ret->customer_name); ?></td>
                        <td class="font-monospace fw-bold text-danger" style="direction:ltr; text-align:right;">-<?php echo number_format($ret->total_refund, 2); ?></td>
                        <td class="text-muted fs-6"><?php echo htmlspecialchars($ret->reason ?? '—'); ?></td>
                        <td class="text-muted fs-6"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($ret->created_at)); ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding: 40px;">لا توجد مرتجعات مبيعات مسجلة حتى الآن.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>