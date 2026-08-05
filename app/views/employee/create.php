<?php
// تعريف المتغيرات من مصفوفة البيانات
$departments = $data['departments'] ?? [];
?>
<!-- المسار: app/views/employee/create.php -->

<div style="background:linear-gradient(135deg, var(--primary) 0%, #0d9488 100%); border-radius:12px; padding:28px 32px; color:#fff; margin-bottom:24px; position:relative; overflow:hidden; box-shadow:var(--shadow-sm);">
    <i class="fas fa-user-tie" style="position:absolute; left:-20px; bottom:-30px; font-size:120px; opacity:0.1;"></i>
    <h2 style="margin:0 0 6px; font-size:20px; font-weight:700; position:relative; z-index:2;"><i class="fas fa-user-plus"></i> تسجيل موظف جديد</h2>
    <p style="margin:0; font-size:13px; opacity:0.9; position:relative; z-index:2;">الرجاء إدخال البيانات الوظيفية والمالية بدقة لارتباطها بمسيرات الرواتب.</p>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <form action="<?php echo URL_ROOT; ?>/employee/create" method="POST">
        
        <div style="padding:30px; display:grid; grid-template-columns:1fr 1fr; gap:24px;">
            <div style="grid-column:1/-1; border-bottom:1px solid var(--border); padding-bottom:12px; margin-bottom:8px;">
                <h4 style="margin:0; font-size:14px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;"><i class="fas fa-id-card" style="color:var(--primary);"></i> البيانات الأساسية</h4>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">الاسم الرباعي <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" required style="padding:12px 16px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; font-size:14px; outline:none;" placeholder="مثال: خالد محمد عبدالله">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">البريد الإلكتروني المهني <span style="color:var(--danger);">*</span></label>
                <input type="email" name="email" required style="padding:12px 16px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; font-size:14px; outline:none; direction:ltr; text-align:right;" placeholder="employee@company.com">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">رقم الجوال للتواصل</label>
                <input type="text" name="phone" style="padding:12px 16px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; font-size:14px; outline:none; direction:ltr; text-align:right;" placeholder="05XXXXXXXX">
            </div>

            <div style="grid-column:1/-1; border-bottom:1px solid var(--border); padding-bottom:12px; margin-bottom:8px; margin-top:12px;">
                <h4 style="margin:0; font-size:14px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;"><i class="fas fa-briefcase" style="color:var(--accent);"></i> البيانات الوظيفية والمالية</h4>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">المسمى الوظيفي <span style="color:var(--danger);">*</span></label>
                <input type="text" name="position" required style="padding:12px 16px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; font-size:14px; outline:none;" placeholder="مثال: محاسب مالي">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">القسم / الإدارة</label>
                <select name="department_id" style="padding:12px 16px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; font-size:14px; outline:none; cursor:pointer;">
                    <option value="">-- اختر القسم --</option>
                    <?php foreach ($departments as $dept) : ?>
                        <option value="<?php echo $dept->id; ?>"><?php echo htmlspecialchars($dept->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">الراتب الأساسي (ر.س) <span style="color:var(--danger);">*</span></label>
                <input type="number" name="salary" step="0.01" min="0" required style="padding:12px 16px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; font-size:14px; outline:none; direction:ltr; text-align:right;" placeholder="0.00">
                <span style="font-size:11px; color:var(--text-muted);"><i class="fas fa-info-circle"></i> يستخدم هذا الرقم كأساس في مسيرات الرواتب وتوزيع العُهد.</span>
            </div>

        </div>
        
        <div style="padding:24px 30px; background:#f8fafc; border-top:1px solid var(--border); display:flex; gap:12px;">
            <button type="submit" style="padding:12px 28px; background:linear-gradient(135deg, var(--primary), var(--primary-dark)); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-size:14px; font-weight:700; cursor:pointer; box-shadow:0 4px 12px rgba(20,184,166,0.25); transition:0.2s;"><i class="fas fa-save"></i> حفظ ملف الموظف</button>
            <a href="<?php echo URL_ROOT; ?>/employee/index" style="padding:12px 28px; background:transparent; border:1.5px solid var(--border); color:var(--text-body); border-radius:8px; text-decoration:none; font-weight:600; font-size:14px; transition:0.2s;">إلغاء والتراجع</a>
        </div>
    </form>
</div>

<script>
    // تغيير نمط الحقول عند التركيز لتعزيز تجربة المستخدم (UI/UX)
    document.querySelectorAll('input, select').forEach(el => {
        el.addEventListener('focus', function() { this.style.borderColor = 'var(--primary)'; this.style.boxShadow = '0 0 0 3px rgba(20,184,166,0.1)'; });
        el.addEventListener('blur', function() { this.style.borderColor = 'var(--border)'; this.style.boxShadow = 'none'; });
    });
</script>