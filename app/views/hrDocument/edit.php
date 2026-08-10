<?php
// app/views/hrDocument/edit.php
$doc = $data['document'] ?? null;
$employees = $data['employees'] ?? [];
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-warning-light border-warning">
        <h3 class="card-title text-warning-dark mb-0"><i class="fas fa-pen"></i> تعديل بيانات الوثيقة الرسمية</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/hrDocument/edit/<?php echo $doc->id; ?>" method="POST">
        <div class="card-body form-grid">
            
            <div class="form-group full-width">
                <label class="form-label">الموظف (Employee) <span class="required">*</span></label>
                <select name="employee_id" class="form-control" required>
                    <?php foreach($employees as $emp): 
                        // استخدام full_name لدعم التحديث الأخير لبيانات الموظف
                        $empName = $emp->full_name ?? $emp->name_ar ?? $emp->name ?? 'بدون اسم';
                    ?>
                        <option value="<?php echo $emp->id; ?>" <?php echo $emp->id == $doc->employee_id ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($empName); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">نوع الوثيقة (Document Type) <span class="required">*</span></label>
                <select name="doc_type" class="form-control fw-bold text-primary" required>
                    <option value="National ID / Civil ID" <?php echo $doc->doc_type == 'National ID / Civil ID' ? 'selected' : ''; ?>>هوية وطنية / مدنية (National ID)</option>
                    <option value="Passport" <?php echo $doc->doc_type == 'Passport' ? 'selected' : ''; ?>>جواز سفر (Passport)</option>
                    <option value="Residence Permit / Iqama" <?php echo $doc->doc_type == 'Residence Permit / Iqama' ? 'selected' : ''; ?>>إقامة (Iqama)</option>
                    <option value="Work Permit" <?php echo $doc->doc_type == 'Work Permit' ? 'selected' : ''; ?>>رخصة عمل (Work Permit)</option>
                    <option value="Driving License" <?php echo $doc->doc_type == 'Driving License' ? 'selected' : ''; ?>>رخصة قيادة (Driving License)</option>
                    <option value="Visa" <?php echo $doc->doc_type == 'Visa' ? 'selected' : ''; ?>>تأشيرة (Visa)</option>
                    <option value="Insurance" <?php echo $doc->doc_type == 'Insurance' ? 'selected' : ''; ?>>تأمين طبي (Insurance)</option>
                    <option value="Educational Certificate" <?php echo $doc->doc_type == 'Educational Certificate' ? 'selected' : ''; ?>>شهادة تعليمية (Educational)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">رقم الوثيقة (Document Number) <span class="required">*</span></label>
                <input type="text" name="doc_number" class="form-control font-monospace" value="<?php echo htmlspecialchars($doc->doc_number); ?>" required style="direction:ltr; text-align:right;">
            </div>

            <div class="form-group">
                <label class="form-label">تاريخ الإصدار (Issue Date)</label>
                <input type="date" name="issue_date" class="form-control" value="<?php echo $doc->issue_date; ?>">
            </div>

            <div class="form-group border border-danger p-2 rounded bg-light">
                <label class="form-label text-danger">تاريخ الانتهاء (Expiry Date)</label>
                <input type="date" name="expiry_date" class="form-control text-danger fw-bold font-monospace" value="<?php echo $doc->expiry_date; ?>">
            </div>

            <div class="form-group full-width mt-2">
                <label class="form-label">جهة الإصدار (Issuing Authority)</label>
                <input type="text" name="issuing_authority" class="form-control" value="<?php echo htmlspecialchars($doc->issuing_authority ?? ''); ?>">
            </div>

        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/hrDocument/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>