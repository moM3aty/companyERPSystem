<?php
// app/views/payment/show.php
$p = $data['payment'] ?? null;
$typeLabel = $p->payment_type == 'Out' ? 'سند صرف (Payment Voucher)' : 'سند قبض (Receipt Voucher)';
$colorClass = $p->payment_type == 'Out' ? 'danger' : 'success';
$partyLabel = $p->payment_type == 'Out' ? 'يُصرف إلى السادة:' : 'استلمنا من السادة:';
$partyName = $p->supplier_name ?: $p->customer_name ?: 'جهات أخرى';
?>

<div class="card" style="max-width: 800px; margin: 0 auto; border: none; box-shadow: var(--shadow-md);">
    <div class="card-header bg-white d-print-none d-flex justify-content-between align-items-center" style="border-bottom: 2px solid var(--border-color);">
        <h3 class="card-title text-dark mb-0"><i class="fas fa-money-check text-<?php echo $colorClass; ?>"></i> <?php echo $typeLabel; ?> #<?php echo htmlspecialchars($p->voucher_number); ?></h3>
        <div class="d-flex gap-2">
            <button class="btn btn-dark btn-sm" onclick="window.print()"><i class="fas fa-print"></i> طباعة السند</button>
            <a href="<?php echo URLROOT; ?>/payment/index" class="btn btn-secondary btn-sm">رجوع</a>
        </div>
    </div>

    <div class="card-body p-5 bg-white position-relative">
        
        <!-- Watermark -->
        <div class="position-absolute" style="top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 80px; font-weight: 900; opacity: 0.03; z-index: 0; pointer-events: none; white-space: nowrap;">
            <?php echo strtoupper($p->payment_type == 'Out' ? 'PAYMENT OUT' : 'RECEIPT IN'); ?>
        </div>

        <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4 position-relative" style="z-index: 1;">
            <div>
                <h1 style="font-size: 26px; font-weight: 900; color: var(--<?php echo $colorClass; ?>-dark); margin-bottom: 5px;"><?php echo $typeLabel; ?></h1>
                <div class="font-monospace fw-bold text-dark mt-2" style="font-size: 16px;">Voucher No: <?php echo htmlspecialchars($p->voucher_number); ?></div>
            </div>
            <div class="text-left" style="direction: ltr; text-align: left;">
                <h2 style="font-size: 22px; font-weight: 900; color: var(--text-dark); margin-bottom: 5px;">ERP Pro</h2>
                <div class="text-muted fs-6 font-monospace mt-2">Date: <?php echo $p->payment_date; ?></div>
            </div>
        </div>

        <div class="mb-4 position-relative" style="z-index: 1;">
            <div style="font-size: 16px; font-weight: 700; color: var(--text-dark); display: flex; align-items: baseline; gap: 15px; margin-bottom: 20px;">
                <span style="width: 150px;" class="text-muted"><?php echo $partyLabel; ?></span>
                <span style="flex: 1; border-bottom: 1px dotted #000; padding-bottom: 5px; font-size: 18px; color: var(--primary-dark); font-weight: 900;"><?php echo htmlspecialchars($partyName); ?></span>
            </div>
            <div style="font-size: 16px; font-weight: 700; color: var(--text-dark); display: flex; align-items: baseline; gap: 15px; margin-bottom: 20px;">
                <span style="width: 150px;" class="text-muted">مبلغاً وقدره (أرقام):</span>
                <span style="flex: 1; border-bottom: 1px dotted #000; padding-bottom: 5px; font-family: 'Fira Code', monospace; font-size: 20px; font-weight: 900; direction:ltr; text-align:right;">SAR <?php echo number_format($p->amount, 2); ?></span>
            </div>
            <div style="font-size: 16px; font-weight: 700; color: var(--text-dark); display: flex; align-items: baseline; gap: 15px; margin-bottom: 20px;">
                <span style="width: 150px;" class="text-muted">وذلك عن (البيان):</span>
                <span style="flex: 1; border-bottom: 1px dotted #000; padding-bottom: 5px; line-height: 1.6;"><?php echo htmlspecialchars($p->notes); ?></span>
            </div>
            
            <div class="row mt-4 pt-3 border-top">
                <div class="col-6 mb-3">
                    <span class="text-muted fw-bold d-block" style="font-size:13px;">طريقة الدفع:</span>
                    <span class="fw-bold text-dark"><i class="fas fa-wallet text-<?php echo $colorClass; ?> me-1"></i> <?php echo htmlspecialchars($p->payment_method); ?></span>
                </div>
                <div class="col-6 mb-3">
                    <span class="text-muted fw-bold d-block" style="font-size:13px;">الخزنة / البنك:</span>
                    <span class="fw-bold text-dark"><i class="fas fa-building-columns text-muted me-1"></i> <?php echo htmlspecialchars($p->treasury_name); ?></span>
                </div>
                <?php if($p->reference_number): ?>
                <div class="col-6 mb-3">
                    <span class="text-muted fw-bold d-block" style="font-size:13px;">المرجع (الشيك/الحوالة):</span>
                    <span class="font-monospace fw-bold text-dark"><?php echo htmlspecialchars($p->reference_number); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Signatures (Print Only) -->
        <div class="mt-5 pt-5 d-none d-print-block" style="page-break-inside: avoid;">
            <div class="row" style="display: flex; justify-content: space-around; text-align: center;">
                <div style="flex: 1;"><div class="fw-bold text-dark mb-4">المحاسب (المُعد)</div><div style="border-bottom: 1px dashed var(--slate-400); width: 60%; margin: 0 auto 10px;"></div><div class="text-muted" style="font-size:11px;"><?php echo htmlspecialchars($p->user_name); ?></div></div>
                <div style="flex: 1;"><div class="fw-bold text-dark mb-4">الاعتماد (المدير المالي)</div><div style="border-bottom: 1px dashed var(--slate-400); width: 60%; margin: 0 auto 10px;"></div></div>
                <div style="flex: 1;"><div class="fw-bold text-dark mb-4">توقيع المستلم</div><div style="border-bottom: 1px dashed var(--slate-400); width: 60%; margin: 0 auto 10px;"></div></div>
            </div>
        </div>

    </div>
</div>

<style>
    @media print { body { background: #fff !important; } .d-print-none, .sidebar, .topbar { display: none !important; } .main-content { margin: 0 !important; } .card { box-shadow: none !important; border: 2px solid #000 !important; max-width: 100% !important; margin: 0 !important;} .card-body { padding: 40px !important; } }
</style>