<?php
// app/views/journal/view.php
$entry = $data['entry'] ?? null;
$lines = $data['lines'] ?? [];
?>

<div class="card" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-primary text-white" style="border-radius: var(--radius-md) var(--radius-md) 0 0;">
        <h3 class="card-title text-white"><i class="fas fa-book-journal-whills"></i> قيد يومية مزدوج</h3>
        <span class="badge badge-secondary" style="background: rgba(255,255,255,0.2); border:none;"><?php echo htmlspecialchars($entry->entry_number); ?></span>
    </div>
    
    <div class="card-body">
        <div class="form-grid mb-4">
            <div>
                <div class="text-muted fs-6 fw-bold text-uppercase mb-1">البيان الرئيسي</div>
                <div class="fs-5 fw-bold text-dark"><?php echo nl2br(htmlspecialchars($entry->description)); ?></div>
            </div>
            <div class="text-left">
                <div class="text-muted fs-6 fw-bold text-uppercase mb-1">تاريخ القيد</div>
                <div class="fs-6 fw-bold text-dark"><i class="far fa-calendar-alt text-primary"></i> <?php echo date('Y-m-d', strtotime($entry->entry_date)); ?></div>
                <div class="text-muted fs-6 mt-1"><i class="fas fa-user-pen"></i> بواسطة: <?php echo htmlspecialchars($entry->creator_name ?? 'النظام'); ?></div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table border rounded">
                <thead class="bg-light">
                    <tr>
                        <th>كود החساب</th>
                        <th>اسم الحساب</th>
                        <th>البيان الفرعي</th>
                        <th>مدين (Debit)</th>
                        <th>دائن (Credit)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalDebit = 0; $totalCredit = 0;
                    foreach($lines as $line): 
                        $totalDebit += $line->debit;
                        $totalCredit += $line->credit;
                    ?>
                    <tr>
                        <td class="font-monospace fw-bold text-primary"><?php echo htmlspecialchars($line->account_code); ?></td>
                        <td class="fw-bold text-dark"><?php echo htmlspecialchars($line->account_name); ?></td>
                        <td class="text-muted fs-6"><?php echo htmlspecialchars($line->description ?? '—'); ?></td>
                        <td class="font-monospace fw-bold <?php echo $line->debit > 0 ? 'text-success' : 'text-muted'; ?>" style="direction:ltr; text-align:right;"><?php echo number_format($line->debit, 2); ?></td>
                        <td class="font-monospace fw-bold <?php echo $line->credit > 0 ? 'text-danger' : 'text-muted'; ?>" style="direction:ltr; text-align:right;"><?php echo number_format($line->credit, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <td colspan="3" class="fw-bold text-dark" style="padding: 16px;">الإجمالي الكلي:</td>
                        <td class="font-monospace fs-5 fw-bold text-success" style="direction:ltr; text-align:right; padding: 16px; border-bottom:4px double var(--success);"><?php echo number_format($totalDebit, 2); ?></td>
                        <td class="font-monospace fs-5 fw-bold text-danger" style="direction:ltr; text-align:right; padding: 16px; border-bottom:4px double var(--danger);"><?php echo number_format($totalCredit, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <div class="card-footer d-flex justify-content-between">
        <a href="<?php echo URLROOT; ?>/journal/index" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> عودة للقائمة</a>
        <button class="btn btn-dark" onclick="window.print()"><i class="fas fa-print"></i> طباعة القيد</button>
    </div>
</div>

<style>
    @media print {
        body { background: #fff !important; }
        .sidebar, .topbar, .btn { display: none !important; }
        .main-content { margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; max-width: 100% !important;}
        .card-header { background: #fff !important; color: #000 !important; border-bottom: 2px solid #000; }
        .card-title { color: #000 !important; }
        .badge { color: #000 !important; border: 1px solid #000; }
    }
</style>