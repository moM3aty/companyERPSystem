<?php
// app/views/auth/login.php
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | نظام ERP المتقدم</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #14b8a6;
            --primary-dark: #0d9488;
            --primary-light: #5eead4;
            --accent: #f59e0b;
            --accent-dark: #d97706;
            --bg-deep: #020617;
            --bg-dark: #0f172a;
            --bg-card: rgba(15, 23, 42, 0.85);
            --text-light: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(255, 255, 255, 0.08);
            --danger: #ef4444;
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Cairo', sans-serif;
            min-height: 100vh;
            background: var(--bg-deep);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* === خلفية متحركة === */
        .scene-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: 
                radial-gradient(ellipse 80% 60% at 70% 20%, rgba(20,184,166,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 20% 80%, rgba(245,158,11,0.08) 0%, transparent 60%),
                var(--bg-deep);
        }

        .scene-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 64px 64px;
        }

        /* أشكال عائمة */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 1;
            pointer-events: none;
        }
        .orb-1 {
            width: 500px; height: 500px;
            background: rgba(20,184,166,0.12);
            top: -15%; right: -10%;
            animation: orbFloat1 20s ease-in-out infinite;
        }
        .orb-2 {
            width: 400px; height: 400px;
            background: rgba(245,158,11,0.08);
            bottom: -10%; left: -8%;
            animation: orbFloat2 25s ease-in-out infinite;
        }
        .orb-3 {
            width: 250px; height: 250px;
            background: rgba(20,184,166,0.06);
            top: 60%; left: 50%;
            animation: orbFloat3 18s ease-in-out infinite;
        }

        @keyframes orbFloat1 {
            0%, 100% { transform: translate(0,0) scale(1); }
            33% { transform: translate(-60px,50px) scale(1.08); }
            66% { transform: translate(30px,-30px) scale(0.95); }
        }
        @keyframes orbFloat2 {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(50px,-70px) scale(1.12); }
        }
        @keyframes orbFloat3 {
            0%, 100% { transform: translate(0,0); }
            50% { transform: translate(-40px,40px); }
        }

        /* جزيئات */
        .particle {
            position: fixed;
            border-radius: 50%;
            background: var(--primary);
            z-index: 2;
            pointer-events: none;
        }

        /* === بطاقة الدخول === */
        .login-container {
            position: relative;
            z-index: 10;
            display: flex;
            width: 920px;
            max-width: 95vw;
            min-height: 580px;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.03),
                0 24px 80px rgba(0,0,0,0.5),
                0 8px 32px rgba(0,0,0,0.3);
            backdrop-filter: blur(40px);
            animation: cardEnter 0.8s cubic-bezier(0.16,1,0.3,1) both;
        }

        @keyframes cardEnter {
            from { opacity: 0; transform: translateY(30px) scale(0.97); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* لوحة العلامة التجارية */
        .brand-side {
            flex: 0 0 380px;
            background: linear-gradient(160deg, #0f172a 0%, #1e293b 60%, #0f172a 100%);
            padding: 48px 36px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .brand-side::before {
            content: '';
            position: absolute;
            width: 260px; height: 260px;
            background: radial-gradient(circle, rgba(20,184,166,0.18), transparent 70%);
            top: 50%; left: 50%;
            transform: translate(-50%,-50%);
            animation: brandPulse 5s ease-in-out infinite;
        }

        @keyframes brandPulse {
            0%, 100% { opacity: 0.6; transform: translate(-50%,-50%) scale(1); }
            50% { opacity: 1; transform: translate(-50%,-50%) scale(1.15); }
        }

        .brand-logo {
            width: 76px; height: 76px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #fff;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
            box-shadow: 0 8px 30px rgba(20,184,166,0.25);
            animation: logoFloat 6s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .brand-name {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-light);
            margin-bottom: 6px;
            position: relative;
            z-index: 2;
            letter-spacing: -0.5px;
        }

        .brand-name span { color: var(--primary); }

        .brand-desc {
            font-size: 13px;
            color: var(--text-muted);
            text-align: center;
            line-height: 1.9;
            margin-bottom: 36px;
            position: relative;
            z-index: 2;
            max-width: 280px;
        }

        .brand-features {
            list-style: none;
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 280px;
        }

        .brand-features li {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--text-muted);
            font-size: 13px;
            padding: 8px 0;
            opacity: 0;
            animation: featureIn 0.5s ease forwards;
        }

        .brand-features li:nth-child(1) { animation-delay: 0.3s; }
        .brand-features li:nth-child(2) { animation-delay: 0.45s; }
        .brand-features li:nth-child(3) { animation-delay: 0.6s; }
        .brand-features li:nth-child(4) { animation-delay: 0.75s; }
        .brand-features li:nth-child(5) { animation-delay: 0.9s; }

        @keyframes featureIn {
            from { opacity: 0; transform: translateX(20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .brand-features li .feat-icon {
            width: 28px; height: 28px;
            background: rgba(20,184,166,0.1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: var(--primary);
            font-size: 11px;
        }

        /* لوحة النموذج */
        .form-side {
            flex: 1;
            background: var(--bg-card);
            padding: 52px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header { margin-bottom: 32px; }

        .form-header h2 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-light);
            margin-bottom: 6px;
        }

        .form-header p {
            font-size: 14px;
            color: var(--text-muted);
        }

        /* حقول الإدخال */
        .field {
            margin-bottom: 22px;
            transition: transform 0.3s ease;
        }

        .field:focus-within {
            transform: translateX(-3px);
        }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .field-inner {
            position: relative;
        }

        .field-inner .fi-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 15px;
            transition: color 0.3s;
            pointer-events: none;
        }

        .field-inner input {
            width: 100%;
            padding: 14px 48px 14px 48px;
            background: rgba(255,255,255,0.04);
            border: 1.5px solid var(--border);
            border-radius: 14px;
            color: var(--text-light);
            font-family: 'Cairo', sans-serif;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
        }

        .field-inner input::placeholder {
            color: rgba(148,163,184,0.4);
        }

        .field-inner input:focus {
            border-color: var(--primary);
            background: rgba(20,184,166,0.04);
            box-shadow: 0 0 0 4px rgba(20,184,166,0.08);
        }

        .field-inner input:focus ~ .fi-icon {
            color: var(--primary);
        }

        .field-inner input.has-error {
            border-color: var(--danger);
            background: rgba(239,68,68,0.04);
        }

        .field-inner input.has-error ~ .fi-icon {
            color: var(--danger);
        }

        .toggle-pass {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            cursor: pointer;
            font-size: 14px;
            transition: color 0.2s;
            pointer-events: all;
        }

        .toggle-pass:hover { color: var(--text-light); }

        .field-error {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--danger);
            font-size: 12px;
            margin-top: 7px;
            animation: shakeIn 0.4s ease;
        }

        @keyframes shakeIn {
            0% { transform: translateX(-8px); opacity: 0; }
            40% { transform: translateX(4px); }
            70% { transform: translateX(-2px); }
            100% { transform: translateX(0); opacity: 1; }
        }

        /* زر الدخول */
        .btn-submit {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            font-family: 'Cairo', sans-serif;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            margin-top: 4px;
        }

        .btn-submit::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: translateX(-100%);
            transition: transform 0.6s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(20,184,166,0.3);
        }

        .btn-submit:hover::after {
            transform: translateX(100%);
        }

        .btn-submit:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(20,184,166,0.2);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-submit .spinner {
            display: none;
        }

        .btn-submit.loading .btn-label { display: none; }
        .btn-submit.loading .spinner { display: inline-flex; align-items: center; gap: 8px; }

        /* استجابة */
        @media (max-width: 768px) {
            .brand-side { display: none; }
            .login-container { border-radius: 20px; min-height: auto; }
            .form-side { padding: 36px 24px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>

    <!-- خلفية المشهد -->
    <div class="scene-bg"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- بطاقة الدخول -->
    <div class="login-container">

        <!-- الجانب التجاري -->
        <div class="brand-side">
            <div class="brand-logo">
                <i class="fas fa-cubes"></i>
            </div>
            <h1 class="brand-name">ERP <span>Pro</span></h1>
            <p class="brand-desc">نظام إدارة موارد المؤسسة المتكامل لإدارة أعمالك بكفاءة واحترافية عالية</p>
            <ul class="brand-features">
                <li>
                    <span class="feat-icon"><i class="fas fa-users"></i></span>
                    إدارة الموارد البشرية والموظفين
                </li>
                <li>
                    <span class="feat-icon"><i class="fas fa-warehouse"></i></span>
                    متابعة المخزون والمنتجات
                </li>
                <li>
                    <span class="feat-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                    إدارة الفواتير والمبيعات
                </li>
                <li>
                    <span class="feat-icon"><i class="fas fa-chart-pie"></i></span>
                    التقارير المالية والمحاسبة
                </li>
                <li>
                    <span class="feat-icon"><i class="fas fa-gauge-high"></i></span>
                    لوحة تحكم تحليلية متقدمة
                </li>
            </ul>
        </div>

        <!-- جانب النموذج -->
        <div class="form-side">
            <div class="form-header">
                <h2>مرحباً بعودتك</h2>
                <p>سجّل دخولك للوصول إلى لوحة التحكم</p>
            </div>

            <form action="<?php echo URL_ROOT; ?>/auth/login" method="POST" id="loginForm" novalidate>
                <!-- البريد الإلكتروني -->
                <div class="field">
                    <label for="email">البريد الإلكتروني</label>
                    <div class="field-inner">
                        <input type="email" name="email" id="email"
                               class="<?php echo !empty($data['email_err']) ? 'has-error' : ''; ?>"
                               value="<?php echo $data['email'] ?? ''; ?>"
                               placeholder="example@company.com"
                               required autocomplete="email">
                        <i class="fas fa-envelope fi-icon"></i>
                    </div>
                    <?php if(!empty($data['email_err'])) : ?>
                        <div class="field-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $data['email_err']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- كلمة المرور -->
                <div class="field">
                    <label for="password">كلمة المرور</label>
                    <div class="field-inner">
                        <input type="password" name="password" id="password"
                               class="<?php echo !empty($data['password_err']) ? 'has-error' : ''; ?>"
                               placeholder="أدخل كلمة المرور"
                               required autocomplete="current-password">
                        <i class="fas fa-lock fi-icon"></i>
                        <i class="fas fa-eye toggle-pass" id="togglePass" aria-label="إظهار/إخفاء كلمة المرور"></i>
                    </div>
                    <?php if(!empty($data['password_err'])) : ?>
                        <div class="field-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $data['password_err']; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- زر الدخول -->
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <span class="btn-label">تسجيل الدخول</span>
                    <span class="spinner">
                        <i class="fas fa-circle-notch fa-spin"></i>
                        جاري التحقق...
                    </span>
                </button>
            </form>
        </div>
    </div>

    <script>
        /* إظهار/إخفاء كلمة المرور */
        const togglePass = document.getElementById('togglePass');
        const passInput = document.getElementById('password');
        togglePass.addEventListener('click', function() {
            const isPassword = passInput.type === 'password';
            passInput.type = isPassword ? 'text' : 'password';
            this.classList.toggle('fa-eye', !isPassword);
            this.classList.toggle('fa-eye-slash', isPassword);
        });

        /* حالة التحميل عند الإرسال */
        const loginForm = document.getElementById('loginForm');
        const btnSubmit = document.getElementById('btnSubmit');
        loginForm.addEventListener('submit', function() {
            btnSubmit.classList.add('loading');
            btnSubmit.disabled = true;
        });

        /* إنشاء جزيئات عائمة */
        (function createParticles() {
            for (let i = 0; i < 25; i++) {
                const p = document.createElement('div');
                p.className = 'particle';
                const size = Math.random() * 3 + 1.5;
                p.style.cssText = `
                    width: ${size}px;
                    height: ${size}px;
                    left: ${Math.random() * 100}vw;
                    top: ${Math.random() * 100}vh;
                    opacity: ${Math.random() * 0.25 + 0.05};
                    animation: orbFloat${(i % 3) + 1} ${Math.random() * 15 + 12}s ease-in-out infinite;
                    animation-delay: -${Math.random() * 12}s;
                `;
                document.body.appendChild(p);
            }
        })();
    </script>
</body>
</html>