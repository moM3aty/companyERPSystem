<?php
// app/views/superadmin/edit.php
$company = $data['company'] ?? null;
$activeModules = explode(',', $company->active_modules ?? '');

function isModuleActive($mod, $activeArr) {
    return in_array($mod, $activeArr) ? 'checked' : '';
}
function moduleClass($mod, $activeArr) {
    return in_array($mod, $activeArr) ? 'bg-success-light border-success' : 'bg-light';
}
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-warning text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-cogs"></i> ضبط الموارد والصلاحيات: <?php echo htmlspecialchars($company->name); ?></h3>
    </div>

    <form action="<?php echo URLROOT; ?>/superadmin/edit/<?php echo $company->id; ?>" method="POST">
        <div class="card-body">
            
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">بيانات الاتصال</h5>
            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">اسم الشركة</label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($company->name); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني للعميل</label>
                    <input type="email" name="email" class="form-control font-monospace" value="<?php echo htmlspecialchars($company->email); ?>" style="direction:ltr;">
                </div>
            </div>

            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4 text-primary"><i class="fas fa-sliders"></i> حدود النظام (Limits)</h5>
            <div class="form-grid mb-4 bg-light p-3 rounded border">
                <div class="form-group mb-0">
                    <label class="form-label">الخطة (Plan)</label>
                    <select name="subscription_plan" class="form-control fw-bold">
                        <option value="basic" <?php echo $company->subscription_plan == 'basic' ? 'selected' : ''; ?>>أساسية (Basic)</option>
                        <option value="premium" <?php echo $company->subscription_plan == 'premium' ? 'selected' : ''; ?>>متقدمة (Premium)</option>
                        <option value="enterprise" <?php echo $company->subscription_plan == 'enterprise' ? 'selected' : ''; ?>>شركات كبرى (Enterprise)</option>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label text-danger">تاريخ نهاية الاشتراك</label>
                    <input type="date" name="subscription_end" class="form-control fw-bold text-danger" value="<?php echo $company->subscription_end; ?>" required>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">الحد الأقصى للمستخدمين</label>
                    <input type="number" name="max_users" class="form-control font-monospace fw-bold" value="<?php echo $company->max_users; ?>" required>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">الحد الأقصى للفروع/المستودعات</label>
                    <input type="number" name="max_branches" class="form-control font-monospace fw-bold" value="<?php echo $company->max_branches; ?>" required>
                </div>
                <input type="hidden" name="status" value="<?php echo $company->status; ?>">
            </div>

            <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4 text-success"><i class="fas fa-toggle-on"></i> الموديولات المتاحة للشركة</h5>
            <div class="form-grid mb-4" style="grid-template-columns: repeat(3, 1fr);">
                
                <label class="d-flex align-items-center gap-2 p-3 border rounded cursor-pointer <?php echo moduleClass('pos', $activeModules); ?>">
                    <input type="checkbox" name="modules[]" value="pos" <?php echo isModuleActive('pos', $activeModules); ?> style="width:18px;height:18px;">
                    <span class="fw-bold text-dark">نقطة البيع (POS)</span>
                </label>

                <label class="d-flex align-items-center gap-2 p-3 border rounded cursor-pointer <?php echo moduleClass('inventory', $activeModules); ?>">
                    <input type="checkbox" name="modules[]" value="inventory" <?php echo isModuleActive('inventory', $activeModules); ?> style="width:18px;height:18px;">
                    <span class="fw-bold text-dark">المخزون والمستودعات</span>
                </label>

                <label class="d-flex align-items-center gap-2 p-3 border rounded cursor-pointer <?php echo moduleClass('accounting', $activeModules); ?>">
                    <input type="checkbox" name="modules[]" value="accounting" <?php echo isModuleActive('accounting', $activeModules); ?> style="width:18px;height:18px;">
                    <span class="fw-bold text-dark">المحاسبة والقيود</span>
                </label>

                <label class="d-flex align-items-center gap-2 p-3 border rounded cursor-pointer <?php echo moduleClass('hr', $activeModules); ?>">
                    <input type="checkbox" name="modules[]" value="hr" <?php echo isModuleActive('hr', $activeModules); ?> style="width:18px;height:18px;">
                    <span class="fw-bold text-dark">الموارد البشرية (HR)</span>
                </label>

                <label class="d-flex align-items-center gap-2 p-3 border rounded cursor-pointer <?php echo moduleClass('projects', $activeModules); ?>">
                    <input type="checkbox" name="modules[]" value="projects" <?php echo isModuleActive('projects', $activeModules); ?> style="width:18px;height:18px;">
                    <span class="fw-bold text-dark">إدارة المشاريع</span>
                </label>

                <label class="d-flex align-items-center gap-2 p-3 border rounded cursor-pointer <?php echo moduleClass('crm', $activeModules); ?>">
                    <input type="checkbox" name="modules[]" value="crm" <?php echo isModuleActive('crm', $activeModules); ?> style="width:18px;height:18px;">
                    <span class="fw-bold text-dark">نظام العملاء (CRM)</span>
                </label>

            </div>

        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ الإعدادات</button>
            <a href="<?php echo URLROOT; ?>/superadmin/dashboard" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>