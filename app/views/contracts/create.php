<?php
// المسار: app/views/contracts/create.php
$customers = $data['customers'] ?? [];
$suppliers = $data['suppliers'] ?? [];
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-primary text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-file-signature"></i> تسجيل عقد جديد</h3>
    </div>

    <div class="card-body bg-light border-bottom">
        <p class="text-muted m-0 fs-6"><i class="fas fa-info-circle"></i> قم بإدخال بيانات العقد المرتبط بالعميل أو المورد لضمان استلام تنبيهات التجديد في الوقت المناسب.</p>
    </div>

    <form action="<?php echo URLROOT; ?>/contract/create" method="POST" id="contractForm">
        <div class="card-body border-bottom">
            <h4 class="mb-3 text-primary" style="font-size: 15px; font-weight: 700;"><i class="fas fa-handshake"></i> الطرف المعني</h4>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">عنوان العقد <span class="required">*</span></label>
                    <input type="text" name="title" class="form-control" id="contractTitle" placeholder="مثال: عقد صيانة سنوي للفرع الرئيسي" required>
                </div>
                <div class="form-group">
                    <label class="form-label">نوع العقد <span class="required">*</span></label>
                    <select name="party_type" id="partyType" class="form-control" required>
                        <option value="customer">عقد عميل</option>
                        <option value="supplier">عقد مورد</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">العميل / المورد <span class="required">*</span></label>
                    <select name="party_id" id="customerSelect" class="form-control" required>
                        <option value="">-- اختر العميل --</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="party_id" id="supplierSelect" class="form-control" disabled style="display:none;">
                        <option value="">-- اختر المورد --</option>
                        <?php foreach($suppliers as $s): ?>
                            <option value="<?php echo $s->id; ?>"><?php echo htmlspecialchars($s->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <h4 class="mb-3 text-purple" style="font-size: 15px; font-weight: 700;"><i class="fas fa-calendar-check"></i> الشروط والتواريخ</h4>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">تاريخ البداية <span class="required">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الانتهاء <span class="required">*</span></label>
                    <input type="date" name="end_date" id="endDate" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">القيمة المالية (ر.س)</label>
                    <input type="number" step="0.01" min="0" name="value" class="form-control font-monospace text-success fw-bold" placeholder="0.00" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-control">
                        <option value="active">ساري المفعول</option>
                        <option value="pending">تحت الإجراء</option>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label class="form-label">بنود مختصرة / ملاحظات</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="تفاصيل الدفعات، التجديد التلقائي..."></textarea>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-primary" id="btnSubmit"><i class="fas fa-save"></i> تسجيل العقد</button>
            <a href="<?php echo URLROOT; ?>/contract/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    // تبديل قائمة العملاء / الموردين
    const partyType = document.getElementById('partyType');
    const custSelect = document.getElementById('customerSelect');
    const supSelect = document.getElementById('supplierSelect');

    partyType.addEventListener('change', function() {
        if(this.value === 'customer') {
            custSelect.disabled = false;
            custSelect.style.display = 'block';
            supSelect.disabled = true;
            supSelect.style.display = 'none';
        } else {
            supSelect.disabled = false;
            supSelect.style.display = 'block';
            custSelect.disabled = true;
            custSelect.style.display = 'none';
        }
    });

    // التحقق قبل الإرسال
    const form = document.getElementById('contractForm');
    const btnSubmit = document.getElementById('btnSubmit');
    
    form.addEventListener('submit', function(e) {
        let valid = true;
        const title = document.getElementById('contractTitle');
        const end = document.getElementById('endDate');
        
        form.querySelectorAll('.form-control').forEach(el => el.style.borderColor = '');
        
        if (!title.value.trim()) { title.style.borderColor = 'var(--danger)'; valid = false; }
        if (partyType.value === 'customer' && !custSelect.value) { custSelect.style.borderColor = 'var(--danger)'; valid = false; }
        if (partyType.value === 'supplier' && !supSelect.value) { supSelect.style.borderColor = 'var(--danger)'; valid = false; }
        if (!end.value) { end.style.borderColor = 'var(--danger)'; valid = false; }
        
        if (!valid) {
            e.preventDefault();
        } else {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري الحفظ...';
        }
    });
</script>