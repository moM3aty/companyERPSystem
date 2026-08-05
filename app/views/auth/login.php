<?php
$flash = $data['flash'] ?? null;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title'] ?? 'تسجيل الدخول'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', sans-serif; }
        body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-wrapper { display: flex; width: 900px; max-width: 95%; background: #fff; border-radius: 24px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); min-height: 550px;}
        
        .login-branding { flex: 1; background: linear-gradient(135deg, #14b8a6, #0d9488); padding: 40px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #fff; text-align: center; position: relative; overflow: hidden;}
        .login-branding::before { content: ''; position: absolute; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%; top: -100px; right: -100px; }
        .login-branding::after { content: ''; position: absolute; width: 200px; height: 200px; background: rgba(245,158,11,0.15); border-radius: 50%; bottom: -50px; left: -50px; }
        .lb-icon { font-size: 64px; margin-bottom: 20px; text-shadow: 0 10px 20px rgba(0,0,0,0.2); position: relative; z-index: 2;}
        .lb-title { font-size: 36px; font-weight: 900; margin-bottom: 10px; position: relative; z-index: 2;}
        .lb-title span { color: #fef3c7; }
        .lb-desc { font-size: 16px; opacity: 0.9; line-height: 1.6; max-width: 80%; position: relative; z-index: 2;}
        
        .login-form-container { flex: 1; padding: 50px; display: flex; flex-direction: column; justify-content: center; }
        .lf-header { margin-bottom: 32px; }
        .lf-header h3 { font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 6px; }
        .lf-header p { font-size: 14px; color: #64748b; }
        
        .form-group { margin-bottom: 20px; position: relative; }
        .form-label { display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 8px; }
        .form-input { width: 100%; padding: 14px 16px 14px 44px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 15px; transition: all 0.3s; background: #f8fafc; color: #0f172a; }
        .form-input:focus { border-color: #14b8a6; background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(20,184,166,0.1); }
        .form-icon { position: absolute; left: 16px; top: 40px; color: #94a3b8; font-size: 16px; transition: color 0.3s; }
        .form-input:focus + .form-icon { color: #14b8a6; }
        
        .btn-login { width: 100%; padding: 16px; background: #0f172a; color: #fff; border: none; border-radius: 12px; font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.3s; margin-top: 10px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-login:hover { background: #14b8a6; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(20,184,166,0.3); }
        
        .flash-msg { padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .flash-error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .flash-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }

        .demo-credentials { margin-top: 24px; padding: 16px; background: #f1f5f9; border-radius: 10px; border: 1px dashed #cbd5e1; text-align: center; font-size: 13px; color: #475569; }
        .demo-credentials code { font-weight: 800; color: #14b8a6; background: #fff; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0;}

        @media (max-width: 768px) {
            .login-wrapper { flex-direction: column; }
            .login-branding { padding: 40px 20px; min-height: 250px; }
            .login-form-container { padding: 30px 20px; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-branding">
            <i class="fas fa-cubes lb-icon"></i>
            <h1 class="lb-title">ERP <span>Pro</span></h1>
            <p class="lb-desc">نظام متكامل لإدارة موارد مؤسستك باحترافية وأمان عالٍ.</p>
        </div>
        <div class="login-form-container">
            <div class="lf-header">
                <h3>مرحباً بك مجدداً 👋</h3>
                <p>قم بتسجيل الدخول للوصول إلى مساحة العمل الخاصة بك.</p>
            </div>
            
            <?php if ($flash) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                    <i class="fas fa-<?php echo $flash['type'] === 'error' ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo URL_ROOT; ?>/auth/login" method="POST" id="loginForm">
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-input" placeholder="admin@system.com" style="direction:ltr; text-align:right;" required>
                    <i class="fas fa-envelope form-icon"></i>
                </div>
                <div class="form-group">
                    <label class="form-label">كلمة المرور</label>
                    <input type="password" name="password" class="form-input" placeholder="••••••••" style="direction:ltr; text-align:right;" required>
                    <i class="fas fa-lock form-icon"></i>
                </div>
                
                <button type="submit" class="btn-login" id="submitBtn">
                    تسجيل الدخول <i class="fas fa-arrow-left"></i>
                </button>
            </form>

            <div class="demo-credentials">
                بناءً على التقرير 🚀: استخدم <code>admin@system.com</code> وكلمة المرور <code>admin</code> للدخول.
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري التحقق...';
            btn.style.pointerEvents = 'none';
        });
    </script>
</body>
</html>