<?php
// app/views/account/create_journal.php
$pageTitle = $data['title'] ?? 'إنشاء قيد يومي';
$accounts = $data['accounts'] ?? [];
$flash = $data['flash'] ?? null;
$currentUrl = 'account/create-journal';
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
            <div class="s-text">
                <span class="s-name">ERP <span>Pro</span></span>
            </div>
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
                        <a href="<?php echo URLROOT; ?>/account/ledger">دفتر الأستاذ</a>
                        <i class="fas fa-chevron-left" style="font-size:9px;"></i>
                        <span>قيد جديد</span>
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

            <div class="form-header-card">
                <h2><i class="fas fa-pen-to-square" style="margin-left:8px;"></i> إنشاء قيد يومي يدوي</h2>
                <p>قم بتسجيل حركة محاسبية جديدة مع التأكد من توازن القيد (المدين = الدائن)</p>
            </div>

            <div class="form-card">
                <form action="<?php echo URLROOT; ?>/account/create-journal" method="POST" id="journalForm">
                    
                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="fst-icon fst-teal"><i class="fas fa-info-circle"></i></span>
                            المعلومات الأساسية
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">تاريخ القيد <span class="req">*</span></label>
                                <input type="date" name="entry_date" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">البيان (شرح القيد) <span class="req">*</span></label>
                                <input type="text" name="description" class="form-input" placeholder="مثال: تسوية عهدة موظف" required autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <span class="fst-icon fst-purple"><i class="fas fa-list-ol"></i></span>
                            سطور القيد
                        </div>
                        
                        <!-- حاوية السطور -->
                        <div class="lines-container" id="linesContainer">
                            <!-- السطر الأول -->
                            <div class="line-row">
                                <select name="lines[0][account_id]" class="form-input account-select" required>
                                    <option value="">-- اختر الحساب --</option>
                                    <?php foreach ($accounts as $acc) : ?>
                                        <option value="<?php echo $acc->id; ?>"><?php echo $acc->code . ' - ' . htmlspecialchars($acc->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="number" name="lines[0][debit]" placeholder="مدين (ر.س)" class="form-input debit-input" step="0.01" min="0" autocomplete="off" style="direction:ltr; text-align:center;">
                                <input type="number" name="lines[0][credit]" placeholder="دائن (ر.س)" class="form-input credit-input" step="0.01" min="0" autocomplete="off" style="direction:ltr; text-align:center;">
                                <input type="text" name="lines[0][description]" placeholder="بيان السطر (اختياري)" class="form-input" autocomplete="off">
                                <button type="button" class="btn-remove-line" onclick="removeLine(this)" disabled title="لا يمكن حذف السطر الأول"><i class="fas fa-trash"></i></button>
                            </div>
                            <!-- السطر الثاني (القيد المزدوج يتطلب سطرين على الأقل) -->
                            <div class="line-row">
                                <select name="lines[1][account_id]" class="form-input account-select" required>
                                    <option value="">-- اختر الحساب --</option>
                                    <?php foreach ($accounts as $acc) : ?>
                                        <option value="<?php echo $acc->id; ?>"><?php echo $acc->code . ' - ' . htmlspecialchars($acc->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="number" name="lines[1][debit]" placeholder="مدين (ر.س)" class="form-input debit-input" step="0.01" min="0" autocomplete="off" style="direction:ltr; text-align:center;">
                                <input type="number" name="lines[1][credit]" placeholder="دائن (ر.س)" class="form-input credit-input" step="0.01" min="0" autocomplete="off" style="direction:ltr; text-align:center;">
                                <input type="text" name="lines[1][description]" placeholder="بيان السطر (اختياري)" class="form-input" autocomplete="off">
                                <button type="button" class="btn-remove-line" onclick="removeLine(this)" title="حذف السطر"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>

                        <button type="button" onclick="addLine()" class="btn-add-line">
                            <i class="fas fa-plus"></i> إضافة سطر جديد
                        </button>

                        <!-- المجاميع -->
                        <div class="totals-panel">
                            <div class="total-item">
                                <span class="total-label">إجمالي المدين</span>
                                <span class="total-value debit" id="totalDebit">0.00</span>
                            </div>
                            
                            <div class="total-status status-unbalanced" id="balanceStatus">
                                <i class="fas fa-scale-unbalanced"></i> القيد غير متوازن
                            </div>

                            <div class="total-item" style="text-align: left;">
                                <span class="total-label">إجمالي الدائن</span>
                                <span class="total-value credit" id="totalCredit">0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit" id="btnSubmit" disabled>
                            <i class="fas fa-save"></i> حفظ القيد
                        </button>
                        <a href="<?php echo URLROOT; ?>/account/ledger" class="btn-cancel">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- مصفوفة الحسابات لخيارات السطور الجديدة -->
    <script>
        const accountsList = <?php 
            $opts = [];
            foreach($accounts as $acc) {
                $opts[] = '<option value="'.$acc->id.'">'.$acc->code.' - '.htmlspecialchars($acc->name, ENT_QUOTES).'</option>';
            }
            echo json_encode(implode('', $opts));
        ?>;
    </script>

    <script>
        let lineIndex = 2; // لدينا سطرين مبدئياً
        const container = document.getElementById('linesContainer');
        const btnSubmit = document.getElementById('btnSubmit');
        const totalDebitEl = document.getElementById('totalDebit');
        const totalCreditEl = document.getElementById('totalCredit');
        const balanceStatus = document.getElementById('balanceStatus');

        function addLine() {
            const newRow = document.createElement('div');
            newRow.className = 'line-row';
            newRow.innerHTML = `
                <select name="lines[${lineIndex}][account_id]" class="form-input account-select" required>
                    <option value="">-- اختر الحساب --</option>
                    ${accountsList}
                </select>
                <input type="number" name="lines[${lineIndex}][debit]" placeholder="مدين (ر.س)" class="form-input debit-input" step="0.01" min="0" autocomplete="off" style="direction:ltr; text-align:center;">
                <input type="number" name="lines[${lineIndex}][credit]" placeholder="دائن (ر.س)" class="form-input credit-input" step="0.01" min="0" autocomplete="off" style="direction:ltr; text-align:center;">
                <input type="text" name="lines[${lineIndex}][description]" placeholder="بيان السطر (اختياري)" class="form-input" autocomplete="off">
                <button type="button" class="btn-remove-line" onclick="removeLine(this)" title="حذف السطر"><i class="fas fa-trash"></i></button>
            `;
            container.appendChild(newRow);
            lineIndex++;
            attachEvents();
            calculateTotals();
        }

        function removeLine(btn) {
            const rows = container.querySelectorAll('.line-row');
            if (rows.length > 2) {
                btn.parentElement.remove();
                calculateTotals();
            } else {
                alert('يجب أن يحتوي القيد المزدوج على سطرين على الأقل.');
            }
        }

        function calculateTotals() {
            let tDebit = 0;
            let tCredit = 0;

            document.querySelectorAll('.debit-input').forEach(input => {
                const val = parseFloat(input.value);
                if (!isNaN(val) && val > 0) {
                    tDebit += val;
                    // تصفير الدائن في نفس السطر إذا أُدخل مدين
                    const siblingCredit = input.parentElement.querySelector('.credit-input');
                    if(siblingCredit && siblingCredit.value > 0) siblingCredit.value = '';
                }
            });

            document.querySelectorAll('.credit-input').forEach(input => {
                const val = parseFloat(input.value);
                if (!isNaN(val) && val > 0) {
                    tCredit += val;
                    // تصفير المدين في نفس السطر إذا أُدخل دائن
                    const siblingDebit = input.parentElement.querySelector('.debit-input');
                    if(siblingDebit && siblingDebit.value > 0) siblingDebit.value = '';
                }
            });

            totalDebitEl.textContent = tDebit.toLocaleString('ar-SA', {minimumFractionDigits: 2});
            totalCreditEl.textContent = tCredit.toLocaleString('ar-SA', {minimumFractionDigits: 2});

            // التحقق من التوازن
            const diff = Math.abs(tDebit - tCredit);
            
            if (tDebit > 0 && tCredit > 0 && diff < 0.01) {
                balanceStatus.className = 'total-status status-balanced';
                balanceStatus.innerHTML = '<i class="fas fa-scale-balanced"></i> القيد متوازن';
                btnSubmit.disabled = false;
            } else {
                balanceStatus.className = 'total-status status-unbalanced';
                balanceStatus.innerHTML = '<i class="fas fa-scale-unbalanced"></i> القيد غير متوازن';
                btnSubmit.disabled = true;
            }
        }

        function attachEvents() {
            document.querySelectorAll('.debit-input, .credit-input').forEach(input => {
                // إزالة الحدث القديم إن وجد لتجنب التكرار
                input.removeEventListener('input', calculateTotals);
                input.addEventListener('input', calculateTotals);
            });
        }

        // تشغيل الأحداث لأول مرة
        attachEvents();

        /* القائمة الجانبية للموبايل */
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const menuBtn = document.getElementById('mobileMenuBtn');
        if (menuBtn) { menuBtn.addEventListener('click', () => { sidebar.classList.toggle('open'); overlay.classList.toggle('show'); }); }
        if (overlay) { overlay.addEventListener('click', () => { sidebar.classList.remove('open'); overlay.classList.remove('show'); }); }
    </script>
</body>
</html>