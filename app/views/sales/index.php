<?php
// app/views/sales/index.php
$invoices = $data['invoices'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-invoice-dollar text-success"></i> فواتير المبيعات</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة المبيعات ونقاط البيع والفواتير المصدرة.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/sale/create" class="btn btn-success">
        <i class="fas fa-plus"></i> إصدار فاتورة جديدة
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>العميل</th>
                        <th>المبلغ الإجمالي</th>
                        <th>تاريخ الإصدار</th>
                        <th>بواسطة (المندوب)</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($invoices)): foreach($invoices as $inv): ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($inv->invoice_number); ?></td>
                        <td class="fw-bold"><i class="fas fa-user-circle text-muted"></i> <?php echo htmlspecialchars($inv->customer_name ?? 'عميل نقدي'); ?></td>
                        <td class="font-monospace fw-bold text-success" style="direction: ltr;"><?php echo number_format($inv->total_amount, 2); ?></td>
                        <td class="text-muted font-monospace" style="font-size: 13px;"><?php echo date('Y-m-d H:i', strtotime($inv->created_at)); ?></td>
                        <td class="text-muted fs-6"><i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($inv->sales_rep_name ?? 'النظام'); ?></td>
                        <td class="text-center">
                            <a href="<?php echo URLROOT; ?>/sale/show/<?php echo $inv->id; ?>" class="btn-icon view" title="عرض الفاتورة"><i class="fas fa-eye"></i></a>
                            <!-- لا يوجد زر تعديل للفواتير المعتمدة لضمان النزاهة المالية -->
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted" style="padding: 40px;">لا توجد فواتير مبيعات مسجلة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>