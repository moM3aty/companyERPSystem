<?php
//app/views/auth/login.php
 $flash = $data['flash'] ?? null;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title'] ?? 'تسجيل الدخول'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --bg-deep: #0a0e1a;
            --bg-card: #ffffff;
            --accent: #14b8a6;
            --accent-light: #2dd4bf;
            --accent-dark: #0d9488;
            --gold: #f59e0b;
            --gold-light: #fbbf24;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --border: #e2e8f0;
            --input-bg: #f8fafc;
            --error: #dc2626;
            --error-bg: #fee2e2;
            --success: #15803d;
            --success-bg: #dcfce7;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Cairo', sans-serif;
            background: var(--bg-deep);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* === خلفية متحركة === */
        .bg-scene {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }

        .bg-scene::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(20,184,166,0.15) 0%, transparent 70%);
            top: -200px;
            right: -150px;
            animation: floatBlob 12s ease-in-out infinite;
        }

        .bg-scene::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(245,158,11,0.1) 0%, transparent 70%);
            bottom: -200px;
            left: -100px;
            animation: floatBlob 15s ease-in-out infinite reverse;
        }

        @keyframes floatBlob {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(40px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        .grid-pattern {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            animation: gridMove 20s linear infinite;
        }

        @keyframes gridMove {
            0% { transform: translate(0, 0); }
            100% { transform: translate(60px, 60px); }
        }

        /* === جزيئات عائمة === */
        .particle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            opacity: 0;
            animation: particleFloat linear infinite;
        }

        @keyframes particleFloat {
            0% { opacity: 0; transform: translateY(100vh) scale(0); }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { opacity: 0; transform: translateY(-10vh) scale(1); }
        }

        /* === بطاقة تسجيل الدخول === */
        .login-wrapper {
            position: relative;
            z-index: 10;
            display: flex;
            width: 960px;
            max-width: 95%;
            background: var(--bg-card);
            border-radius: 28px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.05),
                0 25px 60px -12px rgba(0, 0, 0, 0.6),
                0 0 120px -40px rgba(20,184,166,0.2);
            min-height: 580px;
            animation: cardEntrance 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(30px) scale(0.97);
        }

        @keyframes cardEntrance {
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* === الجانب الترويجي === */
        .login-branding {
            flex: 0 0 400px;
            background: linear-gradient(160deg, #0f766e 0%, #0d9488 30%, #14b8a6 60%, #2dd4bf 100%);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-branding::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            top: -150px;
            right: -150px;
            animation: brandPulse 8s ease-in-out infinite;
        }

        .login-branding::after {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(251,191,36,0.15) 0%, transparent 70%);
            bottom: -80px;
            left: -80px;
            animation: brandPulse 10s ease-in-out infinite reverse;
        }

        @keyframes brandPulse {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50% { transform: scale(1.3); opacity: 1; }
        }

        /* أشكال هندسية عائمة */
        .geo-shape {
            position: absolute;
            border: 2px solid rgba(255,255,255,0.12);
            pointer-events: none;
            z-index: 1;
        }

        .geo-shape.s1 {
            width: 80px; height: 80px;
            border-radius: 16px;
            top: 12%; left: 15%;
            transform: rotate(45deg);
            animation: geoSpin 20s linear infinite;
        }

        .geo-shape.s2 {
            width: 50px; height: 50px;
            border-radius: 50%;
            bottom: 20%; right: 12%;
            animation: geoFloat 6s ease-in-out infinite;
        }

        .geo-shape.s3 {
            width: 0; height: 0;
            border: none;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-bottom: 35px solid rgba(255,255,255,0.08);
            top: 60%; left: 20%;
            animation: geoFloat 8s ease-in-out infinite reverse;
        }

        .geo-shape.s4 {
            width: 35px; height: 35px;
            border-radius: 8px;
            top: 25%; right: 20%;
            animation: geoSpin 15s linear infinite reverse;
        }

        .geo-shape.s5 {
            width: 60px; height: 60px;
            border-radius: 50%;
            border-style: dashed;
            bottom: 35%; left: 8%;
            animation: geoSpin 25s linear infinite;
        }

        @keyframes geoSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes geoFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(10deg); }
        }

        .lb-icon-wrapper {
            position: relative;
            z-index: 2;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 28px;
            border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 8px 32px rgba(0,0,0,0.15);
            animation: iconBreath 4s ease-in-out infinite;
        }

        @keyframes iconBreath {
            0%, 100% { transform: scale(1); box-shadow: 0 8px 32px rgba(0,0,0,0.15); }
            50% { transform: scale(1.05); box-shadow: 0 12px 40px rgba(0,0,0,0.2); }
        }

        .lb-icon {
            font-size: 44px;
            text-shadow: 0 4px 12px rgba(0,0,0,0.2);
            filter: drop-shadow(0 0 20px rgba(255,255,255,0.2));
        }

        .lb-title {
            font-size: 40px;
            font-weight: 900;
            margin-bottom: 8px;
            position: relative;
            z-index: 2;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .lb-title span {
            color: var(--gold-light);
            position: relative;
        }

        .lb-title span::after {
            content: '';
            position: absolute;
            bottom: 2px;
            right: 0;
            width: 100%;
            height: 3px;
            background: var(--gold-light);
            border-radius: 2px;
            opacity: 0.5;
        }

        .lb-desc {
            font-size: 15px;
            opacity: 0.85;
            line-height: 1.8;
            max-width: 85%;
            position: relative;
            z-index: 2;
            font-weight: 300;
        }

        /* نقاط الميزات */
        .lb-features {
            position: relative;
            z-index: 2;
            margin-top: 36px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
            max-width: 260px;
        }

        .lb-feat {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
            opacity: 0.9;
            background: rgba(255,255,255,0.08);
            padding: 10px 14px;
            border-radius: 12px;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.06);
            transition: all 0.3s;
        }

        .lb-feat:hover {
            background: rgba(255,255,255,0.14);
            transform: translateX(-4px);
        }

        .lb-feat i {
            font-size: 14px;
            color: var(--gold-light);
            width: 20px;
            text-align: center;
        }

        /* === جانب النموذج === */
        .login-form-container {
            flex: 1;
            padding: 55px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .lf-header {
            margin-bottom: 36px;
        }

        .lf-header h3 {
            font-size: 26px;
            font-weight: 900;
            color: var(--text-dark);
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }

        .lf-header p {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 400;
        }

        /* === الحقول === */
        .form-group {
            margin-bottom: 22px;
            position: relative;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 15px 48px 15px 16px;
            border: 2px solid var(--border);
            border-radius: 14px;
            font-size: 15px;
            font-family: 'Cairo', sans-serif;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--input-bg);
            color: var(--text-dark);
            direction: ltr;
            text-align: right;
        }

        .form-input::placeholder {
            color: var(--text-light);
            font-weight: 300;
        }

        .form-input:focus {
            border-color: var(--accent);
            background: #fff;
            outline: none;
            box-shadow: 0 0 0 4px rgba(20,184,166,0.08), 0 4px 12px rgba(20,184,166,0.06);
        }

        .form-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 16px;
            transition: all 0.35s;
            pointer-events: none;
        }

        .form-input:focus ~ .form-icon {
            color: var(--accent);
            transform: translateY(-50%) scale(1.15);
        }

        /* زر إظهار كلمة المرور */
        .toggle-pass {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 15px;
            padding: 4px;
            transition: color 0.3s;
        }

        .toggle-pass:hover { color: var(--accent); }

        /* === الزر === */
        .btn-login {
            width: 100%;
            padding: 17px;
            background: linear-gradient(135deg, var(--text-dark) 0%, #1e293b 100%);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 700;
            font-family: 'Cairo', sans-serif;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--accent-dark) 0%, var(--accent) 100%);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .btn-login:hover::before { opacity: 1; }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(20,184,166,0.35);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(20,184,166,0.2);
        }

        .btn-login span,
        .btn-login i {
            position: relative;
            z-index: 2;
        }

        .btn-login.loading {
            pointer-events: none;
        }

        .btn-login.loading::before { opacity: 1; }

        /* === رسائل الفلاش === */
        .flash-msg {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 22px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: flashIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes flashIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .flash-error {
            background: var(--error-bg);
            color: var(--error);
            border: 1px solid #fecaca;
        }

        .flash-success {
            background: var(--success-bg);
            color: var(--success);
            border: 1px solid #bbf7d0;
        }

        .flash-msg i { font-size: 16px; flex-shrink: 0; }

        /* === خط فاصل مزخرف === */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: var(--text-light);
            font-size: 12px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(to left, transparent, var(--border), transparent);
        }

        /* === تذييل === */
        .lf-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 12px;
            color: var(--text-light);
        }

        .lf-footer i { color: var(--accent); margin: 0 2px; }

        /* === الاستجابة === */
        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                min-height: auto;
                border-radius: 20px;
            }
            .login-branding {
                flex: none;
                padding: 40px 24px 30px;
                min-height: 220px;
            }
            .lb-features { display: none; }
            .lb-title { font-size: 32px; }
            .login-form-container { padding: 30px 24px 36px; }
            .lf-header h3 { font-size: 22px; }
        }

        @media (max-width: 400px) {
            .login-form-container { padding: 24px 18px 30px; }
            .form-input { padding: 13px 42px 13px 14px; font-size: 14px; }
        }

        /* === تقليل الحركة === */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <!-- خلفية متحركة -->
    <div class="bg-scene">
        <div class="grid-pattern"></div>
    </div>

    <div class="login-wrapper">
        <!-- الجانب الترويجي -->
        <div class="login-branding">
            <div class="geo-shape s1"></div>
            <div class="geo-shape s2"></div>
            <div class="geo-shape s3"></div>
            <div class="geo-shape s4"></div>
            <div class="geo-shape s5"></div>

            <div class="lb-icon-wrapper">
                <i class="fas fa-cubes lb-icon"></i>
            </div>
            <h1 class="lb-title">ERP <span>Pro</span></h1>
            <p class="lb-desc">نظام متكامل لإدارة موارد مؤسستك باحترافية وأمان عالٍ.</p>

            <div class="lb-features">
                <div class="lb-feat">
                    <i class="fas fa-shield-halved"></i>
                    حماية متقدمة بتشفير كامل
                </div>
                <div class="lb-feat">
                    <i class="fas fa-chart-line"></i>
                    تقارير وتحليلات لحظية
                </div>
                <div class="lb-feat">
                    <i class="fas fa-bolt"></i>
                    أداء فائق السرعة
                </div>
            </div>
        </div>

        <!-- جانب النموذج -->
        <div class="login-form-container">
            <div class="lf-header">
                <h3>مرحباً بك مجدداً</h3>
                <p>قم بتسجيل الدخول للوصول إلى مساحة العمل الخاصة بك.</p>
            </div>

            <?php if ($flash) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                    <i class="fas fa-<?php echo $flash['type'] === 'error' ? 'circle-exclamation' : 'circle-check'; ?>"></i>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo URLROOT; ?>/auth/login" method="POST" id="loginForm">
                <div class="form-group">
                    <label class="form-label" for="email">البريد الإلكتروني</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" id="email" class="form-input" placeholder="admin@system.com" required autocomplete="email">
                        <i class="fas fa-envelope form-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">كلمة المرور</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" class="form-input" placeholder="••••••••" required autocomplete="current-password">
                        <i class="fas fa-lock form-icon"></i>
                        <button type="button" class="toggle-pass" id="togglePass" aria-label="إظهار كلمة المرور">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="submitBtn">
                    <span>تسجيل الدخول</span>
                    <i class="fas fa-arrow-left"></i>
                </button>
            </form>

            <div class="divider">مؤمن بتقنية SSL</div>

            <div class="lf-footer">
                <i class="fas fa-lock"></i> جميع البيانات مشفرة ومحمية
            </div>
        </div>
    </div>

    <script>
        /* === إنشاء الجزيئات العائمة === */
        (function createParticles() {
            const scene = document.querySelector('.bg-scene');
            const colors = ['rgba(20,184,166,0.4)', 'rgba(245,158,11,0.3)', 'rgba(255,255,255,0.15)'];
            for (let i = 0; i < 25; i++) {
                const p = document.createElement('div');
                p.classList.add('particle');
                const size = Math.random() * 4 + 2;
                p.style.width = size + 'px';
                p.style.height = size + 'px';
                p.style.background = colors[Math.floor(Math.random() * colors.length)];
                p.style.left = Math.random() * 100 + '%';
                p.style.animationDuration = (Math.random() * 12 + 8) + 's';
                p.style.animationDelay = (Math.random() * 10) + 's';
                scene.appendChild(p);
            }
        })();

        /* === إظهار/إخفاء كلمة المرور === */
        document.getElementById('togglePass').addEventListener('click', function() {
            const passInput = document.getElementById('password');
            const icon = this.querySelector('i');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });

        /* === حالة التحميل عند الإرسال === */
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.classList.add('loading');
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin" style="position:relative;z-index:2;"></i><span style="position:relative;z-index:2;">جاري التحقق...</span>';
        });

        /* === تأثير التركيز المتتابع === */
        document.querySelectorAll('.form-input').forEach((input, i) => {
            input.addEventListener('focus', function() {
                this.closest('.form-group').style.transform = 'translateX(-3px)';
                this.closest('.form-group').style.transition = 'transform 0.3s cubic-bezier(0.4,0,0.2,1)';
            });
            input.addEventListener('blur', function() {
                this.closest('.form-group').style.transform = 'translateX(0)';
            });
        });
    </script>
</body>
</html>