<?php
// app/views/expense/create.php
$treasuries = $data['treasuries'] ?? [];
$categories = $data['categories'] ?? []; // استلام التصنيفات المضافة
?>

<div class="card" style="max-width: 800px; margin: 0 auto; box-shadow: var(--shadow-md);">
    <div class="card-header bg-light border-bottom">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-file-invoice-dollar text-primary"></i> تسجيل مصروف تشغيلي</h3>
    </div>
    
    <?php $flash = Session::getFlash(); if ($flash): ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?> m-3 mb-0"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
    <?php endif; ?>

    <form action="<?php echo URLROOT; ?>/expense/create" method="POST" enctype="multipart/form-data" id="expForm">
        <div class="card-body">
            
            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">الخزنة / البنك الدافع <span class="required">*</span></label>
                    <select name="treasury_id" class="form-control fw-bold" required>
                        <option value="">-- اختر من أين تم الدفع --</option>
                        <?php foreach($treasuries as $t): 
                            $bal = isset($t->current_balance) ? $t->current_balance : ($t->balance ?? 0);
                        ?>
                            <option value="<?php echo $t->id; ?>"><?php echo htmlspecialchars($t->name); ?> (متاح: <?php echo number_format($bal, 2); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ المصروف</label>
                    <input type="date" name="expense_date" class="form-control font-monospace" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">تصنيف المصروف <span class="required">*</span></label>
                    <!-- 🟢 تم التعديل هنا لقراءة التصنيفات من الداتابيز وإرسال ID 🟢 -->
                    <select name="category_id" class="form-control fw-bold text-primary" required>
                        <option value="">-- اختر التصنيف --</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat->id; ?>"><?php echo htmlspecialchars($cat->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">رقم المرجع / الفاتورة</label>
                    <input type="text" name="reference" class="form-control font-monospace">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group p-3 border rounded bg-light" style="border-color: #ef4444 !important;">
                        <label class="form-label fw-bold text-danger">المبلغ (بدون الضريبة) <span class="required">*</span></label>
                        <input type="number" step="0.01" min="0" name="amount" id="amtInput" class="form-control font-monospace fs-4 text-center fw-black text-danger" required placeholder="0.00" oninput="calcTotal()" style="direction:ltr;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group p-3 border rounded bg-light">
                        <label class="form-label fw-bold text-muted">نسبة الضريبة المضافة (VAT %)</label>
                        <input type="number" step="0.01" min="0" name="tax_rate" id="taxInput" class="form-control font-monospace fs-4 text-center fw-bold" value="15.00" oninput="calcTotal()" style="direction:ltr;">
                    </div>
                </div>
            </div>

            <div class="p-3 mb-4 rounded d-flex justify-content-between align-items-center" style="background: var(--slate-800); color:#fff;">
                <span class="fs-5 fw-bold">إجمالي المبلغ المخصوم من الخزنة:</span>
                <span class="fs-2 fw-black font-monospace text-warning" id="totalLabel" style="direction:ltr;">0.00</span>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">مركز التكلفة / المشروع (اختياري)</label>
                    <input type="text" name="cost_center" class="form-control font-monospace" placeholder="توجيه المصروف لقسم معين...">
                </div>
                <div class="form-group">
                    <label class="form-label">مرفقات (صورة الفاتورة/الإيصال)</label>
                    <input type="file" name="attachment" class="form-control bg-white">
                </div>
                <div class="form-group full-width">
                    <label class="form-label">البيان / ملاحظات التفاصيل <span class="required">*</span></label>
                    <textarea name="notes" class="form-control" rows="2" required placeholder="اكتب تفاصيل المصروف هنا..."></textarea>
                </div>
            </div>

        </div>
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-primary fw-bold px-5" id="btnSubmit"><i class="fas fa-save"></i> حفظ وخصم المصروف</button> 
            <a href="<?php echo URLROOT; ?>/expense/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    function calcTotal() {
        let amt = parseFloat(document.getElementById('amtInput').value) || 0;
        let taxR = parseFloat(document.getElementById('taxInput').value) || 0;
        let taxVal = amt * (taxR / 100);
        let total = amt + taxVal;
        document.getElementById('totalLabel').innerText = total.toFixed(2) + ' SAR';
    }
    
    document.getElementById('expForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الاعتماد...';
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
    });
</script>