<?php
// app/views/warehouse/edit.php
$warehouse = $data['warehouse'] ?? null;
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-pen text-primary"></i> تعديل بيانات المستودع: <?php echo htmlspecialchars($warehouse->name ?? ''); ?></h3>
    </div>

    <?php 
        $flash = $data['flash'] ?? Session::getFlash();
        if ($flash): 
    ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>" style="margin: 20px 20px 0 20px;">
            <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo URLROOT; ?>/warehouse/edit/<?php echo $warehouse->id; ?>" method="POST" id="whEditForm">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">اسم المستودع أو الفرع <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($warehouse->name ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">كود المستودع <span class="required">*</span></label>
                    <input type="text" name="code" class="form-control font-monospace" value="<?php echo htmlspecialchars($warehouse->code ?? ''); ?>" required style="direction:ltr; text-align:right;">
                </div>
                
                <div class="form-group full-width">
                    <label class="form-label">العنوان والموقع الجغرافي</label>
                    <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars($warehouse->address ?? ''); ?></textarea>
                </div>

                <div class="form-group full-width mt-3">
                    <label class="d-flex align-items-center gap-2 p-3 border" style="background:#f8fafc; border-radius:var(--radius-sm); cursor:pointer;">
                        <input type="checkbox" name="is_main" value="1" <?php echo ($warehouse->is_main == 1) ? 'checked' : ''; ?> style="width: 20px; height: 20px; accent-color: var(--primary);">
                        <div>
                            <span class="fw-bold text-dark d-block">تعيين كمستودع رئيسي <i class="fas fa-star text-warning"></i></span>
                            <small class="text-muted">يمكنك تعيين أكثر من مستودع رئيسي (مثال: مستودع رئيسي لكل منطقة إدارية).</small>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-warning" id="btnSubmit"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/warehouse/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('whEditForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري الحفظ...';
        btn.style.pointerEvents = 'none';
    });
</script>