<?php
// app/views/collections/create.php
$treasuries = $data['treasuries'] ?? [];
$invoices = $data['invoices'] ?? [];
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-success text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-file-invoice-dollar"></i> توثيق تحصيل مالي (سند قبض لفاتورة)</h3>
    </div>

    <?php 
        $flash = Session::getFlash();
        if ($flash) : 
    ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>" style="margin: 20px 20px 0;">
            <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo URLROOT; ?>/collection/create" method="POST" id="collectionForm">
        <div class="card-body">
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle"></i> سيؤدي حفظ هذا السند إلى تحديث رصيد الخزنة/البنك وتوثيق استلام المبلغ للفاتورة المرتبطة بشكل آلي.
            </div>

            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">الفاتورة المرتبطة <span class="required">*</span></label>
                    <select name="invoice_id" class="form-control" required>
                        <option value="">-- اختر الفاتورة المطلوب تحصيل قيمتها --</option>
                        <?php foreach($invoices as $inv): 
                            $invId = is_object($inv) ? $inv->id : $inv['id'];
                            $invNum = is_object($inv) ? $inv->invoice_number : $inv['invoice_number'];
                            $invTotal = is_object($inv) ? $inv->total : $inv['total'];
                        ?>
                            <option value="<?php echo $invId; ?>">
                                فاتورة #<?php echo htmlspecialchars($invNum); ?> (الإجمالي: <?php echo number_format($invTotal, 2); ?> ر.س)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">الخزنة / البنك المودع به <span class="required">*</span></label>
                    <select name="treasury_id" class="form-control" required>
                        <option value="">-- اختر الخزنة/البنك --</option>
                        <?php foreach($treasuries as $t): 
                            $tId = is_object($t) ? $t->id : $t['id'];
                            $tName = is_object($t) ? $t->name : $t['name'];
                        ?>
                            <option value="<?php echo $tId; ?>"><?php echo htmlspecialchars($tName); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">المبلغ المُحصّل (ر.س) <span class="required">*</span></label>
                    <input type="number" name="amount" step="0.01" min="0.01" class="form-control font-monospace fw-bold text-success" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">طريقة الدفع <span class="required">*</span></label>
                    <select name="payment_method" class="form-control" required>
                        <option value="cash">نقدي (كاش)</option>
                        <option value="bank_transfer">تحويل بنكي</option>
                        <option value="check">شيك</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">تاريخ التحصيل <span class="required">*</span></label>
                    <input type="date" name="collection_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">رقم المرجع (اختياري)</label>
                    <input type="text" name="reference" class="form-control" placeholder="رقم الحوالة، رقم الشيك...">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">ملاحظات التحصيل</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="اكتب تفاصيل إضافية حول عملية الدفع..."></textarea>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-success" id="btnSubmit"><i class="fas fa-save"></i> حفظ وتأكيد التحصيل</button>
            <a href="<?php echo URLROOT; ?>/collection/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('collectionForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري تأكيد المعاملة...';
        btn.classList.add('disabled');
        btn.style.pointerEvents = 'none';
    });
</script>