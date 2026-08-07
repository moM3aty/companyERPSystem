<?php
// app/views/companies/create.php
?>
<div class="card border-0 shadow-sm" style="max-width: 900px; margin: 0 auto;">
    <div class="card-header bg-dark text-white" style="border-radius: var(--radius-lg) var(--radius-lg) 0 0;">
        <h3 class="card-title text-white mb-0"><i class="fas fa-building-flag"></i> إعداد شركة جديدة (Tenant)</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/company/create" method="POST">
        <div class="card-body border-bottom bg-light">
            <h4 class="mb-4 text-primary" style="font-size: 16px; font-weight: 800;"><i class="fas fa-1 me-2"></i> بيانات المؤسسة (الشركة)</h4>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">الاسم التجاري للشركة <span class="required">*</span></label>
                    <input type="text" name="company_name" class="form-control" required placeholder="مثال: مؤسسة الأفق لتقنية المعلومات">
                </div>
                <div class="form-group">
                    <label class="form-label">النطاق المخصص (Subdomain) - اختياري</label>
                    <input type="text" name="domain" class="form-control font-monospace" placeholder="مثال: alofoq.erppro.com" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني للشركة</label>
                    <input type="email" name="company_email" class="form-control font-monospace" placeholder="info@company.com" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">رقم هاتف الشركة</label>
                    <input type="text" name="company_phone" class="form-control font-monospace" placeholder="0500000000" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ انتهاء الاشتراك (SaaS)</label>
                    <input type="date" name="subscription_ends_at" class="form-control" title="اتركه فارغاً لاشتراك مفتوح">
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="alert alert-info mb-4 border-0" style="background: var(--info-50); color: var(--info-700);">
                <i class="fas fa-user-shield fs-4"></i>
                <div class="ms-2">
                    <strong>إعداد المدير الأول للشركة:</strong><br>
                    <span style="font-size: 13px;">سيقوم النظام تلقائياً بإنشاء مستخدم بصلاحية (Admin) وربطه بهذه الشركة لتمكينه من الدخول والبدء في تهيئة بياناته.</span>
                </div>
            </div>

            <h4 class="mb-4 text-accent" style="font-size: 16px; font-weight: 800;"><i class="fas fa-2 me-2"></i> بيانات الدخول للمدير الأول</h4>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">اسم المدير <span class="required">*</span></label>
                    <input type="text" name="admin_name" class="form-control" required placeholder="اسم مدير النظام للشركة">
                </div>
                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني (للدخول) <span class="required">*</span></label>
                    <input type="email" name="admin_email" class="form-control font-monospace" required placeholder="admin@company.com" style="direction:ltr; text-align:right;">
                </div>
                <div class="form-group">
                    <label class="form-label">كلمة المرور الافتراضية <span class="required">*</span></label>
                    <input type="text" name="admin_password" class="form-control font-monospace" required placeholder="123456" style="direction:ltr; text-align:right;">
                </div>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3 bg-white border-top">
            <button type="submit" class="btn btn-primary"><i class="fas fa-rocket"></i> تسجيل الشركة وإنشاء المدير</button>
            <a href="<?php echo URLROOT; ?>/company/index" class="btn btn-secondary">إلغاء الرجوع</a>
        </div>
    </form>
</div>