<?php
// app/views/productBatch/index.php
$product = $data['product'] ?? null;
$batches = $data['batches'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark">
            <i class="fas fa-barcode text-primary"></i> 
            إدارة التشغيلات والسيريالات: <?php echo htmlspecialchars($product->name ?? 'جميع المنتجات'); ?>
        </h3>
        <p class="text-muted mt-1" style="font-size: 13px;">تتبع التواريخ وأرقام التشغيلات (Lots) والسيريالات للمخزون.</p>
    </div>
    <div class="d-flex gap-2">
        <?php if($product): ?>
        <a href="<?php echo URLROOT; ?>/productBatch/create/<?php echo $product->id; ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة تشغيلة / سيريال
        </a>
        <?php endif; ?>
        <a href="<?php echo URLROOT; ?>/product/index" class="btn btn-secondary">العودة للأصناف</a>
    </div>
</div>

<?php 
    $flash = Session::getFlash();
    if ($flash): 
?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>">
        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <?php if(!$product): ?><th>المنتج</th><?php endif; ?>
                        <th>رقم التشغيلة (Lot/Batch)</th>
                        <th>رقم السيريال (Serial)</th>
                        <th class="text-center">تاريخ الإنتاج / الانتهاء</th>
                        <th class="text-center">الكمية</th>
                        <th class="text-center">الحالة</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($batches as $b) : 
                        $statusClass = match($b->status ?? 'active') {
                            'active' => 'badge-success',
                            'expired' => 'badge-danger',
                            'damaged' => 'badge-warning',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($b->status ?? 'active') {
                            'active' => 'نشط / متاح',
                            'expired' => 'منتهي الصلاحية',
                            'damaged' => 'تالف',
                            default => $b->status
                        };
                    ?>
                    <tr>
                        <?php if(!$product): ?>
                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($b->product_name ?? '—'); ?></td>
                        <?php endif; ?>
                        
                        <td class="font-monospace text-muted">
                            <?php echo htmlspecialchars($b->lot_number ?? $b->batch_number ?? '—'); ?>
                        </td>
                        
                        <td>
                            <?php if(!empty($b->serial_number)): ?>
                                <span class="badge badge-secondary font-monospace" style="font-size: 13px;"><i class="fas fa-barcode"></i> <?php echo htmlspecialchars($b->serial_number); ?></span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="text-center">
                            <?php if(!empty($b->production_date) || !empty($b->expiry_date)): ?>
                                <div style="font-size: 12px; color: var(--text-dark);">
                                    <span class="text-muted">إنتاج:</span> <?php echo $b->production_date ?? '—'; ?>
                                </div>
                                <div style="font-size: 12px; color: var(--danger);" class="mt-1">
                                    <span class="text-muted">انتهاء:</span> <strong><?php echo $b->expiry_date ?? '—'; ?></strong>
                                </div>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        
                        <td class="text-center font-monospace fs-5 fw-bold text-primary"><?php echo $b->quantity; ?></td>
                        
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                        </td>
                        
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="<?php echo URLROOT; ?>/productBatch/edit/<?php echo $b->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/productBatch/delete/<?php echo $b->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('هل أنت متأكد من الحذف النهائي لهذه التشغيلة؟');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($batches)) : ?>
                    <tr>
                        <td colspan="<?php echo $product ? '6' : '7'; ?>" class="text-center text-muted p-5">
                            <i class="fas fa-barcode fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد تشغيلات أو سيريالات مسجلة.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>