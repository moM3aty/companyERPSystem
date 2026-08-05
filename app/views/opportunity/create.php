<?php
// المسار: app/views/opportunity/create.php
$customers = $data['customers'] ?? [];
$users = $data['users'] ?? [];
?>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden; max-width:800px; margin:0 auto;">

    <div style="padding:24px 30px; border-bottom:1px solid var(--border); background:#f8fafc;">
        <h3 style="margin:0; font-size:16px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-bullseye" style="color:var(--primary);"></i> إنشاء فرصة بيعية جديدة
        </h3>
    </div>

    <form action="<?php echo URL_ROOT; ?>/opportunity/create" method="POST">
        <div style="padding:30px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">

            <div style="display:flex; flex-direction:column; gap:8px; grid-column:1/-1;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">عنوان الفرصة <span style="color:var(--danger);">*</span></label>
                <input type="text" name="title" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; font-size:15px; outline:none;" placeholder="مثال: توريد أجهزة لشركة مقاولات">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">العميل المستهدف <span style="color:var(--danger);">*</span></label>
                <select name="customer_id" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; cursor:pointer;">
                    <option value="">-- اختر العميل --</option>
                    <?php foreach ($customers as $c): ?>
                        <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">المرحلة (Stage) <span style="color:var(--danger);">*</span></label>
                <select name="stage" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; cursor:pointer;">
                    <option value="qualification">تأهيل (Qualification)</option>
                    <option value="proposal">تقديم عرض (Proposal)</option>
                    <option value="negotiation">تفاوض (Negotiation)</option>
                    <option value="closed_won">تم الفوز (Closed Won)</option>
                    <option value="closed_lost">تمت الخسارة (Closed Lost)</option>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">القيمة المتوقعة (ر.س)</label>
                <input type="number" name="estimated_value" step="0.01" value="0.00" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:monospace; font-size:14px; outline:none; direction:ltr; text-align:right;">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">احتمالية الفوز (%)</label>
                <input type="number" name="probability" min="0" max="100" value="50" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:monospace; font-size:14px; outline:none; direction:ltr; text-align:right;">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">تاريخ الإغلاق المتوقع</label>
                <input type="date" name="expected_close_date" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">الموظف المسؤول (Assigned To)</label>
                <select name="assigned_to" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; cursor:pointer;">
                    <option value="">-- تعيين لموظف مبيعات --</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?php echo $u->id; ?>" <?php echo ($u->id == Session::getUserId()) ? 'selected' : ''; ?>><?php echo htmlspecialchars($u->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px; grid-column:1/-1;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">وصف تفصيلي</label>
                <textarea name="description" rows="4" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;" placeholder="أدخل تفاصيل الفرصة، الملاحظات، أو متطلبات العميل..."></textarea>
            </div>

        </div>

        <div style="padding:20px 30px; background:#f8fafc; border-top:1px solid var(--border); display:flex; gap:12px;">
            <button type="submit" style="padding:10px 24px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:700; cursor:pointer;"><i class="fas fa-save"></i> حفظ الفرصة</button>
            <a href="<?php echo URL_ROOT; ?>/opportunity/index" style="padding:10px 24px; background:transparent; border:1px solid var(--border); color:var(--text-body); border-radius:8px; text-decoration:none; font-weight:600;">إلغاء</a>
        </div>
    </form>
</div>