<?php
// المسار: app/views/opportunity/view.php
$opportunity = $data['opportunity'] ?? null;
?>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden; max-width: 900px; margin: 0 auto;">

    <!-- الترويسة -->
    <div style="padding:30px; border-bottom:2px solid var(--border); display:flex; justify-content:space-between; align-items:flex-start; background:#f8fafc;">
        <div>
            <h2 style="margin:0 0 5px; font-size:20px; font-weight:800; color:var(--text-dark); display:flex; align-items:center; gap:10px;">
                <i class="fas fa-bullseye" style="color:var(--primary);"></i> <?php echo htmlspecialchars($opportunity->title); ?>
            </h2>
            <div style="font-family:'Cairo'; font-size:14px; color:var(--text-muted); font-weight:600;">
                العميل: <?php echo htmlspecialchars($opportunity->customer_name); ?>
            </div>
        </div>
        <div style="text-align:left; font-size:13px; color:var(--text-body);">
            <?php
            $stageClass = match ($opportunity->stage) {
                'qualification' => 'badge-info',
                'proposal' => 'badge-primary',
                'negotiation' => 'badge-warning',
                'closed_won' => 'badge-success',
                'closed_lost' => 'badge-danger',
                default => 'badge-secondary'
            };
            $stageLabel = match ($opportunity->stage) {
                'qualification' => 'تأهيل',
                'proposal' => 'تقديم عرض',
                'negotiation' => 'تفاوض',
                'closed_won' => 'تم الفوز',
                'closed_lost' => 'تمت الخسارة',
                default => $opportunity->stage
            };
            ?>
            <span class="badge <?php echo $stageClass; ?>" style="padding: 6px 12px; border-radius: 6px; font-size: 14px; font-weight: bold;"><?php echo $stageLabel; ?></span>
        </div>
    </div>

    <!-- التفاصيل -->
    <div style="padding:30px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

        <div style="background: #f1f5f9; padding: 15px; border-radius: 8px; border: 1px dashed var(--border);">
            <div style="font-size:12px; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom: 5px;">القيمة المتوقعة</div>
            <div style="font-size: 18px; font-weight: bold; color: var(--success); direction: ltr; text-align: right;">
                <?php echo number_format($opportunity->estimated_value, 2); ?> ر.س
            </div>
        </div>

        <div style="background: #f1f5f9; padding: 15px; border-radius: 8px; border: 1px dashed var(--border);">
            <div style="font-size:12px; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom: 5px;">احتمالية الفوز</div>
            <div style="font-size: 18px; font-weight: bold; color: var(--info); direction: ltr; text-align: right;">
                <?php echo $opportunity->probability; ?>%
            </div>
        </div>

        <div style="background: #f1f5f9; padding: 15px; border-radius: 8px; border: 1px dashed var(--border);">
            <div style="font-size:12px; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom: 5px;">تاريخ الإغلاق المتوقع</div>
            <div style="font-size: 15px; font-weight: bold; color: var(--text-dark);">
                <?php echo $opportunity->expected_close_date ? date('Y-m-d', strtotime($opportunity->expected_close_date)) : 'غير محدد'; ?>
            </div>
        </div>

        <div style="background: #f1f5f9; padding: 15px; border-radius: 8px; border: 1px dashed var(--border);">
            <div style="font-size:12px; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom: 5px;">الموظف المسؤول</div>
            <div style="font-size: 15px; font-weight: bold; color: var(--text-dark);">
                <i class="fas fa-user-tie"></i> <?php echo htmlspecialchars($opportunity->assigned_name ?? 'غير معين'); ?>
            </div>
        </div>

        <div style="grid-column: 1 / -1; padding-top: 15px;">
            <div style="font-size:14px; color:var(--text-dark); font-weight:700; margin-bottom: 10px; border-bottom: 1px solid var(--border); padding-bottom: 5px;">وصف الفرصة</div>
            <div style="font-size: 14px; color: var(--text-body); line-height: 1.6;">
                <?php echo nl2br(htmlspecialchars($opportunity->description ?? 'لا يوجد وصف متاح.')); ?>
            </div>
        </div>

    </div>

    <div style="padding:20px 30px; background:#f8fafc; border-top:1px solid var(--border); display:flex; justify-content:flex-start; align-items:center; gap: 10px;">
        <a href="<?php echo URLROOT; ?>/opportunity/index" style="padding:10px 20px; border:1px solid var(--border); color:var(--text-body); border-radius:8px; text-decoration:none; font-weight:600; font-size:13px; background:#fff;"><i class="fas fa-arrow-right"></i> عودة للقائمة</a>
        <button style="padding:10px 20px; background:var(--accent); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:600; cursor:not-allowed; font-size:13px; opacity: 0.7;" title="غير مفعل حالياً"><i class="fas fa-pen"></i> تعديل الفرصة</button>
    </div>
</div>

<style>
    /* CSS classes for badges */
    .badge-info {
        background-color: var(--info-light);
        color: var(--info-dark);
        border: 1px solid var(--info);
    }

    .badge-primary {
        background-color: var(--primary-light);
        color: var(--primary-dark);
        border: 1px solid var(--primary);
    }

    .badge-warning {
        background-color: var(--accent-light);
        color: #b45309;
        border: 1px solid var(--accent);
    }

    .badge-success {
        background-color: var(--success-light);
        color: #15803d;
        border: 1px solid var(--success);
    }

    .badge-danger {
        background-color: var(--danger-light);
        color: #dc2626;
        border: 1px solid var(--danger);
    }

    .badge-secondary {
        background-color: var(--page-bg);
        color: var(--text-muted);
        border: 1px solid var(--border);
    }
</style>