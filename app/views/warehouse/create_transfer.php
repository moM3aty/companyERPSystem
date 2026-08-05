<?php
$flash = $data['flash'] ?? null;
$warehouses = $data['warehouses'] ?? [];
$products = $data['products'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <div class="form-card">
        <form action="<?php echo URL_ROOT; ?>/warehouse/create-transfer" method="POST">
            <div class="form-section">
                <div class="form-grid">
                    <div class="form-group">
                        <label>من مستودع</label>
                        <select name="from_warehouse" class="form-input" required>
                            <option value="">-- اختر --</option>
                            <?php foreach ($warehouses as $wh) : ?>
                                <option value="<?php echo $wh->id; ?>"><?php echo $wh->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>إلى مستودع</label>
                        <select name="to_warehouse" class="form-input" required>
                            <option value="">-- اختر --</option>
                            <?php foreach ($warehouses as $wh) : ?>
                                <option value="<?php echo $wh->id; ?>"><?php echo $wh->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>المنتج</label>
                        <select name="product_id" class="form-input" required>
                            <option value="">-- اختر --</option>
                            <?php foreach ($products as $prod) : ?>
                                <option value="<?php echo $prod->id; ?>"><?php echo $prod->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>الكمية</label>
                        <input type="number" name="quantity" class="form-input" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-input" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-submit"><i class="fas fa-arrows-left-right"></i> تنفيذ النقل</button>
                <a href="<?php echo URL_ROOT; ?>/warehouse/transfers" class="btn-cancel">إلغاء</a>
            </div>
        </form>
    </div>
</div>