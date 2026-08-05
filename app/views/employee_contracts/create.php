<?php
// app/views/employee_contracts/create.php
$pageTitle = $data['title'] ?? 'إبرام عقد جديد';
$employees = $data['employees'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'employeeContract/index';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | ERP Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
        body { font-family: 'Cairo', sans-serif; background: var(--page-bg); color: var(--text-body); min-height: 100vh; overflow-x: hidden; }

        .sidebar { position: fixed; top: 0; right: 0; width: var(--sidebar-w); height: 100vh; background: linear-gradient(180deg, #0f172a 0%, #1a2332 100%); z-index: 100; display: flex; flex-direction: column; transition: transform 0.3s ease; border-left: 1px solid rgba(255,255,255,0.05); }
        .sidebar-brand { padding: 24px 24px 20px; display: flex; align-items: center; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar-brand .s-logo { width: 42px; height: 42px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #fff; flex-shrink: 0; box-shadow: 0 4px 15px rgba(20,184,166,0.25); }
        .sidebar-brand .s-text { display: flex; flex-direction: column; }
        .sidebar-brand .s-name { font-size: 17px; font-weight: 800; color: #f8fafc; letter-spacing: -0.3px; }
        .sidebar-brand .s-name span { color: var(--primary); }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-section-title { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; padding: 12px 14px 8px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: var(--radius-sm); color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s ease; position: relative; }
        .nav-link i { width: 20px; text-align: center; font-size: 15px; transition: color 0.2s; }
        .nav-link:hover { background: #1e293b; color: #e2e8f0; }
        .nav-link.active { background: rgba(20, 184, 166, 0.1); color: var(--primary); font-weight: 600; }
        .nav-link.active::before { content: ''; position: absolute; right: -12px; top: 50%; transform: translateY(-50%); width: 3px; height: 24px; background: var(--primary); border-radius: 0 4px 4px 0; }
        .sidebar-user { padding: 16px 20px; border-top: 1px solid rgba(255, 255, 255, 0.06); display: flex; align-items: center; gap: 12px; }
        .sidebar-user .su-avatar { width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 14px; flex-shrink: 0; }
        .sidebar-user .su-info { flex: 1; min-width: 0; }
        .sidebar-user .su-name { font-size: 13px; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user .su-role { font-size: 11px; color: var(--text-muted); }
        .sidebar-user .su-logout { color: var(--text-muted); font-size: 14px; padding: 6px; border-radius: 8px; transition: all 0.2s; text-decoration: none; margin-right: auto;}
        .sidebar-user .su-logout:hover { color: var(--danger); background: rgba(239, 68, 68, 0.1); }

        .main-content { margin-right: var(--sidebar-w); min-height: 100vh; transition: margin 0.3s ease; }
        .topbar { height: var(--topbar-h); background: var(--card-bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 50; }
        .page-title { font-size: 18px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-muted); margin-top: 2px; }
        .breadcrumb a { color: var(--text-muted); text-decoration: none; transition: color 0.2s; }
        .breadcrumb a:hover { color: var(--primary); }
        .mobile-menu-btn { display: none; }
        .page-body { padding: 28px 32px 40px; max-width: 900px; margin: 0 auto;}

        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        
        .flash-msg { padding: 14px 20px; border-radius: var(--radius-sm); display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; margin-bottom: 24px; border: 1px solid transparent; animation: fadeUp 0.4s ease both;}
        .flash-msg.flash-error { background: var(--danger-light); color: #dc2626; border-color: #fecaca; }

        .form-header-card { background: linear-gradient(135deg, var(--purple) 0%, #7c3aed 100%); border-radius: var(--radius); padding: 28px 32px; color: #fff; margin-bottom: 24px; position: relative; overflow: hidden; animation: fadeUp 0.5s ease both; }
        .form-header-card::before { content: ''; position: absolute; width: 250px; height: 250px; background: rgba(255,255,255,0.08); border-radius: 50%; top: -100px; left: -50px; pointer-events: none;}
        .form-header-card h2 { font-size: 20px; font-weight: 700; margin-bottom: 4px; position: relative; z-index: 2; display: flex; align-items: center; gap: 10px;}
        .form-header-card p { font-size: 13px; opacity: 0.9; position: relative; z-index: 2; }

        .form-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; animation: fadeUp 0.5s ease 0.1s both; }
        .form-section { padding: 28px 32px; border-bottom: 1px solid var(--border); }
        .form-section:last-of-type { border-bottom: none; }
        
        .form-section-title { display: flex; align-items: center; gap: 10px; font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 24px; }
        .form-section-title .fst-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; background: var(--purple-light); color: var(--purple); }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-group.full { grid-column: 1 / -1; }
        .form-label { font-size: 13px; font-weight: 600; color: var(--text-body); margin-bottom: 8px; display: flex; align-items: center; gap: 4px; }
        .form-label .req { color: var(--danger); font-size: 14px; }
        .form-input { padding: 12px 16px; border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 14px; background: var(--card-bg); color: var(--text-dark); outline: none; transition: all 0.25s; }
        .form-input:focus { border-color: var(--purple); box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.08); }
        select.form-input { appearance: none; cursor: pointer; padding-left: 36px; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: left 14px center; }

        .salary-hint { font-size: 12px; color: var(--text-muted); background: var(--page-bg); padding: 8px 12px; border-radius: 8px; margin-top: 8px; display: none; align-items: center; justify-content: space-between; font-weight: 600; border: 1px dashed var(--border);}
        .salary-hint .btn-sync { background: var(--purple); color:#fff; border:none; padding:4px 8px; border-radius:4px; font-family:'Cairo'; font-size:11px; cursor:pointer;}

        .form-actions { padding: 24px 32px; display: flex; align-items: center; justify-content: flex-start; gap: 12px; background: #f8fafc; border-top: 1px solid var(--border); }
        .btn-submit { display: inline-flex; align-items: center; gap: 8px; padding: 12px 32px; background: linear-gradient(135deg, var(--purple), #7c3aed); color: #fff; border: none; border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.25s; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.25); }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(139, 92, 246, 0.35); }
        .btn-cancel { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: transparent; color: var(--text-body); border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .btn-cancel:hover { background: var(--page-bg); }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); } .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text-body); font-size: 16px; cursor: pointer;}
            .page-body { padding: 20px 16px; } .topbar { padding: 0 16px; }
            .form-grid { grid-template-columns: 1fr; }
        }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; backdrop-filter: blur(2px);}
        .sidebar-overlay.show { display: block; }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="s-logo"><i class="fas fa-cubes"></i></div>
            <div class="s-text"><span class="s-name">ERP <span>Pro</span></span></div>
        </div>
        <?php if(class_exists('Layout')) echo Layout::renderSidebar($currentUrl); ?>
        <div class="sidebar-user">
            <div class="su-avatar"><?php echo mb_substr($_SESSION['user_name'] ?? 'م', 0, 2); ?></div>
            <div class="su-info">
                <div class="su-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'مدير النظام'); ?></div>
                <div class="su-role"><?php echo htmlspecialchars($_SESSION['user_role'] ?? 'admin'); ?></div>
            </div>
            <a href="<?php echo URL_ROOT; ?>/auth/logout" class="su-logout" title="تسجيل الخروج"><i class="fas fa-right-from-bracket"></i></a>
        </div>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:16px;">
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="page-title"><?php echo $pageTitle; ?></div>
                    <div class="breadcrumb">
                        <a href="<?php echo URL_ROOT; ?>/dashboard">الرئيسية</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <a href="<?php echo URL_ROOT; ?>/employeeContract/index">الموارد البشرية</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>إبرام عقد</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-body">
            
            <?php if ($flash) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                    <i class="fas fa-circle-xmark"></i>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <div class="form-header-card">
                <h2><i class="fas fa-file-contract"></i> توثيق عقد وظيفي جديد</h2>
                <p>يتم تسجيل العقد لحفظ الحقوق وتوثيق فترة العمل والراتب المعتمد رسمياً.</p>
            </div>

            <div class="form-card">
                <form action="<?php echo URL_ROOT; ?>/employeeContract/create" method="POST" id="contractForm" novalidate>
                    
                    <div class="form-section">
                        <div class="form-section-title"><span class="fst-icon"><i class="fas fa-user-tie"></i></span> أطراف وبيانات العقد</div>
                        <div class="form-grid">
                            <div class="form-group full">
                                <label class="form-label">الموظف (الطرف الثاني) <span class="req">*</span></label>
                                <select name="employee_id" id="empSelect" class="form-input" required>
                                    <option value="">-- يرجى اختيار الموظف --</option>
                                    <?php foreach ($employees as $emp) : ?>
                                        <option value="<?php echo $emp->id; ?>" data-salary="<?php echo $emp->salary; ?>" data-pos="<?php echo htmlspecialchars($emp->position); ?>">
                                            <?php echo htmlspecialchars($emp->name); ?> — <?php echo htmlspecialchars($emp->position ?? 'بدون مسمى'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="salaryHint" class="salary-hint">
                                    <div>الراتب المسجل في ملف الموظف: <strong id="salaryVal" style="font-family:monospace;direction:ltr;">0</strong> ر.س</div>
                                    <button type="button" class="btn-sync" onclick="syncSalary()">استخدام هذا الراتب</button>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">عنوان ونوع العقد <span class="req">*</span></label>
                                <input type="text" name="title" class="form-input" value="عقد عمل محدد المدة" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">رقم العقد المرجعي</label>
                                <input type="text" name="contract_number" class="form-input" placeholder="يُترك فارغاً للتوليد التلقائي" style="direction:ltr;text-align:right;font-family:monospace;">
                            </div>

                            <div class="form-group">
                                <label class="form-label">تاريخ بداية العقد <span class="req">*</span></label>
                                <input type="date" name="start_date" id="startDate" class="form-input" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">تاريخ نهاية العقد <span class="req">*</span></label>
                                <input type="date" name="end_date" id="endDate" class="form-input" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">قيمة العقد (الراتب الأساسي) <span class="req">*</span></label>
                                <input type="number" name="value" id="contractValue" class="form-input" step="0.01" min="0" placeholder="0.00" required style="direction:ltr; text-align:right;">
                            </div>

                            <div class="form-group">
                                <label class="form-label">حالة العقد</label>
                                <select name="status" class="form-input">
                                    <option value="active">نشط (ساري المفعول)</option>
                                    <option value="draft">مسودة (قيد الإعداد)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="btnSubmit"><i class="fas fa-file-signature"></i> توثيق العقد</button>
                        <a href="<?php echo URL_ROOT; ?>/employeeContract/index" class="btn-cancel">إلغاء</a>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        const empSelect = document.getElementById('empSelect');
        const salaryHint = document.getElementById('salaryHint');
        const salaryVal = document.getElementById('salaryVal');
        const contractValue = document.getElementById('contractValue');
        
        let currentEmpSalary = 0;

        // إظهار الراتب عند اختيار الموظف
        empSelect.addEventListener('change', function() {
            const selectedOpt = this.options[this.selectedIndex];
            if(selectedOpt.value) {
                currentEmpSalary = parseFloat(selectedOpt.dataset.salary) || 0;
                salaryVal.textContent = currentEmpSalary.toLocaleString('ar-SA', {minimumFractionDigits: 2});
                salaryHint.style.display = 'flex';
            } else {
                currentEmpSalary = 0;
                salaryHint.style.display = 'none';
            }
        });

        // دالة سحب الراتب لحقل الإدخال
        function syncSalary() {
            if (currentEmpSalary > 0) {
                contractValue.value = currentEmpSalary;
                contractValue.style.borderColor = 'var(--success)';
                setTimeout(() => contractValue.style.borderColor = '', 1000);
            }
        }

        // التحقق قبل الإرسال
        const form = document.getElementById('contractForm');
        const btnSubmit = document.getElementById('btnSubmit');
        const startDate = document.getElementById('startDate');
        const endDate = document.getElementById('endDate');

        form.addEventListener('submit', function(e) {
            let valid = true;
            
            form.querySelectorAll('.form-input').forEach(el => el.style.borderColor = '');

            if (!empSelect.value) { empSelect.style.borderColor = 'var(--danger)'; valid = false; }
            if (!startDate.value) { startDate.style.borderColor = 'var(--danger)'; valid = false; }
            if (!endDate.value) { endDate.style.borderColor = 'var(--danger)'; valid = false; }
            if (!contractValue.value || parseFloat(contractValue.value) <= 0) { contractValue.style.borderColor = 'var(--danger)'; valid = false; }
            
            if (startDate.value && endDate.value) {
                if (new Date(endDate.value) <= new Date(startDate.value)) {
                    alert('تاريخ النهاية يجب أن يكون بعد تاريخ البداية.');
                    endDate.style.borderColor = 'var(--danger)';
                    valid = false;
                }
            }
            
            if (!valid) {
                e.preventDefault();
            } else {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري التوثيق...';
            }
        });

        // القائمة الجانبية للموبايل
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>