<?php
// app/views/treasury/index.php
$treasuries = $data['treasuries'] ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h3 class="mb-0 text-dark"><i class="fas fa-vault text-success"></i> إدارة الصناديق والبنوك (Treasury)</h3></div>
    <a href="<?php echo URLROOT; ?>/treasury/create" class="btn btn-success"><i class="fas fa-plus"></i> إضافة صندوق/بنك</a>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="form-grid mb-4" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
    <?php foreach($treasuries as $t): 
        $icon = match($t->type) { 'Bank' => 'fa-building-columns text-primary', 'Petty Cash' => 'fa-wallet text-warning', default => 'fa-money-bill-wave text-success' };
    ?>
    <div class="card mb-0 hover-card" style="border-top: 4px solid var(--success);">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($t->name); ?></h5>
                    <div class="text-muted font-monospace" style="font-size:11px;"><?php echo htmlspecialchars($t->account_number ?? 'نقدية'); ?></div>
                </div>
                <i class="fas <?php echo $icon; ?> fs-3"></i>
            </div>
            <div class="font-monospace fw-black text-success fs-3 mb-3" style="direction:ltr; text-align:right;">
                <?php echo number_format($t->current_balance, 2); ?> <span class="fs-6 text-muted"><?php echo $t->currency; ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                <span class="badge badge-secondary fs-7"><i class="fas fa-link"></i> <?php echo htmlspecialchars($t->linked_account ?? 'غير مربوط بـ الشجرة'); ?></span>
                <a href="<?php echo URLROOT; ?>/treasury/edit/<?php echo $t->id; ?>" class="btn-icon edit"><i class="fas fa-pen"></i></a>
            </div>
        </div>
    </div>
    <?php endforeach; if(empty($treasuries)): ?>
        <div class="col-12 text-center text-muted p-5 bg-white rounded border"><i class="fas fa-vault fa-3x mb-3 opacity-25"></i><br>لم يتم إعداد صناديق حتى الآن.</div>
    <?php endif; ?>
</div>