<?php
$pageTitle = $data['title'] ?? 'تسجيل تحصيل جديد';
$treasuries = $data['treasuries'] ?? [];
$invoices = $data['invoices'] ?? [];
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
                        <a href="<?php echo URLROOT; ?>/collection/index">التحصيلات</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>تحصيل جديد</span>
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

            <div class="card" style="max-width: 800px; margin: 0 auto;">
                <div class="card-header bg-success text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
                    <h3 class="card-title text-white mb-0"><i class="fas fa-file-invoice-dollar"></i> توثيق تحصيل مالي (سند قبض لفاتورة)</h3>
                </div>

                <form action="<?php echo URLROOT; ?>/collection/create" method="POST" id="collectionForm">
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <i class="fas fa-info-circle"></i> سيؤدي حفظ هذا السند إلى تحديث رصيد الخزنة/البنك وتوثيق استلام المبلغ للفاتورة المرتبطة بشكل آلي.
                        </div>

                        <div class="form-grid">
                            
                            <div class="form-group full-width">
                                <label class="form-label">الفاتورة المرتبطة <span class="required">*</span></label>
                                <select name="invoice_id" class="form-control" required>
                                    <option value="">-- اختر الفاتورة المطلوب تحصيل قيمتها --</option>
                                    <?php foreach($invoices as $inv): ?>
                                        <option value="<?php echo $inv['id']; ?>">
                                            فاتورة #<?php echo htmlspecialchars($inv['invoice_number']); ?> (الإجمالي: <?php echo number_format($inv['total'], 2); ?> ر.س)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">الخزنة / البنك المودع به <span class="required">*</span></label>
                                <select name="treasury_id" class="form-control" required>
                                    <option value="">-- اختر الخزنة/البنك --</option>
                                    <?php foreach($treasuries as $t): ?>
                                        <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">المبلغ المُحصّل (ر.س) <span class="required">*</span></label>
                                <input type="number" name="amount" step="0.01" min="0.01" class="form-control font-monospace fw-bold text-success" required style="direction:ltr; text-align:right;">
                            </div>

                            <div class="form-group">
                                <label class="form-label">طريقة الدفع <span class="required">*</span></label>
                                <select name="payment_method" class="form-control" required>
                                    <option value="cash">نقدي (كاش)</option>
                                    <option value="bank_transfer">تحويل بنكي</option>
                                    <option value="check">شيك</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">تاريخ التحصيل <span class="required">*</span></label>
                                <input type="date" name="collection_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">رقم المرجع (اختياري)</label>
                                <input type="text" name="reference" class="form-control" placeholder="رقم الحوالة، رقم الشيك...">
                            </div>

                            <div class="form-group full-width">
                                <label class="form-label">ملاحظات التحصيل</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="اكتب تفاصيل إضافية حول عملية الدفع..."></textarea>
                            </div>

                        </div>
                    </div>
                    
                    <div class="card-footer d-flex gap-3 bg-light">
                        <button type="submit" class="btn btn-success" id="btnSubmit"><i class="fas fa-save"></i> حفظ وتأكيد التحصيل</button>
                        <a href="<?php echo URLROOT; ?>/collection/index" class="btn btn-secondary">إلغاء</a>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('collectionForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('btnSubmit');
            if (btn.disabled) {
                e.preventDefault();
                return;
            }
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> جاري تأكيد المعاملة...';
        });

        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>