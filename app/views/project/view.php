<?php
// app/views/project/view.php
$project = $project ?? ($data['project'] ?? null);
$tasks = $tasks ?? ($data['tasks'] ?? []);
$employees = $employees ?? ($data['employees'] ?? []);

// حساب نسبة إنجاز المشروع بناءً على متوسط إنجاز المهام
$totalProgress = 0;
$taskCount = count($tasks);
if ($taskCount > 0) {
    foreach ($tasks as $t) {
        $totalProgress += $t->progress;
    }
    $projectProgress = round($totalProgress / $taskCount);
} else {
    $projectProgress = 0;
}
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-diagram-project text-primary"></i> <?php echo htmlspecialchars($project->name ?? ''); ?></h3>
        <p class="text-muted mt-1 font-monospace fs-6"><i class="fas fa-barcode"></i> الكود: <?php echo htmlspecialchars($project->code ?? ''); ?></p>
    </div>
    <div class="text-left bg-white p-3 rounded border shadow-sm">
        <div class="text-muted fs-6 fw-bold text-uppercase mb-1">التقدم العام للمشروع</div>
        <div class="d-flex align-items-center gap-3">
            <div style="width: 200px; height: 10px; background: var(--border-color); border-radius: 5px; overflow: hidden;">
                <div style="width: <?php echo $projectProgress; ?>%; height: 100%; background: <?php echo $projectProgress == 100 ? 'var(--success)' : 'var(--primary)'; ?>; transition: width 0.5s ease;"></div>
            </div>
            <div class="font-monospace fw-bold <?php echo $projectProgress == 100 ? 'text-success' : 'text-primary'; ?>" style="font-size: 24px;"><?php echo $projectProgress; ?>%</div>
        </div>
    </div>
</div>

<div class="card mb-4 border-0 shadow-sm" style="background: linear-gradient(to left, #ffffff, #f8fafc);">
    <div class="card-body p-0">
        <div class="form-grid p-4" style="grid-template-columns: repeat(4, 1fr); gap: 20px;">
            <div>
                <div class="text-muted fs-6 fw-bold text-uppercase mb-1">الميزانية</div>
                <div class="fs-5 fw-bold text-success font-monospace" style="direction:ltr; text-align:right;"><?php echo number_format($project->budget ?? 0, 2); ?> ر.س</div>
            </div>
        </div>
        <div class="p-3 bg-light border-top d-flex gap-2">
            <a href="<?php echo URLROOT; ?>/timesheet/project/<?php echo $project->id; ?>" class="btn btn-dark"><i class="fas fa-stopwatch"></i> سجل تتبع الوقت (Timesheets)</a>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-chart-gantt text-info"></i> المخطط الزمني للمهام (Gantt Chart)</h3>
    </div>
    <div class="card-body">
        <?php if(count($tasks) > 0): ?>
            <div id="gantt_chart_div" style="width: 100%; height: auto; min-height: 250px; overflow-x: auto;"></div>
        <?php else: ?>
            <div class="text-center text-muted p-4">
                <i class="fas fa-tasks fs-1 mb-3 d-block opacity-50"></i>
                لا توجد مهام لرسم المخطط الزمني. قم بإضافة المهام بالأسفل.
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="form-grid" style="grid-template-columns: 1fr 2.5fr; align-items: start;">
    
    <!-- نموذج إضافة مهمة -->
    <div class="card mb-0">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-plus text-primary"></i> إضافة مهمة</h3>
        </div>
        <form action="<?php echo URLROOT; ?>/project/addTask/<?php echo $project->id; ?>" method="POST">
            <div class="card-body form-group gap-3">
                <div class="form-group">
                    <label class="form-label">العنوان <span class="required">*</span></label>
                    <input type="text" name="title" class="form-control" required placeholder="مثال: تحليل المتطلبات">
                </div>
                <div class="form-group">
                    <label class="form-label">المسؤول</label>
                    <select name="assigned_to" class="form-control">
                        <option value="">-- تفويض لاحقاً --</option>
                        <?php foreach($employees as $emp): ?>
                            <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">من تاريخ <span class="required">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">إلى تاريخ <span class="required">*</span></label>
                    <input type="date" name="due_date" class="form-control" required>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> تسجيل المهمة</button>
            </div>
        </form>
    </div>

    <!-- جدول إدارة المهام التفاعلي -->
    <div class="card mb-0">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-list-check text-success"></i> إدارة إنجاز المهام</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 30%;">اسم المهمة</th>
                            <th style="width: 20%;">المسؤول</th>
                            <th style="width: 20%;">المدة</th>
                            <th style="width: 30%;" class="text-center">تحديث نسبة الإنجاز (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($tasks as $t): ?>
                        <tr>
                            <td class="fw-bold text-dark"><?php echo htmlspecialchars($t->title); ?></td>
                            <td class="text-muted fs-6"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($t->assigned_to_name ?? 'غير معين'); ?></td>
                            <td class="text-muted font-monospace fs-6">
                                <?php echo date('M d', strtotime($t->start_date)); ?> <i class="fas fa-arrow-left" style="font-size: 10px;"></i> <?php echo date('M d', strtotime($t->due_date)); ?>
                            </td>
                            <td class="text-center">
                                <!-- شريط تحديث الإنجاز التفاعلي -->
                                <form action="<?php echo URLROOT; ?>/project/updateTaskProgress/<?php echo $t->id; ?>" method="POST" class="d-flex align-items-center gap-3">
                                    <input type="hidden" name="project_id" value="<?php echo $project->id; ?>">
                                    <input type="range" name="progress" min="0" max="100" step="5" value="<?php echo $t->progress; ?>" style="flex:1; cursor:pointer; accent-color: var(--primary);" onchange="this.form.submit()" oninput="this.nextElementSibling.textContent = this.value + '%'">
                                    <span class="font-monospace fw-bold <?php echo $t->progress == 100 ? 'text-success' : 'text-primary'; ?>" style="min-width: 40px; text-align: left;"><?php echo $t->progress; ?>%</span>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($tasks)): ?>
                        <tr><td colspan="4" class="text-center text-muted p-5">لم يتم تسجيل أي مهام تشغيلية في هذا المشروع.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if(count($tasks) > 0): ?>
<script type="text/javascript">
    google.charts.load('current', {'packages':['gantt']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Task ID');
        data.addColumn('string', 'Task Name');
        data.addColumn('string', 'Resource');
        data.addColumn('date', 'Start Date');
        data.addColumn('date', 'End Date');
        data.addColumn('number', 'Duration');
        data.addColumn('number', 'Percent Complete');
        data.addColumn('string', 'Dependencies');

        data.addRows([
            <?php foreach($tasks as $t): ?>
            [
                'Task_<?php echo $t->id; ?>', 
                '<?php echo addslashes($t->title); ?>', 
                '<?php echo addslashes($t->assigned_to_name ?? "مهمة"); ?>',
                new Date(<?php echo date('Y, m-1, d', strtotime($t->start_date)); ?>), 
                new Date(<?php echo date('Y, m-1, d', strtotime($t->due_date)); ?>), 
                null, 
                <?php echo (int)$t->progress; ?>, 
                null
            ],
            <?php endforeach; ?>
        ]);

        var options = {
            height: <?php echo (count($tasks) * 45) + 50; ?>,
            gantt: {
                trackHeight: 35,
                barHeight: 20,
                innerGridTrack: {fill: '#f8fafc'},
                innerGridDarkTrack: {fill: '#f1f5f9'},
                labelStyle: {
                    fontName: 'Cairo',
                    fontSize: 13,
                    color: '#334155'
                }
            }
        };

        var chart = new google.visualization.Gantt(document.getElementById('gantt_chart_div'));
        chart.draw(data, options);
    }
</script>
<?php endif; ?>