<?php
// المسار: app/views/stocktake/index.php
$adjustments = $data['adjustments'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-clipboard-check text-primary"></i> سجل تسويات المخزون</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع حركات الجرد، الفوائض، والتوالف</p>
    </div>
    <a href="<?php echo URLROOT; ?>/stocktake/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> تسجيل حركة تسوية
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>الرقم المرجعي</th>
                        <th>التاريخ</th>
                        <th>الصنف</th>
                        <th class="text-center">نوع الحركة</th>
                        <th class="text-center">الكمية</th>
                        <th>المسؤول</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($adjustments)): foreach($adjustments as $adj): 
                        $typeBadge = match($adj->type) {
                            'addition' => 'badge-success', 'subtraction' => 'badge-warning', 'damage' => 'badge-danger', 'loss' => 'badge-danger', default => 'badge-secondary'
                        };
                        $typeLabel = match($adj->type) {
                            'addition' => '<i class="fas fa-arrow-trend-up"></i> إضافة / فائض', 'subtraction' => '<i class="fas fa-arrow-trend-down"></i> خصم / عجز', 'damage' => '<i class="fas fa-house-crack"></i> تالف', 'loss' => '<i class="fas fa-circle-minus"></i> مفقود', default => $adj->type
                        };
                        $qtySign = $adj->type === 'addition' ? '+' : '-';
                        $qtyColor = $adj->type === 'addition' ? 'text-success' : 'text-danger';
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($adj->reference_no); ?></td>
                        <td class="text-muted fs-6"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($adj->date)); ?></td>
                        <td>
                            <div class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($adj->product_name); ?></div>
                            <div class="text-muted" style="font-size: 11px;"><i class="fas fa-barcode"></i> SKU: <?php echo htmlspecialchars($adj->sku); ?></div>
                        </td>
                        <td class="text-center"><span class="badge <?php echo $typeBadge; ?>"><?php echo $typeLabel; ?></span></td>
                        <td class="text-center font-monospace fs-5 fw-bold <?php echo $qtyColor; ?>" style="direction:ltr;">
                            <?php echo $qtySign . $adj->quantity; ?>
                        </td>
                        <td class="text-muted fs-6"><i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($adj->created_by_name ?? 'النظام'); ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted p-5">لا توجد تسويات مخزنية مسجلة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>