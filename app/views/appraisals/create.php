<?php
// المسار: app/views/appraisals/create.php
$employees = $data['employees'] ?? [];
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-star-half-stroke text-primary"></i> تقييم أداء موظف</h3>
    </div>

    <form action="<?php echo URLROOT; ?>/appraisal/create" method="POST" id="appraisalForm">
        <div class="card-body border-bottom">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">الموظف المراد تقييمه <span class="required">*</span></label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- يرجى اختيار الموظف --</option>
                        <?php foreach ($employees as $emp) : ?>
                            <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?> — <?php echo htmlspecialchars($emp->position ?? 'بدون مسمى'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">تاريخ التقييم <span class="required">*</span></label>
                    <input type="date" name="evaluation_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
        </div>

        <div class="card-body">
            <h4 class="mb-4" style="font-size: 15px; font-weight: 700;"><i class="fas fa-chart-line text-muted"></i> معايير التقييم (من 100)</h4>
            
            <div class="d-flex flex-column gap-4">
                <div class="form-group">
                    <label class="form-label d-flex justify-content-between">1. الأداء المهني وجودة العمل <span id="valPerf" class="font-monospace fw-bold text-primary">80</span></label>
                    <input type="range" name="performance_score" class="form-control p-0" style="height: 6px;" min="0" max="100" value="80" oninput="document.getElementById('valPerf').textContent = this.value; calculateTotal();">
                </div>

                <div class="form-group">
                    <label class="form-label d-flex justify-content-between">2. السلوك التعاوني وروح الفريق <span id="valBeh" class="font-monospace fw-bold text-primary">80</span></label>
                    <input type="range" name="behavior_score" class="form-control p-0" style="height: 6px;" min="0" max="100" value="80" oninput="document.getElementById('valBeh').textContent = this.value; calculateTotal();">
                </div>

                <div class="form-group">
                    <label class="form-label d-flex justify-content-between">3. الانضباط والحضور <span id="valAtt" class="font-monospace fw-bold text-primary">80</span></label>
                    <input type="range" name="attendance_score" class="form-control p-0" style="height: 6px;" min="0" max="100" value="80" oninput="document.getElementById('valAtt').textContent = this.value; calculateTotal();">
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 p-3 bg-light border rounded">
                <span class="fw-bold">النتيجة النهائية والتقدير العام:</span>
                <div class="d-flex align-items-center gap-3">
                    <span id="gradeBadge" class="badge badge-info fs-6">جيد جداً</span>
                    <span id="finalScore" class="font-monospace fs-4 fw-bold text-primary">80.00%</span>
                </div>
            </div>
        </div>

        <div class="card-body border-top">
            <div class="form-group full-width">
                <label class="form-label">ملاحظات وتوصيات (اختياري)</label>
                <textarea name="comments" class="form-control" rows="3" placeholder="توصيات للموظف، نقاط القوة، نقاط تحتاج لتطوير..."></textarea>
            </div>
        </div>
        
        <div class="card-footer d-flex gap-3">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التقييم</button>
            <a href="<?php echo URLROOT; ?>/appraisal/index" class="btn btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

<script>
    function calculateTotal() {
        const perf = parseInt(document.getElementsByName('performance_score')[0].value) || 0;
        const beh = parseInt(document.getElementsByName('behavior_score')[0].value) || 0;
        const att = parseInt(document.getElementsByName('attendance_score')[0].value) || 0;
        
        const avg = (perf + beh + att) / 3;
        document.getElementById('finalScore').textContent = avg.toFixed(2) + '%';
        
        const badge = document.getElementById('gradeBadge');
        if (avg >= 90) { badge.className = 'badge badge-success fs-6'; badge.textContent = 'ممتاز'; }
        else if (avg >= 80) { badge.className = 'badge badge-info fs-6'; badge.textContent = 'جيد جداً'; }
        else if (avg >= 70) { badge.className = 'badge badge-primary fs-6'; badge.textContent = 'جيد'; }
        else if (avg >= 60) { badge.className = 'badge badge-warning fs-6'; badge.textContent = 'مقبول'; }
        else { badge.className = 'badge badge-danger fs-6'; badge.textContent = 'ضعيف'; }
    }
</script>