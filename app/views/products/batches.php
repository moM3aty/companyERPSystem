<?php
// المسار: app/views/products/batches.php
$product = $product ?? ($data['product'] ?? null);
$batches = $batches ?? ($data['batches'] ?? []);
$productsList = $data['products'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-barcode text-primary"></i> تتبع السيريال والتشغيلة (Lot)</h3>
        <?php if($product): ?>
            <p class="text-muted mt-1 font-monospace fs-6">الصنف: <?php echo htmlspecialchars($product->name); ?> (SKU: <?php echo $product->sku; ?>)</p>
        <?php else: ?>
            <p class="text-muted mt-1 fs-6">سجل شامل لجميع التشغيلات، السيريالات، والتواريخ المتوفرة في المخازن.</p>
        <?php endif; ?>
    </div>
</div>

<div class="form-grid" style="grid-template-columns: 1fr 2fr; align-items: start;">
    
    <!-- نموذج تسجيل سيريال -->
    <div class="card mb-0">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus-circle text-success"></i> تسجيل دفعة جديدة</h3>
        </div>
        <form action="<?php echo URLROOT; ?>/productBatch/create/<?php echo $product ? $product->id : ''; ?>" method="POST">
            <div class="card-body form-group gap-3">
                <?php if(!$product): ?>
                <div class="form-group border-bottom pb-3 mb-3">
                    <label class="form-label">المنتج المرتبط <span class="required">*</span></label>
                    <select name="product_id" class="form-control" required>
                        <option value="">-- يرجى اختيار المنتج --</option>
                        <?php foreach($productsList as $p): ?>
                            <option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label class="form-label">رقم التشغيلة (Lot Number)</label>
                    <input type="text" name="lot_number" class="form-control font-monospace" placeholder="مثال: LOT-2024-X1">
                </div>
                <div class="form-group">
                    <label class="form-label">رقم السيريال (القطعة) - إن وجد</label>
                    <input type="text" name="serial_number" class="form-control font-monospace" placeholder="Serial Number">
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الإنتاج</label>
                    <input type="date" name="production_date" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الصلاحية (الانتهاء) <span class="required">*</span></label>
                    <input type="date" name="expiry_date" class="form-control" required>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">الكمية المدخلة</label>
                        <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control">
                        <option value="available">متاح للبيع</option>
                        <option value="expired">منتهي الصلاحية</option>
                        <option value="damaged">تالف</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-success w-100"><i class="fas fa-save"></i> تسجيل في المخزن</button>
        </div>
    </form>
</div>

<!-- جدول السيريالات -->
<div class="card mb-0 h-100">
    <div class="card-header d-flex justify-content-between">
        <h3 class="card-title"><i class="fas fa-list text-info"></i> سجل التشغيلات والكميات</h3>
        <a href="<?php echo URLROOT; ?>/product/index" class="btn btn-sm btn-secondary">دليل المخزون</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <?php if(!$product): ?><th>الصنف</th><?php endif; ?>
                        <th>التشغيلة / السيريال</th>
                        <th>الإنتاج</th>
                        <th>الانتهاء</th>
                        <th class="text-center">الكمية</th>
                        <th class="text-center">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($batches as $b): 
                        $statusClass = match($b->status) {
                            'available' => 'badge-success',
                            'sold' => 'badge-secondary',
                            'expired' => 'badge-danger',
                            'damaged' => 'badge-warning',
                            default => 'badge-secondary'
                        };
                        $statusLabel = match($b->status) {
                            'available' => 'متاح', 'sold' => 'مباع', 'expired' => 'منتهي', 'damaged' => 'تالف', default => $b->status
                        };
                    ?>
                    <tr>
                        <?php if(!$product): ?>
                            <td class="fw-bold text-dark">
                                <?php echo htmlspecialchars($b->product_name); ?>
                                <div class="text-muted font-monospace" style="font-size: 11px;">SKU: <?php echo htmlspecialchars($b->sku ?? '—'); ?></div>
                            </td>
                        <?php endif; ?>
                        <td>
                            <?php if($b->lot_number): ?><div class="font-monospace fw-bold text-dark">Lot: <?php echo htmlspecialchars($b->lot_number); ?></div><?php endif; ?>
                            <?php if($b->serial_number): ?><div class="font-monospace text-muted" style="font-size:11px;">SN: <?php echo htmlspecialchars($b->serial_number); ?></div><?php endif; ?>
                        </td>
                        <td class="text-muted fs-6"><?php echo $b->production_date ? date('Y-m-d', strtotime($b->production_date)) : '—'; ?></td>
                            <td class="font-monospace fw-bold text-danger fs-6"><?php echo $b->expiry_date ? date('Y-m-d', strtotime($b->expiry_date)) : '—'; ?></td>
                            <td class="text-center font-monospace fw-bold fs-5 text-primary">
                                <?php echo $b->quantity; ?>
                            </td>
                            <td class="text-center"><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($batches)): ?>
                        <tr><td colspan="5" class="text-center text-muted p-5">لا توجد تشغيلات مسجلة لهذا الصنف.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>