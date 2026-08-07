<?php
// app/views/layouts/main.php

$db = Database::getInstance();
$cid = Session::get('company_id') ?: 1; 

$db->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('company_name', 'company_logo') AND (company_id = :cid OR company_id IS NULL) ORDER BY company_id DESC");
$db->bind(':cid', $cid);
$sysSettings = $db->resultSet();

$companyName = 'ERP Pro';
$companyLogo = '';

foreach ($sysSettings as $s) {
    if ($s->setting_key === 'company_name' && !empty($s->setting_value)) {
        $companyName = $s->setting_value;
    }
    if ($s->setting_key === 'company_logo' && !empty($s->setting_value)) {
        $cleanPath = ltrim($s->setting_value, '/');
        $physicalPath = dirname(APP_ROOT) . '/public/' . $cleanPath;
        if (file_exists($physicalPath)) {
            $ext = pathinfo($physicalPath, PATHINFO_EXTENSION);
            $imgData = file_get_contents($physicalPath);
            $companyLogo = 'data:image/' . $ext . ';base64,' . base64_encode($imgData);
        } else {
            if (strpos(URLROOT, 'public') === false && strpos($cleanPath, 'public') === false) {
                $companyLogo = URLROOT . '/public/' . $cleanPath;
            } else {
                $companyLogo = URLROOT . '/' . $cleanPath;
            }
        }
    }
}

// قراءة الـ CSS ودمجه مباشرة لضمان عدم وجود أخطاء في الروابط
$cssPhysicalPath = dirname(APP_ROOT) . '/public/assets/css/style.css';
$inlineCss = '';
if (file_exists($cssPhysicalPath)) {
    $inlineCss = file_get_contents($cssPhysicalPath);
}

$unreadNotifs = [];
$notifCount = 0;
if (Session::isLoggedIn()) {
    require_once APP_ROOT . '/app/models/Notification.php';
    $notifModel = new Notification();
    $unreadNotifs = $notifModel->getUnread(Session::getUserId(), 10);
    $notifCount = count($unreadNotifs);
}

$title = $data['title'] ?? 'ERP Pro';
$userName = Session::getUserName();
$userRole = Session::getUserRole();
$currentUri = $_SERVER['REQUEST_URI'] ?? '';

function isActive($uri, $paths) {
    $checkUri = $uri . '/'; 
    foreach ($paths as $path) {
        if (strpos($checkUri, $path . '/') !== false) {
            return true;
        }
    }
    return false;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - <?php echo htmlspecialchars($companyName); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <?php if (!empty($inlineCss)): ?>
        <style>
            <?php echo $inlineCss; ?>
        </style>
    <?php else: ?>
        <link rel="stylesheet" href="<?php echo URLROOT; ?>/assets/css/style.css?v=<?php echo time(); ?>">
    <?php endif; ?>
    
</head>
<body>

    <div class="app-wrapper">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <!-- 🌟 Sidebar 🌟 -->
        <aside class="sidebar d-print-none" id="sidebar">
            
            <div style="padding: 30px 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); background: var(--slate-900); min-height: 180px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                <?php if(!empty($companyLogo)): ?>
                    <img src="<?php echo $companyLogo; ?>" alt="Logo" style="width: 85px; height: 85px; border-radius: 12px; object-fit: contain; background: #ffffff; padding: 6px; margin-bottom: 15px; border: 2px solid var(--primary); display: block; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                <?php else: ?>
                    <div style="width: 85px; height: 85px; border-radius: 12px; background: #ffffff; color: var(--primary); font-size: 36px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; border: 2px solid var(--primary); box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                        <i class="fas fa-building"></i>
                    </div>
                <?php endif; ?>
                
                <h2 style="color: #ffffff; font-size: 16px; font-weight: 800; margin: 0; width: 100%; white-space: normal; word-wrap: break-word; line-height: 1.4;"><?php echo htmlspecialchars($companyName); ?></h2>
                <div style="font-size: 10px; color: var(--primary-light); margin-top: 8px; font-weight: 900; letter-spacing: 1px; background: rgba(14, 165, 233, 0.15); padding: 4px 12px; border-radius: 20px; border: 1px solid rgba(14, 165, 233, 0.3);"><i class="fas fa-cloud"></i> ERP SYSTEM</div>
            </div>
            
            <div class="sidebar-scroll-area">
                <nav class="nav-menu">
                    <div class="nav-item">
                        <a href="<?php echo URLROOT; ?>/dashboard/index" class="nav-link <?php echo isActive($currentUri, ['/dashboard']) ? 'active' : ''; ?>">
                            <div class="nav-link-content"><i class="fas fa-chart-pie nav-icon"></i><span class="nav-text">لوحة القيادة</span></div>
                        </a>
                    </div>

                    <div class="nav-item">
                        <a href="<?php echo URLROOT; ?>/pos/index" class="nav-link <?php echo isActive($currentUri, ['/pos']) ? 'active' : ''; ?>">
                            <div class="nav-link-content"><i class="fas fa-cash-register nav-icon text-success"></i><span class="nav-text">نقطة البيع (POS)</span></div>
                        </a>
                    </div>

                    <?php $salesActive = isActive($currentUri, ['/customer', '/lead', '/opportunity', '/quote', '/sale', '/campaign', '/saleReturn']); ?>
                    <div class="nav-item has-dropdown">
                        <div class="nav-link dropdown-toggle <?php echo $salesActive ? 'active open' : ''; ?>" data-target="submenu-sales">
                            <div class="nav-link-content">
                                <i class="fas fa-bullseye nav-icon"></i><span class="nav-text">المبيعات و CRM</span>
                            </div>
                            <i class="fas fa-chevron-down arrow"></i>
                        </div>
                        <ul class="submenu <?php echo $salesActive ? 'active' : ''; ?>" id="submenu-sales">
                            <li><a href="<?php echo URLROOT; ?>/lead/index" class="sub-link <?php echo isActive($currentUri, ['/lead']) ? 'active' : ''; ?>">العملاء المحتملين</a></li>
                            <li><a href="<?php echo URLROOT; ?>/opportunity/index" class="sub-link <?php echo isActive($currentUri, ['/opportunity']) ? 'active' : ''; ?>">مسار الفرص (Pipeline)</a></li>
                            <li><a href="<?php echo URLROOT; ?>/quote/index" class="sub-link <?php echo isActive($currentUri, ['/quote']) ? 'active' : ''; ?>">عروض الأسعار</a></li>
                            <li><a href="<?php echo URLROOT; ?>/sale/index" class="sub-link <?php echo isActive($currentUri, ['/sale']) ? 'active' : ''; ?>">فواتير المبيعات</a></li>
                            <li><a href="<?php echo URLROOT; ?>/customer/index" class="sub-link <?php echo isActive($currentUri, ['/customer']) ? 'active' : ''; ?>">دليل العملاء</a></li>
                            <li><a href="<?php echo URLROOT; ?>/campaign/index" class="sub-link <?php echo isActive($currentUri, ['/campaign']) ? 'active' : ''; ?>">الحملات التسويقية</a></li>
                        </ul>
                    </div>

                    <?php $purchasesActive = isActive($currentUri, ['/supplier', '/purchase', '/product', '/category', '/stocktake', '/productBatch', '/warehouse', '/purchaseRequest']); ?>
                    <div class="nav-item has-dropdown">
                        <div class="nav-link dropdown-toggle <?php echo $purchasesActive ? 'active open' : ''; ?>" data-target="submenu-purchases">
                            <div class="nav-link-content">
                                <i class="fas fa-boxes-stacked nav-icon"></i><span class="nav-text">المشتريات والمخازن</span>
                            </div>
                            <i class="fas fa-chevron-down arrow"></i>
                        </div>
                        <ul class="submenu <?php echo $purchasesActive ? 'active' : ''; ?>" id="submenu-purchases">
                            <li><a href="<?php echo URLROOT; ?>/purchaseRequest/index" class="sub-link <?php echo isActive($currentUri, ['/purchaseRequest']) ? 'active' : ''; ?>">طلبات شراء داخلية (PR)</a></li>
                            <li><a href="<?php echo URLROOT; ?>/purchase/index" class="sub-link <?php echo isActive($currentUri, ['/purchase']) ? 'active' : ''; ?>">أوامر الشراء (PO)</a></li>
                            <li><a href="<?php echo URLROOT; ?>/supplier/index" class="sub-link <?php echo isActive($currentUri, ['/supplier']) ? 'active' : ''; ?>">دليل الموردين</a></li>
                            <li><a href="<?php echo URLROOT; ?>/product/index" class="sub-link <?php echo isActive($currentUri, ['/product']) ? 'active' : ''; ?>">المنتجات والمخزون</a></li>
                            <li><a href="<?php echo URLROOT; ?>/warehouse/index" class="sub-link <?php echo isActive($currentUri, ['/warehouse']) ? 'active' : ''; ?>">المستودعات والنقل</a></li>
                            <li><a href="<?php echo URLROOT; ?>/productBatch/index" class="sub-link <?php echo isActive($currentUri, ['/productBatch']) ? 'active' : ''; ?>">التشغيلات والسيريال</a></li>
                            <li><a href="<?php echo URLROOT; ?>/stocktake/index" class="sub-link <?php echo isActive($currentUri, ['/stocktake']) ? 'active' : ''; ?>">تسويات الجرد</a></li>
                        </ul>
                    </div>

                    <?php $financeActive = isActive($currentUri, ['/accounting', '/account', '/treasury', '/payment', '/expense', '/journal', '/collection']); ?>
                    <div class="nav-item has-dropdown">
                        <div class="nav-link dropdown-toggle <?php echo $financeActive ? 'active open' : ''; ?>" data-target="submenu-finance">
                            <div class="nav-link-content">
                                <i class="fas fa-calculator nav-icon"></i><span class="nav-text">المالية والمحاسبة</span>
                            </div>
                            <i class="fas fa-chevron-down arrow"></i>
                        </div>
                        <ul class="submenu <?php echo $financeActive ? 'active' : ''; ?>" id="submenu-finance">
                            <li><a href="<?php echo URLROOT; ?>/accounting/dashboard" class="sub-link <?php echo isActive($currentUri, ['/accounting']) ? 'active' : ''; ?>">اللوحة المالية</a></li>
                            <li><a href="<?php echo URLROOT; ?>/treasury/index" class="sub-link <?php echo isActive($currentUri, ['/treasury']) ? 'active' : ''; ?>">الصندوق والبنوك</a></li>
                            <li><a href="<?php echo URLROOT; ?>/payment/index" class="sub-link <?php echo isActive($currentUri, ['/payment']) ? 'active' : ''; ?>">سندات الصرف والقبض</a></li>
                            <li><a href="<?php echo URLROOT; ?>/expense/index" class="sub-link <?php echo isActive($currentUri, ['/expense']) ? 'active' : ''; ?>">المصروفات التشغيلية</a></li>
                            <li><a href="<?php echo URLROOT; ?>/journal/index" class="sub-link <?php echo isActive($currentUri, ['/journal']) ? 'active' : ''; ?>">القيود اليومية</a></li>
                            <li><a href="<?php echo URLROOT; ?>/account/tree" class="sub-link <?php echo isActive($currentUri, ['/account']) ? 'active' : ''; ?>">شجرة الحسابات</a></li>
                        </ul>
                    </div>

                    <?php $hrActive = isActive($currentUri, ['/employee', '/attendance', '/leave', '/payroll', '/advance', '/sanction', '/appraisal']); ?>
                    <div class="nav-item has-dropdown">
                        <div class="nav-link dropdown-toggle <?php echo $hrActive ? 'active open' : ''; ?>" data-target="submenu-hr">
                            <div class="nav-link-content">
                                <i class="fas fa-users-gear nav-icon"></i><span class="nav-text">الموارد البشرية</span>
                            </div>
                            <i class="fas fa-chevron-down arrow"></i>
                        </div>
                        <ul class="submenu <?php echo $hrActive ? 'active' : ''; ?>" id="submenu-hr">
                            <li><a href="<?php echo URLROOT; ?>/employee/index" class="sub-link <?php echo isActive($currentUri, ['/employee']) ? 'active' : ''; ?>">شؤون الموظفين</a></li>
                            <li><a href="<?php echo URLROOT; ?>/attendance/index" class="sub-link <?php echo isActive($currentUri, ['/attendance']) ? 'active' : ''; ?>">الحضور والانصراف</a></li>
                            <li><a href="<?php echo URLROOT; ?>/leave/index" class="sub-link <?php echo isActive($currentUri, ['/leave']) ? 'active' : ''; ?>">الإجازات والمغادرات</a></li>
                            <li><a href="<?php echo URLROOT; ?>/advance/index" class="sub-link <?php echo isActive($currentUri, ['/advance']) ? 'active' : ''; ?>">السلف والعهد</a></li>
                            <li><a href="<?php echo URLROOT; ?>/sanction/index" class="sub-link <?php echo isActive($currentUri, ['/sanction']) ? 'active' : ''; ?>">الجزاءات والمخالفات</a></li>
                            <li><a href="<?php echo URLROOT; ?>/appraisal/index" class="sub-link <?php echo isActive($currentUri, ['/appraisal']) ? 'active' : ''; ?>">تقييمات الأداء</a></li>
                            <li><a href="<?php echo URLROOT; ?>/payroll/index" class="sub-link <?php echo isActive($currentUri, ['/payroll']) ? 'active' : ''; ?>">مسيرات الرواتب</a></li>
                        </ul>
                    </div>

                    <?php $pmActive = isActive($currentUri, ['/project', '/timesheet', '/contract', '/fixedAsset']); ?>
                    <div class="nav-item has-dropdown">
                        <div class="nav-link dropdown-toggle <?php echo $pmActive ? 'active open' : ''; ?>" data-target="submenu-pm">
                            <div class="nav-link-content">
                                <i class="fas fa-diagram-project nav-icon"></i><span class="nav-text">المشاريع والأصول</span>
                            </div>
                            <i class="fas fa-chevron-down arrow"></i>
                        </div>
                        <ul class="submenu <?php echo $pmActive ? 'active' : ''; ?>" id="submenu-pm">
                            <li><a href="<?php echo URLROOT; ?>/project/index" class="sub-link <?php echo isActive($currentUri, ['/project']) ? 'active' : ''; ?>">إدارة المشاريع والمهام</a></li>
                            <li><a href="<?php echo URLROOT; ?>/timesheet/index" class="sub-link <?php echo isActive($currentUri, ['/timesheet']) ? 'active' : ''; ?>">تتبع أوقات العمل</a></li>
                            <li><a href="<?php echo URLROOT; ?>/contract/index" class="sub-link <?php echo isActive($currentUri, ['/contract']) ? 'active' : ''; ?>">إدارة العقود</a></li>
                            <li><a href="<?php echo URLROOT; ?>/fixedAsset/index" class="sub-link <?php echo isActive($currentUri, ['/fixedAsset']) ? 'active' : ''; ?>">الأصول الثابتة (الإهلاك)</a></li>
                        </ul>
                    </div>
                    
                    <?php $sysActive = isActive($currentUri, ['/user', '/role', '/document', '/ticket', '/report', '/activityLog', '/settings', '/company', '/superadmin']); ?>
                    <div class="nav-item has-dropdown">
                        <div class="nav-link dropdown-toggle <?php echo $sysActive ? 'active open' : ''; ?>" data-target="submenu-sys">
                            <div class="nav-link-content">
                                <i class="fas fa-cogs nav-icon"></i><span class="nav-text">الإدارة والدعم</span>
                            </div>
                            <i class="fas fa-chevron-down arrow"></i>
                        </div>
                        <ul class="submenu <?php echo $sysActive ? 'active' : ''; ?>" id="submenu-sys">
                            <li><a href="<?php echo URLROOT; ?>/report/index" class="sub-link <?php echo isActive($currentUri, ['/report']) ? 'active' : ''; ?>">التقارير الذكية</a></li>
                            <li><a href="<?php echo URLROOT; ?>/document/index" class="sub-link <?php echo isActive($currentUri, ['/document']) ? 'active' : ''; ?>">الأرشيف والوثائق</a></li>
                            <li><a href="<?php echo URLROOT; ?>/ticket/index" class="sub-link <?php echo isActive($currentUri, ['/ticket']) ? 'active' : ''; ?>">الدعم الفني وتذاكر</a></li>
                            
                            <?php if($userRole === 'admin' || $userRole === 'super_admin'): ?>
                            <li><a href="<?php echo URLROOT; ?>/user/index" class="sub-link <?php echo isActive($currentUri, ['/user']) ? 'active' : ''; ?>">المستخدمين</a></li>
                            <li><a href="<?php echo URLROOT; ?>/role/index" class="sub-link <?php echo isActive($currentUri, ['/role']) ? 'active' : ''; ?>">إدارة الصلاحيات</a></li>
                            
                            <?php if($userRole === 'super_admin'): ?>
                            <li><a href="<?php echo URLROOT; ?>/superadmin/dashboard" class="sub-link <?php echo isActive($currentUri, ['/superadmin']) ? 'active' : ''; ?>"><i class="fas fa-crown text-warning me-2"></i>لوحة المالك (SaaS)</a></li>
                            <li><a href="<?php echo URLROOT; ?>/company/index" class="sub-link <?php echo isActive($currentUri, ['/company']) ? 'active' : ''; ?>"><i class="fas fa-building text-info me-2"></i>الشركات المشتركة</a></li>
                            <?php endif; ?>
                            
                            <li><a href="<?php echo URLROOT; ?>/activityLog/index" class="sub-link <?php echo isActive($currentUri, ['/activityLog']) ? 'active' : ''; ?>">سجل التدقيق (Audit)</a></li>
                            <li><a href="<?php echo URLROOT; ?>/settings/index" class="sub-link <?php echo isActive($currentUri, ['/settings']) ? 'active' : ''; ?>">الإعدادات العامة</a></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </nav>
            </div>
            
            <div class="sidebar-user">
                <div class="su-avatar"><?php echo mb_substr($userName, 0, 2); ?></div>
                <div class="su-info">
                    <div class="su-name" title="<?php echo htmlspecialchars($userName); ?>"><?php echo htmlspecialchars($userName); ?></div>
                    <div class="su-role"><?php echo htmlspecialchars($userRole); ?></div>
                </div>
            </div>
        </aside>

        <!-- 🌟 Main Content Area 🌟 -->
        <main class="main-content">
            
            <!-- 🌟 Topbar 🌟 -->
            <header class="topbar d-print-none">
                <div class="topbar-right">
                    <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h1 class="page-title"><?php echo htmlspecialchars($title); ?></h1>
                        <?php if(Session::get('company_name') && Session::getUserRole() === 'super_admin'): ?>
                            <div class="text-muted" style="font-size:12px; margin-top:4px;"><i class="fas fa-building text-primary"></i> <?php echo Session::get('company_name'); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="topbar-left">
                    <div class="notif-dropdown">
                        <button class="notif-btn" id="notifToggle" title="الإشعارات والتنبيهات">
                            <i class="far fa-bell"></i>
                            <?php if($notifCount > 0): ?>
                                <span class="notif-badge"><?php echo $notifCount; ?></span>
                            <?php endif; ?>
                        </button>
                        
                        <div class="notif-menu" id="notifMenu">
                            <div class="notif-header">
                                <span>الإشعارات الواردة</span>
                                <?php if($notifCount > 0): ?>
                                    <span class="badge badge-danger"><?php echo $notifCount; ?> جديدة</span>
                                <?php endif; ?>
                            </div>
                            <div class="notif-list">
                                <?php if(!empty($unreadNotifs)): foreach($unreadNotifs as $note): ?>
                                    <a href="<?php echo URLROOT; ?>/dashboard/readNotification/<?php echo $note->id; ?>" class="notif-item unread">
                                        <div class="notif-title">
                                            <?php echo htmlspecialchars($note->title); ?>
                                            <i class="fas fa-circle text-primary" style="font-size:8px;"></i>
                                        </div>
                                        <div class="notif-msg"><?php echo htmlspecialchars($note->message); ?></div>
                                        <div class="notif-time"><i class="far fa-clock"></i> <?php echo date('M d, H:i', strtotime($note->created_at)); ?></div>
                                    </a>
                                <?php endforeach; else: ?>
                                    <div class="notif-item text-center text-muted py-5">
                                        <i class="fas fa-bell-slash fs-2 mb-3 opacity-50 d-block"></i>
                                        لا توجد إشعارات جديدة بانتظارك.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="topbar-divider"></div>
                    
                    <div class="user-profile">
                        <div class="user-info">
                            <span class="user-name"><?php echo htmlspecialchars($userName); ?></span>
                            <span class="user-role"><?php echo htmlspecialchars($userRole); ?></span>
                        </div>
                        <div class="user-avatar">
                            <?php echo mb_substr($userName, 0, 1); ?>
                        </div>
                    </div>
                    
                    <a href="<?php echo URLROOT; ?>/auth/logout" class="logout-btn" title="تسجيل الخروج الآمن" onclick="return confirm('هل تريد بالتأكيد تسجيل الخروج من النظام؟');">
                        <i class="fas fa-power-off"></i>
                    </a>
                </div>
            </header>

            <!-- 🌟 Page Body 🌟 -->
            <div class="page-body">
                <?php
                    // Flash Messages Auto-Hide System
                    if (isset($_SESSION['flash'])) {
                        $flashInfo = $_SESSION['flash'];
                        $icon = $flashInfo['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
                        $alertClass = $flashInfo['type'] === 'success' ? 'alert-success' : 'alert-danger';
                        echo '<div class="flash-msg ' . $alertClass . '"><i class="fas ' . $icon . '"></i> ' . $flashInfo['message'] . '</div>';
                        unset($_SESSION['flash']);
                    }
                ?>

                <?php 
                // SaaS Expiry Alert
                if(Session::get('company_id') && Session::getUserRole() === 'admin'): 
                    require_once APP_ROOT . '/app/helpers/SaasHelper.php';
                    $daysLeft = SaasHelper::getExpiryDays((int)Session::get('company_id'));
                    if($daysLeft !== null && $daysLeft <= 14 && $daysLeft >= 0):
                ?>
                    <div class="alert alert-warning shadow-sm d-flex align-items-center gap-3">
                        <i class="fas fa-stopwatch fa-2x text-warning"></i>
                        <div>
                            <strong class="d-block text-dark mb-1">تنبيه حرج: اقتراب موعد تجديد الاشتراك</strong>
                            <span class="text-muted">اشتراك مؤسستك في النظام سينتهي بعد <strong><?php echo $daysLeft; ?> يوم/أيام</strong>. لضمان استمرارية العمل يرجى التجديد.</span>
                        </div>
                    </div>
                <?php endif; endif; ?>

                <!-- The Actual Content Injected Here -->
                <?php echo $viewContent ?? ''; ?>
                
            </div>
        </main>
    </div>

    <!-- 🌟 Core UI Scripts 🌟 -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Sidebar Dropdown Logic
            const dropdowns = document.querySelectorAll('.dropdown-toggle');
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('click', function(e) {
                    e.preventDefault(); 
                    
                    const targetId = this.getAttribute('data-target');
                    const targetMenu = document.getElementById(targetId);
                    const isCurrentlyOpen = this.classList.contains('open');
                    
                    document.querySelectorAll('.submenu').forEach(menu => menu.classList.remove('active'));
                    document.querySelectorAll('.dropdown-toggle').forEach(btn => {
                        btn.classList.remove('open');
                    });

                    if (!isCurrentlyOpen && targetMenu) {
                        targetMenu.classList.add('active');
                        this.classList.add('open');
                    }
                });
            });

            // Notification Dropdown Logic
            const notifBtn = document.getElementById('notifToggle');
            const notifMenu = document.getElementById('notifMenu');
            if(notifBtn && notifMenu) {
                notifBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notifMenu.classList.toggle('show');
                });
                document.addEventListener('click', function(e) {
                    if (!notifMenu.contains(e.target)) notifMenu.classList.remove('show');
                });
            }

            // Mobile Menu Toggle
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

            // Auto-hide Flash Messages after 5 seconds
            const flashMsgs = document.querySelectorAll('.flash-msg');
            if(flashMsgs.length > 0) {
                setTimeout(() => {
                    flashMsgs.forEach(msg => {
                        msg.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                        msg.style.opacity = "0";
                        msg.style.transform = "translateY(-20px)";
                        setTimeout(() => msg.remove(), 500);
                    });
                }, 5000);
            }
        });
    </script>
</body>
</html>