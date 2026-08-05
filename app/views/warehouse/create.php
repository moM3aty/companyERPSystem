<?php
$flash = $data['flash'] ?? null;
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="form-card">
        <form action="<?php echo URL_ROOT; ?>/warehouse/create" method="POST">
            <div class="form-section">
                <div class="form-grid">
                    <div class="form-group">
                        <label>كود المستودع</label>
                        <input type="text" name="code" class="form-input" placeholder="مثل: WH-001" required>
                    </div>
                    <div class="form-group">
                        <label>اسم المستودع</label>
                        <input type="text" name="name" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>العنوان</label>
                        <textarea name="address" class="form-input" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="is_main" value="1"> مستودع رئيسي
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> حفظ</button>
                <a href="<?php echo URL_ROOT; ?>/warehouse/index" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>