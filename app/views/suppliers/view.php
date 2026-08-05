<?php
// app/views/suppliers/view.php
$supplier = $data['supplier'];
$purchaseOrders = $data['purchaseOrders'] ?? [];
$payments = $data['payments'] ?? [];
$totalPaid = $data['totalPaid'] ?? 0;
$totalPayables = $data['totalPayables'] ?? 0;
$outstanding = $data['outstanding'] ?? 0;
$flash = $data['flash'] ?? null;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيانات المورد — <?php echo htmlspecialchars($supplier->name); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* نفس تنسيقات view الخاصة بالعميل مع تغيير الألوان */
        :root {
            --primary: #14b8a6; --primary-dark: #0d9488; --primary-light: #ccfbf1;
            --accent: #f59e0b; --accent-light: #fef3c7;
            --success: #22c55e; --success-light: #dcfce7;
            --danger: #ef4444; --danger-light: #fee2e2;
            --info: #06b6d4; --info-light: #cffafe;
            --purple: #8b5cf6; --purple-light: #ede9fe;
            --sidebar-w: 272px; --topbar-h: 68px;
            --page-bg: #f1f5f9; --card-bg: #ffffff;
            --text-dark: #0f172a; --text-body: #475569; --text-muted: #94a3b8;
            --border: #e2e8f0; --radius: 14px; --radius-sm: 10px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.06); --shadow-md: 0 4px 20px rgba(0,0,0,0.08);
        }
        /* ... باقي التنسيقات مشابهة لـ customers/view ... */
    </style>
</head>
<body>
    <!-- Sidebar و Topbar (نفس الموجود في الملف الأصلي) -->

    <div class="page-body">
        <?php if ($flash) : ?>
            <div class="flash-msg flash-<?php echo $flash['type']; ?>">
                <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
                <?php echo htmlspecialchars($flash['message']); ?>
            </div>
        <?php endif; ?>

        <!-- رأس المورد -->
        <div class="cust-profile-header" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
            <div class="cph-top">
                <div class="cph-avatar" style="background: linear-gradient(135deg, var(--accent), #d97706);"><?php echo mb_substr($supplier->name, 0, 2); ?></div>
                <div class="cph-info">
                    <h2><?php echo htmlspecialchars($supplier->name); ?></h2>
                    <div class="cph-email"><i class="far fa-envelope"></i> <?php echo htmlspecialchars($supplier->email ?? '—'); ?></div>
                    <div class="cph-type"><i class="fas fa-<?php echo $supplier->type === 'company' ? 'building' : 'user'; ?>"></i> <?php echo $supplier->type === 'company' ? 'شركة' : 'فرد'; ?></div>
                    <?php if (!empty($supplier->contact_person)) : ?>
                        <div class="cph-type" style="background:rgba(255,255,255,0.05);"><i class="fas fa-user-tie"></i> جهة اتصال: <?php echo htmlspecialchars($supplier->contact_person); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="cph-stats">
                <div class="cph-stat">
                    <div class="cph-stat-val"><?php echo (int)($supplier->po_count ?? 0); ?></div>
                    <div class="cph-stat-label">أمر شراء</div>
                </div>
                <div class="cph-stat">
                    <div class="cph-stat-val"><?php echo number_format($supplier->total_purchases ?? 0, 0); ?></div>
                    <div class="cph-stat-label">مشتريات (ر.س)</div>
                </div>
                <div class="cph-stat">
                    <div class="cph-stat-val" style="color:<?php echo $outstanding > 0 ? 'var(--danger)' : 'var(--success)'; ?>;">
                        <?php echo number_format($outstanding, 2); ?>
                    </div>
                    <div class="cph-stat-label">رصيد مستحق (ر.س)</div>
                </div>
            </div>
        </div>

        <!-- شبكة أوامر الشراء + المدفوعات -->
        <div class="content-grid">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-file-invoice" style="color:var(--accent);"></i> أوامر الشراء</h3>
                    <span style="font-size:12px;color:var(--text-muted);"><?php echo count($purchaseOrders); ?></span>
                </div>
                <div class="card-body np">
                    <?php if (!empty($purchaseOrders)) : ?>
                    <table class="inv-table">
                        <thead>
                            <tr><th>رقم الأمر</th><th>الإجمالي</th><th>التاريخ</th><th>الحالة</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($purchaseOrders as $po) :
                                $statusClass = match($po->status) {
                                    'delivered' => 'st-paid',
                                    'pending' => 'st-unpaid',
                                    'cancelled' => 'st-cancelled',
                                    default => 'st-unpaid'
                                };
                                $statusLabel = match($po->status) {
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
                                <td><span class="inv-num"><?php echo htmlspecialchars($po->po_number); ?></span></td>
                                <td><span class="inv-amount"><?php echo number_format($po->total_amount, 2); ?><span class="curr">ر.س</span></span></td>
                                <td><span class="inv-date"><i class="far fa-calendar"></i> <?php echo date('Y-m-d', strtotime($po->created_at)); ?></span></td>
                                <td>
                                    <span class="inv-status <?php echo $statusClass; ?>">
                                        <i class="fas fa-<?php echo $po->status === 'delivered' ? 'circle-check' : 'clock'; ?>"></i>
                                        <?php echo $statusLabel; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo URL_ROOT; ?>/purchase/view/<?php echo $po->id; ?>" class="act-btn btn-view" title="عرض"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else : ?>
                    <div class="no-invoices"><i class="fas fa-receipt"></i><p>لا توجد أوامر شراء لهذا المورد بعد</p></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3><i class="fas fa-money-bill-wave" style="color:var(--success);"></i> المدفوعات</h3>
                    <span style="font-size:12px;color:<?php echo $totalPaid > 0 ? 'var(--success)' : 'var(--text-muted)'; ?>; font-weight:700;"><?php echo number_format($totalPaid, 2); ?> ر.س</span>
                </div>
                <div class="card-body np">
                    <?php if (!empty($payments)) : ?>
                    <table class="pay-table">
                        <thead>
                            <tr><th>التاريخ</th><th>البيان</th><th>المبلغ</th><th>الطريقة</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payments as $p) : ?>
                            <tr>
                                <td><span class="inv-date"><i class="far fa-calendar"></i> <?php echo date('Y-m-d H:i', strtotime($p->created_at)); ?></span></td>
                                <td><?php echo htmlspecialchars($p->notes ?? '—'); ?></td>
                                <td><span class="pay-amount"><span class="curr">ر.س</span> <?php echo number_format($p->amount, 2); ?></span></td>
                                <td>
                                    <?php
                                    $method = $p->method ?? 'cash';
                                    $methodLabel = ['cash' => 'نقدي', 'bank_transfer' => 'تحويل بنكي', 'check' => 'شيك', 'card' => 'بطاقة ائتمان'];
                                    $methodClass = ['cash' => 'pm-cash', 'bank_transfer' => 'pm-bank', 'check' => 'pm-check', 'card' => 'pm-card'];
                                    $methodIcon = ['cash' => 'fa-money-bill', 'bank_transfer' => 'fa-building-columns', 'check' => 'fa-file-invoice-dollar', 'card' => 'fa-credit-card'];
                                    $m = $methodLabel[$method] ?? $method;
                                    $mc = $methodClass[$method] ?? 'pm-cash';
                                    $mi = $methodIcon[$method] ?? 'fa-money-bill';
                                ?>
                                <span class="pay-method <?php echo $mc; ?>"><i class="fas <?php echo $mi; ?>"></i> <?php echo $m; ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else : ?>
                    <div class="no-invoices"><i class="fas fa-check-circle"></i><p>لا توجد مدفوعات مسجلة بعد</p></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ملخص الرصيد -->
        <div class="balance-box">
            <div class="balance-row">
                <span class="br-label"><i class="fas fa-calculator" style="margin-left:6px;"></i> إجمالي المشتريات</span>
                <span class="br-value"><?php echo number_format($totalPayables, 2); ?> <span class="curr">ر.س</span></span>
            </div>
            <div class="balance-row">
                <span class="br-label"><i class="fas fa-arrow-down" style="margin-left:6px;"></i> إجمالي المدفوعات</span>
                <span class="br-value positive"><?php echo number_format($totalPaid, 2); ?> <span class="curr">ر.س</span></span>
            </div>
            <div class="balance-row">
                <span class="br-label"><i class="fas fa-wallet" style="margin-left:6px;"></i> الرصيد المستحق</span>
                <span class="br-value <?php echo $outstanding > 0 ? 'negative' : 'zero'; ?>"><?php echo number_format($outstanding, 2); ?> <span class="curr">ر.س</span></span>
            </div>
        </div>
    </div>

    <script>
        // كود الـ Sidebar للموبايل (نفس الموجود في الملفات الأخرى)
    </script>
</body>
</html>