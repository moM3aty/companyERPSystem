<?php
// app/views/sales_returns/index.php
$pageTitle = $data['title'] ?? 'سجل المرتجعات';
$returns = $data['returns'] ?? [];
$currentUrl = 'saleReturn/index';
$flash = $data['flash'] ?? null;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> | ERP Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #14b8a6; --danger: #ef4444; --danger-light: #fee2e2; --sidebar-w: 272px; --topbar-h: 68px; --page-bg: #f1f5f9; --card-bg: #ffffff; --text-dark: #0f172a; --text-body: #475569; --text-muted: #94a3b8; --border: #e2e8f0; --radius: 14px; --shadow-sm: 0 1px 3px rgba(0,0,0,0.06); }
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; background: var(--page-bg); color: var(--text-body); min-height: 100vh; }
        .sidebar { position: fixed; top: 0; right: 0; width: var(--sidebar-w); height: 100vh; background: linear-gradient(180deg, #0f172a 0%, #1a2332 100%); z-index: 100; border-left: 1px solid rgba(255,255,255,0.05); }
        .sidebar-brand { padding: 24px; border-bottom: 1px solid rgba(255,255,255,0.06); color: #fff; font-weight: bold; font-size: 18px; }
        .main-content { margin-right: var(--sidebar-w); }
        .topbar { height: var(--topbar-h); background: var(--card-bg); border-bottom: 1px solid var(--border); display: flex; align-items: center; padding: 0 32px; }
        .page-body { padding: 32px; }
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-add { background: var(--danger); color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-sm); }
        th { background: #f8fafc; padding: 15px; text-align: right; border-bottom: 2px solid var(--border); font-size: 13px; color: var(--text-muted); }
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 14px; color: var(--text-dark); }
        @media print { .sidebar, .topbar, .btn-add { display: none; } .main-content { margin: 0; } }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand"><i class="fas fa-cubes"></i> ERP Pro</div>
        <?php if(class_exists('Layout')) echo Layout::renderSidebar($currentUrl); ?>
    </aside>

    <div class="main-content">
        <header class="topbar">
            <h1 style="font-size: 18px; color: var(--text-dark);"><?php echo $pageTitle; ?></h1>
        </header>

        <div class="page-body">
            <div class="toolbar">
                <h3><i class="fas fa-arrow-rotate-left" style="color:var(--danger);"></i> سجل البضائع المرتجعة</h3>
                <a href="<?php echo URL_ROOT; ?>/saleReturn/create" class="btn-add"><i class="fas fa-plus"></i> تسجيل مرتجع جديد</a>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>رقم المرتجع</th>
                        <th>الفاتورة الأصلية</th>
                        <th>العميل</th>
                        <th>إجمالي المبلغ المردود</th>
                        <th>السبب</th>
                        <th>تاريخ الإرجاع</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($returns)): foreach($returns as $ret): ?>
                    <tr>
                        <td style="font-family:monospace; font-weight:bold; color:var(--danger);"><?php echo htmlspecialchars($ret->return_number); ?></td>
                        <td style="font-family:monospace; font-weight:bold;"><?php echo htmlspecialchars($ret->invoice_number); ?></td>
                        <td><i class="fas fa-user" style="color:var(--text-muted);"></i> <?php echo htmlspecialchars($ret->customer_name); ?></td>
                        <td style="direction:ltr; text-align:right; font-weight:bold; color:var(--danger);">-<?php echo number_format($ret->total_refund, 2); ?></td>
                        <td style="font-size:13px; color:var(--text-muted);"><?php echo htmlspecialchars($ret->reason ?? '—'); ?></td>
                        <td style="font-size:13px;"><i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($ret->created_at)); ?></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="6" style="text-align:center; padding:40px;">لا توجد حركات ترجيع مسجلة.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>