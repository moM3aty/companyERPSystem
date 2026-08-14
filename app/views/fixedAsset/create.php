<?php
// app/views/fixedAsset/create.php
$employees = $data['employees'] ?? [];
$suppliers = $data['suppliers'] ?? [];
$auto_code = $data['auto_code'] ?? 'AST-' . date('Ymd') . '-' . rand(10,99);
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-info-light border-info">
        <h3 class="card-title text-info-dark mb-0"><i class="fas fa-plus"></i> تسجيل أصل ثابت جديد</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/fixedAsset/create" method="POST">
        <div class="card-body">
            
            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">البيانات الأساسية والتصنيف</h5>
            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">كود الأصل (Asset ID) <span class="required">*</span></label>
                    <input type="text" name="asset_code" class="form-control font-monospace bg-light fw-bold" value="<?php echo $auto_code; ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">اسم الأصل (Asset Name) <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: سيارة نقل تويوتا, جهاز سيرفر...">
                </div>
                <div class="form-group">
                    <label class="form-label">الفئة / التصنيف (Category)</label>
                    <select name="category" class="form-control fw-bold">
                        <option value="مركبات">سيارات ومركبات (Vehicles)</option>
                        <option value="معدات وأجهزة">أجهزة ومعدات (Equipment)</option>
                        <option value="مباني وعقارات">مباني وعقارات (Buildings)</option>
                        <option value="أثاث مكتبي">أثاث مكتبي (Furniture)</option>
                        <option value="أجهزة حاسب">أجهزة حاسب وتقنية (IT)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">حالة الأصل</label>
                    <select name="status" class="form-control fw-bold text-success">
                        <option value="Active" selected>نشط (في الخدمة)</option>
                        <option value="Disposed">مستبعد (تالف)</option>
                    </select>
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">البيانات المالية والإهلاك (Depreciation)</h5>
            <div class="form-grid mb-4 bg-light p-3 rounded border">
                <div class="form-group">
                    <label class="form-label">تاريخ الشراء (Purchase Date) <span class="required">*</span></label>
                    <input type="date" name="purchase_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label text-info">تكلفة الشراء (Purchase Cost) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="0" name="purchase_cost" class="form-control font-monospace fw-black text-info text-center fs-4" required placeholder="0.00" style="direction:ltr;">
                </div>
                <div class="form-group">
                    <label class="form-label">العمر الافتراضي بالسنوات (Useful Life)</label>
                    <input type="number" min="1" max="100" name="useful_life_years" class="form-control font-monospace fw-bold text-center" value="5">
                </div>
                <div class="form-group">
                    <label class="form-label">قيمة الخردة المتوقعة (Salvage Value)</label>
                    <input type="number" step="0.01" min="0" name="salvage_value" class="form-control font-monospace text-center" value="0.00" style="direction:ltr;">
                    <small class="text-muted d-block mt-1">القيمة المتبقية بعد نهاية عمره الافتراضي.</small>
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">المسؤولية والموقع (Assignment)</h5>
            <div class="form-grid mb-2">
                <div class="form-group">
                    <label class="form-label">المورد (Supplier)</label>
                    <select name="supplier_id" class="form-control">
                        <option value="">-- غير محدد --</option>
                        <?php foreach($suppliers as $sup): ?><option value="<?php echo $sup->id; ?>"><?php echo htmlspecialchars($sup->company_name); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">الموظف المسؤول (العهدة)</label>
                    <select name="employee_id" class="form-control">
                        <option value="">-- غير مسلم لموظف --</option>
                        <?php foreach($employees as $emp): ?><option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->full_name ?? $emp->name_ar); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">الموقع / الفرع (Location)</label>
                    <input type="text" name="location" class="form-control" placeholder="أين يوجد الأصل حالياً؟">
                </div>
                <div class="form-group">
                    <label class="form-label">القسم (Department)</label>
                    <input type="text" name="department" class="form-control" placeholder="القسم المستفيد">
                </div>
            </div>

        </div>
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-info text-white"><i class="fas fa-save"></i> تسجيل الأصل</button>
            <a href="<?php echo URLROOT; ?>/fixedAsset/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>