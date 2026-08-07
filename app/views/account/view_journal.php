<?php
// app/views/account/view_journal.php
$pageTitle = $data['title'] ?? 'تفاصيل القيد اليومي';
$entry = $data['entry'] ?? null;
$lines = $data['lines'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'account/ledger';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>القيد رقم: <?php echo htmlspecialchars($entry->entry_number); ?></title>
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
                        <a href="<?php echo URLROOT; ?>/account/ledger">دفتر الأستاذ</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>عرض القيد</span>
                    </div>
                </div>
            </div>
            <div class="print-actions">
                <a href="<?php echo URLROOT; ?>/account/ledger" class="btn-back"><i class="fas fa-arrow-right"></i> السجل</a>
                <button class="btn-print" onclick="window.print()"><i class="fas fa-print"></i> طباعة القيد</button>
            </div>
        </header>

        <div class="page-body">
            
            <div class="journal-card">
                <div class="jc-header">
                    <div class="jc-title-area">
                        <div class="jc-icon"><i class="fas fa-book-open"></i></div>
                        <div>
                            <div class="jc-number">قيد #<?php echo htmlspecialchars($entry->entry_number); ?></div>
                            <div class="jc-desc"><?php echo htmlspecialchars($entry->description ?? 'بدون بيان'); ?></div>
                        </div>
                    </div>
                    <div class="jc-meta">
                        <div class="jc-meta-item">
                            تاريخ السريان: <strong><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($entry->entry_date)); ?></strong>
                        </div>
                        <div class="jc-meta-item">
                            بواسطة: <strong><i class="fas fa-user-gear"></i> <?php echo htmlspecialchars($entry->created_by_name ?? 'النظام'); ?></strong>
                        </div>
                        <?php if ($entry->reference_type): ?>
                        <div class="jc-meta-item" style="margin-top: 10px;">
                            <span class="ref-badge"><i class="fas fa-link"></i> مرجع: <?php echo htmlspecialchars($entry->reference_type); ?> (#<?php echo $entry->reference_id; ?>)</span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="jc-table-wrap">
                    <table class="jc-table">
                        <thead>
                            <tr>
                                <th>اسم الحساب المحاسبي</th>
                                <th>البيان الفرعي</th>
                                <th style="width: 140px;">مدين (Debit)</th>
                                <th style="width: 140px;">دائن (Credit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $totalDebit = 0;
                                $totalCredit = 0;
                                foreach($lines as $line) : 
                                    $totalDebit += $line->debit;
                                    $totalCredit += $line->credit;
                            ?>
                            <tr>
                                <td>
                                    <span class="acc-code"><?php echo htmlspecialchars($line->account_code); ?></span>
                                    <span class="acc-cell"><?php echo htmlspecialchars($line->account_name); ?></span>
                                </td>
                                <td><span style="font-size:12px;color:var(--text-muted);"><?php echo htmlspecialchars($line->description ?? '—'); ?></span></td>
                                <td><?php echo $line->debit > 0 ? number_format($line->debit, 2) : '—'; ?></td>
                                <td><?php echo $line->credit > 0 ? number_format($line->credit, 2) : '—'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">إجمالي القيد المزدوج:</td>
                                <td><?php echo number_format($totalDebit, 2); ?></td>
                                <td><?php echo number_format($totalCredit, 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="jc-footer">
                    <div class="jc-footer-stamp">
                        <i class="fas fa-scale-balanced"></i>
                        <span>قيد محاسبي معتمد في دفتر الأستاذ العام.</span>
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