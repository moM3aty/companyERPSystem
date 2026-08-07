<?php
// app/views/warehouse/create_transfer.php
$pageTitle = $data['title'] ?? 'نقل مخزون جديد';
$warehouses = $data['warehouses'] ?? [];
$products = $data['products'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'warehouse/transfers';
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
                        <a href="<?php echo URLROOT; ?>/warehouse/transfers">نقل المخزون</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>طلب جديد</span>
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
                <h2><i class="fas fa-truck-ramp-box" style="margin-left:8px;"></i> إنشاء طلب نقل مخزون</h2>
                <p>تأكد من توافر الكمية المطلوبة في المستودع المصدر قبل تنفيذ العملية</p>
            </div>

            <div class="form-card">
                <form action="<?php echo URLROOT; ?>/warehouse/create-transfer" method="POST" id="transferForm" novalidate>
                    
                    <div class="form-section">
                        <div class="form-section-title"><span class="fst-icon fst-teal"><i class="fas fa-map-location-dot"></i></span> مسار النقل</div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">من مستودع (المصدر) <span class="req">*</span></label>
                                <select name="from_warehouse" id="fromWh" class="form-input" required>
                                    <option value="">-- حدد مستودع --</option>
                                    <?php foreach ($warehouses as $wh) : ?>
                                        <option value="<?php echo $wh->id; ?>"><?php echo htmlspecialchars($wh->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">إلى مستودع (الوجهة) <span class="req">*</span></label>
                                <select name="to_warehouse" id="toWh" class="form-input" required>
                                    <option value="">-- حدد مستودع --</option>
                                    <?php foreach ($warehouses as $wh) : ?>
                                        <option value="<?php echo $wh->id; ?>"><?php echo htmlspecialchars($wh->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <div class="form-section-title"><span class="fst-icon fst-purple"><i class="fas fa-box"></i></span> تفاصيل المنتج</div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">المنتج المراد نقله <span class="req">*</span></label>
                                <select name="product_id" id="prodSel" class="form-input" required>
                                    <option value="">-- اختر المنتج --</option>
                                    <?php foreach ($products as $prod) : ?>
                                        <option value="<?php echo $prod->id; ?>"><?php echo htmlspecialchars($prod->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">الكمية <span class="req">*</span></label>
                                <input type="number" name="quantity" id="qtyVal" class="form-input" min="1" placeholder="مثال: 50" required style="direction:ltr; text-align:center; font-weight:700;">
                            </div>
                            <div class="form-group full">
                                <label class="form-label">ملاحظات / أسباب النقل</label>
                                <textarea name="notes" class="form-input" rows="2" placeholder="أدخل أي ملاحظات إضافية بخصوص هذا النقل..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="btnSubmit"><i class="fas fa-paper-plane"></i> تنفيذ طلب النقل</button>
                        <a href="<?php echo URLROOT; ?>/warehouse/transfers" class="btn-cancel">إلغاء</a>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        const form = document.getElementById('transferForm');
        const btnSubmit = document.getElementById('btnSubmit');
        
        form.addEventListener('submit', function(e) {
            let valid = true;
            const fromWh = document.getElementById('fromWh');
            const toWh = document.getElementById('toWh');
            const prod = document.getElementById('prodSel');
            const qty = document.getElementById('qtyVal');
            
            form.querySelectorAll('.form-input').forEach(el => el.classList.remove('has-error'));
            
            if (!fromWh.value) { fromWh.classList.add('has-error'); valid = false; }
            if (!toWh.value) { toWh.classList.add('has-error'); valid = false; }
            if (fromWh.value && toWh.value && fromWh.value === toWh.value) {
                fromWh.classList.add('has-error');
                toWh.classList.add('has-error');
                alert('لا يمكن نقل البضاعة لنفس المستودع المصدر!');
                valid = false;
            }
            if (!prod.value) { prod.classList.add('has-error'); valid = false; }
            if (!qty.value || parseInt(qty.value) <= 0) { qty.classList.add('has-error'); valid = false; }
            
            if (!valid) {
                e.preventDefault();
            } else {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري التنفيذ...';
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