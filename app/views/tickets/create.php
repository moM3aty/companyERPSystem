<?php
// المسار: app/views/tickets/create.php
$customers = $data['customers'] ?? [];
$users = $data['users'] ?? [];
?>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden; max-width:800px; margin:0 auto;">
    
    <div style="padding:24px 30px; border-bottom:1px solid var(--border); background:#f8fafc;">
        <h3 style="margin:0; font-size:16px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-headset" style="color:var(--primary);"></i> فتح تذكرة شكوى أو دعم جديدة
        </h3>
    </div>

    <form action="<?php echo URL_ROOT; ?>/ticket/create" method="POST">
        <div style="padding:30px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            
            <div style="display:flex; flex-direction:column; gap:8px; grid-column:1/-1;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">عنوان التذكرة (Subject) <span style="color:var(--danger);">*</span></label>
                <input type="text" name="subject" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; font-size:15px; outline:none;" placeholder="اكتب ملخصاً قصيراً للمشكلة...">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">العميل المرتبط (إن وجد)</label>
                <select name="customer_id" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; cursor:pointer;">
                    <option value="">-- تذكرة عامة أو داخلية --</option>
                    <?php foreach($customers as $c): ?>
                        <option value="<?php echo $c->id; ?>"><?php echo htmlspecialchars($c->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">تعيين إلى الموظف (Assigned To)</label>
                <select name="assigned_to" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; cursor:pointer;">
                    <option value="">-- يحدد لاحقاً --</option>
                    <?php foreach($users as $u): ?>
                        <option value="<?php echo $u->id; ?>"><?php echo htmlspecialchars($u->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px; grid-column:1/-1;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">مستوى الأولوية (Priority)</label>
                <div style="display:flex; gap:15px; margin-top:5px;">
                    <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer;">
                        <input type="radio" name="priority" value="low"> منخفضة
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer;">
                        <input type="radio" name="priority" value="medium" checked> متوسطة
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer; color:var(--accent); font-weight:700;">
                        <input type="radio" name="priority" value="high"> عالية
                    </label>
                    <label style="display:flex; align-items:center; gap:6px; font-size:13px; cursor:pointer; color:var(--danger); font-weight:700;">
                        <input type="radio" name="priority" value="urgent"> عاجلة جداً (طارئة)
                    </label>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px; grid-column:1/-1;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">وصف المشكلة بالتفصيل <span style="color:var(--danger);">*</span></label>
                <textarea name="description" required rows="5" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;" placeholder="اكتب كل التفاصيل التي ستساعد في حل المشكلة بسرعة..."></textarea>
            </div>

        </div>
        
        <div style="padding:20px 30px; background:#f8fafc; border-top:1px solid var(--border); display:flex; gap:12px;">
            <button type="submit" style="padding:10px 24px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:700; cursor:pointer;"><i class="fas fa-save"></i> فتح التذكرة</button>
            <a href="<?php echo URL_ROOT; ?>/ticket/index" style="padding:10px 24px; background:transparent; border:1px solid var(--border); color:var(--text-body); border-radius:8px; text-decoration:none; font-weight:600;">إلغاء</a>
        </div>
    </form>
</div>