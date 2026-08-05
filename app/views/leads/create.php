<?php
// المسار: app/views/leads/create.php
$users = $data['users'] ?? [];
?>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden; max-width:800px; margin:0 auto;">

    <div style="padding:24px 30px; border-bottom:1px solid var(--border); background:#f8fafc;">
        <h3 style="margin:0; font-size:16px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-bullseye" style="color:var(--primary);"></i> إضافة عميل محتمل جديد (Lead)
        </h3>
    </div>

    <form action="<?php echo URL_ROOT; ?>/lead/create" method="POST">
        <div style="padding:30px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">اسم العميل <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;" placeholder="الاسم الكامل">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">اسم الشركة (إن وجد)</label>
                <input type="text" name="company" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;" placeholder="شركة كذا...">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">رقم الجوال</label>
                <input type="text" name="phone" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; direction:ltr; text-align:right;" placeholder="05XXXXXXXX">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">البريد الإلكتروني</label>
                <input type="email" name="email" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; direction:ltr; text-align:right;" placeholder="email@example.com">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">مصدر العميل (Source)</label>
                <select name="source" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; cursor:pointer;">
                    <option value="social_media">السوشيال ميديا</option>
                    <option value="website">الموقع الإلكتروني</option>
                    <option value="referral">إحالة (توصية)</option>
                    <option value="cold_call">اتصال بارد (Cold Call)</option>
                    <option value="other">أخرى</option>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">تعيين إلى (موظف المبيعات)</label>
                <select name="assigned_to" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; cursor:pointer;">
                    <option value="">-- غير معين (مفتوح) --</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?php echo $u->id; ?>"><?php echo htmlspecialchars($u->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px; grid-column:1/-1;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">ملاحظات مبدئية</label>
                <textarea name="notes" rows="3" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;" placeholder="اكتب اهتمامات العميل أو الملاحظات التي تم أخذها عند تسجيله..."></textarea>
            </div>

        </div>

        <div style="padding:20px 30px; background:#f8fafc; border-top:1px solid var(--border); display:flex; gap:12px;">
            <button type="submit" style="padding:10px 24px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:700; cursor:pointer;"><i class="fas fa-save"></i> حفظ العميل</button>
            <a href="<?php echo URL_ROOT; ?>/lead/index" style="padding:10px 24px; background:transparent; border:1px solid var(--border); color:var(--text-body); border-radius:8px; text-decoration:none; font-weight:600;">إلغاء</a>
        </div>
    </form>
</div>