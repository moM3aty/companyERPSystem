<?php 
//app/views/collections/create.php
/** @var array $treasuries */ 
/** @var array $invoices */ 
?>
<div class="container-fluid mt-4">
    <h2>تسجيل تحصيل مبيعات (سند قبض عميل)</h2>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form action="/collections/create" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>رقم الفاتورة المحصلة</label>
                        <select name="invoice_id" class="form-control select2" required>
                            <option value="">-- اختر الفاتورة --</option>
                            <?php foreach ($invoices as $inv): ?>
                                <option value="<?= $inv['id'] ?>">فاتورة #<?= htmlspecialchars($inv['invoice_number']) ?> (<?= number_format($inv['total'], 2) ?> ج.م)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>إيداع في (الخزنة / البنك)</label>
                        <select name="treasury_id" class="form-control" required>
                            <?php foreach ($treasuries as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>المبلغ المحصل</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>تاريخ التحصيل</label>
                        <input type="date" name="collection_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>طريقة الدفع</label>
                        <select name="payment_method" class="form-control">
                            <option value="cash">نقداً</option>
                            <option value="bank_transfer">تحويل بنكي</option>
                            <option value="check">شيك</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>الرقم المرجعي (اختياري - رقم الحوالة أو الشيك)</label>
                        <input type="text" name="reference" class="form-control">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> حفظ وتأكيد التحصيل</button>
                <a href="/collections" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </div>
</div>