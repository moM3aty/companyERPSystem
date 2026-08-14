<?php
// app/views/fixedAsset/edit.php
$asset = $data['asset'] ?? null;
$employees = $data['employees'] ?? [];
$suppliers = $data['suppliers'] ?? [];
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning-light border-warning">
        <h3 class="card-title text-warning-dark mb-0"><i class="fas fa-pen"></i> تعديل الأصل: <?php echo htmlspecialchars($asset->name); ?></h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/fixedAsset/edit/<?php echo $asset->id; ?>" method="POST">
        <div class="card-body">
            
            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">البيانات الأساسية والتصنيف</h5>
            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">كود الأصل (Asset ID)</label>
                    <input type="text" class="form-control font-monospace bg-light fw-bold" value="<?php echo htmlspecialchars($asset->asset_code); ?>" disabled readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">اسم الأصل (Asset Name) <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($asset->name); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">الفئة / التصنيف (Category)</label>
                    <select name="category" class="form-control fw-bold">
                        <option value="مركبات" <?php echo $asset->category=='مركبات'?'selected':'';?>>سيارات ومركبات</option>
                        <option value="معدات وأجهزة" <?php echo $asset->category=='معدات وأجهزة'?'selected':'';?>>أجهزة ومعدات</option>
                        <option value="مباني وعقارات" <?php echo $asset->category=='مباني وعقارات'?'selected':'';?>>مباني وعقارات</option>
                        <option value="أثاث مكتبي" <?php echo $asset->category=='أثاث مكتبي'?'selected':'';?>>أثاث مكتبي</option>
                        <option value="أجهزة حاسب" <?php echo $asset->category=='أجهزة حاسب'?'selected':'';?>>أجهزة حاسب وتقنية</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">حالة الأصل</label>
                    <select name="status" class="form-control fw-bold text-primary">
                        <option value="Active" <?php echo $asset->status=='Active'?'selected':'';?>>نشط (في الخدمة)</option>
                        <option value="Disposed" <?php echo $asset->status=='Disposed'?'selected':'';?>>مستبعد (تالف)</option>
                        <option value="Sold" <?php echo $asset->status=='Sold'?'selected':'';?>>مُباع (تم التخلص منه)</option>
                    </select>
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">البيانات المالية والإهلاك (Depreciation)</h5>
            <div class="form-grid mb-4 bg-light p-3 rounded border">
                <div class="form-group">
                    <label class="form-label">تاريخ الشراء (Purchase Date) <span class="required">*</span></label>
                    <input type="date" name="purchase_date" class="form-control" value="<?php echo $asset->purchase_date; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label text-info">تكلفة الشراء (Purchase Cost) <span class="required">*</span></label>
                    <input type="number" step="0.01" min="0" name="purchase_cost" class="form-control font-monospace fw-black text-info text-center fs-4" value="<?php echo $asset->purchase_cost; ?>" required style="direction:ltr;">
                </div>
                <div class="form-group">
                    <label class="form-label">العمر الافتراضي بالسنوات</label>
                    <input type="number" min="1" max="100" name="useful_life_years" class="form-control font-monospace fw-bold text-center" value="<?php echo $asset->useful_life_years; ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">قيمة الخردة المتوقعة (Salvage Value)</label>
                    <input type="number" step="0.01" min="0" name="salvage_value" class="form-control font-monospace text-center" value="<?php echo $asset->salvage_value; ?>" style="direction:ltr;">
                </div>
            </div>

            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">المسؤولية والموقع (Assignment)</h5>
            <div class="form-grid mb-2">
                <div class="form-group">
                    <label class="form-label">المورد (Supplier)</label>
                    <select name="supplier_id" class="form-control">
                        <option value="">-- غير محدد --</option>
                        <?php foreach($suppliers as $sup): ?><option value="<?php echo $sup->id; ?>" <?php echo $asset->supplier_id==$sup->id?'selected':'';?>><?php echo htmlspecialchars($sup->company_name); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">الموظف المسؤول (العهدة)</label>
                    <select name="employee_id" class="form-control">
                        <option value="">-- غير مسلم لموظف --</option>
                        <?php foreach($employees as $emp): ?><option value="<?php echo $emp->id; ?>" <?php echo $asset->employee_id==$emp->id?'selected':'';?>><?php echo htmlspecialchars($emp->full_name ?? $emp->name_ar); ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">الموقع / الفرع</label>
                    <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($asset->location ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">القسم (Department)</label>
                    <input type="text" name="department" class="form-control" value="<?php echo htmlspecialchars($asset->department ?? ''); ?>">
                </div>
            </div>

        </div>
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
            <a href="<?php echo URLROOT; ?>/fixedAsset/show/<?php echo $asset->id; ?>" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>