<?php
// app/views/accounting/index.php
$pageTitle = $data['title'] ?? 'المحاسبة والأرباح';
$expenses = $data['expenses'] ?? [];
$totalSales = $data['total_sales'] ?? 0;
$totalExpenses = $data['total_expenses'] ?? 0;
$netProfit = $data['net_profit'] ?? 0;
$search = $data['search'] ?? '';
$flash = $data['flash'] ?? null;
$currentUrl = 'accounting/index';

$incomePct = $totalSales > 0 ? 100 : 0;
$expensePct = $totalSales > 0 ? min(($totalExpenses / $totalSales) * 100, 100) : 0;
$profitPct = $totalSales > 0 ? max(($netProfit / $totalSales) * 100, 0) : 0;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
                <div class="su-name"><?php echo $_SESSION['user_name'] ?? 'مدير النظام'; ?></div>
                <div class="su-role"><?php echo $_SESSION['user_role'] ?? 'admin'; ?></div>
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
                        <span>المحاسبة والأرباح</span>
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

            <!-- بطاقات الإحصائيات المالية -->
            <div class="finance-grid">
                <div class="finance-card fc-income">
                    <div class="fc-top">
                        <span class="fc-label">إجمالي المبيعات (الإيرادات)</span>
                        <div class="fc-icon"><i class="fas fa-arrow-trend-up"></i></div>
                    </div>
                    <div class="fc-value"><?php echo number_format($totalSales, 0); ?> <span class="fc-unit">ر.س</span></div>
                    <div class="fc-bar"><div class="fc-bar-fill" style="width:<?php echo $incomePct; ?>%;"></div></div>
                </div>
                
                <div class="finance-card fc-expense">
                    <div class="fc-top">
                        <span class="fc-label">إجمالي المصروفات</span>
                        <div class="fc-icon"><i class="fas fa-arrow-trend-down"></i></div>
                    </div>
                    <div class="fc-value"><?php echo number_format($totalExpenses, 0); ?> <span class="fc-unit">ر.س</span></div>
                    <div class="fc-bar"><div class="fc-bar-fill" style="width:<?php echo $expensePct; ?>%;"></div></div>
                </div>
                
                <div class="finance-card fc-profit">
                    <div class="fc-top">
                        <span class="fc-label">صافي الربح</span>
                        <div class="fc-icon"><i class="fas fa-wallet"></i></div>
                    </div>
                    <div class="fc-value"><?php echo number_format($netProfit, 0); ?> <span class="fc-unit">ر.س</span></div>
                    <div class="fc-bar"><div class="fc-bar-fill" style="width:<?php echo $profitPct; ?>%;"></div></div>
                </div>
            </div>

            <div class="content-grid">
                
                <!-- الرسم البياني (Chart.js) -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-pie" style="color:var(--primary);"></i> التحليل المالي</h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrap">
                            <canvas id="financeChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- نموذج إضافة مصروف -->
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fas fa-receipt" style="color:var(--danger);"></i> تسجيل مصروف جديد</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo URLROOT; ?>/accounting/index" method="POST" id="expenseForm" class="expense-form" novalidate>
                            <div class="ef-group">
                                <label class="ef-label">تصنيف المصروف</label>
                                <select name="category" class="ef-input">
                                    <option value="تشغيلية">تشغيلية (مكتبية ونثرية)</option>
                                    <option value="رواتب">رواتب وأجور</option>
                                    <option value="إيجار">إيجار مقار</option>
                                    <option value="كهرباء وماء">فواتير مرافق (كهرباء وماء)</option>
                                    <option value="صيانة">صيانة عامة</option>
                                    <option value="تسويق">تسويق وإعلانات</option>
                                    <option value="نقل وشحن">نقل وشحن</option>
                                    <option value="أخرى">مصروفات أخرى</option>
                                </select>
                            </div>
                            <div class="ef-group">
                                <label class="ef-label">البيان (تفاصيل المصروف) <span style="color:var(--danger);">*</span></label>
                                <input type="text" name="description" class="ef-input" id="expDesc" placeholder="مثال: سداد فاتورة كهرباء شهر يناير" required>
                            </div>
                            <div class="ef-group">
                                <label class="ef-label">المبلغ المدفوع (ر.س) <span style="color:var(--danger);">*</span></label>
                                <input type="number" step="0.01" name="amount" class="ef-input" id="expAmount" placeholder="0.00" required style="direction:ltr;text-align:right;">
                            </div>
                            <button type="submit" class="btn-expense" id="btnExpSubmit">
                                <i class="fas fa-plus-circle"></i> تسجيل واعتماد المصروف
                            </button>
                        </form>
                    </div>
                </div>
                
            </div>

            <!-- جدول المصروفات -->
            <div class="table-card">
                <div class="card-header">
                    <h3><i class="fas fa-list-ul" style="color:var(--accent);"></i> سجل المصروفات التفصيلي</h3>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>البيان والتصنيف</th>
                                <th style="text-align:center;">المبلغ</th>
                                <th>التاريخ</th>
                                <th style="text-align:center;">إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($expenses as $index => $exp) : ?>
                            <tr>
                                <td style="color:var(--text-muted);font-weight:600;font-size:12px;"><?php echo $index + 1; ?></td>
                                <td>
                                    <div class="exp-desc"><?php echo htmlspecialchars($exp->description); ?></div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-top:2px;">
                                        <i class="fas fa-tag"></i> <?php echo htmlspecialchars($exp->category); ?>
                                    </div>
                                </td>
                                <td style="text-align:center;">
                                    <span class="exp-amount"><span class="curr">ر.س</span> <?php echo number_format($exp->amount, 2); ?></span>
                                </td>
                                <td>
                                    <span class="exp-date">
                                        <i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($exp->created_at)); ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex;justify-content:center;">
                                        <button class="act-btn" title="حذف" onclick="openDeleteModal(<?php echo $exp->id; ?>, '<?php echo htmlspecialchars(addslashes($exp->description)); ?>', <?php echo $exp->amount; ?>)">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if(empty($expenses)) : ?>
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="fas fa-receipt"></i>
                                        <h4>لا توجد مصروفات مسجلة</h4>
                                        <p>ابدأ بتسجيل أول مصروف من النموذج أعلاه ليظهر هنا</p>
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

    <!-- مودال حذف المصروف -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-box">
            <div class="modal-header">
                <div class="modal-icon"><i class="fas fa-triangle-exclamation"></i></div>
                <h3>تأكيد حذف المصروف</h3>
                <p>هل أنت متأكد من حذف المصروف "<strong id="delExpName"></strong>" بقيمة <strong id="delExpAmount"></strong>؟</p>
            </div>
            <div class="modal-footer">
                <button class="modal-btn btn-cancel" onclick="closeDeleteModal()">إلغاء</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    <input type="hidden" name="delete_expense" value="1">
                    <button type="submit" class="modal-btn btn-confirm-del">نعم، تأكيد الحذف</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // إعداد الرسم البياني المالي
        (function renderChart() {
            const ctx = document.getElementById('financeChart');
            if (!ctx) return;
            const sales = <?php echo $totalSales ?? 0; ?>;
            const expenses = <?php echo $totalExpenses ?? 0; ?>;
            const profit = sales - expenses;

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['الإيرادات', 'المصروفات', 'صافي الربح'],
                    datasets: [{
                        data: [Math.max(sales, 0), Math.max(expenses, 0), Math.max(profit, 0)],
                        backgroundColor: ['#22c55e', '#ef4444', '#14b8a6'],
                        borderWidth: 0, hoverOffset: 10, borderRadius: 5, spacing: 3
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false, cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom', rtl: true, labels: { font: { family: 'Cairo', size: 12, weight: '600' }, usePointStyle: true, padding: 20 } },
                        tooltip: { rtl: true, titleFont: { family: 'Cairo' }, bodyFont: { family: 'Cairo' }, cornerRadius: 8, callbacks: { label: function(c) { return c.label + ': ' + c.parsed.toLocaleString('ar-SA') + ' ر.س'; } } }
                    }
                }
            });
        })();

        // التحكم في المودال
        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        function openDeleteModal(id, name, amount) {
            document.getElementById('delExpName').textContent = name;
            document.getElementById('delExpAmount').textContent = amount.toLocaleString('ar-SA', { minimumFractionDigits: 2 }) + ' ر.س';
            deleteForm.action = '<?php echo URLROOT; ?>/accounting/index?delete=' + id;
            deleteModal.classList.add('show');
        }
        function closeDeleteModal() { deleteModal.classList.remove('show'); }
        deleteModal.addEventListener('click', function(e) { if (e.target === this) closeDeleteModal(); });

        // التحقق من النموذج
        document.getElementById('expenseForm').addEventListener('submit', function(e) {
            let valid = true;
            const desc = document.getElementById('expDesc');
            const amount = document.getElementById('expAmount');
            [desc, amount].forEach(el => el.classList.remove('has-error'));
            
            if (!desc.value.trim()) { desc.classList.add('has-error'); valid = false; }
            if (!amount.value || parseFloat(amount.value) <= 0) { amount.classList.add('has-error'); valid = false; }
            
            if (!valid) { e.preventDefault(); }
            else { 
                const btn = document.getElementById('btnExpSubmit');
                btn.disabled = true; 
                btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري التسجيل...'; 
            }
        });

        // القائمة الجانبية للموبايل
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>