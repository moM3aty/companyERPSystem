<?php
// app/views/suppliers/view.php
$pageTitle = $data['title'] ?? 'بيانات المورد';
$s = $data['supplier'] ?? null;
$purchaseOrders = $data['purchaseOrders'] ?? [];
$payments = $data['payments'] ?? [];
$totalPaid = $data['totalPaid'] ?? 0;
$totalPayables = $data['totalPayables'] ?? 0;
$outstanding = $data['outstanding'] ?? 0;
$flash = $data['flash'] ?? null;
$currentUrl = 'supplier/index';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيانات المورد — <?php echo htmlspecialchars($s->name ?? ''); ?></title>
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
        <?php if (class_exists('Layout')) echo Layout::renderSidebar($currentUrl); ?>
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
                        <a href="<?php echo URLROOT; ?>/supplier/index">إدارة الموردين</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>تفاصيل المورد</span>
                    </div>
                </div>
            </div>
            <div class="topbar-left">
                <a href="<?php echo URLROOT; ?>/supplier/edit/<?php echo $s->id; ?>" class="topbar-btn" title="تعديل بيانات المورد"><i class="fas fa-pen"></i></a>
                <button class="topbar-btn" title="طباعة الكشف" onclick="window.print()"><i class="fas fa-print"></i></button>
            </div>
        </header>

        <div class="page-body">

            <?php if ($flash) : ?>
                <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                    <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endif; ?>

            <!-- رأس ملف المورد -->
            <div class="supplier-profile-header">
                <div class="sph-top">
                    <div class="sph-avatar"><?php echo mb_substr($s->name ?? 'م', 0, 2); ?></div>
                    <div class="sph-info">
                        <h2><?php echo htmlspecialchars($s->name ?? ''); ?></h2>
                        <div class="sph-email">
                            <i class="far fa-envelope"></i> <?php echo htmlspecialchars($s->email ?? '—'); ?>
                            &nbsp;|&nbsp; <i class="fas fa-phone" style="font-size:11px;"></i> <?php echo htmlspecialchars($s->phone ?? '—'); ?>
                        </div>
                        <div class="sph-type">
                            <i class="fas fa-<?php echo ($s->type ?? '') === 'company' ? 'building' : 'user'; ?>"></i>
                            <?php echo ($s->type ?? '') === 'company' ? 'شركة' : 'فرد'; ?>
                        </div>
                        <?php if (!empty($s->contact_person)) : ?>
                            <div class="sph-type" style="background:rgba(255,255,255,0.05); margin-right: 8px;">
                                <i class="fas fa-user-tie"></i> جهة الاتصال: <?php echo htmlspecialchars($s->contact_person); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="sph-stats">
                    <div class="sph-stat">
                        <div class="sph-stat-val"><?php echo (int)($s->po_count ?? 0); ?></div>
                        <div class="sph-stat-label">إجمالي الأوامر</div>
                    </div>
                    <div class="sph-stat">
                        <div class="sph-stat-val"><?php echo number_format($totalPayables, 0); ?></div>
                        <div class="sph-stat-label">المشتريات (ر.س)</div>
                    </div>
                    <div class="sph-stat" style="background: rgba(255,255,255,0.12);">
                        <div class="sph-stat-val" style="color:<?php echo $outstanding > 0 ? '#fca5a5' : '#e2e8f0'; ?>;">
                            <?php echo number_format($outstanding, 2); ?>
                        </div>
                        <div class="sph-stat-label">مستحق للمورد (ر.س)</div>
                    </div>
                </div>
            </div>

            <!-- شبكة الجداول -->
            <div class="content-grid">

                <!-- أوامر الشراء -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-file-invoice" style="color:var(--accent);"></i> أوامر الشراء</h3>
                        <span style="font-size:12px;color:var(--text-muted);font-weight:600;"><?php echo count($purchaseOrders); ?> طلب</span>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($purchaseOrders)) : ?>
                            <table class="details-table">
                                <thead>
                                    <tr>
                                        <th>رقم الأمر</th>
                                        <th style="text-align:left;">الإجمالي</th>
                                        <th>الحالة</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($purchaseOrders as $po) :
                                        $statusClass = match ($po->status) {
                                            'delivered' => 'st-delivered',
                                            'pending', 'ordered', 'approved' => 'st-pending',
                                            'cancelled', 'rejected' => 'st-cancelled',
                                            default => 'st-pending'
                                        };
                                        $statusLabel = match ($po->status) {
                                            'delivered' => 'تم التسليم',
                                            'pending' => 'قيد الانتظار',
                                            'approved' => 'معتمد',
                                            'ordered' => 'تم الطلب',
                                            'cancelled' => 'ملغى',
                                            'rejected' => 'مرفوض',
                                            default => $po->status
                                        };
                                    ?>
                                        <tr>
                                            <td>
                                                <span class="po-num"><?php echo htmlspecialchars($po->po_number); ?></span>
                                                <div class="date-val" style="margin-top:4px;"><i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($po->created_at)); ?></div>
                                            </td>
                                            <td style="text-align:left;"><span class="amount-val"><?php echo number_format($po->total_amount, 2); ?> <span class="curr">ر.س</span></span></td>
                                            <td>
                                                <span class="po-status <?php echo $statusClass; ?>">
                                                    <i class="fas fa-<?php echo $po->status === 'delivered' ? 'circle-check' : ($po->status === 'cancelled' ? 'xmark' : 'clock'); ?>"></i>
                                                    <?php echo $statusLabel; ?>
                                                </span>
                                            </td>
                                            <td style="text-align:left;">
                                                <a href="<?php echo URLROOT; ?>/purchase/view/<?php echo $po->id; ?>" class="act-btn" title="عرض الأمر"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <div class="empty-box"><i class="fas fa-receipt"></i>
                                <p>لا توجد أوامر شراء سابقة لهذا المورد</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- مدفوعات المورد -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-money-bill-wave" style="color:var(--success);"></i> سجل الدفعات السابقة</h3>
                        <span style="font-size:12px;color:var(--success);font-weight:700;"><?php echo number_format($totalPaid, 2); ?> ر.س</span>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($payments)) : ?>
                            <table class="details-table">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>الطريقة</th>
                                        <th style="text-align:left;">المبلغ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $p) :
                                        $method = $p->method ?? 'cash';
                                        $methodLabel = match ($method) {
                                            'cash' => 'نقدي',
                                            'bank_transfer' => 'تحويل بنكي',
                                            'check' => 'شيك',
                                            'card' => 'بطاقة',
                                            default => 'غير محدد'
                                        };
                                        $methodIcon = match ($method) {
                                            'cash' => 'fa-money-bill',
                                            'bank_transfer' => 'fa-building-columns',
                                            'check' => 'fa-file-signature',
                                            'card' => 'fa-credit-card',
                                            default => 'fa-wallet'
                                        };
                                    ?>
                                        <tr>
                                            <td><span class="date-val"><i class="far fa-clock"></i> <?php echo date('Y-m-d', strtotime($p->created_at)); ?></span></td>
                                            <td><span class="pay-method"><i class="fas <?php echo $methodIcon; ?>"></i> <?php echo $methodLabel; ?></span></td>
                                            <td style="text-align:left;"><span class="amount-val success"><?php echo number_format($p->amount, 2); ?> <span class="curr">ر.س</span></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <div class="empty-box"><i class="fas fa-hand-holding-dollar"></i>
                                <p>لا توجد مدفوعات مسجلة حتى الآن</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div> <!-- .content-grid -->

            <!-- ملخص الحساب -->
            <div class="balance-box">
                <div class="balance-row">
                    <span class="br-label"><i class="fas fa-calculator" style="color:var(--text-muted);"></i> إجمالي المشتريات (أوامر الشراء)</span>
                    <span class="br-value"><?php echo number_format($totalPayables, 2); ?> <span style="font-size:12px;font-weight:500;color:var(--text-muted);">ر.س</span></span>
                </div>
                <div class="balance-row">
                    <span class="br-label"><i class="fas fa-arrow-down" style="color:var(--success);"></i> إجمالي ما تم دفعه</span>
                    <span class="br-value positive"><?php echo number_format($totalPaid, 2); ?> <span style="font-size:12px;font-weight:500;color:var(--text-muted);">ر.س</span></span>
                </div>
                <div class="balance-row" style="padding-top:16px; margin-top:4px; border-top:2px solid var(--border);">
                    <span class="br-label" style="font-size:16px; color:var(--text-dark);"><i class="fas fa-wallet" style="color:var(--danger);"></i> المستحق للمورد (الدائن)</span>
                    <span class="br-value <?php echo $outstanding > 0 ? 'negative' : 'zero'; ?>" style="font-size:24px;">
                        <?php echo number_format($outstanding, 2); ?> <span style="font-size:14px;font-weight:600;color:var(--text-muted);">ر.س</span>
                    </span>
                </div>
            </div>

        </div> <!-- .page-body -->
    </div> <!-- .main-content -->

    <script>
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