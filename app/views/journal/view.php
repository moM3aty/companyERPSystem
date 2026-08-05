<?php
// المسار: app/views/journal/view.php
$entry = $data['entry'] ?? null;
$lines = $data['lines'] ?? [];
?>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden; max-width: 900px; margin: 0 auto;">
    
    <!-- الترويسة -->
    <div style="padding:30px; border-bottom:2px solid var(--border); display:flex; justify-content:space-between; align-items:flex-start; background:#f8fafc;">
        <div>
            <h2 style="margin:0 0 5px; font-size:20px; font-weight:800; color:var(--text-dark); display:flex; align-items:center; gap:10px;">
                <i class="fas fa-book-journal-whills" style="color:var(--primary);"></i> قيد يومية
            </h2>
            <div style="font-family:monospace; font-size:16px; color:var(--primary-dark); font-weight:700; background:var(--primary-light); display:inline-block; padding:4px 10px; border-radius:6px; direction:ltr;">
                <?php echo htmlspecialchars($entry->entry_number); ?>
            </div>
        </div>
        <div style="text-align:left; font-size:13px; color:var(--text-body);">
            <div style="margin-bottom:5px;"><i class="far fa-calendar-alt"></i> التاريخ: <strong><?php echo date('Y-m-d', strtotime($entry->entry_date)); ?></strong></div>
            <div><i class="fas fa-user-pen"></i> أُنشئ بواسطة: <strong><?php echo htmlspecialchars($entry->creator_name ?? 'النظام'); ?></strong></div>
        </div>
    </div>

    <!-- معلومات المرجع -->
    <div style="padding:20px 30px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; background:#fff;">
        <div style="flex:1;">
            <span style="font-size:12px; color:var(--text-muted); font-weight:700; text-transform:uppercase;">البيان (الوصف)</span>
            <p style="margin:5px 0 0; font-size:14px; color:var(--text-dark); font-weight:600;"><?php echo nl2br(htmlspecialchars($entry->description)); ?></p>
        </div>
        <?php if($entry->reference_type): ?>
        <div style="text-align:left; border-right:1px solid var(--border); padding-right:20px;">
            <span style="font-size:12px; color:var(--text-muted); font-weight:700; text-transform:uppercase;">مستند مرجعي</span>
            <div style="margin-top:5px; font-size:14px; font-weight:700;">
                <span style="background:var(--page-bg); padding:4px 8px; border-radius:6px; border:1px solid var(--border);">
                    <?php 
                        $refLabel = match($entry->reference_type) {
                            'invoice' => 'فاتورة', 'purchase_order' => 'أمر شراء', 'payroll' => 'رواتب', default => $entry->reference_type
                        };
                        echo htmlspecialchars($refLabel . ' #' . $entry->reference_id); 
                    ?>
                </span>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- جدول السطور -->
    <div style="padding:30px;">
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:12px; font-size:12px; color:var(--text-muted);">رمز الحساب</th>
                    <th style="padding:12px; font-size:12px; color:var(--text-muted);">اسم الحساب</th>
                    <th style="padding:12px; font-size:12px; color:var(--text-muted);">البيان</th>
                    <th style="padding:12px; font-size:12px; color:var(--text-muted); text-align:left;">مدين</th>
                    <th style="padding:12px; font-size:12px; color:var(--text-muted); text-align:left;">دائن</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalDebit = 0; $totalCredit = 0;
                foreach($lines as $line): 
                    $totalDebit += $line->debit;
                    $totalCredit += $line->credit;
                ?>
                <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:12px; font-family:monospace; font-size:13px; color:var(--primary-dark); font-weight:700;"><?php echo htmlspecialchars($line->account_code); ?></td>
                    <td style="padding:12px; font-weight:600; color:var(--text-dark);"><?php echo htmlspecialchars($line->account_name); ?></td>
                    <td style="padding:12px; font-size:13px; color:var(--text-muted);"><?php echo htmlspecialchars($line->description ?? '—'); ?></td>
                    <td style="padding:12px; text-align:left; font-family:monospace; font-size:14px; font-weight:700; direction:ltr; <?php echo $line->debit > 0 ? 'color:var(--text-dark);' : 'color:var(--text-muted); opacity:0.5;'; ?>"><?php echo number_format($line->debit, 2); ?></td>
                    <td style="padding:12px; text-align:left; font-family:monospace; font-size:14px; font-weight:700; direction:ltr; <?php echo $line->credit > 0 ? 'color:var(--text-dark);' : 'color:var(--text-muted); opacity:0.5;'; ?>"><?php echo number_format($line->credit, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot style="background:#f1f5f9; border-top:2px solid var(--text-muted);">
                <tr>
                    <td colspan="3" style="padding:15px; font-weight:800; color:var(--text-dark); text-align:left;">الإجمالي:</td>
                    <td style="padding:15px; text-align:left; font-family:monospace; font-size:16px; font-weight:800; color:var(--text-dark); direction:ltr; border-bottom:4px double var(--text-dark);"><?php echo number_format($totalDebit, 2); ?></td>
                    <td style="padding:15px; text-align:left; font-family:monospace; font-size:16px; font-weight:800; color:var(--text-dark); direction:ltr; border-bottom:4px double var(--text-dark);"><?php echo number_format($totalCredit, 2); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div style="padding:20px 30px; background:#f8fafc; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
        <a href="<?php echo URL_ROOT; ?>/journal/index" style="padding:10px 20px; border:1px solid var(--border); color:var(--text-body); border-radius:8px; text-decoration:none; font-weight:600; font-size:13px; background:#fff;"><i class="fas fa-arrow-right"></i> عودة للقائمة</a>
        <button onclick="window.print()" style="padding:10px 20px; background:var(--text-dark); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:600; cursor:pointer; font-size:13px;"><i class="fas fa-print"></i> طباعة القيد</button>
    </div>
</div>