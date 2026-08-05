<?php
// المسار: app/views/followups/create.php
/** @var array $data */
$leads = $data['leads'] ?? [];
?>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden; max-width:600px; margin:0 auto;">
    <div style="padding:24px 30px; border-bottom:1px solid var(--border); background:#f8fafc;">
        <h3 style="margin:0; font-size:16px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-calendar-plus" style="color:var(--primary);"></i> جدولة متابعة جديدة
        </h3>
    </div>

    <form action="<?php echo URL_ROOT; ?>/followup/create" method="POST">
        <div style="padding:30px; display:flex; flex-direction:column; gap:20px;">
            
            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">العميل المحتمل <span style="color:var(--danger);">*</span></label>
                <select name="lead_id" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;">
                    <option value="">-- اختر العميل --</option>
                    <?php foreach($leads as $lead): ?>
                        <option value="<?php echo $lead->id; ?>"><?php echo htmlspecialchars($lead->name . ($lead->company ? ' - ' . $lead->company : '')); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">نوع المتابعة <span style="color:var(--danger);">*</span></label>
                <select name="type" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;">
                    <option value="call">مكالمة هاتفية</option>
                    <option value="meeting">اجتماع مباشر</option>
                    <option value="email">إرسال بريد إلكتروني</option>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">التاريخ والوقت <span style="color:var(--danger);">*</span></label>
                <input type="datetime-local" name="scheduled_date" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; direction:ltr;">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">الهدف والملاحظات</label>
                <textarea name="notes" rows="3" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;" placeholder="مثال: مناقشة تفاصيل العرض الفني..."></textarea>
            </div>

        </div>
        
        <div style="padding:20px 30px; background:#f8fafc; border-top:1px solid var(--border); display:flex; gap:12px;">
            <button type="submit" style="padding:10px 24px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:700; cursor:pointer;"><i class="fas fa-save"></i> حفظ وجدولة</button>
            <a href="<?php echo URL_ROOT; ?>/followup/index" style="padding:10px 24px; background:transparent; border:1px solid var(--border); color:var(--text-body); border-radius:8px; text-decoration:none; font-weight:600;">إلغاء</a>
        </div>
    </form>
</div>