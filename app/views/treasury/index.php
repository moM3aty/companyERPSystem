<?php /** @var array $treasuries */ ?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>الصندوق والبنوك</h2>
        <a href="/treasury/createTransaction" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة سند قبض/صرف</a>
    </div>

    <div class="row">
        <?php foreach ($treasuries as $treasury): ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title"><?= htmlspecialchars($treasury['name']) ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?= $treasury['type'] == 'cash' ? 'خزينة نقدية' : 'حساب بنكي' ?></h6>
                    <h2 class="<?= $treasury['current_balance'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= number_format($treasury['current_balance'], 2) ?> ج.م
                    </h2>
                    <a href="/treasury/show/<?= $treasury['id'] ?>" class="btn btn-sm btn-outline-info mt-3">عرض الحركات</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>