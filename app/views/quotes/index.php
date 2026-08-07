<?php
// المسار: app/views/quotes/index.php
$quotes = $quotes ?? ($data['quotes'] ?? []);
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-file-signature text-primary"></i> سجل عروض الأسعار</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة وتتبع عروض الأسعار المقدمة للعملاء.</p>
    </div>
    <div>
        <a href="<?php echo URLROOT; ?>/quote/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> إنشاء عرض سعر
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>رقم العرض</th>
                        <th>العميل المستهدف</th>
                        <th class="text-left">الإجمالي</th>
                        <th>التاريخ</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quotes as $qte) : 
                        $statusClass = match($qte->status) {
                            'draft' => 'badge-secondary',
                            'sent' => 'badge-info',
                            'accepted' => 'badge-success',
                            'rejected' => 'badge-danger',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($qte->status) {
                            'draft' => 'مسودة',
                            'sent' => 'مُرسل للعميل',
                            'accepted' => 'مقبول',
                            'rejected' => 'مرفوض',
                            default => $qte->status
                        };
                    ?>
                    <tr class="search-row">
                        <td><span class="font-monospace fw-bold text-primary"><?php echo htmlspecialchars($qte->quote_number); ?></span></td>
                        <td>
                            <div class="fw-bold text-dark">
                                <i class="fas fa-building text-muted me-1"></i>
                                <?php echo htmlspecialchars($qte->customer_name ?? 'غير محدد'); ?>
                            </div>
                        </td>
                        <td style="text-align:left;">
                            <span class="font-monospace fw-bold text-dark"><?php echo number_format($qte->total_amount, 2); ?> ر.س</span>
                        </td>
                        <td class="text-muted fs-6">
                            <i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($qte->created_at)); ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo $statusLabel; ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="<?php echo URLROOT; ?>/quote/show/<?php echo $qte->id; ?>" class="btn-icon view" title="عرض وطباعة"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($quotes)) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-5">
                            <i class="fas fa-file-signature fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد عروض أسعار مسجلة.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>