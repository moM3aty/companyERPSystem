<?php
$pageTitle = $data['title'] ?? 'تحصيلات الفواتير';
$collections = $data['collections'] ?? [];
$flash = Session::getFlash();
$currentUrl = 'collection/index';
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
                        <span>المالية</span>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>التحصيلات</span>
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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0 text-dark"><i class="fas fa-hand-holding-dollar text-success"></i> سجل التحصيلات (Receipts)</h3>
                    <p class="text-muted mt-1" style="font-size: 13px;">تتبع المبالغ المحصلة من العملاء والمسددة للفواتير.</p>
                </div>
                <a href="<?php echo URLROOT; ?>/collection/create" class="btn btn-success">
                    <i class="fas fa-plus"></i> تسجيل تحصيل جديد
                </a>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>رقم السند</th>
                                    <th>رقم الفاتورة</th>
                                    <th>الخزنة / البنك</th>
                                    <th>طريقة الدفع</th>
                                    <th class="text-left">المبلغ المُحصّل</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($collections)): foreach($collections as $col): 
                                    $methodLabel = match($col['payment_method']) {
                                        'cash' => 'نقدي', 'bank_transfer' => 'تحويل بنكي', 'check' => 'شيك', default => $col['payment_method']
                                    };
                                ?>
                                <tr>
                                    <td class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($col['receipt_number']); ?></td>
                                    <td class="font-monospace fw-bold text-primary"><?php echo htmlspecialchars($col['invoice_number'] ?? '—'); ?></td>
                                    <td class="fw-bold"><i class="fas fa-vault text-muted me-1"></i> <?php echo htmlspecialchars($col['treasury_name'] ?? '—'); ?></td>
                                    <td class="text-center"><span class="badge badge-secondary"><?php echo $methodLabel; ?></span></td>
                                    <td class="font-monospace fw-bold text-success fs-5" style="direction:ltr; text-align:right;">
                                        +<?php echo number_format($col['amount'], 2); ?>
                                    </td>
                                    <td class="text-muted fs-6"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($col['collection_date'])); ?></td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr><td colspan="6" class="text-center text-muted" style="padding: 40px;">لا توجد عمليات تحصيل مسجلة بعد.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>