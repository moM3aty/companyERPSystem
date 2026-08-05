<?php
// المسار: app/views/accounting/dashboard.php
$pageTitle = $data['title'] ?? 'المحاسبة والمالية';
$currentUrl = 'accounting/dashboard';
$stats = $data['stats'] ?? [];
$recentEntries = $data['recent_entries'] ?? [];
$flash = $data['flash'] ?? null;
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
        /* (تم استخدام نفس متغيرات التصميم والتنسيق لضمان تجربة مستخدم موحدة) */
        :root {
            --primary: #14b8a6; --primary-dark: #0d9488; --primary-light: #ccfbf1;
            --accent: #f59e0b; --accent-light: #fef3c7;
            --success: #22c55e; --success-light: #dcfce7;
            --danger: #ef4444; --danger-light: #fee2e2;
            --info: #06b6d4; --info-light: #cffafe;
            --purple: #8b5cf6; --purple-light: #ede9fe;
            --sidebar-w: 280px; --topbar-h: 70px;
            --page-bg: #f8fafc; --card-bg: #ffffff;
            --text-dark: #0f172a; --text-body: #334155; --text-muted: #64748b;
            --border: #e2e8f0; --radius: 16px; --radius-sm: 10px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05); --shadow-md: 0 4px 20px rgba(0,0,0,0.05);
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; background: var(--page-bg); color: var(--text-body); min-height: 100vh; overflow-x: hidden; }

        /* Sidebar */
        .sidebar { position: fixed; top: 0; right: 0; width: var(--sidebar-w); height: 100vh; background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%); z-index: 100; display: flex; flex-direction: column; transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); border-left: 1px solid rgba(255,255,255,0.05); box-shadow: -4px 0 25px rgba(0,0,0,0.1); }
        .sidebar-brand { padding: 20px 24px; display: flex; align-items: center; gap: 14px; border-bottom: 1px solid rgba(255,255,255,0.08); background: rgba(0,0,0,0.2); }
        .sidebar-brand .s-logo { width: 44px; height: 44px; background: linear-gradient(135deg, var(--primary), var(--info)); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: #fff; flex-shrink: 0; box-shadow: 0 4px 15px rgba(20,184,166,0.3); }
        .sidebar-brand .s-text { display: flex; flex-direction: column; }
        .sidebar-brand .s-name { font-size: 20px; font-weight: 800; color: #f8fafc; letter-spacing: -0.5px; line-height: 1.2; }
        .sidebar-brand .s-name span { color: var(--primary); }
        
        .sidebar-nav { flex: 1; padding: 20px 16px; overflow-y: auto; }
        .nav-section-title { font-size: 11px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; padding: 16px 14px 8px; margin-top: 8px; }
        .nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; color: #cbd5e1; text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; position: relative; overflow: hidden; margin-bottom: 4px;}
        .nav-link i { width: 22px; text-align: center; font-size: 16px; transition: transform 0.2s; color: #64748b; }
        .nav-link:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .nav-link.active { background: linear-gradient(90deg, rgba(20,184,166,0.15) 0%, rgba(20,184,166,0.05) 100%); color: var(--primary-light); border-right: 4px solid var(--primary); }
        .nav-link.active i { color: var(--primary); }
        
        /* Main */
        .main-content { margin-right: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }
        .topbar { height: var(--topbar-h); background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; position: sticky; top: 0; z-index: 50; }
        .page-title { font-size: 20px; font-weight: 800; color: var(--text-dark); line-height: 1.2; }
        .page-body { padding: 32px; flex: 1; display: flex; flex-direction: column; }
        
        @keyframes fadeUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Accounting Specific Styles */
        .acc-header { background: linear-gradient(135deg, var(--primary) 0%, #0891b2 100%); border-radius: var(--radius); padding: 32px 40px; color: #fff; margin-bottom: 28px; position: relative; overflow: hidden; animation: fadeUp 0.5s ease both; box-shadow: 0 10px 30px rgba(20,184,166,0.2);}
        .acc-header::before { content: ''; position: absolute; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%; top: -150px; left: -100px; }
        .acc-title { font-size: 24px; font-weight: 800; margin-bottom: 8px; position: relative; z-index: 2; display: flex; align-items: center; gap: 10px;}
        .acc-subtitle { font-size: 15px; opacity: 0.9; max-width: 600px; line-height: 1.6; position: relative; z-index: 2;}

        .widget-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 28px; animation: fadeUp 0.5s ease 0.1s both; }
        .stat-card { background: var(--card-bg); border-radius: var(--radius); padding: 24px; border: 1px solid var(--border); display: flex; align-items: center; gap: 16px; box-shadow: var(--shadow-sm); transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); }
        .stat-icon { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
        .stat-details { flex: 1; }
        .stat-value { font-size: 26px; font-weight: 800; color: var(--text-dark); margin-bottom: 4px; font-variant-numeric: tabular-nums; line-height: 1; direction: ltr; text-align: right;}
        .stat-label { font-size: 13px; font-weight: 600; color: var(--text-muted); }

        .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; animation: fadeUp 0.5s ease 0.2s both; }
        .dashboard-card { background: var(--card-bg); border-radius: var(--radius); border: 1px solid var(--border); overflow: hidden; box-shadow: var(--shadow-sm);}
        .dc-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: #f8fafc;}
        .dc-header h3 { font-size: 16px; font-weight: 700; color: var(--text-dark); display: flex; align-items: center; gap: 8px;}
        .dc-body { padding: 0; }

        /* Table */
        table { width: 100%; border-collapse: collapse; }
        thead th { padding: 14px 24px; font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; background: #f8fafc; border-bottom: 1px solid var(--border); text-align: right; }
        tbody tr { border-bottom: 1px solid var(--border); transition: background 0.2s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(20,184,166,0.02); }
        tbody td { padding: 14px 24px; font-size: 14px; color: var(--text-body); }
        .entry-num { font-family: monospace; font-weight: 700; color: var(--primary-dark); background: var(--primary-light); padding: 4px 8px; border-radius: 6px; font-size: 12px;}

        /* Quick Actions Menu */
        .qa-list { padding: 16px; display: flex; flex-direction: column; gap: 8px; }
        .qa-item { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 12px; background: var(--page-bg); color: var(--text-dark); text-decoration: none; font-weight: 600; font-size: 14px; transition: all 0.2s; border: 1px solid transparent;}
        .qa-item i { width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 6px; background: #fff; color: var(--primary); box-shadow: var(--shadow-sm); font-size: 12px;}
        .qa-item:hover { background: #fff; border-color: var(--primary); color: var(--primary-dark); box-shadow: var(--shadow-sm); transform: translateX(-4px);}

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
            <div>
                <h1 class="page-title"><?php echo $pageTitle; ?></h1>
            </div>
        </header>

        <main class="page-body">
            
            <div class="acc-header">
                <h1 class="acc-title"><i class="fas fa-scale-balanced"></i> لوحة التحكم المالية</h1>
                <p class="acc-subtitle">نظرة عامة على الأرصدة، الحركات المحاسبية، والتقارير المالية الأساسية لدعم اتخاذ القرار.</p>
            </div>

            <div class="widget-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--success-light); color:var(--success);">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value"><?php echo number_format($stats['total_assets'] ?? 0, 2); ?></div>
                        <div class="stat-label">إجمالي الأصول (ر.س)</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--danger-light); color:var(--danger);">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value"><?php echo number_format($stats['total_liabilities'] ?? 0, 2); ?></div>
                        <div class="stat-label">إجمالي الخصوم والالتزامات</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background:var(--info-light); color:var(--info);">
                        <i class="fas fa-money-bill-trend-up"></i>
                    </div>
                    <div class="stat-details">
                        <div class="stat-value"><?php echo number_format($stats['net_income'] ?? 0, 2); ?></div>
                        <div class="stat-label">صافي الدخل (تقريبي)</div>
                    </div>
                </div>
            </div>

            <div class="content-grid">
                <!-- أحدث القيود -->
                <div class="dashboard-card">
                    <div class="dc-header">
                        <h3><i class="fas fa-book-journal-whills" style="color:var(--primary);"></i> أحدث القيود اليومية</h3>
                        <a href="<?php echo URL_ROOT; ?>/journal/index" style="font-size:13px; color:var(--primary); font-weight:600; text-decoration:none;">عرض الكل</a>
                    </div>
                    <div class="dc-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>رقم القيد</th>
                                    <th>التاريخ</th>
                                    <th>البيان</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentEntries)): foreach($recentEntries as $entry): ?>
                                <tr>
                                    <td><span class="entry-num"><?php echo htmlspecialchars($entry->entry_number); ?></span></td>
                                    <td><span style="font-size:12px; color:var(--text-muted);"><i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($entry->entry_date)); ?></span></td>
                                    <td style="font-weight:600;"><?php echo htmlspecialchars($entry->description); ?></td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="3" style="text-align:center; padding:40px 20px; color:var(--text-muted);">
                                        <i class="fas fa-folder-open" style="font-size:32px; margin-bottom:10px; color:var(--border);"></i><br>
                                        لا توجد قيود مسجلة مؤخراً.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- قائمة سريعة -->
                <div class="dashboard-card">
                    <div class="dc-header">
                        <h3><i class="fas fa-bolt" style="color:var(--accent);"></i> عمليات محاسبية</h3>
                    </div>
                    <div class="dc-body">
                        <div class="qa-list">
                            <a href="<?php echo URL_ROOT; ?>/journal/create" class="qa-item">
                                <i class="fas fa-plus"></i> إنشاء قيد يومية مزدوج
                            </a>
                            <a href="<?php echo URL_ROOT; ?>/account/tree" class="qa-item">
                                <i class="fas fa-sitemap"></i> استعراض دليل الحسابات
                            </a>
                            <a href="<?php echo URL_ROOT; ?>/account/create" class="qa-item">
                                <i class="fas fa-folder-plus"></i> إضافة حساب جديد
                            </a>
                            <a href="<?php echo URL_ROOT; ?>/accounting/incomeStatement" class="qa-item">
                                <i class="fas fa-file-invoice-dollar"></i> قائمة الدخل (Income Statement)
                            </a>
                            <a href="<?php echo URL_ROOT; ?>/expense/create" class="qa-item" style="border-top:1px dashed var(--border); border-radius:0;">
                                <i class="fas fa-money-bill-transfer"></i> تسجيل مصروف تشغيلي
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</body>
</html>