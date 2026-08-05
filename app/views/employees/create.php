<?php
// app/views/employees/create.php
 $flash = $data['flash'] ?? null;
 $departments = $data['departments'] ?? [];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* نفس التنسيقات السابقة ... */
        :root {
            --primary: #14b8a6; --primary-dark: #0d9488; --primary-light: #ccfbf1;
            --accent: #f59e0b; --accent-light: #fef3c7;
            --success: #22c55e; --success-light: #dcfce7;
            --danger: #ef4444; --danger-light: #fee2e2;
            --info: #06b6d4; --info-light: #cffafe;
            --purple: #8b5cf6; --purple-light: #ede9fe;
            --sidebar-w: 272px; --topbar-h: 68px;
            --page-bg: #f1f5f9; --card-bg: #ffffff;
            --text-dark: #0f172a; --text-body: #475569; --text-muted: #94a3b8;
            --border: #e2e8f0; --radius: 14px; --radius-sm: 10px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06); --shadow-md: 0 4px 20px rgba(0,0,0,0.08);
        }
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; background: var(--page-bg); color: var(--text-body); min-height: 100vh; }
        /* ... باقي التنسيقات (نفس الملف الأصلي) ... */
    </style>
</head>
<body>
    <!-- Sidebar و Topbar (نفس الموجود في الملف الأصلي) -->

    <div class="page-body">
        <?php if ($flash) : ?>
            <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        <?php endif; ?>

        <div class="form-header-card">
            <h2><i class="fas fa-user-plus" style="margin-left:8px;"></i> إضافة موظف جديد</h2>
            <p>أدخل بيانات الموظف بدقة — ستُستخدم في الرواتب والتقارير</p>
        </div>

        <div class="form-card">
            <form action="<?php echo URL_ROOT; ?>/employee/create" method="POST" id="empForm" novalidate>
                <div class="form-section">
                    <div class="form-section-title"><span class="fst-icon fst-teal"><i class="fas fa-user"></i></span> البيانات الشخصية</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">اسم الموظف الكامل <span class="req">*</span></label>
                            <input type="text" name="name" class="form-input" id="empName" placeholder="مثال: أحمد محمد علي" required>
                            <div class="form-hint"><i class="fas fa-info-circle"></i> الاسم كما سيظهر في جميع تقارير النظام</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">البريد الإلكتروني <span class="req">*</span></label>
                            <input type="email" name="email" class="form-input" id="empEmail" placeholder="ahmed@company.com" required style="direction:ltr;text-align:right;">
                            <div class="form-hint"><i class="fas fa-envelope"></i> سيُستخدم لإرسال الإشعارات</div>
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <div class="form-section-title"><span class="fst-icon fst-amber"><i class="fas fa-briefcase"></i></span> الوظيفة والقسم</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">المسمى الوظيفي</label>
                            <input type="text" name="position" class="form-input" id="empPos" placeholder="مثال: مهندس برمجيات">
                            <div class="form-hint"><i class="fas fa-info-circle"></i> المسمى الوظيفي الرسمي</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">القسم</label>
                            <select name="department_id" class="form-input" id="empDept">
                                <option value="">-- اختر القسم --</option>
                                <?php foreach($departments as $dept) : ?>
                                    <option value="<?php echo $dept->id; ?>"><?php echo htmlspecialchars($dept->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-hint"><i class="fas fa-building"></i> القسم الإداري التابع له</div>
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <div class="form-section-title"><span class="fst-icon fst-purple"><i class="fas fa-money-check-dollar"></i></span> بيانات مالية وتواصل</div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-input" id="empPhone" placeholder="05xxxxxxxx" style="direction:ltr;text-align:right;">
                            <div class="form-hint"><i class="fas fa-phone"></i> رقم جوال للتواصل</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">الراتب الشهري (ر.س) <span class="req">*</span></label>
                            <input type="number" step="0.01" name="salary" class="form-input" id="empSalary" placeholder="0.00" required style="direction:ltr;text-align:right;">
                            <div class="form-hint"><i class="fas fa-coins"></i> الراتب الأساسي الشهري قبل أي خصومات</div>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-submit" id="btnSubmit"><i class="fas fa-check-circle"></i> حفظ الموظف</button>
                    <a href="<?php echo URL_ROOT; ?>/employee/index" class="btn-cancel"><i class="fas fa-arrow-right"></i> رجوع للقائمة</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('empForm');
        const btnSubmit = document.getElementById('btnSubmit');
        form.addEventListener('submit', function(e) {
            let valid = true;
            const name = document.getElementById('empName');
            const email = document.getElementById('empEmail');
            const salary = document.getElementById('empSalary');
            form.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
            form.querySelectorAll('.form-error').forEach(el => el.remove());
            if (!name.value.trim()) { name.classList.add('has-error'); name.parentNode.appendChild(mkErr('اسم الموظف مطلوب')); valid = false; }
            if (!email.value.trim()) { email.classList.add('has-error'); email.parentNode.appendChild(mkErr('البريد الإلكتروني مطلوب')); valid = false; }
            else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) { email.classList.add('has-error'); email.parentNode.appendChild(mkErr('صيغة البريد غير صحيحة')); valid = false; }
            if (!salary.value || parseFloat(salary.value) <= 0) { salary.classList.add('has-error'); salary.parentNode.appendChild(mkErr('يرجى إدخال راتب صحيح أكبر من صفر')); valid = false; }
            if (!valid) { e.preventDefault(); const f = form.querySelector('.has-error'); if (f) f.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
            else { btnSubmit.disabled = true; btnSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري الحفظ...'; }
        });
        function mkErr(m) { const d = document.createElement('div'); d.className = 'form-error'; d.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + m; return d; }
        // كود الموبايل (نفس الموجود في الملف الأصلي)
    </script>
</body>
</html>