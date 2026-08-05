<?php
// المسار: app/views/assets/create.php
?>

<div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden; max-width:800px; margin:0 auto;">
    
    <div style="padding:24px 30px; border-bottom:1px solid var(--border); background:#f8fafc;">
        <h3 style="margin:0; font-size:16px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
            <i class="fas fa-building" style="color:var(--primary);"></i> تسجيل أصل ثابت جديد
        </h3>
        <p style="margin:4px 0 0; font-size:12px; color:var(--text-muted);">سيتم حساب الإهلاك السنوي تلقائياً باستخدام طريقة القسط الثابت.</p>
    </div>

    <form action="<?php echo URL_ROOT; ?>/asset/create" method="POST">
        <div style="padding:30px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
            
            <div style="display:flex; flex-direction:column; gap:8px; grid-column:1/-1;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">اسم الأصل <span style="color:var(--danger);">*</span></label>
                <input type="text" name="name" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; font-size:15px; outline:none;" placeholder="مثال: لابتوب ديل ماكبوك، أو سيارة تويوتا كامري 2024...">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">الرقم التسلسلي (Asset Tag)</label>
                <input type="text" name="asset_tag" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:monospace; outline:none; direction:ltr; text-align:right;" placeholder="يُترك فارغاً للتوليد التلقائي">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">تصنيف الأصل <span style="color:var(--danger);">*</span></label>
                <select name="category" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; cursor:pointer;">
                    <option value="equipment">معدات وأجهزة كمبيوتر</option>
                    <option value="vehicle">مركبات وسيارات</option>
                    <option value="furniture">أثاث ومفروشات</option>
                    <option value="real_estate">عقارات ومباني</option>
                    <option value="other">أخرى</option>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">تاريخ الشراء / الاستحواذ <span style="color:var(--danger);">*</span></label>
                <input type="date" name="purchase_date" required value="<?php echo date('Y-m-d'); ?>" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">موقع تواجد الأصل</label>
                <input type="text" name="location" placeholder="مثال: الإدارة العامة، فرع الرياض..." style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">تكلفة الشراء (ر.س) <span style="color:var(--danger);">*</span></label>
                <input type="number" name="purchase_cost" step="0.01" min="1" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:monospace; font-size:15px; font-weight:800; color:var(--danger); outline:none; direction:ltr; text-align:right;" placeholder="0.00">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">القيمة التخريدية (الخردة)</label>
                <input type="number" name="salvage_value" step="0.01" min="0" value="0.00" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:monospace; font-size:14px; outline:none; direction:ltr; text-align:right;" placeholder="0.00">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">العمر الإنتاجي المقدر (بالسنوات) <span style="color:var(--danger);">*</span></label>
                <input type="number" name="useful_life_years" min="1" value="5" required style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:monospace; font-size:14px; outline:none; direction:ltr; text-align:right;">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">الحالة التشغيلية</label>
                <select name="status" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none; cursor:pointer;">
                    <option value="active">نشط (مستخدم حالياً)</option>
                    <option value="maintenance">في الصيانة</option>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px; grid-column:1/-1;">
                <label style="font-size:13px; font-weight:600; color:var(--text-body);">ملاحظات إضافية</label>
                <textarea name="notes" rows="3" style="padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:'Cairo'; outline:none;" placeholder="أي تفاصيل أخرى (مثل رقم الموديل، المورد، إلخ)..."></textarea>
            </div>

        </div>
        
        <div style="padding:20px 30px; background:#f8fafc; border-top:1px solid var(--border); display:flex; gap:12px;">
            <button type="submit" style="padding:10px 24px; background:var(--primary); color:#fff; border:none; border-radius:8px; font-family:'Cairo'; font-weight:700; cursor:pointer;"><i class="fas fa-save"></i> حفظ الأصل</button>
            <a href="<?php echo URL_ROOT; ?>/asset/index" style="padding:10px 24px; background:transparent; border:1px solid var(--border); color:var(--text-body); border-radius:8px; text-decoration:none; font-weight:600;">إلغاء</a>
        </div>
    </form>
</div>