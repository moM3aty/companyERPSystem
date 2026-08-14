<?php
// app/views/tickets/edit.php
$ticket = $data['ticket'] ?? null;
?>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header bg-warning-light border-warning">
        <h3 class="card-title text-warning-dark mb-0"><i class="fas fa-pen"></i> تعديل التذكرة #<?php echo $ticket->id; ?></h3>
    </div>
    <form action="<?php echo URLROOT; ?>/ticket/edit/<?php echo $ticket->id; ?>" method="POST">
        <div class="card-body form-grid" style="grid-template-columns: 1fr;">
            <div class="form-group mb-0">
                <label class="form-label">موضوع المشكلة <span class="required">*</span></label>
                <input type="text" name="subject" class="form-control" value="<?php echo htmlspecialchars($ticket->subject); ?>" required>
            </div>
            <div class="form-group mb-0 mt-3">
                <label class="form-label">درجة الأهمية</label>
                <select name="priority" class="form-control fw-bold">
                    <option value="low" <?php echo $ticket->priority == 'low' ? 'selected' : ''; ?>>منخفضة (Low)</option>
                    <option value="medium" <?php echo $ticket->priority == 'medium' ? 'selected' : ''; ?>>متوسطة (Medium)</option>
                    <option value="high" class="text-danger" <?php echo $ticket->priority == 'high' ? 'selected' : ''; ?>>عاجلة / حرجة (High)</option>
                </select>
            </div>
            <div class="form-group mb-0 mt-3">
                <label class="form-label">تفاصيل المشكلة <span class="required">*</span></label>
                <textarea name="description" class="form-control" rows="5" required><?php echo htmlspecialchars($ticket->description); ?></textarea>
            </div>
        </div>
        <div class="card-footer d-flex gap-3 bg-light mt-0">
            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> تحديث التذكرة</button>
            <a href="<?php echo URLROOT; ?>/ticket/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>