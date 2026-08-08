<?php
// app/views/warehouse/create.php
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-warehouse text-primary"></i> إضافة مستودع جديد</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/warehouse/create" method="POST" id="whForm">
        <div class="card-body">
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle"></i> قم بإنشاء مستودع لربطه بطلبات الشراء وعمليات نقل المخزون بين الفروع.
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">اسم المستودع أو الفرع <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="مثال: مستودع الرياض الفرعي" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">كود المستودع (معرف فريد) <span class="required">*</span></label>
                    <input type="text" name="code" class="form-control font-monospace" placeholder="مثال: WH-RYD-01" required style="direction:ltr; text-align:right;">
                </div>
                
                <div class="form-group full-width">
                    <label class="form-label">العنوان والموقع الجغرافي</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="المدينة، الحي، الشارع... يستخدم كعنوان في طلبات الشحن"></textarea>
                </div>

                <div class="form-group full-width mt-3">
                    <label class="d-flex align-items-center gap-2 p-3 border" style="background:#f8fafc; border-radius:var(--radius-sm); cursor:pointer;">
                        <input type="checkbox" name="is_main" value="1" style="width: 20px; height: 20px; accent-color: var(--primary);">
                        <div>
                            <span class="fw-bold text-dark d-block">تعيين كمستودع رئيسي <i class="fas fa-star text-warning"></i></span>
                            <small class="text-muted">يمكنك تعيين أكثر من مستودع رئيسي (مثال: مستودع رئيسي لكل منطقة إدارية).</small>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-primary" id="btnSubmit"><i class="fas fa-save"></i> حفظ المستودع</button>
            <a href="<?php echo URLROOT; ?>/warehouse/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('whForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري الحفظ...';
        btn.style.pointerEvents = 'none';
    });
</script>