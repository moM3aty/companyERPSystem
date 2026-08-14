<?php
// app/views/fixedAsset/show.php
$a = $data['asset'] ?? null;
$dep = $data['depreciation'] ?? ['book_value'=>0, 'monthly'=>0, 'accumulated'=>0];
$isDisposed = $a->status === 'Disposed';
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div class="d-flex align-items-center gap-3">
        <h3 class="mb-0 text-dark"><i class="fas fa-car-side text-primary"></i> ملف الأصل: <?php echo htmlspecialchars($a->asset_name); ?></h3>
        <span class="badge <?php echo $isDisposed ? 'badge-danger' : 'badge-success'; ?> fs-6"><?php echo $a->status; ?></span>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-print"></i> طباعة</button>
        <a href="<?php echo URLROOT; ?>/fixedAsset/index" class="btn btn-secondary">العودة</a>
    </div>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light"><h5 class="mb-0 text-dark fw-bold"><i class="fas fa-info-circle"></i> البيانات الأساسية</h5></div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0 text-center">
                    <thead class="bg-slate-50">
                        <tr><th>كود الأصل</th><th>التصنيف</th><th>تاريخ الشراء</th><th>الضمان ينتهي</th><th>الموظف المسؤول</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="font-monospace fw-bold"><?php echo $a->asset_id; ?></td>
                            <td><?php echo htmlspecialchars($a->category); ?></td>
                            <td class="font-monospace text-primary"><?php echo $a->purchase_date; ?></td>
                            <td class="font-monospace text-danger"><?php echo $a->warranty_expiry ?? 'بدون'; ?></td>
                            <td class="fw-bold"><i class="fas fa-user-tie text-muted"></i> <?php echo htmlspecialchars($a->emp_name ?? 'الشركة'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if(!$isDisposed): ?>
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-danger-light border-danger"><h5 class="mb-0 text-danger-dark fw-bold"><i class="fas fa-trash-alt"></i> استبعاد الأصل (Disposal)</h5></div>
            <div class="card-body">
                <form action="<?php echo URLROOT; ?>/fixedAsset/dispose/<?php echo $a->id; ?>" method="POST" onsubmit="return confirm('تأكيد استبعاد/بيع الأصل؟ لا يمكن التراجع عن هذه الخطوة وسيتم إقفال حساب الأصل.');">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">نوع الاستبعاد</label>
                            <select name="disposal_type" class="form-control" required>
                                <option value="Sold">بيع (Sold)</option>
                                <option value="Scrapped">تخريد / إتلاف (Scrapped)</option>
                                <option value="Lost">فقدان / سرقة (Lost)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">قيمة الاستبعاد (البيع)</label>
                            <input type="number" step="0.01" name="disposal_value" class="form-control font-monospace" value="0.00" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">تاريخ الاستبعاد</label>
                            <input type="date" name="disposal_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-danger w-100">تنفيذ</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-danger p-4 shadow-sm text-center">
            <h4 class="fw-black mb-3"><i class="fas fa-ban"></i> هذا الأصل تم استبعاده من الدفاتر</h4>
            <div class="font-monospace fs-5">تم (<?php echo $a->disposal_type; ?>) بتاريخ: <strong><?php echo $a->disposal_date; ?></strong> بقيمة <strong><?php echo number_format($a->disposal_value, 2); ?> SAR</strong></div>
        </div>
        <?php endif; ?>

    </div>

    <div class="col-md-4">
        <div class="card shadow-sm h-100 border-primary">
            <div class="card-header bg-primary text-white text-center py-3">
                <h5 class="mb-0 fw-bold text-white"><i class="fas fa-calculator"></i> السجل المالي والإهلاك</h5>
            </div>
            <div class="card-body bg-light">
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="text-muted fw-bold">تكلفة الشراء الأصلية:</span>
                    <span class="font-monospace fw-bold text-dark fs-5" style="direction:ltr;"><?php echo number_format($a->purchase_cost, 2); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="text-muted fw-bold">العمر الافتراضي:</span>
                    <span class="font-monospace fw-bold text-dark fs-5"><?php echo $a->useful_life; ?> <small class="text-muted">سنوات</small></span>
                </div>
                <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                    <span class="text-muted fw-bold">مجمع الإهلاك التراكمي:</span>
                    <span class="font-monospace fw-bold text-danger fs-5" style="direction:ltr;">(<?php echo number_format($dep['accumulated'], 2); ?>)</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span class="text-primary fw-black fs-5">القيمة الدفترية الحالية:</span>
                    <span class="font-monospace fw-black text-primary fs-3" style="direction:ltr;"><?php echo number_format($dep['book_value'], 2); ?></span>
                </div>

                <?php if(!$isDisposed): ?>
                <div class="bg-white p-3 rounded border border-info">
                    <h6 class="fw-bold text-info-dark mb-3 text-center">إثبات قيد إهلاك يدوي / شهري</h6>
                    <form action="<?php echo URLROOT; ?>/fixedAsset/postDepreciation/<?php echo $a->id; ?>" method="POST">
                        <div class="mb-2">
                            <label class="text-muted" style="font-size:11px;">المبلغ (المقترح شهرياً: <?php echo number_format($dep['monthly'], 2); ?>)</label>
                            <input type="number" step="0.01" name="amount" class="form-control font-monospace text-center fw-bold text-info" value="<?php echo $dep['monthly']; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-info text-white w-100 mt-2"><i class="fas fa-bolt"></i> ترحيل القيد لدفتر اليومية</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>