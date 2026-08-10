<?php
// app/views/employee/create.php
$auto_emp_num = $data['auto_emp_num'] ?? 'EMP-' . date('ym') . rand(100, 999);
?>

<div class="card" style="max-width: 1000px; margin: 0 auto;">
    <div class="card-header bg-light">
        <h3 class="card-title text-dark"><i class="fas fa-user-plus text-primary"></i> تسجيل موظف جديد (Employee Master)</h3>
    </div>
    
    <form action="<?php echo URLROOT; ?>/employee/create" method="POST">
        <div class="card-body">
            
            <!-- 1. Basic Information -->
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-4 text-primary"><i class="fas fa-address-card"></i> المعلومات الأساسية (Basic Information)</h5>
            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">الرقم الوظيفي (Employee ID) <span class="required">*</span></label>
                    <input type="text" name="employee_number" class="form-control font-monospace bg-light fw-bold" value="<?php echo htmlspecialchars($auto_emp_num); ?>" readonly>
                </div>
                <div class="form-group">
                    <label class="form-label">الاسم الكامل (Full Name) <span class="required">*</span></label>
                    <input type="text" name="full_name" class="form-control" required placeholder="الاسم الرباعي الرسمي">
                </div>
                <div class="form-group">
                    <label class="form-label">الاسم بالعربية (Name in Arabic)</label>
                    <input type="text" name="name_ar" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">الاسم بالإنجليزية (Name in English)</label>
                    <input type="text" name="name_en" class="form-control" style="direction:ltr; text-align:left;">
                </div>
                <div class="form-group">
                    <label class="form-label">الجنس (Gender)</label>
                    <select name="gender" class="form-control">
                        <option value="Male">ذكر (Male)</option>
                        <option value="Female">أنثى (Female)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الميلاد (Date of Birth)</label>
                    <input type="date" name="dob" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">الجنسية (Nationality)</label>
                    <input type="text" name="nationality" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">الحالة الاجتماعية (Marital Status)</label>
                    <select name="marital_status" class="form-control">
                        <option value="Single">أعزب (Single)</option>
                        <option value="Married">متزوج (Married)</option>
                        <option value="Divorced">مطلق (Divorced)</option>
                        <option value="Widowed">أرمل (Widowed)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">فصيلة الدم (Blood Group)</label>
                    <input type="text" name="blood_group" class="form-control font-monospace" placeholder="مثال: O+">
                </div>
            </div>

            <!-- Contact Information -->
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-4 text-info"><i class="fas fa-phone"></i> بيانات التواصل والطوارئ (Contact Info)</h5>
            <div class="form-grid mb-4 bg-light p-3 rounded border">
                <div class="form-group mb-0">
                    <label class="form-label">الجوال الشخصي (Personal Mobile)</label>
                    <input type="text" name="personal_mobile" class="form-control font-monospace" style="direction:ltr;">
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">البريد الإلكتروني (Personal Email)</label>
                    <input type="email" name="personal_email" class="form-control font-monospace" style="direction:ltr;">
                </div>
                <div class="form-group mb-0 full-width mt-2 border-top pt-3">
                    <strong class="text-danger d-block mb-2"><i class="fas fa-truck-medical"></i> جهة اتصال للطوارئ (Emergency Contact):</strong>
                    <div class="d-flex gap-2">
                        <input type="text" name="emergency_contact" class="form-control" placeholder="اسم جهة الطوارئ">
                        <input type="text" name="emergency_relation" class="form-control" placeholder="صلة القرابة">
                        <input type="text" name="emergency_phone" class="form-control font-monospace" placeholder="رقم الجوال" style="direction:ltr;">
                    </div>
                </div>
            </div>

            <!-- 2. Employment Information -->
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-4 text-success"><i class="fas fa-briefcase"></i> البيانات الوظيفية (Employment Info)</h5>
            <div class="form-grid mb-4">
                <div class="form-group">
                    <label class="form-label">المسمى الوظيفي العام (Job Title)</label>
                    <input type="text" name="job_title" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">المنصب (Position)</label>
                    <input type="text" name="position" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">فئة الموظف (Employee Category)</label>
                    <input type="text" name="employee_category" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">نوع الدوام (Employment Type)</label>
                    <select name="employment_type" class="form-control fw-bold">
                        <option value="Full-time">دوام كامل (Full-time)</option>
                        <option value="Part-time">دوام جزئي (Part-time)</option>
                        <option value="Temporary">مؤقت (Temporary)</option>
                        <option value="Contract">عقد (Contract)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الالتحاق (Date of Joining)</label>
                    <input type="date" name="date_of_joining" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">بداية فترة التجربة (Probation Start)</label>
                    <input type="date" name="probation_start_date" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label text-warning">نهاية فترة التجربة (Probation End)</label>
                    <input type="date" name="probation_end_date" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">حالة الموظف (Employment Status)</label>
                    <select name="employment_status" class="form-control fw-bold">
                        <option value="Active" selected>نشط (Active)</option>
                        <option value="Exit Process">في فترة إخلاء الطرف (Exit Process)</option>
                        <option value="Terminated">منهي خدماته (Terminated)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">موقع العمل (Work Location)</label>
                    <input type="text" name="work_location" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">مركز التكلفة (Cost Center)</label>
                    <input type="text" name="cost_center" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">المشروع/التكليف (Project/Assignment)</label>
                    <input type="text" name="project_assignment" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">درجة الموظف (Employee Grade)</label>
                    <input type="text" name="employee_grade" class="form-control font-monospace">
                </div>
            </div>

            <!-- 3. Bank & Payroll Information -->
            <h5 class="fw-bold text-dark border-bottom pb-2 mb-4 text-warning"><i class="fas fa-university"></i> البيانات المالية والبنكية (Bank & Payroll)</h5>
            <div class="form-grid mb-4 bg-light p-3 rounded border">
                <div class="form-group">
                    <label class="form-label">اسم البنك (Bank Name)</label>
                    <input type="text" name="bank_name" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">اسم صاحب الحساب (Account Holder)</label>
                    <input type="text" name="account_holder" class="form-control">
                </div>
                <div class="form-group full-width">
                    <label class="form-label">رقم الآيبان (IBAN)</label>
                    <input type="text" name="iban" class="form-control font-monospace fw-bold" style="direction:ltr;">
                </div>
                <div class="form-group">
                    <label class="form-label">طريقة دفع الراتب (Payment Method)</label>
                    <select name="salary_payment_method" class="form-control">
                        <option value="Bank Transfer">تحويل بنكي (Bank Transfer)</option>
                        <option value="Cash">نقدي (Cash)</option>
                        <option value="Cheque">شيك (Cheque)</option>
                    </select>
                </div>
                
                <div class="form-group full-width mt-3 border-top pt-3 border-secondary">
                    <strong class="text-success d-block mb-3"><i class="fas fa-money-bill-wave"></i> تفاصيل الراتب والبدلات (Salary & Allowances):</strong>
                    <div class="d-flex gap-2">
                        <div class="flex-1">
                            <label class="form-label text-muted" style="font-size: 11px;">الراتب الأساسي</label>
                            <input type="number" step="0.01" name="basic_salary" class="form-control font-monospace text-success fw-bold fs-5" placeholder="Basic Salary" value="0.00" style="direction:ltr;">
                        </div>
                        <div class="flex-1">
                            <label class="form-label text-muted" style="font-size: 11px;">بدل السكن</label>
                            <input type="number" step="0.01" name="housing_allowance" class="form-control font-monospace text-primary fw-bold fs-5" placeholder="Housing Allowance" value="0.00" style="direction:ltr;">
                        </div>
                        <div class="flex-1">
                            <label class="form-label text-muted" style="font-size: 11px;">بدل المواصلات</label>
                            <input type="number" step="0.01" name="transport_allowance" class="form-control font-monospace text-info fw-bold fs-5" placeholder="Transport Allowance" value="0.00" style="direction:ltr;">
                        </div>
                        <div class="flex-1">
                            <label class="form-label text-muted" style="font-size: 11px;">بدلات أخرى</label>
                            <input type="number" step="0.01" name="other_allowances" class="form-control font-monospace fw-bold fs-5" placeholder="Other Allowances" value="0.00" style="direction:ltr;">
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> إنشاء ملف الموظف</button>
            <a href="<?php echo URLROOT; ?>/employee/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>