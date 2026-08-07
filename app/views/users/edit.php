<?php
// المسار: app/views/users/edit.php
$user = $user ?? ($data['user'] ?? null);
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-pen text-accent"></i> تعديل حساب المستخدم: <?php echo htmlspecialchars($user->name); ?></h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/user/edit/<?php echo $user->id; ?>" method="POST">
        <div class="card-body">
            <div class="form-grid">
                
                <div class="form-group full-width">
                    <label class="form-label">الاسم الكامل <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user->name); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني (تسجيل الدخول) <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control font-monospace" value="<?php echo htmlspecialchars($user->email); ?>" required style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الجوال</label>
                    <input type="text" name="phone" class="form-control font-monospace" value="<?php echo htmlspecialchars($user->phone ?? ''); ?>" style="direction:ltr; text-align:right;">
                </div>

                <div class="form-group">
                    <label class="form-label">الدور والصلاحيات <span class="required">*</span></label>
                    <select name="role" class="form-control" required <?php echo $user->id === Session::getUserId() ? 'disabled title="لا يمكنك تغيير صلاحياتك بنفسك"' : ''; ?>>
                        <option value="admin" <?php echo $user->role === 'admin' ? 'selected' : ''; ?>>مدير عام (Admin)</option>
                        <option value="manager" <?php echo $user->role === 'manager' ? 'selected' : ''; ?>>مدير قسم (Manager)</option>
                        <option value="editor" <?php echo $user->role === 'editor' ? 'selected' : ''; ?>>محرر بيانات (Editor)</option>
                        <option value="employee" <?php echo $user->role === 'employee' ? 'selected' : ''; ?>>موظف / عارض (Employee)</option>
                    </select>
                    <?php if($user->id === Session::getUserId()): ?>
                        <small class="text-danger mt-1">لا يمكنك تعديل دورك (صلاحيتك) من داخل حسابك الحالي.</small>
                    <?php endif; ?>
                </div>

                <div class="form-group full-width">
                    <label class="form-label">كلمة المرور الجديدة (اختياري)</label>
                    <input type="password" name="password" class="form-control font-monospace" placeholder="اتركه فارغاً للاحتفاظ بالباسورد القديم" style="direction:ltr; text-align:right;">
                    <small class="text-muted"><i class="fas fa-info-circle"></i> في حال رغبت بتغيير كلمة المرور، اكتبها هنا. وإلا اترك الحقل فارغاً.</small>
                </div>

            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> تحديث الحساب</button>
            <a href="<?php echo URLROOT; ?>/user/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>