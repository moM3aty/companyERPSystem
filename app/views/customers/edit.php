<?php
// المسار: app/views/customers/edit.php
$customer = $customer ?? ($data['customer'] ?? null);
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-user-pen" style="color: var(--accent);"></i> تعديل بيانات العميل: <?php echo htmlspecialchars($customer->name); ?></h3>
    </div>
    
    <div class="card-body">
        <form action="<?php echo URLROOT; ?>/customer/edit/<?php echo $customer->id; ?>" method="POST">
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">الاسم <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($customer->name); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">نوع العميل <span class="required">*</span></label>
                    <select name="type" class="form-control" required>
                        <option value="individual" <?php echo $customer->type === 'individual' ? 'selected' : ''; ?>>فرد (Individual)</option>
                        <option value="company" <?php echo $customer->type === 'company' ? 'selected' : ''; ?>>شركة (Company)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control font-monospace text-right" value="<?php echo htmlspecialchars($customer->phone); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control font-monospace text-right" value="<?php echo htmlspecialchars($customer->email); ?>">
                </div>

                <div class="form-group full-width">
                    <label class="form-label">العنوان التفصيلي</label>
                    <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($customer->address); ?>">
                </div>
            </div>

            <!-- لا نسمح بتعديل الرصيد من هنا لأنه يؤثر على الحسابات، يتم التعديل عبر القيود -->
            <div class="alert alert-warning mt-4 mb-0">
                <i class="fas fa-info-circle"></i> لتعديل الرصيد المالي للعميل، الرجاء إجراء قيد محاسبي أو تسجيل دفعة من شاشة التحصيلات.
            </div>

            <div class="card-footer" style="margin: 24px -24px -24px; padding: 20px 24px;">
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> حفظ التعديلات</button>
                <a href="<?php echo URLROOT; ?>/customer/index" class="btn btn-secondary">إلغاء</a>
            </div>
            
        </form>
    </div>
</div>