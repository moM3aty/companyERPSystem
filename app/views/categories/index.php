<?php
// app/views/categories/index.php
$categories = $data['categories'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-tags text-primary"></i> إدارة تصنيفات المخزون</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">قم بإنشاء تصنيفات لترتيب منتجاتك وتسهيل استخراج التقارير وتقييم المخزون.</p>
    </div>
    <a href="<?php echo URLROOT; ?>/product/index" class="btn btn-secondary">
        العودة للأصناف
    </a>
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

<div class="content-grid" style="grid-template-columns: 1fr 2.5fr; align-items: start;">
    
    <!-- الجانب الأيمن: نموذج إضافة تصنيف -->
    <div class="card mb-0">
        <div class="card-header bg-light">
            <h3 class="card-title"><i class="fas fa-plus-circle text-success"></i> إضافة تصنيف جديد</h3>
        </div>
        <form action="<?php echo URLROOT; ?>/category/create" method="POST">
            <div class="card-body d-flex flex-column gap-3">
                <div class="form-group mb-0">
                    <label class="form-label">اسم التصنيف <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: أجهزة إلكترونية، مواد غذائية...">
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">الوصف (اختياري)</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="وصف قصير لمحتوى هذا التصنيف..."></textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success w-100"><i class="fas fa-save"></i> حفظ التصنيف</button>
            </div>
        </form>
    </div>

    <!-- الجانب الأيسر: جدول التصنيفات الحالية -->
    <div class="card mb-0 h-100">
        <div class="card-header bg-light">
            <h3 class="card-title"><i class="fas fa-list text-info"></i> التصنيفات الحالية في النظام</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0 table-hover">
                    <thead class="bg-white">
                        <tr>
                            <th style="width: 35%;">اسم التصنيف</th>
                            <th style="width: 35%;">الوصف</th>
                            <th class="text-center" style="width: 15%;">المنتجات المرتبطة</th>
                            <?php if(Session::hasRole('admin')): ?><th class="text-center" style="width: 15%;">إجراء</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($categories as $cat): ?>
                        <tr>
                            <td class="fw-bold text-dark"><i class="fas fa-folder text-muted me-2"></i> <?php echo htmlspecialchars($cat->name); ?></td>
                            <td class="text-muted" style="font-size: 13px; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($cat->description ?? ''); ?>">
                                <?php echo htmlspecialchars($cat->description ?: '—'); ?>
                            </td>
                            <td class="text-center">
                                <?php if(($cat->products_count ?? 0) > 0): ?>
                                    <span class="badge badge-primary font-monospace fs-6"><?php echo $cat->products_count; ?> منتج</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary font-monospace fs-6">فارغ</span>
                                <?php endif; ?>
                            </td>
                            <?php if(Session::hasRole('admin')): ?>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <a href="<?php echo URLROOT; ?>/category/edit/<?php echo $cat->id; ?>" class="btn-icon edit" title="تعديل التصنيف"><i class="fas fa-pen"></i></a>
                                    <form action="<?php echo URLROOT; ?>/category/delete/<?php echo $cat->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد حذف هذا التصنيف؟ لا يمكن إتمام العملية إذا كان هناك منتجات تستخدمه.');">
                                        <button type="submit" class="btn-icon delete" title="حذف التصنيف"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($categories)): ?>
                        <tr><td colspan="4" class="text-center text-muted p-5">
                            <i class="fas fa-tags fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد تصنيفات مسجلة حتى الآن.
                        </td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>