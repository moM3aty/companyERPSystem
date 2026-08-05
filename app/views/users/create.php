<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden; max-width:800px; margin:0 auto;">
    
    <div style="padding:24px 30px; border-bottom:1px solid var(--border); background:#f8fafc;">
        <h3 style="margin:0; font-size:16px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-user-shield" style="color:var(--primary);"></i> بيانات المستخدم الجديد
        </h3>
    </div>

    <form action="<?php echo URL_ROOT; ?>/user/create" method="POST">
        <div style="padding:30px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            
            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">الاسم الكامل <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;" placeholder="مثال: محمد عبدالله">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">البريد الإلكتروني (للدخول) <span style="color:var(--danger);">*</span></label>
                <input type="email" name="email" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; direction:ltr; text-align:right;" placeholder="user@company.com">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">كلمة المرور الافتراضية <span style="color:var(--danger);">*</span></label>
                <input type="password" name="password" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; direction:ltr; text-align:right;" placeholder="••••••••">
                <span style="font-size:11px; color:var(--text-muted);">سيتم تشفير كلمة المرور فور حفظها.</span>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">رقم الجوال</label>
                <input type="text" name="phone" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; direction:ltr; text-align:right;" placeholder="05XXXXXXXX">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px; grid-column:1/-1;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">الصلاحية والدور <span style="color:var(--danger);">*</span></label>
                <select name="role" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;">
                    <option value="viewer">عارض (صلاحيات محدودة للرؤية فقط)</option>
                    <option value="editor">محرر (صلاحيات الإضافة والتعديل)</option>
                    <option value="admin">مدير عام (صلاحيات مطلقة للنظام)</option>
                </select>
            </div>

        </div>
        
        <div style="padding:20px 30px; background:#f8fafc; border-top:1px solid var(--border); display:flex; gap:12px;">
            <button type="submit" style="padding:10px 24px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:700; cursor:pointer;"><i class="fas fa-save"></i> إنشاء الحساب</button>
            <a href="<?php echo URL_ROOT; ?>/user/index" style="padding:10px 24px; background:transparent; border:1px solid var(--border); color:var(--text-body); border-radius:8px; text-decoration:none; font-weight:600;">إلغاء</a>
        </div>
    </form>
</div>