<?php
$flash = $data['flash'] ?? null;
$accounts = $data['accounts'] ?? [];
$balances = $data['balances'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <h3>قائمة الدخل</h3>
    <div class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>كود الحساب</th>
                        <th>اسم الحساب</th>
                        <th>النوع</th>
                        <th>الرصيد</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalRevenue = 0;
                    $totalExpense = 0;
                    foreach ($accounts as $acc) :
                        $balance = $balances[$acc->id] ?? 0;
                        if ($acc->type == 'revenue') $totalRevenue += $balance;
                        elseif ($acc->type == 'expense') $totalExpense += $balance;
                    ?>
                    <tr>
                        <td><?php echo $acc->code; ?></td>
                        <td><?php echo htmlspecialchars($acc->name); ?></td>
                        <td><?php echo ucfirst($acc->type); ?></td>
                        <td style="direction:ltr;text-align:right;"><?php echo number_format($balance, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="font-weight:bold;background:#f8fafc;">
                        <td colspan="3">إجمالي الإيرادات</td>
                        <td style="direction:ltr;"><?php echo number_format($totalRevenue, 2); ?></td>
                    </tr>
                    <tr style="font-weight:bold;background:#f8fafc;">
                        <td colspan="3">إجمالي المصروفات</td>
                        <td style="direction:ltr;"><?php echo number_format($totalExpense, 2); ?></td>
                    </tr>
                    <tr style="font-weight:bold;background:#f8fafc;color:<?php echo ($totalRevenue - $totalExpense) >= 0 ? 'var(--success)' : 'var(--danger)'; ?>;">
                        <td colspan="3">صافي الربح / (الخسارة)</td>
                        <td style="direction:ltr;"><?php echo number_format($totalRevenue - $totalExpense, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>