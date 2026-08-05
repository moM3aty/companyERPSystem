<?php
// app/views/employees/edit.php
$pageTitle = $data['title'] ?? 'تعديل بيانات موظف';
$emp = $data['employee'] ?? null;
$departments = $data['departments'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'employee/index';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> — <?php echo htmlspecialchars($emp->name); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #14b8a6;
            --primary-dark: #0d9488;
            --primary-light: #ccfbf1;
            --accent: #f59e0b;
            --accent-light: #fef3c7;
            --success: #22c55e;
            --success-light: #dcfce7;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --info: #06b6d4;
            --info-light: #cffafe;
            --purple: #8b5cf6;
            --purple-light: #ede9fe;
            --sidebar-w: 272px;
            --topbar-h: 68px;
            --page-bg: #f1f5f9;
            --card-bg: #ffffff;
            --text-dark: #0f172a;
            --text-body: #475569;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --radius: 14px;
            --radius-sm: 10px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--page-bg);
            color: var(--text-body);
            min-height: 100vh;
        }

        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(180deg, #0f172a 0%, #1a2332 100%);
            z-index: 100;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            border-left: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-brand {
            padding: 24px 24px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar-brand .s-logo {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar-brand .s-text {
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand .s-name {
            font-size: 17px;
            font-weight: 800;
            color: #f8fafc;
        }

        .sidebar-brand .s-name span {
            color: var(--primary);
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }

        .nav-section-title {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 12px 14px 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: var(--radius-sm);
            color: #94a3b8;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 15px;
        }

        .nav-link:hover {
            background: #1e293b;
            color: #e2e8f0;
        }

        .nav-link.active {
            background: rgba(20, 184, 166, 0.1);
            color: var(--primary);
            font-weight: 600;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            right: -12px;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 24px;
            background: var(--primary);
            border-radius: 0 4px 4px 0;
        }

        .sidebar-user {
            padding: 16px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-user .su-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            flex-shrink: 0;
        }

        .sidebar-user .su-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-user .su-name {
            font-size: 13px;
            font-weight: 600;
            color: #e2e8f0;
        }

        .sidebar-user .su-role {
            font-size: 11px;
            color: var(--text-muted);
        }

        .sidebar-user .su-logout {
            color: var(--text-muted);
            font-size: 14px;
            padding: 6px;
            border-radius: 8px;
            transition: all 0.2s;
            text-decoration: none;
            margin-right: auto;
        }

        .sidebar-user .su-logout:hover {
            color: var(--danger);
            background: rgba(239, 68, 68, 0.1);
        }

        .main-content {
            margin-right: var(--sidebar-w);
            min-height: 100vh;
            transition: margin 0.3s ease;
        }

        .topbar {
            height: var(--topbar-h);
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .page-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .breadcrumb a {
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .breadcrumb a:hover {
            color: var(--primary);
        }

        .mobile-menu-btn {
            display: none;
        }

        .page-body {
            padding: 28px 32px 40px;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .flash-msg {
            padding: 14px 20px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 24px;
            animation: fadeUp 0.4s ease both;
            border: 1px solid transparent;
        }

        .flash-msg.flash-error {
            background: var(--danger-light);
            color: #dc2626;
            border-color: #fecaca;
        }

        /* رأس شبكي: العنوان و بطاقة المعاينة */
        .edit-layout {
            display: grid;
            grid-template-columns: 1fr 300px;
            gap: 24px;
            margin-bottom: 24px;
            animation: fadeUp 0.5s ease both;
        }

        .form-header-card {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: var(--radius);
            padding: 32px;
            color: #fff;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .form-header-card::before {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: rgba(20, 184, 166, 0.1);
            border-radius: 50%;
            top: -100px;
            left: -50px;
        }

        .fhc-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .fhc-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--primary-light);
        }

        .form-header-card h2 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .form-header-card p {
            font-size: 13px;
            color: #94a3b8;
        }

        /* بطاقة الموظف المصغرة */
        .emp-preview {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            padding: 24px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .ep-avatar {
            width: 72px;
            height: 72px;
            border-radius: 18px;
            background: linear-gradient(135deg, var(--info), #0891b2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 14px;
            box-shadow: 0 6px 20px rgba(6, 182, 212, 0.25);
        }

        .ep-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .ep-pos {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 16px;
        }

        .ep-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            width: 100%;
        }

        .eps-item {
            background: var(--page-bg);
            border-radius: 8px;
            padding: 10px 8px;
        }

        .eps-val {
            font-size: 14px;
            font-weight: 800;
            color: var(--text-dark);
            font-variant-numeric: tabular-nums;
        }

        .eps-label {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .form-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            animation: fadeUp 0.5s ease 0.15s both;
        }

        .form-section {
            padding: 28px 32px;
            border-bottom: 1px solid var(--border);
        }

        .form-section:last-of-type {
            border-bottom: none;
        }

        .form-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 24px;
        }

        .form-section-title .fst-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .fst-icon.fst-teal {
            background: var(--primary-light);
            color: var(--primary-dark);
        }

        .fst-icon.fst-amber {
            background: var(--accent-light);
            color: var(--accent);
        }

        .fst-icon.fst-purple {
            background: var(--purple-light);
            color: var(--purple);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-body);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-label .req {
            color: var(--danger);
            font-size: 14px;
        }

        .form-input {
            padding: 12px 16px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif;
            font-size: 14px;
            background: var(--card-bg);
            color: var(--text-dark);
            outline: none;
            transition: all 0.25s;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.08);
        }

        .form-input.has-error {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.08);
        }

        .form-hint {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-error {
            font-size: 11px;
            color: var(--danger);
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
            animation: fadeUp 0.3s ease;
        }

        select.form-input {
            appearance: none;
            cursor: pointer;
            padding-left: 36px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: left 14px center;
        }

        .form-actions {
            padding: 24px 32px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            background: #f8fafc;
            border-top: 1px solid var(--border);
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 32px;
            background: linear-gradient(135deg, var(--accent), #d97706);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.25);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.35);
        }

        .btn-cancel {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: transparent;
            color: var(--text-body);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: var(--page-bg);
            border-color: var(--text-muted);
        }

        @media (max-width: 992px) {
            .edit-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-right: 0;
            }

            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                border-radius: 10px;
                border: 1px solid var(--border);
                background: transparent;
                color: var(--text-body);
                font-size: 16px;
                cursor: pointer;
            }

            .page-body {
                padding: 20px 16px;
            }

            .topbar {
                padding: 0 16px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-section {
                padding: 24px 20px;
            }

            .form-actions {
                padding: 20px;
            }

            .form-header-card {
                padding: 24px 20px;
            }
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
            backdrop-filter: blur(2px);
        }

        .sidebar-overlay.show {
            display: block;
        }
    </style>
</head>

<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="s-logo"><i class="fas fa-cubes"></i></div>
            <div class="s-text"><span class="s-name">ERP <span>Pro</span></span></div>
        </div>
        <?php if (class_exists('Layout')) echo Layout::renderSidebar($currentUrl); ?>
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
                        <a href="<?php echo URL_ROOT; ?>/employee/index">إدارة الموظفين</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>تعديل الموظف</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-body">

            <?php if ($flash) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                    <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <div class="edit-layout">
                <div class="form-header-card">
                    <div class="fhc-content">
                        <div class="fhc-icon"><i class="fas fa-user-pen"></i></div>
                        <div>
                            <h2>تعديل بيانات الموظف</h2>
                            <p>قم بتحديث البيانات اللازمة ثم احفظ التغييرات</p>
                        </div>
                    </div>
                </div>

                <!-- بطاقة المعاينة -->
                <div class="emp-preview">
                    <div class="ep-avatar"><?php echo mb_substr($emp->name, 0, 2); ?></div>
                    <div class="ep-name"><?php echo htmlspecialchars($emp->name); ?></div>
                    <div class="ep-pos"><?php echo htmlspecialchars($emp->position ?? 'بدون مسمى وظيفي'); ?></div>
                    <div class="ep-stats">
                        <div class="eps-item">
                            <div class="eps-val"><?php echo number_format($emp->salary, 0); ?></div>
                            <div class="eps-label">الراتب (ر.س)</div>
                        </div>
                        <div class="eps-item">
                            <div class="eps-val">#<?php echo $emp->id; ?></div>
                            <div class="eps-label">الرقم الوظيفي</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-card">
                <form action="<?php echo URL_ROOT; ?>/employee/edit/<?php echo $emp->id; ?>" method="POST" id="empForm" novalidate>

                    <div class="form-section">
                        <div class="form-section-title"><span class="fst-icon fst-teal"><i class="fas fa-user"></i></span> البيانات الشخصية</div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">الاسم الكامل <span class="req">*</span></label>
                                <input type="text" name="name" class="form-input" id="empName" value="<?php echo htmlspecialchars($emp->name); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">البريد الإلكتروني <span class="req">*</span></label>
                                <input type="email" name="email" class="form-input" id="empEmail" value="<?php echo htmlspecialchars($emp->email); ?>" required style="direction:ltr;text-align:right;">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title"><span class="fst-icon fst-amber"><i class="fas fa-briefcase"></i></span> التسكين الإداري</div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">المسمى الوظيفي</label>
                                <input type="text" name="position" class="form-input" value="<?php echo htmlspecialchars($emp->position ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label class="form-label">القسم التابع له</label>
                                <select name="department_id" class="form-input">
                                    <option value="">-- اختر القسم --</option>
                                    <?php foreach ($departments as $dept) : ?>
                                        <option value="<?php echo $dept->id; ?>" <?php echo ($emp->department_id == $dept->id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dept->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title"><span class="fst-icon fst-purple"><i class="fas fa-money-check-dollar"></i></span> بيانات الراتب والتواصل</div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">رقم الهاتف</label>
                                <input type="text" name="phone" class="form-input" value="<?php echo htmlspecialchars($emp->phone ?? ''); ?>" style="direction:ltr;text-align:right;">
                            </div>
                            <div class="form-group">
                                <label class="form-label">الراتب الأساسي (ر.س) <span class="req">*</span></label>
                                <input type="number" step="0.01" name="salary" class="form-input" id="empSalary" value="<?php echo $emp->salary; ?>" required style="direction:ltr;text-align:right;">
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="btnSubmit"><i class="fas fa-save"></i> حفظ التعديلات</button>
                        <a href="<?php echo URL_ROOT; ?>/employee/index" class="btn-cancel">إلغاء</a>
                    </div>
                </form>
            </div>

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

            if (!name.value.trim() || name.value.trim().length < 3) {
                name.classList.add('has-error');
                name.parentNode.appendChild(makeErr('اسم الموظف مطلوب'));
                valid = false;
            }

            if (!email.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                email.classList.add('has-error');
                email.parentNode.appendChild(makeErr('صيغة البريد غير صحيحة'));
                valid = false;
            }

            if (!salary.value || parseFloat(salary.value) <= 0) {
                salary.classList.add('has-error');
                salary.parentNode.appendChild(makeErr('يرجى إدخال راتب صحيح'));
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
                const firstErr = form.querySelector('.has-error');
                if (firstErr) firstErr.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            } else {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري الحفظ...';
            }
        });

        function makeErr(msg) {
            const d = document.createElement('div');
            d.className = 'form-error';
            d.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + msg;
            return d;
        }

        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) {
            menuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
            });
        }
        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('open');
                overlay.classList.remove('show');
            });
        }
    </script>
</body>

</html>