<?php
// app/views/warehouse/create.php
$pageTitle = $data['title'] ?? 'إضافة مستودع جديد';
$flash = $data['flash'] ?? null;
$currentUrl = 'warehouse/index';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
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
                        <a href="<?php echo URLROOT; ?>/warehouse/index">المستودعات</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>مستودع جديد</span>
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
                <h2><i class="fas fa-warehouse" style="margin-left:8px;"></i> إضافة مستودع جديد للنظام</h2>
                <p>قم بإنشاء مستودع لربطه بطلبات الشراء وعمليات نقل المخزون بين الفروع</p>
            </div>

            <div class="form-card">
                <form action="<?php echo URLROOT; ?>/warehouse/create" method="POST" id="whForm" novalidate>
                    
                    <div class="form-section">
                        <div class="form-section-title"><span class="fst-icon fst-teal"><i class="fas fa-circle-info"></i></span> بيانات المستودع</div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">اسم المستودع أو الفرع <span class="req">*</span></label>
                                <input type="text" name="name" class="form-input" id="whName" placeholder="مثال: مستودع الرياض الفرعي" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">كود المستودع (معرف فريد) <span class="req">*</span></label>
                                <input type="text" name="code" class="form-input" id="whCode" placeholder="مثال: WH-Riyadh-01" required style="direction:ltr;text-align:right;">
                            </div>
                            <div class="form-group full">
                                <label class="form-label">العنوان والموقع الجغرافي</label>
                                <textarea name="address" class="form-input" rows="2" placeholder="المدينة، الحي، الشارع، أقرب معلم..."></textarea>
                                <div class="form-hint"><i class="fas fa-location-dot"></i> يستخدم كعنوان في طلبات الاستلام والشحن</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <div class="form-section-title"><span class="fst-icon fst-amber"><i class="fas fa-star"></i></span> إعدادات إضافية</div>
                        <div class="form-group">
                            <label class="toggle-label">
                                <div class="toggle-switch">
                                    <input type="checkbox" name="is_main" value="1">
                                    <span class="slider"></span>
                                </div>
                                <span class="toggle-text">تعيين كمستودع رئيسي للنظام</span>
                            </label>
                            <div class="form-hint" style="margin-top:8px;"><i class="fas fa-info-circle"></i> المستودع الرئيسي يستقبل أوامر الشراء بشكل افتراضي إن لم يحدد غيره.</div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="btnSubmit"><i class="fas fa-save"></i> حفظ المستودع</button>
                        <a href="<?php echo URLROOT; ?>/warehouse/index" class="btn-cancel">إلغاء</a>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        const form = document.getElementById('whForm');
        const btnSubmit = document.getElementById('btnSubmit');
        
        form.addEventListener('submit', function(e) {
            let valid = true;
            const name = document.getElementById('whName');
            const code = document.getElementById('whCode');
            
            form.querySelectorAll('.has-error').forEach(el => el.classList.remove('has-error'));
            form.querySelectorAll('.form-error').forEach(el => el.remove());
            
            if (!name.value.trim()) {
                name.classList.add('has-error');
                name.parentNode.appendChild(makeErr('يرجى إدخال اسم المستودع'));
                valid = false;
            }
            
            if (!code.value.trim()) {
                code.classList.add('has-error');
                code.parentNode.appendChild(makeErr('كود المستودع مطلوب'));
                valid = false;
            }
            
            if (!valid) {
                e.preventDefault();
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
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>