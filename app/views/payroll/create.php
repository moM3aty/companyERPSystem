<?php
// app/views/payroll/create.php
$pageTitle = $data['title'] ?? 'إصدار مسير رواتب';
$employees = $data['employees'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'payroll/create';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        body { font-family: 'Cairo', sans-serif; background: var(--page-bg); color: var(--text-body); min-height: 100vh; }

        .sidebar { position: fixed; top: 0; right: 0; width: var(--sidebar-w); height: 100vh; background: linear-gradient(180deg, #0f172a 0%, #1a2332 100%); z-index: 100; display: flex; flex-direction: column; transition: transform 0.3s ease; border-left: 1px solid rgba(255,255,255,0.05); }
        .sidebar-brand { padding: 24px 24px 20px; display: flex; align-items: center; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.06); }
        .sidebar-brand .s-logo { width: 42px; height: 42px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #fff; flex-shrink: 0; }
        .sidebar-brand .s-text { display: flex; flex-direction: column; }
        .sidebar-brand .s-name { font-size: 17px; font-weight: 800; color: #f8fafc; }
        .sidebar-brand .s-name span { color: var(--primary); }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-section-title { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; padding: 12px 14px 8px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: var(--radius-sm); color: #94a3b8; text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s ease; position: relative; }
        .nav-link i { width: 20px; text-align: center; font-size: 15px; }
        .nav-link:hover { background: #1e293b; color: #e2e8f0; }
        .nav-link.active { background: rgba(20, 184, 166, 0.1); color: var(--primary); font-weight: 600; }
        .nav-link.active::before { content: ''; position: absolute; right: -12px; top: 50%; transform: translateY(-50%); width: 3px; height: 24px; background: var(--primary); border-radius: 0 4px 4px 0; }
        .sidebar-user { padding: 16px 20px; border-top: 1px solid rgba(255, 255, 255, 0.06); display: flex; align-items: center; gap: 12px; }
        .sidebar-user .su-avatar { width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 14px; flex-shrink: 0; }
        .sidebar-user .su-info { flex: 1; min-width: 0; }
        .sidebar-user .su-name { font-size: 13px; font-weight: 600; color: #e2e8f0; }
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
        .page-body { padding: 28px 32px 40px; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        
        .form-header-card { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-radius: var(--radius); padding: 28px 32px; color: #fff; margin-bottom: 24px; position: relative; overflow: hidden; animation: fadeUp 0.5s ease both; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;}
        .form-header-card::before { content: ''; position: absolute; width: 250px; height: 250px; background: rgba(20,184,166,0.1); border-radius: 50%; top: -100px; left: -50px; }
        .fhc-left { position: relative; z-index: 2; display: flex; align-items: center; gap: 16px; }
        .fhc-icon { width: 56px; height: 56px; border-radius: 14px; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--primary-light); }
        .form-header-card h2 { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .form-header-card p { font-size: 13px; color: #94a3b8; }
        
        /* أدوات اختيار الشهر والسنة */
        .period-selector { position: relative; z-index: 2; display: flex; gap: 12px; background: rgba(0,0,0,0.2); padding: 12px; border-radius: var(--radius-sm); border: 1px solid rgba(255,255,255,0.1); }
        .ps-input { padding: 8px 12px; background: #fff; border: none; border-radius: 6px; font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 700; color: var(--text-dark); outline: none; width: 120px; text-align: center;}

        .form-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; animation: fadeUp 0.5s ease 0.1s both; }
        
        .table-wrap { overflow-x: auto; padding: 0 16px;}
        table { width: 100%; border-collapse: collapse; margin-top: 16px;}
        thead th { padding: 14px 16px; font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; background: #f8fafc; border-bottom: 1.5px solid var(--border); text-align: right; white-space: nowrap; }
        tbody tr { transition: background 0.15s; border-bottom: 1px solid var(--border); }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(20, 184, 166, 0.02); }
        tbody td { padding: 12px 16px; font-size: 13.5px; color: var(--text-body); vertical-align: middle; }
        
        .emp-name { font-weight: 700; color: var(--text-dark); }
        .emp-pos { font-size: 11px; color: var(--text-muted); }
        
        .calc-input { width: 90px; padding: 8px 10px; border: 1.5px solid var(--border); border-radius: 6px; font-family: 'Cairo', sans-serif; font-size: 13px; text-align: center; direction: ltr; font-weight: 600; transition: border 0.2s;}
        .calc-input:focus { border-color: var(--primary); outline: none; }
        .calc-input.deduct { color: var(--danger); }
        .calc-input.bonus { color: var(--success); }
        
        .td-base { font-weight: 600; color: var(--text-dark); direction: ltr; text-align: right; }
        .td-net { font-size: 15px; font-weight: 800; color: var(--primary-dark); direction: ltr; text-align: right; background: #f8fafc; border-radius: 6px; padding: 4px 10px; display: inline-block; min-width: 90px;}

        .totals-panel { padding: 24px; background: linear-gradient(to left, #f8fafc, #ffffff); border-top: 2px solid var(--primary); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;}
        .tp-label { font-size: 14px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;}
        .tp-value { font-size: 26px; font-weight: 900; color: var(--primary-dark); font-variant-numeric: tabular-nums; direction: ltr; }

        .form-actions { padding: 24px 32px; display: flex; align-items: center; justify-content: flex-start; gap: 12px; background: #f8fafc; border-top: 1px solid var(--border); }
        .btn-submit { display: inline-flex; align-items: center; gap: 8px; padding: 12px 32px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; border: none; border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.25s; box-shadow: 0 4px 15px rgba(20,184,166,0.25); }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(20,184,166,0.35); }
        .btn-cancel { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: transparent; color: var(--text-body); border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .btn-cancel:hover { background: var(--page-bg); border-color: var(--text-muted); }

        @media (max-width: 992px) {
            .form-header-card { flex-direction: column; align-items: flex-start; }
            .period-selector { width: 100%; justify-content: space-between; }
            .ps-input { flex: 1; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); } .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text-body); font-size: 16px; cursor: pointer;}
            .page-body { padding: 20px 16px; } .topbar { padding: 0 16px; }
            .form-actions { padding: 20px; flex-direction: column; align-items: stretch;}
            .btn-submit { justify-content: center; }
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
                        <a href="<?php echo URL_ROOT; ?>/payroll/index">مسير الرواتب</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>إصدار مسير</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-body">
            
            <form action="<?php echo URL_ROOT; ?>/payroll/create" method="POST" id="payrollForm">
                
                <div class="form-header-card">
                    <div class="fhc-left">
                        <div class="fhc-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        <div>
                            <h2>إصدار مسير رواتب جديد</h2>
                            <p>قم بمراجعة الخصومات والمكافآت قبل اعتماد المسير الشهري</p>
                        </div>
                    </div>
                    <div class="period-selector">
                        <select name="month" class="ps-input" required>
                            <?php 
                                $currentMonth = date('n');
                                $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                                foreach($months as $i => $m) {
                                    $val = $i + 1;
                                    $sel = ($val == $currentMonth) ? 'selected' : '';
                                    echo "<option value=\"$val\" $sel>$m ($val)</option>";
                                }
                            ?>
                        </select>
                        <select name="year" class="ps-input" required>
                            <?php 
                                $currentYear = date('Y');
                                for($y = $currentYear; $y >= $currentYear - 2; $y--) {
                                    echo "<option value=\"$y\">سنة $y</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-card">
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الموظف</th>
                                    <th style="text-align:right;">الراتب الأساسي</th>
                                    <th style="text-align:center;">خصومات (غياب/تأخير)</th>
                                    <th style="text-align:center;">بدلات/مكافآت</th>
                                    <th style="text-align:right;">الصافي المستحق</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($employees as $emp) : ?>
                                <tr class="emp-row">
                                    <td style="color:var(--text-muted);font-weight:600;font-size:12px;"><?php echo $emp->id; ?></td>
                                    <td>
                                        <div class="emp-name"><?php echo htmlspecialchars($emp->name); ?></div>
                                        <div class="emp-pos"><?php echo htmlspecialchars($emp->position ?? 'موظف'); ?></div>
                                        <input type="hidden" name="emp_ids[]" value="<?php echo $emp->id; ?>">
                                        <input type="hidden" name="base_salaries[]" value="<?php echo $emp->salary; ?>" class="base-input">
                                    </td>
                                    <td class="td-base"><?php echo number_format($emp->salary, 2); ?></td>
                                    <td style="text-align:center;">
                                        <input type="number" name="deductions[]" class="calc-input deduct" value="0" min="0" step="0.01">
                                    </td>
                                    <td style="text-align:center;">
                                        <input type="number" name="bonuses[]" class="calc-input bonus" value="0" min="0" step="0.01">
                                    </td>
                                    <td style="text-align:right;">
                                        <span class="td-net net-display"><?php echo number_format($emp->salary, 2); ?></span>
                                        <input type="hidden" name="net_salaries[]" value="<?php echo $emp->salary; ?>" class="net-input">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                
                                <?php if (empty($employees)) : ?>
                                <tr>
                                    <td colspan="6" style="text-align:center; padding: 40px; color:var(--text-muted);">
                                        <i class="fas fa-users-slash" style="font-size:32px; margin-bottom:10px; display:block;"></i>
                                        لا يوجد موظفين مسجلين لإصدار رواتب لهم
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="totals-panel">
                        <div>
                            <div class="tp-label">إجمالي المسير (المبلغ المطلوب توفيره)</div>
                        </div>
                        <div class="tp-value" id="grandTotal">0.00 <span style="font-size:14px;color:var(--text-muted);">ر.س</span></div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="btnSubmit" <?php echo empty($employees) ? 'disabled' : ''; ?>>
                            <i class="fas fa-check-double"></i> اعتماد وإصدار المسير
                        </button>
                        <a href="<?php echo URL_ROOT; ?>/payroll/index" class="btn-cancel">إلغاء</a>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        // دالة لحساب صافي الراتب لكل موظف وتحديث الإجمالي
        function calculatePayrolls() {
            let grandTotal = 0;
            const rows = document.querySelectorAll('.emp-row');
            
            rows.forEach(row => {
                const base = parseFloat(row.querySelector('.base-input').value) || 0;
                const deduct = parseFloat(row.querySelector('.deduct').value) || 0;
                const bonus = parseFloat(row.querySelector('.bonus').value) || 0;
                
                // التأكد أن الخصم لا يتجاوز الراتب
                let finalDeduct = deduct;
                if(deduct > base) {
                    finalDeduct = base;
                    row.querySelector('.deduct').value = base;
                }

                const net = base - finalDeduct + bonus;
                
                row.querySelector('.net-display').textContent = net.toLocaleString('ar-SA', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                row.querySelector('.net-input').value = net.toFixed(2);
                
                grandTotal += net;
            });
            
            document.getElementById('grandTotal').innerHTML = grandTotal.toLocaleString('ar-SA', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' <span style="font-size:14px;color:var(--text-muted);">ر.س</span>';
        }

        // ربط الأحداث بمدخلات الخصم والمكافأة
        document.querySelectorAll('.calc-input').forEach(input => {
            input.addEventListener('input', calculatePayrolls);
            // منع القيم السالبة
            input.addEventListener('change', function() {
                if(this.value < 0) this.value = 0;
                calculatePayrolls();
            });
        });

        // الحساب المبدئي عند تحميل الصفحة
        calculatePayrolls();

        // تجربة الإرسال
        document.getElementById('payrollForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('btnSubmit');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري إصدار المسير...';
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