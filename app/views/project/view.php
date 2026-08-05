<?php
// المسار: app/views/project/view.php
$project = $data['project'] ?? null;
$tasks = $data['tasks'] ?? [];

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

// جلب الموظفين لتعيينهم للمهام (اختياري، يمكن جلبهم من الكنترولر ولكن لتبسيط الواجهة)
$db = Database::getInstance();
$db->query("SELECT id, name FROM employees ORDER BY name ASC");
$employees = $db->resultSet();
?>

<!-- استدعاء مكتبة Google Charts لرسم مخطط Gantt -->
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div style="display:flex; flex-direction:column; gap:24px; max-width:1000px; margin:0 auto;">

    <!-- بطاقة تفاصيل المشروع الأساسية -->
    <div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
        <div style="padding:24px 30px; border-bottom:1px solid var(--border); background:linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color:#fff; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 style="margin:0 0 5px; font-size:22px; font-weight:800; display:flex; align-items:center; gap:10px;">
                    <i class="fas fa-diagram-project"></i> <?php echo htmlspecialchars($project->name); ?>
                </h2>
                <div style="font-size:13px; opacity:0.9;"><i class="fas fa-barcode"></i> كود المشروع: <?php echo htmlspecialchars($project->code); ?></div>
            </div>
            <div style="text-align:left;">
                <div style="font-size:12px; margin-bottom:4px; opacity:0.9;">نسبة الإنجاز الكلية</div>
                <div style="font-size:24px; font-weight:900; font-family:monospace;"><?php echo $projectProgress; ?>%</div>
            </div>
        </div>

        <div style="padding:24px 30px; display:grid; grid-template-columns:repeat(4, 1fr); gap:20px; background:#f8fafc; border-bottom:1px solid var(--border);">
            <div>
                <div style="font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:4px;">العميل</div>
                <div style="font-size:14px; font-weight:700; color:var(--text-dark);"><i class="fas fa-building" style="color:var(--primary);"></i> <?php echo htmlspecialchars($project->customer_name ?? 'غير محدد'); ?></div>
            </div>
            <div>
                <div style="font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:4px;">مدير المشروع</div>
                <div style="font-size:14px; font-weight:700; color:var(--text-dark);"><i class="fas fa-user-tie" style="color:var(--accent);"></i> <?php echo htmlspecialchars($project->manager_name ?? 'غير معين'); ?></div>
            </div>
            <div>
                <div style="font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:4px;">المدة الزمنية</div>
                <div style="font-size:13px; font-weight:700; color:var(--text-dark); display:flex; flex-direction:column;">
                    <span>من: <?php echo date('Y-m-d', strtotime($project->start_date)); ?></span>
                    <span>إلى: <?php echo date('Y-m-d', strtotime($project->end_date)); ?></span>
                </div>
            </div>
            <div>
                <div style="font-size:11px; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-bottom:4px;">الميزانية المعتمدة</div>
                <div style="font-size:15px; font-weight:800; color:var(--success); font-family:monospace; direction:ltr; text-align:right;">
                    <?php echo number_format($project->budget, 2); ?> ر.س
                </div>
            </div>
        </div>
    </div>

    <!-- مخطط جانت (Gantt Chart) -->
    <div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
        <div style="padding:20px 24px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0; font-size:16px; font-weight:700; color:var(--text-dark);"><i class="fas fa-chart-gantt" style="color:var(--info);"></i> مخطط جانت الزمني (Gantt Chart)</h3>
        </div>
        <div style="padding:20px;">
            <?php if (count($tasks) > 0): ?>
                <!-- مساحة رسم المخطط -->
                <div id="gantt_chart_div" style="width: 100%; height: auto; min-height: 300px; overflow-x: auto;"></div>
            <?php else: ?>
                <div style="text-align:center; padding:40px; color:var(--text-muted);">
                    <i class="fas fa-tasks" style="font-size:32px; margin-bottom:10px; opacity:0.5;"></i>
                    <p>لا توجد مهام مضافة لرسم المخطط الزمني. قم بإضافة مهام أولاً.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- إضافة وقائمة المهام -->
    <div style="display:grid; grid-template-columns:1fr 2fr; gap:24px;">

        <!-- إضافة مهمة سريعة -->
        <div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden; align-self:start;">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border); background:#f8fafc;">
                <h3 style="margin:0; font-size:15px; font-weight:700; color:var(--text-dark);"><i class="fas fa-plus-circle" style="color:var(--primary);"></i> مهمة جديدة</h3>
            </div>
            <form action="<?php echo URL_ROOT; ?>/project/addTask/<?php echo $project->id; ?>" method="POST" style="padding:20px;">
                <div style="display:flex; flex-direction:column; gap:12px;">
                    <div>
                        <label style="font-size:12px; font-weight:700; margin-bottom:4px; display:block;">عنوان المهمة <span style="color:var(--danger);">*</span></label>
                        <input type="text" name="title" required style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none;">
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:700; margin-bottom:4px; display:block;">المسؤول عن التنفيذ</label>
                        <select name="assigned_to" style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none;">
                            <option value="">-- غير معين --</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <div style="flex:1;">
                            <label style="font-size:12px; font-weight:700; margin-bottom:4px; display:block;">تاريخ البدء <span style="color:var(--danger);">*</span></label>
                            <input type="date" name="start_date" required style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none;">
                        </div>
                        <div style="flex:1;">
                            <label style="font-size:12px; font-weight:700; margin-bottom:4px; display:block;">الاستحقاق <span style="color:var(--danger);">*</span></label>
                            <input type="date" name="due_date" required style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; font-family:'Cairo'; outline:none;">
                        </div>
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:700; margin-bottom:4px; display:block;">نسبة الإنجاز الأولية (%)</label>
                        <input type="number" name="progress" min="0" max="100" value="0" style="width:100%; padding:8px 12px; border:1px solid var(--border); border-radius:6px; font-family:monospace; outline:none;">
                    </div>
                    <button type="submit" style="margin-top:10px; padding:10px; background:var(--primary); color:#fff; border:none; border-radius:6px; font-family:'Cairo'; font-weight:700; cursor:pointer;"><i class="fas fa-save"></i> حفظ المهمة</button>
                </div>
            </form>
        </div>

        <!-- قائمة المهام -->
        <div style="background:var(--card-bg); border-radius:12px; border:1px solid var(--border); box-shadow:var(--shadow-sm); overflow:hidden;">
            <div style="padding:20px 24px; border-bottom:1px solid var(--border); background:#f8fafc;">
                <h3 style="margin:0; font-size:15px; font-weight:700; color:var(--text-dark);"><i class="fas fa-list-check"></i> مهام المشروع (Tasks)</h3>
            </div>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; text-align:right;">
                    <thead style="background:#fff; border-bottom:2px solid var(--border);">
                        <tr>
                            <th style="padding:12px 16px; font-size:11px; color:var(--text-muted);">المهمة</th>
                            <th style="padding:12px 16px; font-size:11px; color:var(--text-muted);">المسؤول</th>
                            <th style="padding:12px 16px; font-size:11px; color:var(--text-muted);">المدة</th>
                            <th style="padding:12px 16px; font-size:11px; color:var(--text-muted); text-align:center;">الإنجاز</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $t): ?>
                            <tr style="border-bottom:1px solid var(--border);">
                                <td style="padding:12px 16px; font-size:13px; font-weight:700; color:var(--text-dark);"><?php echo htmlspecialchars($t->title); ?></td>
                                <td style="padding:12px 16px; font-size:12px; color:var(--text-body);"><i class="fas fa-user" style="color:var(--text-muted);"></i> <?php echo htmlspecialchars($t->assigned_to_name ?? 'غير معين'); ?></td>
                                <td style="padding:12px 16px; font-size:11px; color:var(--text-muted);">
                                    <div><?php echo date('M d', strtotime($t->start_date)); ?> - <?php echo date('M d', strtotime($t->due_date)); ?></div>
                                </td>
                                <td style="padding:12px 16px; text-align:center;">
                                    <div style="display:flex; align-items:center; gap:8px; justify-content:center;">
                                        <div style="width:60px; height:6px; background:var(--border); border-radius:3px; overflow:hidden; position:relative;">
                                            <div style="position:absolute; left:0; top:0; height:100%; width:<?php echo $t->progress; ?>%; background:<?php echo $t->progress == 100 ? 'var(--success)' : 'var(--info)'; ?>;"></div>
                                        </div>
                                        <span style="font-size:11px; font-weight:700; font-family:monospace;"><?php echo $t->progress; ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($tasks)): ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:20px; color:var(--text-muted); font-size:13px;">لا توجد مهام مسجلة.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php if (count($tasks) > 0): ?>
    <script type="text/javascript">
        google.charts.load('current', {
            'packages': ['gantt']
        });
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
                <?php foreach ($tasks as $t): ?>[
                        'Task_<?php echo $t->id; ?>',
                        '<?php echo addslashes($t->title); ?>',
                        '<?php echo addslashes($t->assigned_to_name ?? "غير معين"); ?>',
                        new Date(<?php echo date('Y, m-1, d', strtotime($t->start_date)); ?>),
                        new Date(<?php echo date('Y, m-1, d', strtotime($t->due_date)); ?>),
                        null,
                        <?php echo $t->progress; ?>,
                        null
                    ],
                <?php endforeach; ?>
            ]);

            var options = {
                height: <?php echo (count($tasks) * 40) + 50; ?>, // ديناميكي حسب عدد المهام
                gantt: {
                    trackHeight: 30,
                    barHeight: 20,
                    innerGridTrack: {
                        fill: '#f8fafc'
                    },
                    innerGridDarkTrack: {
                        fill: '#f1f5f9'
                    }
                }
            };

            var chart = new google.visualization.Gantt(document.getElementById('gantt_chart_div'));
            chart.draw(data, options);
        }
    </script>
<?php endif; ?>