<?php
// المسار: app/views/users/create.php
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-shield text-primary"></i> إنشاء حساب مستخدم جديد</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/user/create" method="POST">
        <div class="card-body">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">الاسم الكامل <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="مثال: محمد عبدالله">
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني (للدخول) <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control font-monospace" required style="direction:ltr; text-align:right;" placeholder="user@company.com">
                </div>

                <div class="form-group">
                    <label class="form-label">كلمة المرور الافتراضية <span class="required">*</span></label>
                    <input type="password" name="password" class="form-control font-monospace" required style="direction:ltr; text-align:right;" placeholder="••••••••">
                    <small class="text-muted"><i class="fas fa-lock"></i> سيتم تشفير كلمة المرور بقوة 256-bit.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الجوال</label>
                    <input type="text" name="phone" class="form-control font-monospace" style="direction:ltr; text-align:right;" placeholder="05XXXXXXXX">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">الصلاحية والدور <span class="required">*</span></label>
                    <select name="role" class="form-control" required>
                        <option value="employee">موظف / عارض (صلاحيات محدودة للرؤية فقط)</option>
                        <option value="editor">محرر بيانات (صلاحيات الإضافة والتعديل)</option>
                        <option value="manager">مدير قسم (صلاحيات متقدمة مع اعتماد الطلبات)</option>
                        <option value="admin">مدير عام (صلاحيات مطلقة وحذف)</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> إنشاء الحساب</button>
            <a href="<?php echo URLROOT; ?>/user/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>