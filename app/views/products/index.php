<?php
// app/views/products/index.php
$products = $data['products'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-boxes-stacked text-primary"></i> دليل المخزون والأصناف</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة جميع المنتجات، التكاليف، والأرصدة.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/category/index" class="btn btn-secondary">
            <i class="fas fa-tags"></i> إدارة التصنيفات
        </a>
        <a href="<?php echo URLROOT; ?>/product/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة صنف
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>الرمز (SKU)</th>
                        <th>المنتج / الصنف</th>
                        <th>التصنيف والوحدة</th>
                        <th class="text-left">سعر البيع</th>
                        <th class="text-left">تكلفة الشراء</th>
                        <th class="text-center">الرصيد الفعلي</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($products)): foreach($products as $p): 
                        // 🟢 معالجة القيم الافتراضية في حال كانت الأعمدة مفقودة من قاعدة البيانات القديمة 🟢
                        $cost = $p->cost ?? 0;
                        $quantity = $p->quantity ?? 0;
                        $reorder_point = $p->reorder_point ?? 0;
                        $unit = $p->unit ?? 'قطعة';
                        $barcode = $p->barcode ?? '';
                        $track_batches = $p->track_batches ?? 0;
                        
                        // حساب حالة المخزون بناءً على المتغيرات المؤمنة
                        $isLowStock = $quantity <= $reorder_point;
                        $qtyClass = $quantity <= 0 ? 'text-danger' : ($isLowStock ? 'text-warning' : 'text-success');
                    ?>
                    <tr>
                        <td class="font-monospace text-muted fw-bold"><?php echo htmlspecialchars($p->sku ?? ''); ?></td>
                        <td>
                            <div class="fw-bold text-dark">
                                <?php echo htmlspecialchars($p->name ?? ''); ?>
                                <?php if($isLowStock): ?>
                                    <i class="fas fa-exclamation-triangle text-danger ms-1" title="تنبيه: مخزون منخفض"></i>
                                <?php endif; ?>
                            </div>
                            <?php if(!empty($barcode)): ?>
                                <div class="text-muted font-monospace mt-1" style="font-size:11px;"><i class="fas fa-barcode"></i> <?php echo htmlspecialchars($barcode); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-secondary mb-1"><i class="fas fa-folder"></i> <?php echo htmlspecialchars($p->category_name ?? 'غير مصنف'); ?></span>
                            <div class="text-muted" style="font-size:11px;">الوحدة: <?php echo htmlspecialchars($unit); ?></div>
                        </td>
                        <td class="font-monospace fw-bold text-success text-left" style="direction:ltr;"><?php echo number_format($p->price ?? 0, 2); ?></td>
                        <td class="font-monospace fw-bold text-danger text-left" style="direction:ltr;"><?php echo number_format($cost, 2); ?></td>
                        <td class="text-center">
                            <span class="font-monospace fw-bold fs-5 <?php echo $qtyClass; ?>"><?php echo $quantity; ?></span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <?php if($track_batches): ?>
                                    <a href="<?php echo URLROOT; ?>/productBatch/index/<?php echo $p->id; ?>" class="btn-icon view" title="إدارة السيريال والتشغيلة"><i class="fas fa-barcode"></i></a>
                                <?php endif; ?>
                                <a href="<?php echo URLROOT; ?>/product/edit/<?php echo $p->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/product/delete/<?php echo $p->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد الحذف النهائي؟ لا يمكن التراجع.');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="7" class="text-center text-muted" style="padding: 60px;"><i class="fas fa-box-open fa-3x mb-3 opacity-50 d-block"></i> لا يوجد أصناف في المخزون.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>