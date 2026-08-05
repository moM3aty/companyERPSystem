<?php
// المسار: app/views/stocktake/create.php
$products = $data['products'] ?? [];
?>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden; max-width:800px; margin:0 auto;">
    
    <div style="padding:24px 30px; border-bottom:1px solid var(--border); background:#f8fafc;">
        <h3 style="margin:0; font-size:16px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-balance-scale" style="color:var(--primary);"></i> توثيق تسوية جرد جديدة
        </h3>
        <p style="margin:4px 0 0; font-size:12px; color:var(--text-muted);">سيتم التأثير على كميات المخزون مباشرة بمجرد الحفظ ولا يمكن التراجع.</p>
    </div>

    <form action="<?php echo URL_ROOT; ?>/stocktake/create" method="POST" id="adjForm">
        <div style="padding:30px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            
            <div style="display:flex; flex-direction:column; gap:8px; grid-column:1/-1;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">المنتج / الصنف <span style="color:var(--danger);">*</span></label>
                <select name="product_id" id="prodSelect" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; cursor:pointer;">
                    <option value="">-- ابحث واختر الصنف --</option>
                    <?php foreach($products as $p): ?>
                        <option value="<?php echo $p->id; ?>" data-qty="<?php echo $p->quantity; ?>">
                            <?php echo htmlspecialchars($p->name); ?> (SKU: <?php echo htmlspecialchars($p->sku); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div id="qtyHint" style="font-size:12px; color:var(--text-muted); display:none; margin-top:4px; padding:8px; background:var(--page-bg); border-radius:6px; border:1px dashed var(--border);">
                    الرصيد الفعلي الحالي في النظام: <strong id="currQty" style="font-family:monospace; color:var(--primary-dark); font-size:14px;">0</strong>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">تاريخ الحركة <span style="color:var(--danger);">*</span></label>
                <input type="date" name="date" required value="<?php echo date('Y-m-d'); ?>" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">نوع التسوية <span style="color:var(--danger);">*</span></label>
                <select name="type" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; cursor:pointer;">
                    <option value="addition">إضافة (فائض غير مسجل)</option>
                    <option value="subtraction">خصم (عجز جرد)</option>
                    <option value="damage">تالف (غير صالح للبيع)</option>
                    <option value="loss">مفقود (مسروق/ضائع)</option>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">الكمية <span style="color:var(--danger);">*</span></label>
                <input type="number" name="quantity" min="1" required placeholder="أدخل الكمية..." style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:monospace; font-size:15px; outline:none; direction:ltr; text-align:right;">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px; grid-column:1/-1;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">السبب / الملاحظات</label>
                <textarea name="notes" rows="3" placeholder="اكتب سبب التسوية بالتفصيل (مثل: عجز أثناء الجرد السنوي...)" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;"></textarea>
            </div>

        </div>
        
        <div style="padding:20px 30px; background:#f8fafc; border-top:1px solid var(--border); display:flex; gap:12px;">
            <button type="submit" id="submitBtn" style="padding:10px 24px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:8px;"><i class="fas fa-save"></i> تنفيذ التسوية</button>
            <a href="<?php echo URL_ROOT; ?>/stocktake/index" style="padding:10px 24px; background:transparent; border:1px solid var(--border); color:var(--text-body); border-radius:8px; text-decoration:none; font-weight:600;">إلغاء</a>
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

    document.getElementById('adjForm').addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري التنفيذ...';
        btn.style.pointerEvents = 'none';
        btn.style.opacity = '0.8';
    });
</script>