<?php
// app/views/products/edit.php
 $prod = $data['product'];
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
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 20px rgba(0,0,0,0.08);
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--page-bg);
            color: var(--text-body);
            min-height: 100vh;
        }

        /* === الشريط الجانبي === */
        .sidebar {
            position: fixed; top: 0; right: 0;
            width: var(--sidebar-w); height: 100vh;
            background: linear-gradient(180deg, #0f172a 0%, #1a2332 100%);
            z-index: 100; display: flex; flex-direction: column;
            transition: transform 0.3s ease;
            border-left: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-brand {
            padding: 24px 24px 20px;
            display: flex; align-items: center; gap: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .sidebar-brand .s-logo {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: #fff; flex-shrink: 0;
        }

        .sidebar-brand .s-name { font-size: 17px; font-weight: 800; color: #f8fafc; }
        .sidebar-brand .s-name span { color: var(--primary); }

        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }

        .nav-section-title {
            font-size: 10px; font-weight: 700; color: var(--text-muted);
            text-transform: uppercase; letter-spacing: 1.5px; padding: 12px 14px 8px;
        }

        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 11px 14px; border-radius: var(--radius-sm);
            color: #94a3b8; text-decoration: none;
            font-size: 14px; font-weight: 500;
            transition: all 0.2s ease; position: relative;
        }

        .nav-link i { width: 20px; text-align: center; font-size: 15px; }
        .nav-link:hover { background: #1e293b; color: #e2e8f0; }

        .nav-link.active {
            background: rgba(20,184,166,0.1);
            color: var(--primary); font-weight: 600;
        }

        .nav-link.active::before {
            content: ''; position: absolute; right: -12px; top: 50%;
            transform: translateY(-50%); width: 3px; height: 24px;
            background: var(--primary); border-radius: 0 4px 4px 0;
        }

        .sidebar-user {
            padding: 16px 20px; border-top: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; gap: 12px;
        }

        .sidebar-user .su-avatar {
            width: 38px; height: 38px; border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: 14px; flex-shrink: 0;
        }

        .sidebar-user .su-name { font-size: 13px; font-weight: 600; color: #e2e8f0; }
        .sidebar-user .su-role { font-size: 11px; color: var(--text-muted); }

        .sidebar-user .su-logout {
            color: var(--text-muted); font-size: 14px; padding: 6px;
            border-radius: 8px; transition: all 0.2s; text-decoration: none; margin-right: auto;
        }

        .sidebar-user .su-logout:hover { color: var(--danger); background: rgba(239,68,68,0.1); }

        /* === المحتوى === */
        .main-content { margin-right: var(--sidebar-w); min-height: 100vh; }

        .topbar {
            height: var(--topbar-h); background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 32px; position: sticky; top: 0; z-index: 50;
        }

        .page-title { font-size: 18px; font-weight: 700; color: var(--text-dark); }

        .breadcrumb {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; color: var(--text-muted); margin-top: 2px;
        }

        .breadcrumb a { color: var(--text-muted); text-decoration: none; }
        .breadcrumb a:hover { color: var(--primary); }

        .mobile-menu-btn { display: none; }

        .topbar-btn {
            width: 40px; height: 40px; border-radius: 10px;
            border: 1px solid var(--border); background: transparent;
            color: var(--text-body); display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.2s; font-size: 15px;
        }

        .topbar-btn:hover { background: var(--page-bg); border-color: var(--primary); color: var(--primary); }

        .page-body { padding: 28px 32px 40px; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* === رأس التعديل === */
        .form-header-card {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border-radius: var(--radius); padding: 28px 32px;
            color: #fff; margin-bottom: 24px;
            position: relative; overflow: hidden;
            animation: fadeUp 0.5s ease both;
        }

        .form-header-card::before {
            content: ''; position: absolute;
            width: 220px; height: 220px;
            background: rgba(245,158,11,0.08); border-radius: 50%;
            top: -80px; left: -40px;
        }

        .form-header-card::after {
            content: ''; position: absolute;
            width: 140px; height: 140px;
            background: rgba(20,184,166,0.06); border-radius: 50%;
            bottom: -50px; right: 80px;
        }

        .form-header-card .fhc-top {
            display: flex; align-items: center; justify-content: space-between;
            position: relative; z-index: 2; flex-wrap: wrap; gap: 16px;
        }

        .form-header-card .fhc-title {
            display: flex; align-items: center; gap: 12px;
        }

        .form-header-card .fhc-title .fhc-icon {
            width: 48px; height: 48px; border-radius: 12px;
            background: rgba(245,158,11,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: var(--accent);
        }

        .form-header-card .fhc-title h2 { font-size: 20px; font-weight: 700; }
        .form-header-card .fhc-title p { font-size: 12px; color: #94a3b8; margin-top: 2px; }

        .fhc-product-badge {
            display: flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            padding: 10px 18px; border-radius: 10px;
            position: relative; z-index: 2;
        }

        .fhc-product-badge .fpb-sku {
            font-family: monospace; direction: ltr;
            font-size: 14px; color: var(--accent); font-weight: 700;
        }

        .fhc-product-badge .fpb-id {
            font-size: 11px; color: #94a3b8;
        }

        /* === بطاقة النموذج === */
        .form-card {
            background: var(--card-bg); border-radius: var(--radius);
            border: 1px solid var(--border); box-shadow: var(--shadow-sm);
            overflow: hidden; animation: fadeUp 0.5s ease 0.1s both;
        }

        .form-section {
            padding: 28px 32px;
            border-bottom: 1px solid var(--border);
        }

        .form-section:last-of-type { border-bottom: none; }

        .form-section-title {
            display: flex; align-items: center; gap: 10px;
            font-size: 15px; font-weight: 700; color: var(--text-dark);
            margin-bottom: 24px;
        }

        .form-section-title .fst-icon {
            width: 32px; height: 32px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0;
        }

        .fst-icon.fst-teal { background: var(--primary-light); color: var(--primary-dark); }
        .fst-icon.fst-amber { background: var(--accent-light); color: var(--accent); }
        .fst-icon.fst-purple { background: var(--purple-light); color: var(--purple); }

        .form-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group { display: flex; flex-direction: column; }
        .form-group.full-width { grid-column: 1 / -1; }

        .form-label {
            font-size: 13px; font-weight: 600; color: var(--text-body);
            margin-bottom: 8px; display: flex; align-items: center; gap: 4px;
        }

        .form-label .req { color: var(--danger); font-size: 14px; }

        .form-input {
            padding: 12px 16px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 14px;
            background: var(--card-bg); color: var(--text-dark);
            outline: none; transition: all 0.25s;
        }

        .form-input::placeholder { color: var(--text-muted); }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(20,184,166,0.08);
        }

        .form-input.has-error {
            border-color: var(--danger);
            box-shadow: 0 0 0 3px rgba(239,68,68,0.08);
        }

        .form-hint {
            font-size: 11px; color: var(--text-muted); margin-top: 6px;
            display: flex; align-items: center; gap: 4px;
        }

        .form-hint i { font-size: 10px; }

        .form-error {
            font-size: 11px; color: var(--danger); margin-top: 6px;
            display: flex; align-items: center; gap: 4px;
            animation: shakeIn 0.4s ease;
        }

        @keyframes shakeIn {
            0% { transform: translateX(-6px); opacity: 0; }
            40% { transform: translateX(3px); }
            100% { transform: translateX(0); opacity: 1; }
        }

        select.form-input {
            appearance: none; cursor: pointer; padding-left: 36px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: left 14px center;
        }

        /* أزرار النموذج */
        .form-actions {
            padding: 24px 32px;
            display: flex; align-items: center; justify-content: flex-start; gap: 12px;
            background: #f8fafc; border-top: 1px solid var(--border);
        }

        .btn-submit {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 32px;
            background: linear-gradient(135deg, var(--accent), #d97706);
            color: #fff; border: none; border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 700;
            cursor: pointer; transition: all 0.25s;
            box-shadow: 0 2px 10px rgba(245,158,11,0.25);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245,158,11,0.35);
        }

        .btn-submit:active { transform: translateY(0); }

        .btn-cancel {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 24px; background: transparent; color: var(--text-body);
            border: 1.5px solid var(--border); border-radius: var(--radius-sm);
            font-family: 'Cairo', sans-serif; font-size: 14px; font-weight: 600;
            cursor: pointer; text-decoration: none; transition: all 0.2s;
        }

        .btn-cancel:hover { background: var(--page-bg); border-color: var(--text-muted); }

        /* شريط معلومات المنتج */
        .prod-info-bar {
            display: flex; align-items: center; gap: 24px;
            padding: 16px 24px; background: #f8fafc;
            border-bottom: 1px solid var(--border);
            animation: fadeUp 0.5s ease 0.05s both;
        }

        .pib-item {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: var(--text-body);
        }

        .pib-item i { color: var(--text-muted); font-size: 12px; }
        .pib-item strong { color: var(--text-dark); font-weight: 700; }

        .pib-divider {
            width: 1px; height: 24px; background: var(--border);
        }

        /* استجابة */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-right: 0; }
            .mobile-menu-btn { display: flex; }
            .page-body { padding: 20px 16px; }
            .topbar { padding: 0 16px; }
            .form-grid { grid-template-columns: 1fr; }
            .form-section { padding: 24px 20px; }
            .form-actions { padding: 20px; }
            .form-header-card { padding: 24px 20px; }
            .prod-info-bar { flex-wrap: wrap; gap: 12px; padding: 12px 16px; }
            .pib-divider { display: none; }
            .form-header-card .fhc-top { flex-direction: column; align-items: flex-start; }
        }

        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 99; }
        .sidebar-overlay.show { display: block; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="s-logo"><i class="fas fa-cubes"></i></div>
            <div class="s-name">ERP <span>Pro</span></div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section-title">القائمة الرئيسية</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/dashboard" class="nav-link"><i class="fas fa-gauge-high"></i><span>لوحة التحكم</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/employee" class="nav-link"><i class="fas fa-users"></i><span>الموظفين</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/product" class="nav-link active"><i class="fas fa-boxes-stacked"></i><span>المخزون</span></a></div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/sale" class="nav-link"><i class="fas fa-file-invoice-dollar"></i><span>المبيعات</span></a></div>
            <div class="nav-section-title" style="margin-top:12px;">المالية</div>
            <div class="nav-item"><a href="<?php echo URL_ROOT; ?>/accounting" class="nav-link"><i class="fas fa-chart-pie"></i><span>المحاسبة</span></a></div>
        </nav>
        <div class="sidebar-user">
            <div class="su-avatar"><?php echo mb_substr($_SESSION['user_name'] ?? 'م', 0, 2); ?></div>
            <div>
                <div class="su-name"><?php echo $_SESSION['user_name'] ?? ''; ?></div>
                <div class="su-role"><?php echo $_SESSION['user_role'] ?? 'مدير النظام'; ?></div>
            </div>
            <a href="<?php echo URL_ROOT; ?>/auth/logout" class="su-logout" title="تسجيل الخروج"><i class="fas fa-right-from-bracket"></i></a>
        </div>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:16px;">
                <button class="topbar-btn mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="page-title"><?php echo $data['title']; ?></div>
                    <div class="breadcrumb">
                        <a href="<?php echo URL_ROOT; ?>/dashboard">الرئيسية</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <a href="<?php echo URL_ROOT; ?>/product/index">المخزون</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>تعديل منتج</span>
                    </div>
                </div>
            </div>
        </header>

        <div class="page-body">

            <!-- رأس التعديل -->
            <div class="form-header-card">
                <div class="fhc-top">
                    <div class="fhc-title">
                        <div class="fhc-icon"><i class="fas fa-pen-to-square"></i></div>
                        <div>
                            <h2>تعديل بيانات المنتج</h2>
                            <p>عدّل البيانات واحفظ التغييرات</p>
                        </div>
                    </div>
                    <div class="fhc-product-badge">
                        <div>
                            <div class="fpb-sku"><?php echo htmlspecialchars($prod->sku); ?></div>
                            <div class="fpb-id">معرّف المنتج: #<?php echo $prod->id; ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- شريط معلومات سريعة -->
            <div class="prod-info-bar">
                <div class="pib-item">
                    <i class="fas fa-box"></i>
                    المنتج: <strong><?php echo htmlspecialchars($prod->name); ?></strong>
                </div>
                <div class="pib-divider"></div>
                <div class="pib-item">
                    <i class="fas fa-cubes"></i>
                    المخزون الحالي: <strong><?php echo $prod->quantity; ?> قطعة</strong>
                </div>
                <div class="pib-divider"></div>
                <div class="pib-item">
                    <i class="fas fa-tag"></i>
                    السعر الحالي: <strong><?php echo number_format($prod->price, 2); ?> ر.س</strong>
                </div>
            </div>

            <!-- النموذج -->
            <div class="form-card">
                <form action="<?php echo URL_ROOT; ?>/product/edit/<?php echo $prod->id; ?>" method="POST" id="productForm" novalidate>

                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="fst-icon fst-teal"><i class="fas fa-box"></i></span>
                            البيانات الأساسية
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">اسم المنتج <span class="req">*</span></label>
                                <input type="text" name="name" class="form-input" id="prodName" value="<?php echo htmlspecialchars($prod->name); ?>" required>
                                <div class="form-hint"><i class="fas fa-info-circle"></i> اسم واضح يُسهّل البحث لاحقاً</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">رمز المنتج (SKU) <span class="req">*</span></label>
                                <input type="text" name="sku" class="form-input" id="prodSku" value="<?php echo htmlspecialchars($prod->sku); ?>" required style="direction:ltr;text-align:right;">
                                <div class="form-hint"><i class="fas fa-info-circle"></i> رمز فريد — لا يمكن تكراره</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="fst-icon fst-amber"><i class="fas fa-layer-group"></i></span>
                            التصنيف والكمية
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">التصنيف</label>
                                <select name="category_id" class="form-input" id="prodCat">
                                    <option value="">-- اختر التصنيف --</option>
                                    <?php foreach($data['categories'] as $cat) : ?>
                                        <option value="<?php echo $cat->id; ?>" <?php echo ($prod->category_id == $cat->id) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">الكمية المتوفرة <span class="req">*</span></label>
                                <input type="number" name="quantity" class="form-input" id="prodQty" value="<?php echo $prod->quantity; ?>" min="0" required style="direction:ltr;text-align:right;">
                                <div class="form-hint"><i class="fas fa-info-circle"></i> الكمية الحالية في المستودع</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="fst-icon fst-purple"><i class="fas fa-money-bill-wave"></i></span>
                            التسعير
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">سعر البيع (ر.س) <span class="req">*</span></label>
                                <input type="number" step="0.01" name="price" class="form-input" id="prodPrice" value="<?php echo $prod->price; ?>" required style="direction:ltr;text-align:right;">
                                <div class="form-hint"><i class="fas fa-info-circle"></i> السعر الذي سيظهر في الفواتير</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">القيمة الإجمالية</label>
                                <input type="text" class="form-input" id="totalValue" readonly style="background:#f8fafc;color:var(--text-muted);cursor:default;direction:ltr;text-align:right;" value="<?php echo number_format($prod->quantity * $prod->price, 2); ?> ر.س">
                                <div class="form-hint"><i class="fas fa-calculator"></i> الكمية × سعر البيع</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="btnSubmit">
                            <i class="fas fa-save"></i>
                            حفظ التعديلات
                        </button>
                        <a href="<?php echo URL_ROOT; ?>/product/index" class="btn-cancel">
                            <i class="fas fa-arrow-right"></i>
                            رجوع للقائمة
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        const qtyInput = document.getElementById('prodQty');
        const priceInput = document.getElementById('prodPrice');
        const totalInput = document.getElementById('totalValue');

        function calcTotal() {
            const qty = parseFloat(qtyInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = qty * price;
            totalInput.value = total > 0 ? total.toLocaleString('ar-SA', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ر.س' : '0.00 ر.س';
        }

        qtyInput.addEventListener('input', calcTotal);
        priceInput.addEventListener('input', calcTotal);

        const form = document.getElementById('productForm');
        const btnSubmit = document.getElementById('btnSubmit');

        form.addEventListener('submit', function(e) {
            let valid = true;
            const name = document.getElementById('prodName');
            const sku = document.getElementById('prodSku');

            form.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
            form.querySelectorAll('.form-error').forEach(el => el.remove());

            if (!name.value.trim()) {
                name.classList.add('has-error');
                const err = document.createElement('div');
                err.className = 'form-error';
                err.innerHTML = '<i class="fas fa-exclamation-circle"></i> اسم المنتج مطلوب';
                name.parentNode.appendChild(err);
                valid = false;
            }

            if (!sku.value.trim()) {
                sku.classList.add('has-error');
                const err = document.createElement('div');
                err.className = 'form-error';
                err.innerHTML = '<i class="fas fa-exclamation-circle"></i> رمز المنتج مطلوب';
                sku.parentNode.appendChild(err);
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
                const firstErr = form.querySelector('.has-error');
                if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري الحفظ...';
            }
        });

        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>