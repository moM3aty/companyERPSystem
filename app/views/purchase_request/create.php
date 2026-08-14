<?php $auto_req_num = $data['auto_req_num'] ?? ''; ?>

<div class="card" style="max-width: 900px; margin: 0 auto; box-shadow: var(--shadow-md);">
    <div class="card-header bg-light border-bottom">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-clipboard-list text-primary"></i> رفع طلب احتياج داخلي (PR)</h3>
    </div>
    
    <?php $flash = Session::getFlash(); if ($flash): ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?> m-3 mb-0"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
    <?php endif; ?>

    <form action="<?php echo URLROOT; ?>/purchaseRequest/create" method="POST" id="prForm">
        <div class="card-body">
            
            <div class="form-grid mb-4 p-3 bg-light rounded border">
                <div class="form-group">
                    <label class="form-label">القسم المٌقدم للطلب</label>
                    <input type="text" name="department" class="form-control" placeholder="مثال: قسم الـ IT، المبيعات..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">رقم الطلب (PR Number)</label>
                    <input type="text" name="request_number" class="form-control font-monospace bg-white text-primary fw-bold" value="<?php echo $auto_req_num; ?>" required readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الطلب</label>
                    <input type="date" name="request_date" class="form-control font-monospace" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-box-open text-primary"></i> الأصناف / النواقص المطلوبة</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered text-center align-middle" id="itemsTable">
                    <thead class="bg-slate-50">
                        <tr>
                            <th style="width: 45%;">اسم الصنف ووصفه الدقيق</th>
                            <th style="width: 15%;">الكمية</th>
                            <th style="width: 20%;">السعر التقديري (اختياري)</th>
                            <th style="width: 15%;">الإجمالي التقريبي</th>
                            <th style="width: 5%;">إزالة</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <tr>
                            <td><input type="text" name="product_name[]" class="form-control fw-bold" placeholder="اكتب اسم الصنف هنا..." required></td>
                            <td><input type="number" name="quantity[]" class="form-control text-center font-monospace qty-input" value="1" min="1" step="0.01" required></td>
                            <td><input type="number" name="estimated_price[]" class="form-control text-center font-monospace price-input" value="0.00" min="0" step="0.01"></td>
                            <td><input type="text" name="total_price[]" class="form-control text-center font-monospace total-input bg-light" value="0.00" readonly></td>
                            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row" disabled><i class="fas fa-times"></i></button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" class="btn btn-sm btn-outline-primary fw-bold mt-2" id="addRowBtn"><i class="fas fa-plus"></i> إضافة سطر جديد</button>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <div class="p-3 border rounded text-center bg-light" style="width: 300px;">
                    <div class="text-muted fw-bold mb-1">التكلفة التقديرية (SAR)</div>
                    <input type="text" name="grand_total" id="grandTotal" class="form-control text-center font-monospace fs-4 fw-black text-primary bg-white" value="0.00" readonly>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label fw-bold">مبررات الطلب / الملاحظات</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="اكتب سبب طلب هذه الأصناف..."></textarea>
            </div>

        </div>
        <div class="card-footer d-flex gap-3 bg-light">
            <button type="submit" class="btn btn-primary fw-bold px-5" id="btnSubmit"><i class="fas fa-paper-plane"></i> رفع الطلب للإدارة</button> 
            <a href="<?php echo URLROOT; ?>/purchaseRequest/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('itemsBody');
    const addRowBtn = document.getElementById('addRowBtn');
    const grandTotalInput = document.getElementById('grandTotal');

    function calculateTotals() {
        let grandTotal = 0;
        tableBody.querySelectorAll('tr').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = qty * price;
            row.querySelector('.total-input').value = total.toFixed(2);
            grandTotal += total;
        });
        grandTotalInput.value = grandTotal.toFixed(2);
    }

    addRowBtn.addEventListener('click', function() {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="text" name="product_name[]" class="form-control fw-bold" placeholder="اكتب اسم الصنف هنا..." required></td>
            <td><input type="number" name="quantity[]" class="form-control text-center font-monospace qty-input" value="1" min="1" step="0.01" required></td>
            <td><input type="number" name="estimated_price[]" class="form-control text-center font-monospace price-input" value="0.00" min="0" step="0.01"></td>
            <td><input type="text" name="total_price[]" class="form-control text-center font-monospace total-input bg-light" value="0.00" readonly></td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="fas fa-times"></i></button></td>
        `;
        tableBody.appendChild(newRow);
        if(tableBody.querySelectorAll('tr').length > 1) tableBody.querySelector('tr:first-child .remove-row').disabled = false;
    });

    tableBody.addEventListener('input', e => { if(e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) calculateTotals(); });
    tableBody.addEventListener('click', e => {
        const btn = e.target.closest('.remove-row');
        if (btn && tableBody.querySelectorAll('tr').length > 1) {
            btn.closest('tr').remove(); calculateTotals();
            if(tableBody.querySelectorAll('tr').length === 1) tableBody.querySelector('.remove-row').disabled = true;
        }
    });

    document.getElementById('prForm').addEventListener('submit', function() {
        const btn = document.getElementById('btnSubmit');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الرفع...';
        btn.classList.add('disabled'); btn.style.pointerEvents = 'none';
    });
});
</script>