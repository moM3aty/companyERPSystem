<?php
$flash = $data['flash'] ?? null;
$accounts = $data['accounts'] ?? [];
$balances = $data['balances'] ?? [];
?>
<div class="page-body">
    <?php if ($flash) : ?>
        <div class="flash-msg flash-<?php echo $flash['type']; ?>">...</div>
    <?php endif; ?>

    <h3>الميزانية العمومية</h3>
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
                    $totalAssets = 0;
                    $totalLiabilities = 0;
                    $totalEquity = 0;
                    foreach ($accounts as $acc) :
                        $balance = $balances[$acc->id] ?? 0;
                        if ($acc->type == 'asset') $totalAssets += $balance;
                        elseif ($acc->type == 'liability') $totalLiabilities += $balance;
                        elseif ($acc->type == 'equity') $totalEquity += $balance;
                    ?>
                    <tr>
                        <td><?php echo $acc->code; ?></td>
                        <td><?php echo htmlspecialchars($acc->name); ?></td>
                        <td><?php echo ucfirst($acc->type); ?></td>
                        <td style="direction:ltr;text-align:right;"><?php echo number_format($balance, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr style="font-weight:bold;background:#f8fafc;">
                        <td colspan="3">إجمالي الأصول</td>
                        <td style="direction:ltr;"><?php echo number_format($totalAssets, 2); ?></td>
                    </tr>
                    <tr style="font-weight:bold;background:#f8fafc;">
                        <td colspan="3">إجمالي الخصوم</td>
                        <td style="direction:ltr;"><?php echo number_format($totalLiabilities, 2); ?></td>
                    </tr>
                    <tr style="font-weight:bold;background:#f8fafc;">
                        <td colspan="3">حقوق الملكية</td>
                        <td style="direction:ltr;"><?php echo number_format($totalEquity, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>