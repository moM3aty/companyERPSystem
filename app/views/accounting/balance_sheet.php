<?php
// المسار: app/views/accounting/balance_sheet.php
$assets = $data['assets'] ?? [];
$liabilities = $data['liabilities'] ?? [];
$equities = $data['equities'] ?? [];
$netIncome = $data['net_income'] ?? 0;

$totalAssets = 0; foreach($assets as $a) $totalAssets += $a->balance;
$totalLiabilities = 0; foreach($liabilities as $l) $totalLiabilities += $l->balance;
$totalEquity = 0; foreach($equities as $e) $totalEquity += $e->balance;

$totalEquityAndNetIncome = $totalEquity + $netIncome;
$totalLiabilitiesAndEquity = $totalLiabilities + $totalEquityAndNetIncome;
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-building-columns text-primary"></i> الميزانية العمومية (Balance Sheet)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">المركز المالي للشركة والتحقق من التوازن</p>
    </div>
    <button onclick="window.print()" class="btn btn-dark">
        <i class="fas fa-print"></i> طباعة الميزانية
    </button>
</div>

<div class="card" style="max-width: 1000px; margin: 0 auto; box-shadow: none;">
    <div class="card-body" style="padding: 40px;">
        
        <div class="text-center mb-4 pb-3" style="border-bottom: 2px solid var(--text-dark);">
            <h1 style="font-size: 28px; font-weight: 900; color: var(--text-dark); margin-bottom: 8px;">الميزانية العمومية</h1>
            <p style="font-size: 15px; color: var(--text-muted); font-weight: 600; margin: 0;">المركز المالي كما في تاريخ <?php echo date('Y-m-d'); ?></p>
        </div>
        
        <div class="form-grid" style="gap: 40px;">
            <!-- الأصول -->
            <div>
                <h4 class="mb-3 pb-2" style="font-size: 18px; font-weight: 800; border-bottom: 2px solid var(--info); color: var(--info);"><i class="fas fa-building-columns"></i> الأصول (Assets)</h4>
                <?php foreach($assets as $a): ?>
                <div class="d-flex justify-content-between py-2 border-bottom border-light">
                    <span class="fw-bold text-body"><?php echo htmlspecialchars($a->name); ?></span>
                    <span class="font-monospace fw-bold text-dark" style="direction:ltr;"><?php echo number_format($a->balance, 2); ?></span>
                </div>
                <?php endforeach; ?>
                
                <div class="d-flex justify-content-between p-3 mt-3 rounded" style="background: var(--info-light); color: var(--info-dark); font-weight: 800; font-size: 16px;">
                    <span>إجمالي الأصول:</span>
                    <span class="font-monospace" style="direction:ltr;"><?php echo number_format($totalAssets, 2); ?></span>
                </div>
            </div>

            <!-- الخصوم وحقوق الملكية -->
            <div>
                <h4 class="mb-3 pb-2" style="font-size: 18px; font-weight: 800; border-bottom: 2px solid var(--danger); color: var(--danger);"><i class="fas fa-hand-holding-dollar"></i> الخصوم (Liabilities)</h4>
                <?php foreach($liabilities as $l): ?>
                <div class="d-flex justify-content-between py-2 border-bottom border-light">
                    <span class="fw-bold text-body"><?php echo htmlspecialchars($l->name); ?></span>
                    <span class="font-monospace fw-bold text-dark" style="direction:ltr;"><?php echo number_format($l->balance, 2); ?></span>
                </div>
                <?php endforeach; ?>
                <div class="d-flex justify-content-between p-3 mt-3 mb-4 rounded" style="background: var(--danger-light); color: var(--danger); font-weight: 800; font-size: 16px;">
                    <span>إجمالي الخصوم:</span>
                    <span class="font-monospace" style="direction:ltr;"><?php echo number_format($totalLiabilities, 2); ?></span>
                </div>

                <h4 class="mb-3 pb-2" style="font-size: 18px; font-weight: 800; border-bottom: 2px solid var(--purple); color: var(--purple);"><i class="fas fa-scale-balanced"></i> حقوق الملكية (Equity)</h4>
                <?php foreach($equities as $e): ?>
                <div class="d-flex justify-content-between py-2 border-bottom border-light">
                    <span class="fw-bold text-body"><?php echo htmlspecialchars($e->name); ?></span>
                    <span class="font-monospace fw-bold text-dark" style="direction:ltr;"><?php echo number_format($e->balance, 2); ?></span>
                </div>
                <?php endforeach; ?>
                
                <div class="d-flex justify-content-between py-2 border-bottom border-light" style="color: var(--success); font-weight: 800; background: #f8fafc;">
                    <span><?php echo $netIncome >= 0 ? 'أرباح مُرحلة (من الدخل)' : 'خسائر مُرحلة (من الدخل)'; ?></span>
                    <span class="font-monospace" style="direction:ltr;"><?php echo number_format($netIncome, 2); ?></span>
                </div>

                <div class="d-flex justify-content-between p-3 mt-3 rounded" style="background: var(--purple-light); color: var(--purple); font-weight: 800; font-size: 16px;">
                    <span>إجمالي حقوق الملكية:</span>
                    <span class="font-monospace" style="direction:ltr;"><?php echo number_format($totalEquityAndNetIncome, 2); ?></span>
                </div>
                
                <div class="d-flex justify-content-between p-3 mt-4 rounded" style="background: var(--text-dark); color: #fff; font-weight: 800; font-size: 16px; box-shadow: var(--shadow-md);">
                    <span>الخصوم + حقوق الملكية:</span>
                    <span class="font-monospace" style="direction:ltr;"><?php echo number_format($totalLiabilitiesAndEquity, 2); ?></span>
                </div>
            </div>
        </div>

        <?php $isBalanced = round($totalAssets, 2) === round($totalLiabilitiesAndEquity, 2); ?>
        <div class="text-center mt-5 p-3 rounded fw-bold fs-5" style="background: <?php echo $isBalanced ? 'var(--success-light)' : 'var(--danger-light)'; ?>; color: <?php echo $isBalanced ? 'var(--success)' : 'var(--danger)'; ?>;">
            <?php echo $isBalanced ? '<i class="fas fa-balance-scale"></i> الميزانية متطابقة (الأصول = الخصوم + حقوق الملكية)' : '<i class="fas fa-triangle-exclamation"></i> يوجد عدم تطابق في الميزانية!'; ?>
        </div>

    </div>
</div>

<style>
    @media print {
        body { background: #fff !important; }
        .d-print-none, .sidebar, .topbar { display: none !important; }
        .main-content { margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .form-grid { display: grid !important; grid-template-columns: 1fr 1fr !important; }
    }
</style>