<?php
// app/views/budget/create.php
$categories = $data['categories'] ?? [];
?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning-light border-warning">
        <h3 class="card-title text-warning-dark mb-0"><i class="fas fa-plus"></i> تخصيص موازنة مالية</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/budget/create" method="POST">
        <div class="card-body form-grid" style="grid-template-columns: 1fr;">
            
            <div class="form-group">
                <label class="form-label">السنة المالية <span class="required">*</span></label>
                <select name="fiscal_year" class="form-control fw-bold" required>
                    <?php 
                    $currentY = date('Y');
                    for($y = $currentY; $y <= $currentY + 2; $y++): ?>
                        <option value="<?php echo $y; ?>">السنة المالية <?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">تصنيف المصروف <span class="required">*</span></label>
                <select name="category_id" class="form-control fw-bold" required>
                    <option value="">-- اختر التصنيف المراد تخصيص ميزانية له --</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat->id; ?>"><?php echo htmlspecialchars($cat->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label text-primary fw-bold">الميزانية المخصصة (SAR) <span class="required">*</span></label>
                <input type="number" step="0.01" min="1" name="amount" class="form-control font-monospace fs-4 text-center fw-black text-primary" required placeholder="0.00" style="direction:ltr;">
            </div>
            
            <div class="form-group">
                <label class="form-label">ملاحظات (اختياري)</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="اكتب مبررات هذه الموازنة..."></textarea>
            </div>
            
        </div>
        <div class="card-footer bg-light d-flex gap-2 mt-0">
            <button type="submit" class="btn btn-warning fw-bold"><i class="fas fa-check"></i> اعتماد الموازنة</button>
            <a href="<?php echo URLROOT; ?>/budget/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>