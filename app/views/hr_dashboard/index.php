<?php
// app/views/hr_dashboard/index.php
$wf =$data['workforce'] ?? [];
$rec =$data['recruitment'] ?? [];
$docs =$data['expiringDocs'] ?? [];
$contracts =$data['expiringContracts'] ?? [];
$absences =$data['absences'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-chart-pie text-primary"></i> تحليلات الموارد البشرية (HR Dashboard)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">نظرة شاملة للإدارة العليا على القوى العاملة، التنبيهات، والأداء.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-success" onclick="exportToExcel('hr_dashboard_report')">
            <i class="fas fa-file-excel"></i> تصدير إكسيل
        </button>
        <button class="btn btn-dark" onclick="window.print()">
            <i class="fas fa-file-pdf"></i> طباعة التقرير
        </button>
    </div>
</div>

<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">التقرير الإحصائي للموارد البشرية</h2>
    <h5 class="text-muted font-monospace">تاريخ التقرير: <?php echo date('Y-m-d'); ?></h5>
</div>

<!-- حاوية مخفية لجمع البيانات وتصديرها للإكسيل -->
<div id="hr_dashboard_report" style="display: none;">
    <table>
        <tr><th colspan="2">إحصائيات القوى العاملة</th></tr>
        <tr><td>إجمالي الموظفين</td><td><?php echo $wf->total ?? 0; ?></td></tr>
        <tr><td>الموظفين النشطين</td><td><?php echo $wf->active ?? 0; ?></td></tr>
        <tr><td colspan="2"></td></tr>
        <tr><th colspan="2">إحصائيات التوظيف</th></tr>
        <tr><td>المرشحين</td><td><?php echo $rec->total_candidates ?? 0; ?></td></tr>
        <tr><td>المقابلات المنتظرة</td><td><?php echo $rec->interviews ?? 0; ?></td></tr>
        <tr><td colspan="2"></td></tr>
        <tr><th colspan="4">وثائق تقترب من الانتهاء</th></tr>
        <tr><th>الموظف</th><th>نوع الوثيقة</th><th>الرقم</th><th>الأيام المتبقية</th></tr>
        <?php foreach($docs as$doc): ?>
        <tr>
            <td><?php echo htmlspecialchars($doc->employee_name); ?></td>
            <td><?php echo htmlspecialchars($doc->doc_type); ?></td>
            <td><?php echo htmlspecialchars($doc->doc_number); ?></td>
            <td><?php echo $doc->days_left; ?></td>
        </tr>
        <?php endforeach; ?>
        <tr><td colspan="4"></td></tr>
        <tr><th colspan="2">عقود تقترب من الانتهاء</th></tr>
        <tr><th>الموظف</th><th>تاريخ الانتهاء</th></tr>
        <?php foreach($contracts as$c): ?>
        <tr>
            <td><?php echo htmlspecialchars($c->employee_name); ?></td>
            <td><?php echo $c->end_date; ?></td>
        </tr>
        <?php endforeach; ?>
        <tr><td colspan="2"></td></tr>
        <tr><th colspan="2">الغيابات (آخر 7 أيام)</th></tr>
        <tr><th>الموظف</th><th>التاريخ</th></tr>
        <?php foreach($absences as$abs): ?>
        <tr>
            <td><?php echo htmlspecialchars($abs->employee_name); ?></td>
            <td><?php echo $abs->date; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- 1. إحصائيات علوية سريعة -->
<div class="form-grid mb-4" style="grid-template-columns: repeat(4, 1fr);">
    <div class="card mb-0 border-bottom" style="border-bottom-width: 4px; border-bottom-color: var(--primary);">
        <div class="card-body text-center p-4">
            <div class="text-muted fw-bold mb-2">إجمالي الموظفين</div>
            <div class="font-monospace fs-1 fw-bold text-primary"><?php echo $wf->total ?? 0; ?></div>
            <div class="text-success mt-2" style="font-size: 12px;"><i class="fas fa-circle"></i> <?php echo $wf->active ?? 0; ?> نشط</div>
        </div>
    </div>
    <div class="card mb-0 border-bottom" style="border-bottom-width: 4px; border-bottom-color: var(--warning);">
        <div class="card-body text-center p-4">
            <div class="text-muted fw-bold mb-2">مرشحين للتوظيف</div>
            <div class="font-monospace fs-1 fw-bold text-warning"><?php echo $rec->total_candidates ?? 0; ?></div>
            <div class="text-muted mt-2" style="font-size: 12px;"><i class="fas fa-user-clock"></i> <?php echo $rec->interviews ?? 0; ?> بانتظار مقابلة</div>
        </div>
    </div>
    <div class="card mb-0 border-bottom" style="border-bottom-width: 4px; border-bottom-color: var(--danger);">
        <div class="card-body text-center p-4">
            <div class="text-muted fw-bold mb-2">إقامات/وثائق تنتهي قريباً</div>
            <div class="font-monospace fs-1 fw-bold text-danger"><?php echo count($docs); ?></div>
            <div class="text-muted mt-2" style="font-size: 12px;"><i class="fas fa-triangle-exclamation"></i> تتطلب إجراء فوري</div>
        </div>
    </div>
    <div class="card mb-0 border-bottom" style="border-bottom-width: 4px; border-bottom-color: var(--info);">
        <div class="card-body text-center p-4">
            <div class="text-muted fw-bold mb-2">عقود تنتهي قريباً</div>
            <div class="font-monospace fs-1 fw-bold text-info"><?php echo count($contracts); ?></div>
            <div class="text-muted mt-2" style="font-size: 12px;"><i class="fas fa-file-signature"></i> خلال 60 يوماً</div>
        </div>
    </div>
</div>

<!-- 2. التنبيهات الاستباقية (HR Proactive Alerts) -->
<h4 class="fw-bold text-dark mb-3"><i class="fas fa-bell text-danger"></i> التنبيهات الاستباقية والمؤشرات (Alerts)</h4>
<div class="content-grid" style="grid-template-columns: 1fr 1fr;">
    
    <!-- الوثائق المنتهية -->
    <div class="card mb-0">
        <div class="card-header bg-danger-light border-danger text-danger py-3">
            <h3 class="card-title text-danger fs-6 mb-0"><i class="fas fa-id-card"></i> وثائق وإقامات منتهية أو قاربت على الانتهاء</h3>
        </div>
        <div class="card-body p-0">
            <ul class="list-group list-group-flush">
                <?php foreach($docs as$doc): 
                    $color =$doc->days_left <= 15 ? 'text-danger fw-bold' : 'text-warning';
                ?>
                <li class="list-group-item d-flex justify-content-between align-items-center p-3" style="border-bottom: 1px solid var(--border-color);">
                    <div>
                        <strong class="d-block text-dark"><?php echo htmlspecialchars($doc->employee_name); ?></strong>
                        <span class="text-muted" style="font-size:12px;"><?php echo htmlspecialchars($doc->doc_type); ?> (<?php echo htmlspecialchars($doc->doc_number); ?>)</span>
                    </div>
                    <span class="badge badge-light <?php echo $color; ?> border">
                        <?php echo $doc->days_left <= 0 ? 'منتهية!' : "متبقي {$doc->days_left} يوم"; ?>
                    </span>
                </li>
                <?php endforeach; if(empty($docs)): ?>
                    <li class="list-group-item text-center text-success p-4"><i class="fas fa-check-circle fa-2x mb-2"></i><br>جميع الوثائق سارية ومحدثة.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- العقود المنتهية + الغيابات -->
    <div class="d-flex flex-column gap-4">
        
        <div class="card mb-0 border-warning">
            <div class="card-header bg-warning-light text-warning-dark py-3">
                <h3 class="card-title text-warning-dark fs-6 mb-0"><i class="fas fa-file-contract"></i> عقود تقترب من الانتهاء</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach($contracts as$c): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center p-3" style="border-bottom: 1px solid var(--border-color);">
                        <strong class="text-dark"><?php echo htmlspecialchars($c->employee_name); ?></strong>
                        <span class="badge badge-warning text-dark">ينتهي في <?php echo $c->end_date; ?></span>
                    </li>
                    <?php endforeach; if(empty($contracts)): ?>
                        <li class="list-group-item text-center text-muted p-3">لا توجد عقود تنتهي خلال 60 يوماً.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="card mb-0">
            <div class="card-header bg-light py-3">
                <h3 class="card-title fs-6 mb-0"><i class="fas fa-user-large-slash text-danger"></i> غيابات آخر 7 أيام (للمتابعة)</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach($absences as$abs): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center p-2 px-3" style="border-bottom: 1px solid var(--border-color);">
                        <span class="text-dark" style="font-size:13px;"><?php echo htmlspecialchars($abs->employee_name); ?></span>
                        <span class="text-danger font-monospace" style="font-size:12px;"><?php echo $abs->date; ?></span>
                    </li>
                    <?php endforeach; if(empty($absences)): ?>
                        <li class="list-group-item text-center text-muted p-3" style="font-size:12px;">لم تُسجل حالات غياب مؤخراً.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    </div>
</div>

<!-- 🟢 سكربت التصدير الحقيقي للإكسيل 🟢 -->
<script>
function exportToExcel(divId) {
    let html = document.getElementById(divId).innerHTML;
    let url = 'data:application/vnd.ms-excel;charset=utf-8,' + encodeURIComponent('<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta charset="utf-8"></head><body>' + html + '</body></html>');
    
    let downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);
    downloadLink.href = url;
    downloadLink.download = 'HR_Dashboard_Report_' + new Date().toISOString().slice(0,10) + '.xls';
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>

<style>
@media print {
    body { background: #fff !important; }
    .d-print-none, .sidebar, .topbar { display: none !important; }
    .main-content { margin: 0 !important; }
    .card { box-shadow: none !important; border: 1px solid var(--border-color) !important; page-break-inside: avoid; }
    .list-group-item { border-color: #000 !important; }
    .d-print-block { display: block !important; }
}
</style>