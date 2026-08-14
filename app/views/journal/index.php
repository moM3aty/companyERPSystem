<?php
// app/views/journal/index.php
$journals = $data['journals'] ?? [];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h3 class="mb-0 text-dark"><i class="fas fa-book-journal-whills text-success"></i> القيود اليومية (Journal Entries)</h3></div>
    <a href="<?php echo URLROOT; ?>/journal/create" class="btn btn-success"><i class="fas fa-plus"></i> قيد محاسبي جديد</a>
</div>

<?php $flash = Session::getFlash(); if ($flash): ?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>"><i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($flash['message']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0 table-hover">
            <thead class="bg-light">
                <tr>
                    <th>رقم القيد</th>
                    <th>التاريخ</th>
                    <th>البيان / الوصف</th>
                    <th class="text-center">إجمالي القيد</th>
                    <th class="text-center">الحالة</th>
                    <th class="text-center">بواسطة</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($journals as $j): ?>
                <tr>
                    <td class="font-monospace fw-bold text-primary"><?php echo htmlspecialchars($j->journal_number); ?></td>
                    <td class="font-monospace"><?php echo $j->date; ?></td>
                    <td><?php echo htmlspecialchars($j->description); ?></td>
                    <td class="text-center font-monospace fw-bold text-success" style="direction:ltr;"><?php echo number_format($j->total_amount, 2); ?></td>
                    <td class="text-center"><span class="badge badge-success"><i class="fas fa-check"></i> معتمد ومرحل</span></td>
                    <td class="text-center text-muted" style="font-size:12px;"><?php echo htmlspecialchars($j->creator_name); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>