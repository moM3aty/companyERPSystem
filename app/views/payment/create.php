<?php
// app/views/payment/create.php
$treasuries = $data['treasuries'] ?? [];
$suppliers = $data['suppliers'] ?? [];
$customers = $data['customers'] ?? [];
$auto_pay_num = $data['auto_pay_num'] ?? 'PAY-' . date('Ymd') . '-' . rand(100,999);
?>
<div class="card" style="max-width: 900px; margin: 0 auto; box-shadow: var(--shadow-md);">
    <div class="card-header bg-light border-bottom">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-money-bill-wave text-primary"></i> إصدار سند مالي (صرف / قبض)</h3>
    </div>
    
    <?php $flash = Session::getFlash(); if ($flash): ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?> m-3 mb-0"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
    <?php endif; ?>

    <form action="<?php echo URLROOT; ?>/payment/create" method="POST" enctype="multipart/form-data" id="payForm">
        <div class="card-body">
            
            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">نوع السند <span class="required">*</span></label>
                    <select name="payment_type" id="paymentType" class="form-control fw-bold text-danger" onchange="toggleParty()" required>
                        <option value="Out" selected>سند صرف (Payment Out) - دفع لمورد/مصروف</option>
                        <option value="In">سند قبض (Receipt In) - تحصيل من عميل</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">رقم السند</label>
                    <input type="text" name="voucher_number" class="form-control font-monospace bg-light" value="<?php echo $auto_pay_num; ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ السند</label>
                    <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-group full-width">
                    <label class="form-label">الخزنة / البنك (حساب النقدية) <span class="required">*</span></label>
                    <select name="treasury_id" class="form-control fw-bold" required>
                        <option value="">-- اختر حساب الصندوق أو البنك --</option>
                        <?php foreach($treasuries as $t): ?>
                            <option value="<?php echo $t->id; ?>"><?php echo htmlspecialchars($t->name); ?> (رصيد: <?php echo number_format($t->current_balance, 2); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-grid mb-4 bg-light p-3 rounded border">
                <!-- الموردين (لسند الصرف) -->
                <div class="form-group full-width" id="supplierDiv">
                    <label class="form-label text-danger"><i class="fas fa-truck"></i> دفع إلى (المورد)</label>
                    <select name="supplier_id" class="form-control fw-bold">
                        <option value="">-- دفعة لجهات أخرى / غير مرتبط بمورد --</option>
                        <?php foreach($suppliers as $s): ?>
                            <option value="<?php echo $s->id; ?>"><?php echo htmlspecialchars($s->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- العملاء (لسند القبض) -->
                <div class="form-group full-width" id="customerDiv" style="display: none;">
                    <label class="form-label text-success"><i class="fas fa-users"></i> استلام من (العميل)</label>
                    <select name="customer_id" class="form-control fw-bold">
                        <option value="">-- تحصيل من جهات أخرى / غير مرتبط بعميل --</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group border p-3 rounded" id="amountBox" style="background:#fef2f2; border-color:#ef4444 !important;">
                    <label class="form-label fw-bold" id="amountLabel" style="color:#ef4444;">المبلغ (ر.س) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control font-monospace fw-black fs-3 text-center" required placeholder="0.00" style="direction:ltr;">
                </div>
                <div class="form-group">
                    <label class="form-label">طريقة الدفع</label>
                    <select name="payment_method" class="form-control fw-bold">
                        <option value="Cash">نقدي (Cash)</option>
                        <option value="Bank Transfer">تحويل بنكي</option>
                        <option value="Cheque">شيك</option>
                    </select>
                </div>
                <div class="form-group"><label class="form-label">المرجع (رقم الحوالة/الشيك)</label><input type="text" name="reference_number" class="form-control font-monospace"></div>
                <div class="form-group full-width"><label class="form-label">البيان / ملاحظات (سبب الصرف/القبض) <span class="required">*</span></label><textarea name="notes" class="form-control" rows="2" required placeholder="مثال: دفعة تحت الحساب..."></textarea></div>
                <div class="form-group full-width"><label class="form-label">مرفقات (صورة إيصال / حوالة)</label><input type="file" name="attachment" class="form-control bg-white"></div>
            </div>

        </div>
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-danger fw-bold px-4" id="btnSubmit"><i class="fas fa-check"></i> حفظ وإصدار السند (صرف)</button> 
            <a href="<?php echo URLROOT; ?>/payment/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    function toggleParty() {
        const type = document.getElementById('paymentType').value;
        const btn = document.getElementById('btnSubmit');
        const supDiv = document.getElementById('supplierDiv');
        const custDiv = document.getElementById('customerDiv');
        const amtBox = document.getElementById('amountBox');
        const amtLabel = document.getElementById('amountLabel');

        if(type === 'Out') {
            document.getElementById('paymentType').className = 'form-control fw-bold text-danger';
            btn.className = 'btn btn-danger fw-bold px-4';
            btn.innerHTML = '<i class="fas fa-arrow-up"></i> حفظ وإصدار السند (صرف)';
            
            supDiv.style.display = 'block';
            custDiv.style.display = 'none';
            document.querySelector('select[name="customer_id"]').value = '';
            
            amtBox.style.background = '#fef2f2';
            amtBox.style.borderColor = '#ef4444';
            amtLabel.style.color = '#ef4444';
        } else {
            document.getElementById('paymentType').className = 'form-control fw-bold text-success';
            btn.className = 'btn btn-success fw-bold px-4';
            btn.innerHTML = '<i class="fas fa-arrow-down"></i> حفظ وإصدار السند (قبض)';
            
            supDiv.style.display = 'none';
            custDiv.style.display = 'block';
            document.querySelector('select[name="supplier_id"]').value = '';
            
            amtBox.style.background = '#f0fdf4';
            amtBox.style.borderColor = '#22c55e';
            amtLabel.style.color = '#22c55e';
        }
    }
    
    document.getElementById('payForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الاعتماد...';
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
    });
</script>