<?php
// app/views/warehouse/create_transfer.php
$warehouses = $data['warehouses'] ?? [];
$products = $data['products'] ?? [];
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-info text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-truck-ramp-box"></i> إنشاء أمر نقل مخزون بين الفروع</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/warehouse/createTransfer" method="POST" id="transferForm">
        <div class="card-body bg-light border-bottom">
            <h4 class="mb-3" style="font-size: 15px; font-weight: 700;"><i class="fas fa-route text-muted"></i> مسار النقل</h4>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">من مستودع (المصدر) <span class="required">*</span></label>
                    <select name="from_warehouse" id="fromWh" class="form-control" required>
                        <option value="">-- حدد مستودع المصدر --</option>
                        <?php foreach ($warehouses as $wh) : ?>
                            <option value="<?php echo $wh->id; ?>"><?php echo htmlspecialchars($wh->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">إلى مستودع (الوجهة) <span class="required">*</span></label>
                    <select name="to_warehouse" id="toWh" class="form-control" required>
                        <option value="">-- حدد مستودع الوجهة --</option>
                        <?php foreach ($warehouses as $wh) : ?>
                            <option value="<?php echo $wh->id; ?>"><?php echo htmlspecialchars($wh->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <h4 class="mb-3" style="font-size: 15px; font-weight: 700;"><i class="fas fa-box text-muted"></i> تفاصيل البضاعة</h4>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">المنتج المراد نقله <span class="required">*</span></label>
                    <select name="product_id" id="prodSel" class="form-control" required>
                        <option value="">-- اختر المنتج --</option>
                        <?php foreach ($products as $prod) : ?>
                            <option value="<?php echo $prod->id; ?>"><?php echo htmlspecialchars($prod->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">الكمية المنقولة <span class="required">*</span></label>
                    <input type="number" name="quantity" id="qtyVal" class="form-control font-monospace fw-bold text-primary" min="1" placeholder="مثال: 50" required style="direction:ltr; text-align:right;">
                </div>
                
                <div class="form-group full-width">
                    <label class="form-label">ملاحظات / أسباب النقل</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="أدخل أي ملاحظات إضافية بخصوص هذا النقل..."></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-info text-white" id="btnSubmit"><i class="fas fa-paper-plane"></i> تنفيذ النقل</button>
            <a href="<?php echo URLROOT; ?>/warehouse/transfers" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('transferForm').addEventListener('submit', function(e) {
        const fromWh = document.getElementById('fromWh').value;
        const toWh = document.getElementById('toWh').value;
        
        if (fromWh === toWh && fromWh !== '') {
            e.preventDefault();
            alert('خطأ: لا يمكن نقل البضاعة لنفس المستودع المصدر!');
            return false;
        }

        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري التنفيذ...';
        btn.style.pointerEvents = 'none';
    });
</script>