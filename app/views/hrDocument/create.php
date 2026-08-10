<?php
// app/views/hrDocument/create.php
$employees = $data['employees'] ?? [];
?>
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header bg-light"><h3 class="card-title text-dark"><i class="fas fa-upload text-primary"></i> أرشفة وثيقة رسمية للموظف</h3></div>
    <form action="<?php echo URLROOT; ?>/hrDocument/create" method="POST" enctype="multipart/form-data">
        <div class="card-body form-grid">
            <div class="form-group full-width">
                <label class="form-label">الموظف (Employee) <span class="required">*</span></label>
                <select name="employee_id" class="form-control" required>
                    <option value="">-- اختر --</option>
                    <?php foreach($employees as $emp): ?><option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">نوع الوثيقة (Document Type) <span class="required">*</span></label>
                <select name="doc_type" class="form-control fw-bold" required>
                    <option value="National ID / Civil ID">هوية وطنية / مدنية (National ID)</option>
                    <option value="Passport">جواز سفر (Passport)</option>
                    <option value="Residence Permit / Iqama">إقامة (Iqama)</option>
                    <option value="Work Permit">رخصة عمل (Work Permit)</option>
                    <option value="Driving License">رخصة قيادة (Driving License)</option>
                    <option value="Visa">تأشيرة (Visa)</option>
                    <option value="Insurance">تأمين طبي (Insurance)</option>
                    <option value="Educational Certificate">شهادة تعليمية (Educational)</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">رقم الوثيقة (Document Number) <span class="required">*</span></label>
                <input type="text" name="doc_number" class="form-control font-monospace" required style="direction:ltr; text-align:right;">
            </div>
            <div class="form-group">
                <label class="form-label">تاريخ الإصدار (Issue Date)</label>
                <input type="date" name="issue_date" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label text-danger">تاريخ الانتهاء (Expiry Date)</label>
                <input type="date" name="expiry_date" class="form-control text-danger fw-bold">
            </div>
            <div class="form-group">
                <label class="form-label">جهة الإصدار (Issuing Authority)</label>
                <input type="text" name="issuing_authority" class="form-control" placeholder="مثال: الجوازات، المرور...">
            </div>
            <div class="form-group full-width border p-3 bg-light rounded mt-2">
                <label class="form-label text-primary"><i class="fas fa-paperclip"></i> إرفاق صورة من الوثيقة (Attachment)</label>
                <input type="file" name="attachment" class="form-control bg-white" accept="image/*,.pdf">
            </div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ وأرشفة</button></div>
    </form>
</div>