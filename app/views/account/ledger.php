<?php
// app/views/account/ledger.php
$pageTitle = $data['title'] ?? 'دفتر الأستاذ';
$entries = $data['entries'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = $_GET['url'] ?? 'account/ledger';
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

    <!-- القائمة الجانبية للموبايل -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- الشريط الجانبي -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="s-logo"><i class="fas fa-cubes"></i></div>
            <div class="s-text">
                <span class="s-name">ERP <span>Pro</span></span>
            </div>
        </div>

        <!-- توليد القائمة -->
        <?php 
            if(class_exists('Layout')) {
                echo Layout::renderSidebar($currentUrl); 
            }
        ?>

        <div class="sidebar-user">
            <div class="su-avatar"><?php echo mb_substr($_SESSION['user_name'] ?? 'م', 0, 2); ?></div>
            <div class="su-info">
                <div class="su-name"><?php echo $_SESSION['user_name'] ?? 'مدير النظام'; ?></div>
                <div class="su-role"><?php echo $_SESSION['user_role'] ?? 'admin'; ?></div>
            </div>
            <a href="<?php echo URLROOT; ?>/auth/logout" class="su-logout" title="تسجيل الخروج">
                <i class="fas fa-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <!-- المحتوى الرئيسي -->
    <div class="main-content">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:16px;">
                <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="فتح القائمة"><i class="fas fa-bars"></i></button>
                <div>
                    <div class="page-title"><?php echo $pageTitle; ?></div>
                    <div class="breadcrumb">
                        <a href="<?php echo URLROOT; ?>/dashboard">الرئيسية</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>المحاسبة</span>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>دفتر الأستاذ</span>
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

            <!-- شريط الأدوات -->
            <div class="toolbar">
                <div class="toolbar-right">
                    <h3><i class="fas fa-book-open"></i> سجل القيود اليومية</h3>
                </div>
                <div>
                    <a href="<?php echo URLROOT; ?>/account/create-journal" class="btn-add">
                        <i class="fas fa-plus"></i> إنشاء قيد يومي
                    </a>
                </div>
            </div>

            <!-- جدول دفتر الأستاذ -->
            <div class="table-card">
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>رقم القيد</th>
                                <th>البيان والتاريخ</th>
                                <th>المرجع</th>
                                <th>المنشئ</th>
                                <th style="text-align:center;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entries as $entry) : 
                                // تخصيص الشارات حسب نوع المرجع
                                $refClass = 'type-manual';
                                $refIcon = 'fa-pen-clip';
                                $refText = 'قيد يدوي';
                                
                                if($entry->reference_type === 'invoice') {
                                    $refClass = 'type-invoice';
                                    $refIcon = 'fa-file-invoice';
                                    $refText = 'فاتورة مبيعات';
                                }
                            ?>
                            <tr>
                                <td>
                                    <span class="entry-num"><?php echo $entry->entry_number; ?></span>
                                </td>
                                <td>
                                    <div class="entry-desc"><?php echo htmlspecialchars($entry->description ?? 'بدون بيان'); ?></div>
                                    <div class="entry-date"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($entry->entry_date)); ?></div>
                                </td>
                                <td>
                                    <span class="ref-badge <?php echo $refClass; ?>" title="المعرف: <?php echo $entry->reference_id ?? '—'; ?>">
                                        <i class="fas <?php echo $refIcon; ?>"></i> <?php echo $refText; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="creator-info">
                                        <div class="creator-avatar"><?php echo mb_substr($entry->created_by_name ?? 'م', 0, 1); ?></div>
                                        <span style="font-size:13px; font-weight:600;"><?php echo htmlspecialchars($entry->created_by_name ?? 'النظام'); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; justify-content:center;">
                                        <a href="<?php echo URLROOT; ?>/account/view-journal/<?php echo $entry->id; ?>" class="act-btn" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($entries)) : ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-book-blank"></i>
                                        <h4>لا توجد قيود يومية مسجلة</h4>
                                        <p>قم بإنشاء قيد يومي جديد لبدء تسجيل الحركات المالية.</p>
                                    </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        /* === تفعيل قائمة الموبايل === */
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