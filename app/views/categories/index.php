<?php
// المسار: app/views/categories/index.php
$categories = $categories ?? ($data['categories'] ?? []);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-tags text-primary"></i> إدارة تصنيفات المخزون</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">ترتيب المنتجات في مجموعات لتسهيل الجرد والبيع.</p>
    </div>
</div>

<div class="form-grid" style="grid-template-columns: 2fr 1fr; align-items: start;">
    
    <!-- القائمة (الجدول) -->
    <div class="card mb-0">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list"></i> التصنيفات المسجلة</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>التصنيف</th>
                            <th>الوصف</th>
                            <th class="text-center">عدد المنتجات</th>
                            <th class="text-center">حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($categories)): foreach($categories as $cat): ?>
                        <tr>
                            <td class="fw-bold text-dark"><i class="fas fa-folder text-warning me-2"></i> <?php echo htmlspecialchars($cat->name); ?></td>
                            <td class="text-muted fs-6"><?php echo htmlspecialchars($cat->description ?? '—'); ?></td>
                            <td class="text-center">
                                <span class="badge <?php echo $cat->products_count > 0 ? 'badge-info' : 'badge-secondary'; ?>">
                                    <?php echo $cat->products_count; ?> منتج
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if(Session::hasRole('admin')): ?>
                                <form action="<?php echo URLROOT; ?>/category/delete/<?php echo $cat->id; ?>" method="POST" style="display:inline;" onsubmit="return confirm('تأكيد الحذف؟ لن يتم الحذف إذا كان مرتبطاً بمنتجات.');">
                                    <button type="submit" class="btn-icon delete" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="4" class="text-center text-muted" style="padding: 40px;">لا توجد تصنيفات مسجلة.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- نموذج الإضافة السريع -->
    <div class="card mb-0 bg-light" style="border: 2px dashed var(--border-color);">
        <div class="card-header bg-transparent border-bottom-0 pb-0">
            <h3 class="card-title text-primary"><i class="fas fa-plus-circle"></i> تصنيف جديد</h3>
        </div>
        <form action="<?php echo URLROOT; ?>/category/create" method="POST">
            <div class="card-body form-group gap-3">
                <div class="form-group">
                    <label class="form-label">اسم التصنيف <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="مثال: إلكترونيات، مواد غذائية..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">الوصف (اختياري)</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-2"><i class="fas fa-save"></i> حفظ وإضافة</button>
            </div>
        </form>
    </div>

</div>