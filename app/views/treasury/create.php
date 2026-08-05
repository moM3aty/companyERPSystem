<?php /** @var array $treasuries */ ?>
<div class="container-fluid mt-4">
    <h2>تسجيل حركة مالية (سند قبض / صرف)</h2>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <form action="/treasury/createTransaction" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>الخزنة / البنك</label>
                        <select name="treasury_id" class="form-control" required>
                            <?php foreach ($treasuries as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?> (<?= number_format($t['current_balance'], 2) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>نوع الحركة</label>
                        <select name="transaction_type" class="form-control" required>
                            <option value="receipt">سند قبض (إيداع / إيراد)</option>
                            <option value="payment">سند صرف (سحب / مصروف)</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>المبلغ</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>تاريخ الحركة</label>
                        <input type="date" name="transaction_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>الرقم المرجعي (اختياري)</label>
                        <input type="text" name="reference" class="form-control" placeholder="رقم الفاتورة أو الشيك">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>البيان / الوصف</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-success">حفظ الحركة المالية</button>
                <a href="/treasury" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </div>
</div>