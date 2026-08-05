<?php
// المسار: app/views/journal/index.php
$entries = $data['entries'] ?? [];
?>

<div class="toolbar" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:16px;">
    <div>
        <h3 style="margin:0; font-size:18px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-book-journal-whills" style="color:var(--primary);"></i> سجل القيود اليومية (Journal Entries)
        </h3>
        <p style="margin:4px 0 0; font-size:13px; color:var(--text-muted);">جميع الحركات المالية مسجلة بنظام القيد المزدوج</p>
    </div>
    
    <div style="display:flex; gap:10px;">
        <a href="<?php echo URL_ROOT; ?>/journal/create" style="padding:10px 20px; background:var(--primary); color:#fff; border-radius:8px; text-decoration:none; font-size:13px; font-weight:700; display:inline-flex; align-items:center; gap:8px; transition:0.2s; box-shadow:0 4px 10px rgba(20,184,166,0.2);">
            <i class="fas fa-plus"></i> إضافة قيد يومية
        </a>
    </div>
</div>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; text-align:right;">
            <thead style="background:#f8fafc; border-bottom:2px solid var(--border);">
                <tr>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">#</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">رقم القيد</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">التاريخ</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">البيان (الوصف)</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase;">المنشئ</th>
                    <th style="padding:14px 20px; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; text-align:center;">إجراء</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($entries)): foreach($entries as $entry): ?>
                <tr style="border-bottom:1px solid var(--border); transition:background 0.2s;">
                    <td style="padding:14px 20px; color:var(--text-muted); font-size:12px; font-weight:600;"><?php echo $entry->id; ?></td>
                    <td style="padding:14px 20px; font-weight:700; color:var(--text-dark); font-family:monospace;"><?php echo htmlspecialchars($entry->entry_number); ?></td>
                    <td style="padding:14px 20px; font-size:13px; color:var(--text-body);"><i class="far fa-calendar-alt"></i> <?php echo date('Y-m-d', strtotime($entry->entry_date)); ?></td>
                    <td style="padding:14px 20px; font-size:13px; color:var(--text-body);"><?php echo htmlspecialchars($entry->description); ?></td>
                    <td style="padding:14px 20px; font-size:12px; color:var(--text-muted);"><i class="fas fa-user-pen"></i> <?php echo htmlspecialchars($entry->creator_name ?? 'النظام'); ?></td>
                    <td style="padding:14px 20px; text-align:center;">
                        <a href="<?php echo URL_ROOT; ?>/journal/show/<?php echo $entry->id; ?>" style="display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:8px; background:var(--primary-light); color:var(--primary-dark); text-decoration:none;" title="عرض القيد">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding:60px 20px;">
                        <i class="fas fa-book-open" style="font-size:40px; color:var(--border); margin-bottom:12px;"></i>
                        <h4 style="margin:0 0 6px; font-size:15px; color:var(--text-dark);">لا توجد قيود يومية</h4>
                        <p style="margin:0; font-size:13px; color:var(--text-muted);">ابدأ بتسجيل أول قيد محاسبي لضبط الحسابات.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>