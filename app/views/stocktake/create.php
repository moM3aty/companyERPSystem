<?php
// المسار: app/views/stocktake/create.php
$products = $data['products'] ?? [];
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-balance-scale text-primary"></i> توثيق تسوية جرد جديدة</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/stocktake/create" method="POST" id="adjForm">
        <div class="card-body">
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle"></i> سيتم التأثير على كميات المخزون مباشرة بمجرد الحفظ ولا يمكن التراجع.
            </div>

            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">المنتج / الصنف <span class="required">*</span></label>
                    <select name="product_id" id="prodSelect" class="form-control" required>
                        <option value="">-- ابحث واختر الصنف --</option>
                        <?php foreach($products as $p): ?>
                            <option value="<?php echo $p->id; ?>" data-qty="<?php echo $p->quantity; ?>">
                                <?php echo htmlspecialchars($p->name); ?> (SKU: <?php echo htmlspecialchars($p->sku); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="qtyHint" class="mt-2 text-muted" style="display:none; font-size:12px; background:var(--page-bg); padding:8px; border-radius:var(--radius-sm); border:1px dashed var(--border-color);">
                        الرصيد الفعلي الحالي في النظام: <strong id="currQty" class="font-monospace text-primary fs-6">0</strong>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ الحركة <span class="required">*</span></label>
                    <input type="date" name="date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">نوع التسوية <span class="required">*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="addition">إضافة (فائض غير مسجل)</option>
                        <option value="subtraction">خصم (عجز جرد)</option>
                        <option value="damage">تالف (غير صالح للبيع)</option>
                        <option value="loss">مفقود (مسروق/ضائع)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">الكمية <span class="required">*</span></label>
                    <input type="number" name="quantity" min="1" class="form-control font-monospace text-right ltr fw-bold" required placeholder="أدخل الكمية...">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">السبب / الملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="اكتب سبب التسوية بالتفصيل (مثل: عجز أثناء الجرد السنوي...)"></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i> تنفيذ التسوية</button>
            <a href="<?php echo URLROOT; ?>/stocktake/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    const prodSelect = document.getElementById('prodSelect');
    const qtyHint = document.getElementById('qtyHint');
    const currQty = document.getElementById('currQty');

    prodSelect.addEventListener('change', function() {
        const selectedOpt = this.options[this.selectedIndex];
        if(selectedOpt.value) {
            currQty.textContent = selectedOpt.dataset.qty;
            qtyHint.style.display = 'block';
        } else {
            qtyHint.style.display = 'none';
        }
    });

    document.getElementById('adjForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري التنفيذ...';
        btn.style.pointerEvents = 'none';
    });
</script>