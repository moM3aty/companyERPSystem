<?php
// app/views/account/create_journal.php
$pageTitle = $data['title'] ?? 'إنشاء قيد يومي';
$accounts = $data['accounts'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'account/create-journal';
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
        /* ==========================================
           المتغيرات الأساسية (مشتركة)
           ========================================== */
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

        /* القائمة الجانبية والشريط العلوي */
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
        .sidebar-user .su-logout { color: var(--text-muted); font-size: 14px; padding: 6px; border-radius: 8px; transition: all 0.2s; text-decoration: none; }
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
        
        .flash-msg { padding: 14px 20px; border-radius: var(--radius-sm); display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; margin-bottom: 24px; border: 1px solid transparent; }
        .flash-msg.flash-success { background: var(--success-light); color: #15803d; border-color: #bbf7d0; }
        .flash-msg.flash-error { background: var(--danger-light); color: #dc2626; border-color: #fecaca; }

        /* ==========================================
           تصميم النموذج
           ========================================== */
        .form-header-card {
            background: linear-gradient(135deg, var(--primary) 0%, #0d9488 60%, #0f766e 100%);
            border-radius: var(--radius); padding: 28px 32px; color: #fff; margin-bottom: 24px;
            position: relative; overflow: hidden; animation: fadeUp 0.5s ease both;
        }
        .form-header-card::before { content: ''; position: absolute; width: 250px; height: 250px; background: rgba(255,255,255,0.05); border-radius: 50%; top: -100px; left: -50px; }
        .form-header-card h2 { font-size: 20px; font-weight: 700; margin-bottom: 4px; position: relative; z-index: 2; }
        .form-header-card p { font-size: 13px; opacity: 0.85; position: relative; z-index: 2; }

        .form-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); box-shadow: var(--shadow-sm); overflow: hidden; animation: fadeUp 0.5s ease 0.1s both; }
        .form-section { padding: 28px 32px; border-bottom: 1px solid var(--border); }
        .form-section:last-of-type { border-bottom: none; }
        
        .form-section-title { display: flex; align-items: center; gap: 10px; font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 24px; }
        .form-section-title .fst-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0; }
        .fst-icon.fst-teal { background: var(--primary-light); color: var(--primary-dark); }
        .fst-icon.fst-purple { background: var(--purple-light); color: var(--purple); }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { display: flex; flex-direction: column; }
        .form-label { font-size: 13px; font-weight: 600; color: var(--text-body); margin-bottom: 8px; display: flex; align-items: center; gap: 4px; }
        .form-label .req { color: var(--danger); font-size: 14px; }
        .form-input { padding: 12px 16px; border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 14px; background: var(--card-bg); color: var(--text-dark); outline: none; transition: all 0.25s; }
        .form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(20,184,166,0.08); }
        select.form-input { appearance: none; cursor: pointer; padding-left: 36px; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: left 14px center; }

        /* صفوف القيد الديناميكية */
        .lines-container { display: flex; flex-direction: column; gap: 12px; }
        .line-row { display: grid; grid-template-columns: 2fr 1fr 1fr 2fr auto; gap: 12px; align-items: start; animation: fadeUp 0.3s ease; }
        
        .btn-remove-line { height: 46px; width: 46px; border-radius: var(--radius-sm); border: 1px solid var(--danger-light); background: var(--danger-light); color: var(--danger); display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
        .btn-remove-line:hover { background: var(--danger); color: #fff; }

        .btn-add-line { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: var(--page-bg); color: var(--text-dark); border: 1px dashed var(--border); border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; margin-top: 16px; width: fit-content; }
        .btn-add-line:hover { background: var(--primary-light); color: var(--primary-dark); border-color: var(--primary); }

        /* لوحة المجاميع */
        .totals-panel { margin-top: 24px; padding: 20px; background: #f8fafc; border-radius: var(--radius-sm); border: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
        .total-item { display: flex; flex-direction: column; gap: 4px; }
        .total-label { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
        .total-value { font-size: 20px; font-weight: 800; font-variant-numeric: tabular-nums; direction: ltr; }
        .total-value.debit { color: var(--info); }
        .total-value.credit { color: var(--purple); }
        .total-status { padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .status-balanced { background: var(--success-light); color: var(--success); }
        .status-unbalanced { background: var(--danger-light); color: var(--danger); }

        .form-actions { padding: 24px 32px; display: flex; align-items: center; gap: 12px; background: #f8fafc; border-top: 1px solid var(--border); }
        .btn-submit { display: inline-flex; align-items: center; gap: 8px; padding: 12px 32px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; border: none; border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 700; cursor: pointer; transition: all 0.25s; box-shadow: 0 4px 15px rgba(20,184,166,0.25); }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(20,184,166,0.35); }
        .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .btn-cancel { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: transparent; color: var(--text-body); border: 1.5px solid var(--border); border-radius: var(--radius-sm); font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .btn-cancel:hover { background: var(--page-bg); }

        @media (max-width: 992px) {
            .line-row { grid-template-columns: 1fr; gap: 8px; padding: 16px; background: var(--page-bg); border-radius: var(--radius-sm); border: 1px solid var(--border); position: relative; }
            .btn-remove-line { position: absolute; top: 16px; left: 16px; height: 32px; width: 32px; }
            .totals-panel { flex-direction: column; gap: 16px; align-items: stretch; text-align: center; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); } .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text-body); font-size: 16px; cursor: pointer;}
            .page-body { padding: 20px 16px; } .topbar { padding: 0 16px; }
            .form-grid { grid-template-columns: 1fr; }
        }
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; backdrop-filter: blur(2px); }
        .sidebar-overlay.show { display: block; }
    </style>
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="s-logo"><i class="fas fa-cubes"></i></div>
            <div class="s-text">
                <span class="s-name">ERP <span>Pro</span></span>
            </div>
        </div>
        <?php if(class_exists('Layout')) echo Layout::renderSidebar($currentUrl); ?>
        <div class="sidebar-user">
            <div class="su-avatar"><?php echo mb_substr($_SESSION['user_name'] ?? 'م', 0, 2); ?></div>
            <div class="su-info">
                <div class="su-name"><?php echo $_SESSION['user_name'] ?? 'مدير النظام'; ?></div>
                <div class="su-role"><?php echo $_SESSION['user_role'] ?? 'admin'; ?></div>
            </div>
            <a href="<?php echo URLROOT; ?>/auth/logout" class="su-logout" title="تسجيل الخروج"><i class="fas fa-right-from-bracket"></i></a>
        </div>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:16px;">
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="page-title"><?php echo $pageTitle; ?></div>
                    <div class="breadcrumb">
                        <a href="<?php echo URLROOT; ?>/dashboard">الرئيسية</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <a href="<?php echo URLROOT; ?>/account/ledger">دفتر الأستاذ</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>قيد جديد</span>
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

            <div class="form-header-card">
                <h2><i class="fas fa-pen-to-square" style="margin-left:8px;"></i> إنشاء قيد يومي يدوي</h2>
                <p>قم بتسجيل حركة محاسبية جديدة مع التأكد من توازن القيد (المدين = الدائن)</p>
            </div>

            <div class="form-card">
                <form action="<?php echo URLROOT; ?>/account/create-journal" method="POST" id="journalForm">
                    
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="fst-icon fst-teal"><i class="fas fa-info-circle"></i></span>
                            المعلومات الأساسية
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">تاريخ القيد <span class="req">*</span></label>
                                <input type="date" name="entry_date" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">البيان (شرح القيد) <span class="req">*</span></label>
                                <input type="text" name="description" class="form-input" placeholder="مثال: تسوية عهدة موظف" required autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="fst-icon fst-purple"><i class="fas fa-list-ol"></i></span>
                            سطور القيد
                        </div>
                        
                        <!-- حاوية السطور -->
                        <div class="lines-container" id="linesContainer">
                            <!-- السطر الأول -->
                            <div class="line-row">
                                <select name="lines[0][account_id]" class="form-input account-select" required>
                                    <option value="">-- اختر الحساب --</option>
                                    <?php foreach ($accounts as $acc) : ?>
                                        <option value="<?php echo $acc->id; ?>"><?php echo $acc->code . ' - ' . htmlspecialchars($acc->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="number" name="lines[0][debit]" placeholder="مدين (ر.س)" class="form-input debit-input" step="0.01" min="0" autocomplete="off" style="direction:ltr; text-align:center;">
                                <input type="number" name="lines[0][credit]" placeholder="دائن (ر.س)" class="form-input credit-input" step="0.01" min="0" autocomplete="off" style="direction:ltr; text-align:center;">
                                <input type="text" name="lines[0][description]" placeholder="بيان السطر (اختياري)" class="form-input" autocomplete="off">
                                <button type="button" class="btn-remove-line" onclick="removeLine(this)" disabled title="لا يمكن حذف السطر الأول"><i class="fas fa-trash"></i></button>
                            </div>
                            <!-- السطر الثاني (القيد المزدوج يتطلب سطرين على الأقل) -->
                            <div class="line-row">
                                <select name="lines[1][account_id]" class="form-input account-select" required>
                                    <option value="">-- اختر الحساب --</option>
                                    <?php foreach ($accounts as $acc) : ?>
                                        <option value="<?php echo $acc->id; ?>"><?php echo $acc->code . ' - ' . htmlspecialchars($acc->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="number" name="lines[1][debit]" placeholder="مدين (ر.س)" class="form-input debit-input" step="0.01" min="0" autocomplete="off" style="direction:ltr; text-align:center;">
                                <input type="number" name="lines[1][credit]" placeholder="دائن (ر.س)" class="form-input credit-input" step="0.01" min="0" autocomplete="off" style="direction:ltr; text-align:center;">
                                <input type="text" name="lines[1][description]" placeholder="بيان السطر (اختياري)" class="form-input" autocomplete="off">
                                <button type="button" class="btn-remove-line" onclick="removeLine(this)" title="حذف السطر"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>

                        <button type="button" onclick="addLine()" class="btn-add-line">
                            <i class="fas fa-plus"></i> إضافة سطر جديد
                        </button>

                        <!-- المجاميع -->
                        <div class="totals-panel">
                            <div class="total-item">
                                <span class="total-label">إجمالي المدين</span>
                                <span class="total-value debit" id="totalDebit">0.00</span>
                            </div>
                            
                            <div class="total-status status-unbalanced" id="balanceStatus">
                                <i class="fas fa-scale-unbalanced"></i> القيد غير متوازن
                            </div>

                            <div class="total-item" style="text-align: left;">
                                <span class="total-label">إجمالي الدائن</span>
                                <span class="total-value credit" id="totalCredit">0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="btnSubmit" disabled>
                            <i class="fas fa-save"></i> حفظ القيد
                        </button>
                        <a href="<?php echo URLROOT; ?>/account/ledger" class="btn-cancel">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- مصفوفة الحسابات لخيارات السطور الجديدة -->
    <script>
        const accountsList = <?php 
            $opts = [];
            foreach($accounts as $acc) {
                $opts[] = '<option value="'.$acc->id.'">'.$acc->code.' - '.htmlspecialchars($acc->name, ENT_QUOTES).'</option>';
            }
            echo json_encode(implode('', $opts));
        ?>;
    </script>

    <script>
        let lineIndex = 2; // لدينا سطرين مبدئياً
        const container = document.getElementById('linesContainer');
        const btnSubmit = document.getElementById('btnSubmit');
        const totalDebitEl = document.getElementById('totalDebit');
        const totalCreditEl = document.getElementById('totalCredit');
        const balanceStatus = document.getElementById('balanceStatus');

        function addLine() {
            const newRow = document.createElement('div');
            newRow.className = 'line-row';
            newRow.innerHTML = `
                <select name="lines[${lineIndex}][account_id]" class="form-input account-select" required>
                    <option value="">-- اختر الحساب --</option>
                    ${accountsList}
                </select>
                <input type="number" name="lines[${lineIndex}][debit]" placeholder="مدين (ر.س)" class="form-input debit-input" step="0.01" min="0" autocomplete="off" style="direction:ltr; text-align:center;">
                <input type="number" name="lines[${lineIndex}][credit]" placeholder="دائن (ر.س)" class="form-input credit-input" step="0.01" min="0" autocomplete="off" style="direction:ltr; text-align:center;">
                <input type="text" name="lines[${lineIndex}][description]" placeholder="بيان السطر (اختياري)" class="form-input" autocomplete="off">
                <button type="button" class="btn-remove-line" onclick="removeLine(this)" title="حذف السطر"><i class="fas fa-trash"></i></button>
            `;
            container.appendChild(newRow);
            lineIndex++;
            attachEvents();
            calculateTotals();
        }

        function removeLine(btn) {
            const rows = container.querySelectorAll('.line-row');
            if (rows.length > 2) {
                btn.parentElement.remove();
                calculateTotals();
            } else {
                alert('يجب أن يحتوي القيد المزدوج على سطرين على الأقل.');
            }
        }

        function calculateTotals() {
            let tDebit = 0;
            let tCredit = 0;

            document.querySelectorAll('.debit-input').forEach(input => {
                const val = parseFloat(input.value);
                if (!isNaN(val) && val > 0) {
                    tDebit += val;
                    // تصفير الدائن في نفس السطر إذا أُدخل مدين
                    const siblingCredit = input.parentElement.querySelector('.credit-input');
                    if(siblingCredit && siblingCredit.value > 0) siblingCredit.value = '';
                }
            });

            document.querySelectorAll('.credit-input').forEach(input => {
                const val = parseFloat(input.value);
                if (!isNaN(val) && val > 0) {
                    tCredit += val;
                    // تصفير المدين في نفس السطر إذا أُدخل دائن
                    const siblingDebit = input.parentElement.querySelector('.debit-input');
                    if(siblingDebit && siblingDebit.value > 0) siblingDebit.value = '';
                }
            });

            totalDebitEl.textContent = tDebit.toLocaleString('ar-SA', {minimumFractionDigits: 2});
            totalCreditEl.textContent = tCredit.toLocaleString('ar-SA', {minimumFractionDigits: 2});

            // التحقق من التوازن
            const diff = Math.abs(tDebit - tCredit);
            
            if (tDebit > 0 && tCredit > 0 && diff < 0.01) {
                balanceStatus.className = 'total-status status-balanced';
                balanceStatus.innerHTML = '<i class="fas fa-scale-balanced"></i> القيد متوازن';
                btnSubmit.disabled = false;
            } else {
                balanceStatus.className = 'total-status status-unbalanced';
                balanceStatus.innerHTML = '<i class="fas fa-scale-unbalanced"></i> القيد غير متوازن';
                btnSubmit.disabled = true;
            }
        }

        function attachEvents() {
            document.querySelectorAll('.debit-input, .credit-input').forEach(input => {
                // إزالة الحدث القديم إن وجد لتجنب التكرار
                input.removeEventListener('input', calculateTotals);
                input.addEventListener('input', calculateTotals);
            });
        }

        // تشغيل الأحداث لأول مرة
        attachEvents();

        /* القائمة الجانبية للموبايل */
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>