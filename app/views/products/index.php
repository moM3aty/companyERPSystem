<?php
// المسار: app/views/products/index.php
$products = $data['products'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-boxes-stacked text-primary"></i> دليل المخزون والأصناف</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">إدارة جميع المنتجات المسجلة في المستودع ومراقبة الأرصدة.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/product/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> إضافة صنف جديد
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>الرمز (SKU)</th>
                        <th>المنتج</th>
                        <th>التصنيف</th>
                        <th>سعر البيع</th>
                        <th class="text-center">الرصيد الفعلي</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($products)): foreach($products as $p): 
                        // تنبيه إذا كان المخزون قليل
                        $qtyClass = $p->quantity <= $p->reorder_point ? 'text-danger fw-bold' : 'text-success fw-bold';
                    ?>
                    <tr>
                        <td class="font-monospace text-muted"><?php echo htmlspecialchars($p->sku); ?></td>
                        <td class="fw-bold text-dark">
                            <?php echo htmlspecialchars($p->name); ?>
                            <?php if($p->quantity <= $p->reorder_point): ?>
                                <i class="fas fa-exclamation-triangle text-danger" title="المخزون تحت حد إعادة الطلب"></i>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge badge-secondary"><i class="fas fa-folder"></i> <?php echo htmlspecialchars($p->cat_name ?? 'غير مصنف'); ?></span></td>
                        <td class="font-monospace"><?php echo number_format($p->price, 2); ?> ر.س</td>
                        <td class="text-center font-monospace <?php echo $qtyClass; ?>"><?php echo $p->quantity; ?></td>
                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <?php if($p->track_batches): ?>
                                    <a href="<?php echo URLROOT; ?>/productBatch/index/<?php echo $p->id; ?>" class="btn-icon view" title="إدارة السيريال والتشغيلة"><i class="fas fa-barcode"></i></a>
                                <?php endif; ?>
                                <a href="<?php echo URLROOT; ?>/product/edit/<?php echo $p->id; ?>" class="btn-icon edit" title="تعديل"><i class="fas fa-pen"></i></a>
                                <form action="<?php echo URLROOT; ?>/product/delete/<?php echo $p->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد الحذف؟');">
                                <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" class="text-center text-muted" style="padding: 40px;">لا يوجد أصناف في المخزون.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>