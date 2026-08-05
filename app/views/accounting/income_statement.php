<?php
// المسار: app/views/accounting/income_statement.php
$pageTitle = $data['title'] ?? 'قائمة الدخل';
$currentUrl = 'accounting/incomeStatement';
$revenues = $data['revenues'] ?? [];
$expenses = $data['expenses'] ?? [];

$totalRevenues = 0;
foreach($revenues as $rev) $totalRevenues += $rev->balance;

$totalExpenses = 0;
foreach($expenses as $exp) $totalExpenses += $exp->balance;

$netIncome = $totalRevenues - $totalExpenses;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | ERP Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* (نفس المتغيرات للتوافق) */
        :root { --primary: #14b8a6; --primary-dark: #0d9488; --page-bg: #f8fafc; --card-bg: #ffffff; --text-dark: #0f172a; --text-body: #334155; --text-muted: #64748b; --border: #e2e8f0; --radius: 16px; --success: #22c55e; --danger: #ef4444; --sidebar-w: 280px; --topbar-h: 70px;}
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; background: var(--page-bg); color: var(--text-body); min-height: 100vh; }
        
        .sidebar { position: fixed; top: 0; right: 0; width: var(--sidebar-w); height: 100vh; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); z-index: 100; display: flex; flex-direction: column; border-left: 1px solid rgba(255,255,255,0.05); }
        .sidebar-brand { padding: 20px 24px; display: flex; align-items: center; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.08); background: rgba(0,0,0,0.2); }
        .sidebar-brand .s-logo { width: 44px; height: 44px; background: linear-gradient(135deg, var(--primary), #06b6d4); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fff;}
        .sidebar-brand .s-name { font-size: 20px; font-weight: 800; color: #f8fafc; }
        .sidebar-brand .s-name span { color: var(--primary); }
        .sidebar-nav { flex: 1; padding: 20px 16px; overflow-y: auto; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; color: #cbd5e1; text-decoration: none; font-size: 14px; font-weight: 600; margin-bottom: 4px;}
        .nav-link.active { background: rgba(20,184,166,0.1); color: #ccfbf1; border-right: 4px solid var(--primary); }
        
        .main-content { margin-right: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }
        .topbar { height: var(--topbar-h); background: #fff; border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 32px; }
        .page-title { font-size: 20px; font-weight: 800; color: var(--text-dark); }
        .page-body { padding: 32px; flex: 1; }

        .report-container { max-width: 900px; margin: 0 auto; background: #fff; border-radius: var(--radius); border: 1px solid var(--border); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); padding: 40px; animation: fadeUp 0.5s ease;}
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .report-header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid var(--text-dark); padding-bottom: 20px;}
        .report-header h1 { font-size: 28px; font-weight: 900; color: var(--text-dark); margin-bottom: 8px;}
        .report-header p { font-size: 15px; color: var(--text-muted); font-weight: 600;}

        .report-section { margin-bottom: 30px; }
        .section-title { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 16px; border-bottom: 1px solid var(--border); padding-bottom: 8px; display: flex; justify-content: space-between;}
        
        .account-row { display: flex; justify-content: space-between; padding: 12px 16px; border-bottom: 1px dashed var(--border); font-size: 15px; font-weight: 600; color: var(--text-body);}
        .account-row:hover { background: #f8fafc; }
        .account-name { display: flex; align-items: center; gap: 10px; }
        .account-code { font-family: monospace; color: var(--text-muted); font-size: 13px;}
        .account-bal { font-family: monospace; direction: ltr; font-weight: 700; color: var(--text-dark);}

        .subtotal-row { display: flex; justify-content: space-between; padding: 16px; background: #f1f5f9; border-radius: 8px; margin-top: 12px; font-size: 16px; font-weight: 800; color: var(--text-dark);}
        
        .net-income-row { display: flex; justify-content: space-between; padding: 20px; background: var(--text-dark); color: #fff; border-radius: 12px; margin-top: 40px; font-size: 22px; font-weight: 900; box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.3);}
        .net-bal { font-family: monospace; direction: ltr;}
        .net-positive { color: var(--success-light); }
        .net-negative { color: var(--danger-light); }

        .print-btn { display: block; width: 100%; padding: 16px; text-align: center; background: var(--primary); color: #fff; border: none; border-radius: 12px; font-family: 'Cairo'; font-size: 16px; font-weight: 700; margin-top: 30px; cursor: pointer; transition: 0.3s;}
        .print-btn:hover { background: var(--primary-dark); }

        @media print {
            .sidebar, .topbar, .print-btn { display: none !important; }
            .main-content { margin: 0 !important; }
            .page-body { padding: 0 !important; }
            .report-container { box-shadow: none; border: none; padding: 0; }
            body { background: #fff; }
        }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="s-logo"><i class="fas fa-layer-group"></i></div>
            <div class="s-text">
                <div class="s-name">ERP <span>Pro</span></div>
            </div>
        </div>
        <?php if(class_exists('Layout')) echo Layout::renderSidebar($currentUrl); ?>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <h1 class="page-title"><?php echo $pageTitle; ?></h1>
        </header>

        <main class="page-body">
            
            <div class="report-container">
                <div class="report-header">
                    <h1>قائمة الدخل (Income Statement)</h1>
                    <p>للفترة المالية المنتهية في <?php echo date('Y-m-d'); ?></p>
                </div>

                <!-- قسم الإيرادات -->
                <div class="report-section">
                    <div class="section-title">
                        <span>الإيرادات والمبيعات (Revenues)</span>
                    </div>
                    <?php if(!empty($revenues)): foreach($revenues as $rev): ?>
                        <div class="account-row">
                            <div class="account-name">
                                <span class="account-code">[<?php echo htmlspecialchars($rev->code); ?>]</span>
                                <?php echo htmlspecialchars($rev->name); ?>
                            </div>
                            <div class="account-bal"><?php echo number_format($rev->balance, 2); ?></div>
                        </div>
                    <?php endforeach; else: ?>
                        <div style="padding:15px; text-align:center; color:var(--text-muted); font-size:14px;">لا توجد حسابات إيرادات مسجلة بأرصدة.</div>
                    <?php endif; ?>
                    <div class="subtotal-row">
                        <span>إجمالي الإيرادات:</span>
                        <span style="font-family:monospace; direction:ltr;"><?php echo number_format($totalRevenues, 2); ?> ر.س</span>
                    </div>
                </div>

                <!-- قسم المصروفات -->
                <div class="report-section">
                    <div class="section-title">
                        <span>المصروفات والتكاليف (Expenses)</span>
                    </div>
                    <?php if(!empty($expenses)): foreach($expenses as $exp): ?>
                        <div class="account-row">
                            <div class="account-name">
                                <span class="account-code">[<?php echo htmlspecialchars($exp->code); ?>]</span>
                                <?php echo htmlspecialchars($exp->name); ?>
                            </div>
                            <div class="account-bal"><?php echo number_format($exp->balance, 2); ?></div>
                        </div>
                    <?php endforeach; else: ?>
                        <div style="padding:15px; text-align:center; color:var(--text-muted); font-size:14px;">لا توجد حسابات مصروفات مسجلة بأرصدة.</div>
                    <?php endif; ?>
                    <div class="subtotal-row">
                        <span>إجمالي المصروفات:</span>
                        <span style="font-family:monospace; direction:ltr;"><?php echo number_format($totalExpenses, 2); ?> ر.س</span>
                    </div>
                </div>

                <!-- صافي الدخل -->
                <div class="net-income-row">
                    <span><?php echo $netIncome >= 0 ? 'صافي الربح (Net Profit)' : 'صافي الخسارة (Net Loss)'; ?>:</span>
                    <span class="net-bal <?php echo $netIncome >= 0 ? 'net-positive' : 'net-negative'; ?>">
                        <?php echo number_format(abs($netIncome), 2); ?> ر.س
                    </span>
                </div>

                <button onclick="window.print()" class="print-btn"><i class="fas fa-print"></i> طباعة التقرير المالي</button>
            </div>

        </main>
    </div>
</body>
</html>