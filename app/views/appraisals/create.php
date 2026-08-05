<?php
// app/views/appraisals/create.php
$pageTitle = $data['title'] ?? 'تقييم أداء موظف';
$employees = $data['employees'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'appraisal/index';
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
            overflow-x: hidden;
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
            box-shadow: 0 4px 15px rgba(20, 184, 166, 0.25);
        }

        .sidebar-brand .s-text {
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand .s-name {
            font-size: 17px;
            font-weight: 800;
            color: #f8fafc;
            letter-spacing: -0.3px;
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
            transition: color 0.2s;
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
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
            max-width: 900px;
            margin: 0 auto;
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
            border: 1px solid transparent;
            animation: fadeUp 0.4s ease both;
        }

        .flash-msg.flash-error {
            background: var(--danger-light);
            color: #dc2626;
            border-color: #fecaca;
        }

        .form-header-card {
            background: linear-gradient(135deg, var(--info) 0%, #0891b2 100%);
            border-radius: var(--radius);
            padding: 28px 32px;
            color: #fff;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            animation: fadeUp 0.5s ease both;
        }

        .form-header-card::before {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            top: -100px;
            left: -50px;
            pointer-events: none;
        }

        .form-header-card h2 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-header-card p {
            font-size: 13px;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .form-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            animation: fadeUp 0.5s ease 0.1s both;
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
            background: var(--info-light);
            color: var(--info);
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

        .form-group.full {
            grid-column: 1 / -1;
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
            border-color: var(--info);
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.08);
        }

        select.form-input {
            appearance: none;
            cursor: pointer;
            padding-left: 36px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: left 14px center;
        }

        /* Sliders */
        .slider-wrap {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .slider-input {
            flex: 1;
            -webkit-appearance: none;
            height: 8px;
            background: var(--border);
            border-radius: 4px;
            outline: none;
            transition: background 0.2s;
            cursor: pointer;
        }

        .slider-input::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--info);
            cursor: pointer;
            border: 3px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .slider-input::-moz-range-thumb {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--info);
            cursor: pointer;
            border: 3px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .score-display {
            font-family: monospace;
            font-size: 16px;
            font-weight: 800;
            color: var(--info-dark);
            background: var(--info-light);
            width: 54px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid rgba(6, 182, 212, 0.2);
        }

        .total-box {
            background: #f8fafc;
            border: 1px dashed var(--border);
            padding: 16px 24px;
            border-radius: var(--radius-sm);
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-box-label {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .total-box-val {
            font-size: 28px;
            font-weight: 900;
            color: var(--info);
            font-variant-numeric: tabular-nums;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .grade-badge {
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 20px;
            color: #fff;
            background: var(--border);
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
            background: linear-gradient(135deg, var(--info), #0891b2);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s;
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.25);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(6, 182, 212, 0.35);
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
                        <a href="<?php echo URL_ROOT; ?>/appraisal/index">الموارد البشرية</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>إضافة تقييم</span>
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
                <h2><i class="fas fa-star-half-stroke"></i> تقييم أداء موظف</h2>
                <p>قم بتقييم الموظف بموضوعية في النقاط الأساسية (من 0 إلى 100) ليتم حساب المتوسط والتقدير تلقائياً.</p>
            </div>

            <div class="form-card">
                <form action="<?php echo URL_ROOT; ?>/appraisal/create" method="POST" id="appraisalForm" novalidate>

                    <div class="form-section">
                        <div class="form-section-title"><span class="fst-icon"><i class="fas fa-user-tie"></i></span> الموظف والتاريخ</div>
                        <div class="form-grid">
                            <div class="form-group full">
                                <label class="form-label">الموظف المراد تقييمه <span class="req">*</span></label>
                                <select name="employee_id" id="empSelect" class="form-input" required>
                                    <option value="">-- يرجى اختيار الموظف --</option>
                                    <?php foreach ($employees as $emp) : ?>
                                        <option value="<?php echo $emp->id; ?>">
                                            <?php echo htmlspecialchars($emp->name); ?> — <?php echo htmlspecialchars($emp->position ?? 'بدون مسمى'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">تاريخ التقييم <span class="req">*</span></label>
                                <input type="date" name="evaluation_date" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title"><span class="fst-icon"><i class="fas fa-chart-line"></i></span> معايير التقييم (من 100)</div>
                        <div class="form-grid" style="grid-template-columns: 1fr; gap: 28px;">

                            <div class="form-group">
                                <label class="form-label">1. الأداء المهني وجودة العمل</label>
                                <div class="slider-wrap">
                                    <input type="range" name="performance_score" class="slider-input score-calc" min="0" max="100" value="80">
                                    <span class="score-display">80</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">2. السلوك التعاوني وروح الفريق</label>
                                <div class="slider-wrap">
                                    <input type="range" name="behavior_score" class="slider-input score-calc" min="0" max="100" value="80">
                                    <span class="score-display">80</span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">3. الانضباط والحضور</label>
                                <div class="slider-wrap">
                                    <input type="range" name="attendance_score" class="slider-input score-calc" min="0" max="100" value="80">
                                    <span class="score-display">80</span>
                                </div>
                            </div>

                        </div>

                        <div class="total-box">
                            <span class="total-box-label">النتيجة النهائية والتقدير العام:</span>
                            <div class="total-box-val">
                                <span id="gradeBadge" class="grade-badge" style="background:var(--info);">جيد جداً</span>
                                <span id="finalScore">80.00</span>%
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title"><span class="fst-icon"><i class="fas fa-comment-dots"></i></span> ملاحظات وتوصيات (اختياري)</div>
                        <div class="form-group full">
                            <textarea name="comments" class="form-input" rows="4" placeholder="توصيات للموظف، نقاط القوة، نقاط تحتاج لتطوير..."></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="btnSubmit"><i class="fas fa-star"></i> حفظ التقييم</button>
                        <a href="<?php echo URL_ROOT; ?>/appraisal/index" class="btn-cancel">إلغاء</a>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        const sliders = document.querySelectorAll('.score-calc');
        const finalScoreEl = document.getElementById('finalScore');
        const gradeBadge = document.getElementById('gradeBadge');

        function calculateTotal() {
            let total = 0;
            sliders.forEach(slider => {
                const val = parseInt(slider.value) || 0;
                slider.nextElementSibling.textContent = val;

                // تلوين شريط السلايدر
                const percentage = (val / 100) * 100;
                slider.style.background = `linear-gradient(to left, var(--info) ${percentage}%, var(--border) ${percentage}%)`;

                total += val;
            });

            const avg = total / 3;
            finalScoreEl.textContent = avg.toFixed(2);

            let grade = 'ضعيف';
            let color = 'var(--danger)';

            if (avg >= 90) {
                grade = 'ممتاز';
                color = 'var(--success)';
            } else if (avg >= 80) {
                grade = 'جيد جداً';
                color = 'var(--info)';
            } else if (avg >= 70) {
                grade = 'جيد';
                color = 'var(--primary)';
            } else if (avg >= 60) {
                grade = 'مقبول';
                color = 'var(--accent)';
            }

            gradeBadge.textContent = grade;
            gradeBadge.style.background = color;
        }

        sliders.forEach(slider => {
            slider.addEventListener('input', calculateTotal);
            // تهيئة أولية
            const val = parseInt(slider.value);
            slider.style.background = `linear-gradient(to left, var(--info) ${val}%, var(--border) ${val}%)`;
        });

        // التحقق قبل الإرسال
        const form = document.getElementById('appraisalForm');
        const btnSubmit = document.getElementById('btnSubmit');
        const empSelect = document.getElementById('empSelect');

        form.addEventListener('submit', function(e) {
            if (!empSelect.value) {
                e.preventDefault();
                empSelect.style.borderColor = 'var(--danger)';
                alert('الرجاء اختيار الموظف المراد تقييمه.');
            } else {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري الحفظ...';
            }
        });

        // القائمة الجانبية للموبايل
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