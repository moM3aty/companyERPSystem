<?php
// app/views/treasury/index.php
$treasuries = $data['treasuries'] ?? [];
$totalCash = $data['total_cash'] ?? 0;
$totalBank = $data['total_bank'] ?? 0;
$grandTotal = $totalCash + $totalBank;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-vault text-primary"></i> إدارة الصندوق والبنوك</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">متابعة الأرصدة النقدية والبنكية في المنشأة بشكل لحظي.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo URLROOT; ?>/treasury/transactions" class="btn btn-secondary">
            <i class="fas fa-list"></i> سجل الحركات
        </a>
        <a href="<?php echo URLROOT; ?>/treasury/createTransaction" class="btn btn-primary">
            <i class="fas fa-money-bill-transfer"></i> سند قبض / صرف
        </a>
    </div>
</div>

<div class="form-grid mb-4" style="grid-template-columns: repeat(3, 1fr);">
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--success-light); color: var(--success); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 22px; font-weight: 800;" class="font-monospace text-success"><?php echo number_format($totalCash, 2); ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">إجمالي النقدية (كاش)</span>
            </div>
        </div>
    </div>
    
    <div class="card mb-0">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: var(--info-light); color: var(--info); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-building-columns"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 22px; font-weight: 800;" class="font-monospace text-info"><?php echo number_format($totalBank, 2); ?></h4>
                <span class="text-muted" style="font-size: 12px; font-weight: 700;">إجمالي أرصدة البنوك</span>
            </div>
        </div>
    </div>

    <div class="card mb-0" style="background: linear-gradient(135deg, var(--text-dark), #1e293b); color: #fff; border:none;">
        <div class="card-body d-flex align-items-center gap-3">
            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); color: var(--primary-light); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                <i class="fas fa-scale-balanced"></i>
            </div>
            <div>
                <h4 style="margin: 0; font-size: 24px; font-weight: 900; color: var(--primary-light);" class="font-monospace"><?php echo number_format($grandTotal, 2); ?></h4>
                <span style="font-size: 12px; color: #cbd5e1; font-weight: 700;">السيولة الإجمالية المتاحة</span>
            </div>
        </div>
    </div>
</div>

<div class="form-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
    <?php if(!empty($treasuries)): foreach($treasuries as $t): 
        $icon = $t->type === 'cash' ? 'fa-cash-register' : 'fa-building-columns';
        $iconColor = $t->type === 'cash' ? 'var(--success)' : 'var(--info)';
        $bgColor = $t->type === 'cash' ? 'var(--success-light)' : 'var(--info-light)';
        $typeLabel = $t->type === 'cash' ? 'صندوق نقدية' : 'حساب بنكي';
    ?>
    <div class="card mb-0 text-center" style="transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
        <div class="card-body">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: <?php echo $bgColor; ?>; color: <?php echo $iconColor; ?>; display: flex; align-items: center; justify-content: center; font-size: 28px; margin: 0 auto 15px;">
                <i class="fas <?php echo $icon; ?>"></i>
            </div>
            <h4 style="margin: 0 0 5px; font-size: 18px; font-weight: 800; color: var(--text-dark);"><?php echo htmlspecialchars($t->name); ?></h4>
            <div style="font-size: 12px; color: var(--text-muted); font-weight: 600; margin-bottom: 15px;">
                <?php echo $typeLabel; ?> <?php echo $t->account_number ? ' | ' . htmlspecialchars($t->account_number) : ''; ?>
            </div>
            
            <div style="background: var(--page-bg); padding: 15px; border-radius: var(--radius-sm); border: 1px dashed var(--border);">
                <div style="font-size: 11px; color: var(--text-muted); text-transform: uppercase; margin-bottom: 5px;">الرصيد الحالي</div>
                <div style="font-size: 26px; font-weight: 900; color: var(--text-dark);" class="font-monospace">
                    <?php echo number_format($t->current_balance, 2); ?>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; else: ?>
    <div style="grid-column: 1 / -1;" class="text-center p-5 text-muted card">
        <i class="fas fa-vault fs-1 mb-3"></i>
        <h5>لا توجد خزائن أو بنوك مسجلة</h5>
    </div>
    <?php endif; ?>
</div>