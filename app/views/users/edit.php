<?php
// app/views/users/edit.php
$user = $data['user'] ?? null;
$role = $user->role ?? 'viewer';
?>

<style>
/* نفس التنسيقات الخاصة بالبطاقات في صفحة الإنشاء */
.role-radio { display: none; }
.role-card {
    border: 2px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 20px 15px;
    cursor: pointer;
    transition: all 0.25s ease;
    text-align: center;
    position: relative;
    background: #fff;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
.role-card:hover { border-color: var(--slate-300); background: var(--slate-50); transform: translateY(-2px); }
.role-radio:checked + .role-card { box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1); transform: translateY(-3px); }
.role-radio:checked + .role-card::before { content: '\f058'; font-family: 'Font Awesome 6 Free'; font-weight: 900; position: absolute; top: 12px; right: 12px; font-size: 20px; }
.role-icon { font-size: 28px; margin-bottom: 12px; }
.role-title { font-weight: 800; font-size: 16px; margin-bottom: 4px; color: var(--text-dark); }
.role-code { font-family: 'Fira Code', monospace; font-size: 11px; margin-bottom: 10px; color: var(--slate-500); }
.role-desc { font-size: 12px; color: var(--slate-600); line-height: 1.5; }

/* ألوان الأدوار */
.role-hr .role-icon { color: #a855f7; } .role-radio:checked + .role-hr { border-color: #a855f7; background: #faf5ff; } .role-radio:checked + .role-hr::before { color: #a855f7; }
.role-sales .role-icon { color: #10b981; } .role-radio:checked + .role-sales { border-color: #10b981; background: #ecfdf5; } .role-radio:checked + .role-sales::before { color: #10b981; }
.role-acc .role-icon { color: #3b82f6; } .role-radio:checked + .role-acc { border-color: #3b82f6; background: #eff6ff; } .role-radio:checked + .role-acc::before { color: #3b82f6; }
.role-mgr .role-icon { color: #f59e0b; } .role-radio:checked + .role-mgr { border-color: #f59e0b; background: #fffbeb; } .role-radio:checked + .role-mgr::before { color: #f59e0b; }
.role-admin .role-icon { color: #ef4444; } .role-radio:checked + .role-admin { border-color: #ef4444; background: #fef2f2; } .role-radio:checked + .role-admin::before { color: #ef4444; }
.role-editor .role-icon { color: #0ea5e9; } .role-radio:checked + .role-editor { border-color: #0ea5e9; background: #f0f9ff; } .role-radio:checked + .role-editor::before { color: #0ea5e9; }
.role-viewer .role-icon { color: #64748b; } .role-radio:checked + .role-viewer { border-color: #64748b; background: #f8fafc; } .role-radio:checked + .role-viewer::before { color: #64748b; }

.roles-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-top: 10px; }
</style>

<div class="card" style="max-width: 1000px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-user-edit text-primary"></i> تعديل المستخدم: <?php echo htmlspecialchars($user->name ?? ''); ?></h3>
    </div>

    <?php $flash = Session::getFlash(); if ($flash): ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>" style="margin: 20px 20px 0;">
            <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>
    
    <form action="<?php echo URLROOT; ?>/user/edit/<?php echo $user->id; ?>" method="POST">
        <div class="card-body">
            
            <h5 class="fw-bold mb-3 text-dark border-bottom pb-2">1. البيانات الشخصية</h5>
            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">اسم المستخدم <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user->name ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user->email ?? ''); ?>" required style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group full-width border rounded p-3" style="background:#fef2f2; border-color:#fecaca !important;">
                    <label class="form-label text-danger"><i class="fas fa-lock"></i> تغيير كلمة المرور (اختياري)</label>
                    <input type="password" name="password" class="form-control" placeholder="اتركه فارغاً إذا لم ترد التغيير" style="direction:ltr; text-align:right;">
                    <small class="text-muted d-block mt-1">إذا قمت بكتابة كلمة مرور هنا سيتم تغييرها فوراً للمستخدم.</small>
                </div>
            </div>

            <h5 class="fw-bold mb-2 text-dark border-bottom pb-2 mt-4">2. الصلاحيات وتعيين الدور (RBAC) <span class="required">*</span></h5>
            <div class="roles-grid">
                
                <label>
                    <input type="radio" name="role" value="hr" class="role-radio" <?php echo ($role == 'hr') ? 'checked' : ''; ?>>
                    <div class="role-card role-hr">
                        <i class="fas fa-user-tie role-icon"></i>
                        <div class="role-title">شؤون الموظفين</div>
                        <div class="role-code">[hr]</div>
                        <div class="role-desc">إدارة الموارد البشرية والرواتب والغيابات</div>
                    </div>
                </label>

                <label>
                    <input type="radio" name="role" value="sales_rep" class="role-radio" <?php echo ($role == 'sales_rep') ? 'checked' : ''; ?>>
                    <div class="role-card role-sales">
                        <i class="fas fa-user-tag role-icon"></i>
                        <div class="role-title">مندوب مبيعات</div>
                        <div class="role-code">[sales_rep]</div>
                        <div class="role-desc">إدارة نقاط البيع، العملاء، وعروض الأسعار</div>
                    </div>
                </label>

                <label>
                    <input type="radio" name="role" value="accountant" class="role-radio" <?php echo ($role == 'accountant') ? 'checked' : ''; ?>>
                    <div class="role-card role-acc">
                        <i class="fas fa-file-invoice-dollar role-icon"></i>
                        <div class="role-title">محاسب مالي</div>
                        <div class="role-code">[accountant]</div>
                        <div class="role-desc">إدارة الشؤون المالية، القيود، والمصروفات</div>
                    </div>
                </label>

                <label>
                    <input type="radio" name="role" value="manager" class="role-radio" <?php echo ($role == 'manager') ? 'checked' : ''; ?>>
                    <div class="role-card role-mgr">
                        <i class="fas fa-user-shield role-icon"></i>
                        <div class="role-title">مدير قسم / تشغيل</div>
                        <div class="role-code">[manager]</div>
                        <div class="role-desc">إدارة العمليات، المشاريع، والاعتمادات</div>
                    </div>
                </label>

                <label>
                    <input type="radio" name="role" value="admin" class="role-radio" <?php echo ($role == 'admin') ? 'checked' : ''; ?>>
                    <div class="role-card role-admin">
                        <i class="fas fa-user-cog role-icon"></i>
                        <div class="role-title">مدير المؤسسة</div>
                        <div class="role-code">[admin]</div>
                        <div class="role-desc">صلاحيات مطلقة وتامة داخل الشركة</div>
                    </div>
                </label>

                <label>
                    <input type="radio" name="role" value="editor" class="role-radio" <?php echo ($role == 'editor') ? 'checked' : ''; ?>>
                    <div class="role-card role-editor">
                        <i class="fas fa-pen-to-square role-icon"></i>
                        <div class="role-title">مُدخل بيانات</div>
                        <div class="role-code">[editor]</div>
                        <div class="role-desc">إدخال وتعديل السجلات الأساسية فقط</div>
                    </div>
                </label>

                <label>
                    <input type="radio" name="role" value="viewer" class="role-radio" <?php echo ($role == 'viewer') ? 'checked' : ''; ?>>
                    <div class="role-card role-viewer">
                        <i class="fas fa-eye role-icon"></i>
                        <div class="role-title">مُشاهد (للاطلاع)</div>
                        <div class="role-code">[viewer]</div>
                        <div class="role-desc">قراءة التقارير وعرض البيانات دون تعديل</div>
                    </div>
                </label>

            </div>

        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ وتحديث المستخدم</button>
            <a href="<?php echo URLROOT; ?>/user/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>