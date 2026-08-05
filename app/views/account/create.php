<?php
// المسار: app/views/account/create.php
$parents = $data['parents'] ?? [];
?>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden; max-width:800px; margin:0 auto;">
    
    <div style="padding:24px 30px; border-bottom:1px solid var(--border); background:#f8fafc;">
        <h3 style="margin:0; font-size:16px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-file-invoice-dollar" style="color:var(--primary);"></i> إضافة حساب مالي جديد
        </h3>
    </div>

    <form action="<?php echo URL_ROOT; ?>/account/create" method="POST">
        <div style="padding:30px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            
            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">رقم الحساب (الكود) <span style="color:var(--danger);">*</span></label>
                <input type="text" name="code" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:monospace; font-size:15px; outline:none; direction:ltr; text-align:right;" placeholder="مثال: 10101">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">اسم الحساب <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;" placeholder="مثال: البنك الأهلي - جاري">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">النوع (طبيعة الحساب) <span style="color:var(--danger);">*</span></label>
                <select name="type" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; cursor:pointer;">
                    <option value="asset">أصول (Assets)</option>
                    <option value="liability">خصوم/التزامات (Liabilities)</option>
                    <option value="equity">حقوق الملكية (Equity)</option>
                    <option value="revenue">إيرادات (Revenues)</option>
                    <option value="expense">مصروفات (Expenses)</option>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">الحساب الأب (إن وجد)</label>
                <select name="parent_id" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; cursor:pointer;">
                    <option value="">-- حساب رئيسي (بدون أب) --</option>
                    <?php foreach($parents as $p): ?>
                        <option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->code . ' - ' . $p->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px; grid-column:1/-1;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">الرصيد الافتتاحي</label>
                <input type="number" name="balance" step="0.01" value="0.00" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:monospace; font-size:14px; outline:none; direction:ltr; text-align:right;">
                <span style="font-size:11px; color:var(--text-muted);">رصيد بداية المدة للحساب عند إنشائه.</span>
            </div>

        </div>
        
        <div style="padding:20px 30px; background:#f8fafc; border-top:1px solid var(--border); display:flex; gap:12px;">
            <button type="submit" style="padding:10px 24px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:700; cursor:pointer;"><i class="fas fa-save"></i> حفظ الحساب</button>
            <a href="<?php echo URL_ROOT; ?>/account/tree" style="padding:10px 24px; background:transparent; border:1px solid var(--border); color:var(--text-body); border-radius:8px; text-decoration:none; font-weight:600;">إلغاء</a>
        </div>
    </form>
</div>