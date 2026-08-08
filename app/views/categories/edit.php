<?php
// app/views/categories/edit.php
$category = $data['category'] ?? null;
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title"><i class="fas fa-pen text-accent"></i> تعديل بيانات التصنيف</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/category/edit/<?php echo $category->id; ?>" method="POST">
        <div class="card-body form-group gap-3">
            <div class="form-group full-width">
                <label class="form-label">اسم التصنيف <span class="required">*</span></label>
                <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($category->name); ?>">
            </div>
            <div class="form-group full-width">
                <label class="form-label">الوصف (اختياري)</label>
                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($category->description ?? ''); ?></textarea>
            </div>
        </div>
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/category/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>