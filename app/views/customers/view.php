<?php
// app/views/customers/view.php
$pageTitle = $data['title'] ?? 'بيانات العميل';
$c = $data['customer'] ?? null;
$invoices = $data['invoices'] ?? [];
$payments = $data['payments'] ?? [];
$totalPaid = $data['total_paid'] ?? 0;
$flash = $data['flash'] ?? null;
$currentUrl = 'customer/index';
$totalPurchases = $c->total_purchases ?? 0;
$outstanding = max($totalPurchases - $totalPaid, 0);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيانات العميل — <?php echo htmlspecialchars($c->name ?? ''); ?></title>
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
                        <a href="<?php echo URLROOT; ?>/customer/index">إدارة العملاء</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>تفاصيل العميل</span>
                    </div>
                </div>
            </div>
            <div class="topbar-left">
                <a href="<?php echo URLROOT; ?>/customer/edit/<?php echo $c->id; ?>" class="topbar-btn" title="تعديل بيانات العميل"><i class="fas fa-pen"></i></a>
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

            <!-- رأس ملف العميل -->
            <div class="cust-profile-header">
                <div class="cph-top">
                    <div class="cph-avatar"><?php echo mb_substr($c->name ?? 'ع', 0, 2); ?></div>
                    <div class="cph-info">
                        <h2><?php echo htmlspecialchars($c->name ?? ''); ?></h2>
                        <div class="cph-email"><i class="far fa-envelope"></i> <?php echo htmlspecialchars($c->email ?? '—'); ?> &nbsp;|&nbsp; <i class="fas fa-phone" style="font-size:11px;"></i> <?php echo htmlspecialchars($c->phone ?? '—'); ?></div>
                        <div class="cph-type"><i class="fas fa-<?php echo ($c->type ?? '') === 'company' ? 'building' : 'user'; ?>"></i> <?php echo ($c->type ?? '') === 'company' ? 'شركة' : 'فرد'; ?></div>
                    </div>
                </div>
                <div class="cph-stats">
                    <div class="cph-stat">
                        <div class="cph-stat-val"><?php echo (int)($c->invoice_count ?? 0); ?></div>
                        <div class="cph-stat-label">إجمالي الفواتير</div>
                    </div>
                    <div class="cph-stat">
                        <div class="cph-stat-val"><?php echo number_format($totalPurchases, 0); ?></div>
                        <div class="cph-stat-label">المشتريات (ر.س)</div>
                    </div>
                    <div class="cph-stat" style="background: rgba(255,255,255,0.12);">
                        <div class="cph-stat-val" style="color:<?php echo ($c->balance ?? 0) > 0 ? '#fca5a5' : (($c->balance ?? 0) < 0 ? '#86efac' : '#e2e8f0'); ?>;">
                            <?php echo number_format(abs($c->balance ?? 0), 2); ?>
                        </div>
                        <div class="cph-stat-label">الرصيد المدين (ر.س)</div>
                    </div>
                </div>
            </div>

            <!-- شبكة الجداول -->
            <div class="content-grid">

                <!-- فواتير العميل -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-file-invoice" style="color:var(--primary);"></i> فواتير المبيعات</h3>
                        <span style="font-size:12px;color:var(--text-muted);font-weight:600;"><?php echo count($invoices); ?> فاتورة</span>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($invoices)) : ?>
                            <table class="details-table">
                                <thead>
                                    <tr>
                                        <th>رقم الفاتورة</th>
                                        <th style="text-align:left;">الإجمالي</th>
                                        <th>التاريخ</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoices as $inv) :
                                        $isPaid = isset($inv->payment_status) && $inv->payment_status === 'paid';
                                    ?>
                                        <tr>
                                            <td><span class="inv-num"><?php echo htmlspecialchars($inv->invoice_number); ?></span></td>
                                            <td style="text-align:left;"><span class="amount-val"><?php echo number_format($inv->total_amount, 2); ?> <span class="curr">ر.س</span></span></td>
                                            <td><span class="date-val"><i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($inv->created_at)); ?></span></td>
                                            <td style="text-align:left;">
                                                <a href="<?php echo URLROOT; ?>/sale/view/<?php echo $inv->id; ?>" class="act-btn" title="عرض الفاتورة"><i class="fas fa-eye"></i></a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <div class="empty-box"><i class="fas fa-receipt"></i>
                                <p>لا توجد فواتير سابقة لهذا العميل</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- مدفوعات العميل -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-money-bill-wave" style="color:var(--success);"></i> سجل المدفوعات</h3>
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
                    <span class="br-label"><i class="fas fa-calculator" style="color:var(--text-muted);"></i> إجمالي المشتريات (الفواتير)</span>
                    <span class="br-value"><?php echo number_format($totalPurchases, 2); ?> <span style="font-size:12px;font-weight:500;color:var(--text-muted);">ر.س</span></span>
                </div>
                <div class="balance-row">
                    <span class="br-label"><i class="fas fa-arrow-down" style="color:var(--success);"></i> إجمالي المبالغ المدفوعة</span>
                    <span class="br-value positive"><?php echo number_format($totalPaid, 2); ?> <span style="font-size:12px;font-weight:500;color:var(--text-muted);">ر.س</span></span>
                </div>
                <div class="balance-row" style="padding-top:16px; margin-top:4px; border-top:2px solid var(--border);">
                    <span class="br-label" style="font-size:16px; color:var(--text-dark);"><i class="fas fa-wallet" style="color:var(--primary);"></i> الرصيد المتبقي (المستحق)</span>
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