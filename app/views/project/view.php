<?php
// app/views/project/view.php
$project = $project ?? ($data['project'] ?? null);
$tasks = $tasks ?? ($data['tasks'] ?? []);
$employees = $employees ?? ($data['employees'] ?? []);

if (!$project) {
    echo "<div class='alert alert-danger m-4'><i class='fas fa-exclamation-triangle'></i> خطأ: بيانات المشروع غير متوفرة. الرجاء العودة للقائمة الرئيسية.</div>";
    return;
}

$totalProgress = 0;
$taskCount = count($tasks);
if ($taskCount > 0) {
    foreach ($tasks as $t) {
        $totalProgress += (int)($t->progress ?? 0);
    }
    $projectProgress = round($totalProgress / $taskCount);
} else {
    $projectProgress = 0;
}

// حساب الأيام المتبقية
$daysLeft = 0;
$daysLeftClass = 'text-muted';
$daysLeftLabel = '';
if ($project->end_date) {
    $diff = (strtotime($project->end_date) - time()) / 86400;
    $daysLeft = ceil($diff);
    if ($daysLeft > 0) {
        $daysLeftClass = 'text-success';
        $daysLeftLabel = 'متبقي';
    } elseif ($daysLeft === 0) {
        $daysLeftClass = 'text-warning';
        $daysLeftLabel = 'ينتهي اليوم';
    } else {
        $daysLeftClass = 'text-danger';
        $daysLeftLabel = 'متأخر';
        $daysLeft = abs($daysLeft);
    }
}

// حساب المهام المكتملة
$completedTasks = 0;
$inProgressTasks = 0;
$pendingTasks = 0;
foreach ($tasks as $t) {
    $p = (int)($t->progress ?? 0);
    if ($p >= 100) $completedTasks++;
    elseif ($p > 0) $inProgressTasks++;
    else $pendingTasks++;
}
?>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<style>
    /* === متغيرات الصفحة === */
    :root {
        --page-bg: #f0f4f8;
        --card-bg: #ffffff;
        --card-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.03);
        --card-shadow-hover: 0 4px 12px rgba(0,0,0,0.08), 0 8px 24px rgba(0,0,0,0.04);
        --card-radius: 16px;
        --accent: #2563eb;
        --accent-light: #eff6ff;
        --accent-dark: #1d4ed8;
        --success: #10b981;
        --success-light: #ecfdf5;
        --success-dark: #059669;
        --warning: #f59e0b;
        --warning-light: #fffbeb;
        --danger: #ef4444;
        --danger-light: #fef2f2;
        --info: #06b6d4;
        --info-light: #ecfeff;
        --text-primary: #0f172a;
        --text-secondary: #475569;
        --text-muted: #94a3b8;
        --border: #e2e8f0;
        --border-light: #f1f5f9;
    }

    .project-view-wrapper {
        background: var(--page-bg);
        min-height: calc(100vh - 80px);
        padding: 28px 32px 40px;
        direction: rtl;
    }

    /* === الهيدر العلوي === */
    .pv-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 28px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .pv-header-title {
        font-size: 1.65rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0;
        line-height: 1.4;
        letter-spacing: -0.02em;
    }
    .pv-header-title i {
        color: var(--accent);
        margin-left: 10px;
        font-size: 1.4rem;
    }
    .pv-header-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 8px;
    }
    .pv-badge-code {
        background: var(--border-light);
        color: var(--text-secondary);
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.72rem;
        padding: 4px 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
        font-weight: 600;
        letter-spacing: 0.03em;
    }
    .pv-badge-code i { margin-left: 6px; opacity: 0.5; }

    .pv-badge-status {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 14px;
        border-radius: 8px;
        letter-spacing: 0.02em;
    }
    .pv-badge-status.active { background: var(--success-light); color: var(--success-dark); border: 1px solid #a7f3d0; }
    .pv-badge-status.on_hold { background: var(--warning-light); color: #b45309; border: 1px solid #fde68a; }
    .pv-badge-status.completed { background: var(--info-light); color: #0e7490; border: 1px solid #a5f3fc; }
    .pv-badge-status.cancelled { background: var(--danger-light); color: var(--danger); border: 1px solid #fecaca; }
    .pv-badge-status.default { background: var(--border-light); color: var(--text-muted); border: 1px solid var(--border); }

    .pv-header-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .pv-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        white-space: nowrap;
    }
    .pv-btn-primary {
        background: var(--text-primary);
        color: #fff;
        box-shadow: 0 2px 8px rgba(15,23,42,0.2);
    }
    .pv-btn-primary:hover {
        background: #1e293b;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(15,23,42,0.3);
    }
    .pv-btn-primary i { color: var(--warning); }
    .pv-btn-ghost {
        background: var(--card-bg);
        color: var(--text-secondary);
        border: 1px solid var(--border);
        box-shadow: var(--card-shadow);
    }
    .pv-btn-ghost:hover {
        background: var(--border-light);
        border-color: #cbd5e1;
    }

    /* === فلاش المسج === */
    .pv-flash {
        padding: 14px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.88rem;
        font-weight: 600;
        animation: pvSlideDown 0.35s ease;
    }
    .pv-flash.success { background: var(--success-light); color: var(--success-dark); border: 1px solid #a7f3d0; }
    .pv-flash.error { background: var(--danger-light); color: var(--danger); border: 1px solid #fecaca; }
    .pv-flash.warning { background: var(--warning-light); color: #b45309; border: 1px solid #fde68a; }

    @keyframes pvSlideDown {
        from { opacity: 0; transform: translateY(-12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* === بطاقات الإحصائيات === */
    .pv-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 28px;
    }
    @media (max-width: 1200px) { .pv-stats { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .pv-stats { grid-template-columns: 1fr; } }

    .pv-stat-card {
        background: var(--card-bg);
        border-radius: var(--card-radius);
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-light);
        padding: 22px 24px;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .pv-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        border-radius: 0 16px 16px 0;
    }
    .pv-stat-card:hover {
        box-shadow: var(--card-shadow-hover);
        transform: translateY(-2px);
    }
    .pv-stat-card.card-progress::before { background: var(--accent); }
    .pv-stat-card.card-budget::before { background: var(--success); }
    .pv-stat-card.card-timeline::before { background: var(--info); }
    .pv-stat-card.card-tasks::before { background: var(--warning); }

    .pv-stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
    }
    .pv-stat-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.06em;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .pv-stat-label i { font-size: 0.8rem; }
    .pv-stat-label.icon-blue i { color: var(--accent); }
    .pv-stat-label.icon-green i { color: var(--success); }
    .pv-stat-label.icon-cyan i { color: var(--info); }
    .pv-stat-label.icon-amber i { color: var(--warning); }

    .pv-stat-value {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1;
    }
    .pv-stat-value.size-xl { font-size: 1.8rem; }
    .pv-stat-value.size-lg { font-size: 1.45rem; }
    .pv-stat-value.text-blue { color: var(--accent); }
    .pv-stat-value.text-green { color: var(--success-dark); }

    /* شريط التقدم */
    .pv-progress-track {
        height: 8px;
        background: var(--border-light);
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }
    .pv-progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .pv-progress-fill.blue { background: linear-gradient(90deg, var(--accent), #60a5fa); }
    .pv-progress-fill.green { background: linear-gradient(90deg, var(--success), #34d399); }
    .pv-progress-fill::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.25) 50%, transparent 100%);
        animation: pvShimmer 2s infinite;
    }
    @keyframes pvShimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .pv-stat-sub {
        font-size: 0.72rem;
        color: var(--text-muted);
        margin-top: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* بطاقة الميزانية */
    .pv-budget-amount {
        direction: ltr;
        text-align: left;
    }
    .pv-budget-currency {
        font-size: 0.7rem;
        color: var(--text-muted);
        font-weight: 600;
        margin-left: 4px;
        vertical-align: middle;
    }

    /* بطاقة التواريخ */
    .pv-dates-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .pv-date-block { flex: 1; }
    .pv-date-label {
        font-size: 0.68rem;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .pv-date-value {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-primary);
    }
    .pv-date-value.danger { color: var(--danger); }
    .pv-date-arrow {
        color: var(--text-muted);
        opacity: 0.4;
        font-size: 0.9rem;
    }
    .pv-days-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.72rem;
        font-weight: 700;
        margin-top: 10px;
        padding: 3px 10px;
        border-radius: 6px;
    }
    .pv-days-badge.safe { background: var(--success-light); color: var(--success-dark); }
    .pv-days-badge.warn { background: var(--warning-light); color: #b45309; }
    .pv-days-badge.late { background: var(--danger-light); color: var(--danger); }

    /* بطاقة المهام المصغرة */
    .pv-mini-tasks {
        display: flex;
        gap: 6px;
        margin-top: 12px;
    }
    .pv-mini-task-chip {
        font-size: 0.68rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .pv-mini-task-chip.done { background: var(--success-light); color: var(--success-dark); }
    .pv-mini-task-chip.wip { background: var(--accent-light); color: var(--accent-dark); }
    .pv-mini-task-chip.todo { background: var(--border-light); color: var(--text-muted); border: 1px solid var(--border); }

    /* === المخطط الزمني === */
    .pv-card {
        background: var(--card-bg);
        border-radius: var(--card-radius);
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-light);
        margin-bottom: 28px;
        overflow: hidden;
    }
    .pv-card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 24px;
        border-bottom: 1px solid var(--border-light);
        background: var(--card-bg);
    }
    .pv-card-title {
        font-size: 1rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .pv-card-title i {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }
    .pv-card-title .icon-cyan { background: var(--info-light); color: var(--info); }
    .pv-card-title .icon-green { background: var(--success-light); color: var(--success); }
    .pv-card-title .icon-blue { background: var(--accent-light); color: var(--accent); }

    .pv-card-body { padding: 20px 24px; }

    .pv-gantt-container {
        overflow-x: auto;
        padding: 8px 4px;
    }
    .pv-gantt-container::-webkit-scrollbar { height: 6px; }
    .pv-gantt-container::-webkit-scrollbar-track { background: var(--border-light); border-radius: 3px; }
    .pv-gantt-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

    .pv-empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .pv-empty-icon {
        font-size: 3rem;
        color: var(--border);
        margin-bottom: 16px;
        display: block;
    }
    .pv-empty-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 0 0 6px;
    }
    .pv-empty-desc {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin: 0;
    }

    /* === المنطقة السفلية: نموذج + جدول === */
    .pv-bottom {
        display: flex;
        gap: 24px;
        align-items: flex-start;
    }
    @media (max-width: 1024px) { .pv-bottom { flex-direction: column; } }

    .pv-form-col {
        flex: 0 0 380px;
        min-width: 320px;
    }
    @media (max-width: 1024px) { .pv-form-col { flex: 1 1 100%; max-width: 480px; } }

    .pv-table-col {
        flex: 1;
        min-width: 0;
    }

    /* === النموذج === */
    .pv-form .form-group { margin-bottom: 20px; }
    .pv-form .form-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--text-secondary);
        margin-bottom: 8px;
        display: block;
    }
    .pv-form .form-label .req { color: var(--danger); margin-right: 2px; }
    .pv-form .form-control {
        background: var(--border-light) !important;
        border: 2px solid transparent !important;
        border-radius: 12px !important;
        padding: 11px 16px !important;
        font-size: 0.88rem !important;
        color: var(--text-primary) !important;
        transition: all 0.2s ease !important;
        font-family: inherit !important;
    }
    .pv-form .form-control:focus {
        border-color: var(--accent) !important;
        background: #fff !important;
        box-shadow: 0 0 0 4px rgba(37,99,235,0.08) !important;
    }
    .pv-form .form-control::placeholder { color: var(--text-muted) !important; }
    .pv-form .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .pv-btn-submit {
        width: 100%;
        padding: 13px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #fff;
        font-size: 0.9rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 14px rgba(37,99,235,0.3);
    }
    .pv-btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(37,99,235,0.4);
    }
    .pv-btn-submit:active { transform: translateY(0); }

    .pv-form-footer {
        padding: 0 24px 24px;
    }

    /* === الجدول === */
    .pv-table-wrap {
        overflow-x: auto;
    }
    .pv-table {
        width: 100%;
        border-collapse: collapse;
    }
    .pv-table thead th {
        background: var(--border-light);
        padding: 12px 20px;
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.06em;
        text-align: right;
        border-bottom: 2px solid var(--border);
        white-space: nowrap;
    }
    .pv-table thead th.text-center { text-align: center; }
    .pv-table tbody tr {
        transition: background 0.15s ease;
    }
    .pv-table tbody tr:hover { background: rgba(37,99,235,0.02); }
    .pv-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-light);
        vertical-align: middle;
    }

    .pv-task-name {
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 6px;
        line-height: 1.3;
    }
    .pv-task-assignee {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 0.75rem;
        color: var(--text-muted);
    }
    .pv-avatar-sm {
        width: 22px;
        height: 22px;
        border-radius: 7px;
        background: var(--accent-light);
        color: var(--accent-dark);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        font-weight: 800;
    }

    .pv-date-cell {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.78rem;
    }
    .pv-date-start { color: var(--text-secondary); font-weight: 600; }
    .pv-date-end { color: var(--danger); font-weight: 600; margin-top: 3px; }
    .pv-date-end i { margin-left: 4px; opacity: 0.5; font-size: 0.65rem; }

    /* سلايدر الإنجاز */
    .pv-slider-row {
        display: flex;
        align-items: center;
        gap: 14px;
        background: var(--border-light);
        padding: 8px 14px;
        border-radius: 12px;
    }
    .pv-slider {
        -webkit-appearance: none;
        appearance: none;
        flex: 1;
        height: 6px;
        border-radius: 3px;
        outline: none;
        cursor: pointer;
        transition: opacity 0.15s;
    }
    .pv-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid var(--accent);
        box-shadow: 0 2px 6px rgba(37,99,235,0.3);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .pv-slider::-webkit-slider-thumb:hover {
        transform: scale(1.15);
        box-shadow: 0 3px 10px rgba(37,99,235,0.4);
    }
    .pv-slider::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid var(--accent);
        box-shadow: 0 2px 6px rgba(37,99,235,0.3);
        cursor: pointer;
    }
    .pv-slider-percent {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem;
        font-weight: 800;
        min-width: 52px;
        text-align: center;
        padding: 4px 0;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .pv-slider-percent.active { background: var(--accent-light); color: var(--accent-dark); }
    .pv-slider-percent.done { background: var(--success-light); color: var(--success-dark); }

    .pv-table-empty {
        text-align: center;
        padding: 50px 20px !important;
        color: var(--text-muted);
        font-size: 0.88rem;
    }

    .pv-count-badge {
        font-size: 0.72rem;
        font-weight: 700;
        background: var(--border-light);
        color: var(--text-muted);
        padding: 4px 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
    }

    /* === تحسينات عامة === */
    .pv-card-body.no-pad { padding: 0; }
    select.form-control {
        cursor: pointer;
    }

    /* 🟢 إصلاحات مكتبة Google Charts للغة العربية (RTL) 🟢 */
    .google-visualization-tooltip {
        direction: ltr !important; 
        text-align: left !important;
        padding: 12px !important;
        border-radius: 12px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
        border: 1px solid var(--border-light) !important;
        z-index: 9999 !important;
        background: #ffffff !important;
        font-family: 'Cairo', sans-serif !important;
    }
    .google-visualization-tooltip-item-list {
        margin: 0 !important;
        padding: 5px !important;
    }
    .google-visualization-tooltip-item span {
        font-family: 'Cairo', sans-serif !important;
        font-size: 13px !important;
        color: var(--text-primary) !important;
    }
    .google-visualization-tooltip-item:last-child {
        color: var(--text-muted) !important;
        font-size: 11px !important;
    }
</style>

<div class="project-view-wrapper">

    <!-- هيدر الصفحة -->
    <div class="pv-header">
        <div>
            <h1 class="pv-header-title">
                <i class="fas fa-diagram-project"></i>
                <?php echo htmlspecialchars($project->name ?? ''); ?>
            </h1>
            <div class="pv-header-meta">
                <span class="pv-badge-code">
                    <i class="fas fa-barcode"></i>
                    <?php echo htmlspecialchars($project->code ?? 'بدون كود'); ?>
                </span>
                <?php 
                    $statusClass = match($project->status ?? '') {
                        'active' => 'active', 'on_hold' => 'on_hold', 'completed' => 'completed',
                        'cancelled' => 'cancelled', default => 'default'
                    };
                    $statusLabel = match($project->status ?? '') {
                        'active' => 'نشط', 'on_hold' => 'معلق', 'completed' => 'مكتمل',
                        'cancelled' => 'ملغي', default => 'غير محدد'
                    };
                ?>
                <span class="pv-badge-status <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
            </div>
        </div>
        <div class="pv-header-actions">
            <a href="<?php echo URLROOT; ?>/timesheet/project/<?php echo $project->id; ?>" class="pv-btn pv-btn-primary">
                <i class="fas fa-stopwatch"></i> سجل تتبع الوقت
            </a>
            <a href="<?php echo URLROOT; ?>/project/index" class="pv-btn pv-btn-ghost">
                <i class="fas fa-arrow-right"></i> عودة
            </a>
        </div>
    </div>

    <!-- رسالة الفلاش -->
    <?php $flash = Session::getFlash(); if ($flash): ?>
        <div class="pv-flash <?php echo $flash['type']; ?>">
            <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : ($flash['type'] === 'warning' ? 'triangle-exclamation' : 'circle-xmark'); ?>"></i>
            <?php echo htmlspecialchars($flash['message']); ?>
        </div>
    <?php endif; ?>

    <!-- بطاقات الإحصائيات -->
    <div class="pv-stats">

        <!-- نسبة الإنجاز -->
        <div class="pv-stat-card card-progress">
            <div class="pv-stat-header">
                <span class="pv-stat-label icon-blue"><i class="fas fa-bars-progress"></i> نسبة الإنجاز</span>
                <span class="pv-stat-value size-xl <?php echo $projectProgress == 100 ? 'text-green' : 'text-blue'; ?>"><?php echo $projectProgress; ?>%</span>
            </div>
            <div class="pv-progress-track">
                <div class="pv-progress-fill <?php echo $projectProgress == 100 ? 'green' : 'blue'; ?>" style="width: <?php echo $projectProgress; ?>%;"></div>
            </div>
            <div class="pv-stat-sub">
                <span>بناءً على <?php echo $taskCount; ?> مهمة</span>
                <span style="direction:ltr;"><?php echo $projectProgress; ?>% Complete</span>
            </div>
        </div>

        <!-- الميزانية -->
        <div class="pv-stat-card card-budget">
            <div class="pv-stat-header">
                <span class="pv-stat-label icon-green"><i class="fas fa-sack-dollar"></i> الميزانية المخصصة</span>
            </div>
            <div class="pv-stat-value size-lg pv-budget-amount">
                <span class="pv-budget-currency">SAR</span><?php echo number_format((float)($project->budget ?? 0), 2); ?>
            </div>
            <div class="pv-stat-sub">
                <span>الإجمالي المخطط</span>
                <span style="direction:ltr; font-family:'JetBrains Mono',monospace; font-size:0.7rem;">Budget</span>
            </div>
        </div>

        <!-- الإطار الزمني -->
        <div class="pv-stat-card card-timeline">
            <div class="pv-stat-header">
                <span class="pv-stat-label icon-cyan"><i class="far fa-calendar-alt"></i> الإطار الزمني</span>
            </div>
            <div class="pv-dates-row">
                <div class="pv-date-block">
                    <div class="pv-date-label">البداية</div>
                    <div class="pv-date-value"><?php echo $project->start_date ? date('M d, Y', strtotime($project->start_date)) : '—'; ?></div>
                </div>
                <i class="fas fa-arrow-left pv-date-arrow"></i>
                <div class="pv-date-block" style="text-align:left;">
                    <div class="pv-date-label">التسليم</div>
                    <div class="pv-date-value danger"><?php echo $project->end_date ? date('M d, Y', strtotime($project->end_date)) : '—'; ?></div>
                </div>
            </div>
            <?php if ($project->end_date): ?>
                <div class="pv-days-badge <?php echo $daysLeft > 0 ? 'safe' : ($daysLeft === 0 ? 'warn' : 'late'); ?>">
                    <i class="fas fa-<?php echo $daysLeft > 0 ? 'clock' : ($daysLeft === 0 ? 'bell' : 'exclamation-circle'); ?>"></i>
                    <?php echo $daysLeftLabel; ?> <?php echo $daysLeft; ?> يوم
                </div>
            <?php endif; ?>
        </div>

        <!-- ملخص المهام -->
        <div class="pv-stat-card card-tasks">
            <div class="pv-stat-header">
                <span class="pv-stat-label icon-amber"><i class="fas fa-layer-group"></i> ملخص المهام</span>
                <span class="pv-stat-value size-lg"><?php echo $taskCount; ?></span>
            </div>
            <div class="pv-mini-tasks">
                <span class="pv-mini-task-chip done"><i class="fas fa-check"></i> <?php echo $completedTasks; ?></span>
                <span class="pv-mini-task-chip wip"><i class="fas fa-spinner"></i> <?php echo $inProgressTasks; ?></span>
                <span class="pv-mini-task-chip todo"><i class="fas fa-minus"></i> <?php echo $pendingTasks; ?></span>
            </div>
            <div class="pv-stat-sub">
                <span>مكتملة</span>
                <span>قيد التنفيذ</span>
                <span>لم تبدأ</span>
            </div>
        </div>

    </div>

    <!-- المخطط الزمني -->
    <div class="pv-card">
        <div class="pv-card-head">
            <h2 class="pv-card-title"><i class="icon-cyan"><span class="fas fa-chart-gantt"></span></i> المخطط الزمني للمهام</h2>
        </div>
        <div class="pv-card-body" style="padding: 0; position: relative;">
            <?php if(count($tasks) > 0): ?>
                <!-- 🟢 إضافة dir="ltr" هنا هو ما يمنع قص أسماء المهام 🟢 -->
                <div id="gantt_chart_wrapper" style="width: 100%; overflow-x: auto; overflow-y: hidden; padding: 16px 12px;" dir="ltr">
                    <div id="gantt_chart_div" style="min-width: 700px;"></div>
                </div>
            <?php else: ?>
                <div class="pv-empty-state">
                    <i class="fas fa-tasks pv-empty-icon"></i>
                    <p class="pv-empty-title">لا توجد مهام مسجلة</p>
                    <p class="pv-empty-desc">قم بإضافة المهام التشغيلية بالأسفل لرسم المخطط الزمني تلقائياً.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- المنطقة السفلية -->
    <div class="pv-bottom">

        <!-- نموذج إضافة مهمة -->
        <div class="pv-form-col">
            <div class="pv-card" style="margin-bottom:0;">
                <div class="pv-card-head">
                    <h2 class="pv-card-title"><i class="icon-blue"><span class="fas fa-plus-circle"></span></i> مهمة جديدة</h2>
                </div>
                <form action="<?php echo URLROOT; ?>/project/addTask/<?php echo $project->id; ?>" method="POST">
                    <div class="pv-card-body pv-form">
                        <div class="form-group">
                            <label class="form-label">عنوان المهمة <span class="req">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="مثال: تجهيز السيرفرات">
                        </div>
                        <div class="form-group">
                            <label class="form-label">المسؤول عن التنفيذ</label>
                            <select name="assigned_to" class="form-control">
                                <option value="">-- تفويض لاحقاً --</option>
                                <?php foreach($employees as $emp): ?>
                                    <option value="<?php echo $emp->id; ?>"><?php echo htmlspecialchars($emp->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">تاريخ البداية <span class="req">*</span></label>
                                <input type="date" name="start_date" class="form-control" style="font-family:'JetBrains Mono',monospace; font-size:0.8rem !important;" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">تاريخ الانتهاء <span class="req">*</span></label>
                                <input type="date" name="due_date" class="form-control" style="font-family:'JetBrains Mono',monospace; font-size:0.8rem !important;" required>
                            </div>
                        </div>
                    </div>
                    <div class="pv-form-footer">
                        <button type="submit" class="pv-btn-submit">
                            <i class="fas fa-paper-plane"></i> إضافة المهمة للمشروع
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- جدول المهام -->
        <div class="pv-table-col">
            <div class="pv-card" style="margin-bottom:0;">
                <div class="pv-card-head">
                    <h2 class="pv-card-title"><i class="icon-green"><span class="fas fa-list-check"></span></i> متابعة إنجاز المهام</h2>
                    <span class="pv-count-badge"><?php echo count($tasks); ?> مهمة</span>
                </div>
                <div class="pv-card-body no-pad">
                    <div class="pv-table-wrap">
                        <table class="pv-table">
                            <thead>
                                <tr>
                                    <th style="width:35%;">المهمة / المسؤول</th>
                                    <th style="width:22%;">المدة الزمنية</th>
                                    <th style="width:43%;" class="text-center">تحديث نسبة الإنجاز</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($tasks as $t): ?>
                                <tr>
                                    <td>
                                        <div class="pv-task-name"><?php echo htmlspecialchars($t->title); ?></div>
                                        <div class="pv-task-assignee">
                                            <span class="pv-avatar-sm"><?php echo mb_substr($t->assigned_to_name ?? '؟', 0, 1); ?></span>
                                            <?php echo htmlspecialchars($t->assigned_to_name ?? 'غير معين'); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="pv-date-cell pv-date-start"><?php echo date('M d', strtotime($t->start_date)); ?></div>
                                        <div class="pv-date-cell pv-date-end"><i class="fas fa-caret-down"></i><?php echo date('M d', strtotime($t->due_date)); ?></div>
                                    </td>
                                    <td>
                                        <form action="<?php echo URLROOT; ?>/project/updateTaskProgress/<?php echo $t->id; ?>" method="POST" class="pv-slider-row">
                                            <input type="hidden" name="project_id" value="<?php echo $project->id; ?>">
                                            <input type="range" name="progress" min="0" max="100" step="5" value="<?php echo (int)($t->progress ?? 0); ?>"
                                                   class="pv-slider"
                                                   onchange="this.form.submit()"
                                                   oninput="updateSlider(this)">
                                            <span class="pv-slider-percent <?php echo ($t->progress ?? 0) >= 100 ? 'done' : 'active'; ?>">
                                                <?php echo (int)($t->progress ?? 0); ?>%
                                            </span>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>

                                <?php if(empty($tasks)): ?>
                                <tr><td colspan="3" class="pv-table-empty">لا توجد مهام تشغيلية مسجلة بعد.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php if(count($tasks) > 0): ?>
<script type="text/javascript">
    /* === تلوين السلايدر ديناميكياً === */
    function updateSlider(el) {
        var val = el.value;
        var color = val >= 100 ? 'var(--success)' : 'var(--accent)';
        el.style.background = 'linear-gradient(to left, ' + color + ' ' + val + '%, #e2e8f0 ' + val + '%)';
        var badge = el.nextElementSibling;
        badge.textContent = val + '%';
        badge.className = val >= 100 ? 'pv-slider-percent done' : 'pv-slider-percent active';
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('.pv-slider').forEach(function(slider) {
            updateSlider(slider);
        });
    });

    /* === Gantt Chart === */
    var ganttDrawn = false;

    function initGantt() {
        var wrapper = document.getElementById('gantt_chart_wrapper');
        var container = document.getElementById('gantt_chart_div');
        if (!wrapper || !container) return;

        /* حساب العرض الحقيقي للحاوية */
        var wrapperWidth = wrapper.clientWidth;
        var chartWidth = Math.max(wrapperWidth - 24, 700);

        container.style.width = chartWidth + 'px';

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
            <?php foreach($tasks as $t): 
                $start = strtotime($t->start_date ?: date('Y-m-d'));
                $due = strtotime($t->due_date ?: date('Y-m-d', strtotime('+1 day')));
                $sy = date('Y', $start); $sm = (int)date('n', $start) - 1; $sd = date('j', $start);
                $dy = date('Y', $due); $dm = (int)date('n', $due) - 1; $dd = date('j', $due);
            ?>
            [
                'Task_<?php echo $t->id; ?>', 
                '<?php echo addslashes($t->title); ?>', 
                '<?php echo addslashes($t->assigned_to_name ?? "مهمة"); ?>',
                new Date(<?php echo "$sy, $sm, $sd"; ?>), 
                new Date(<?php echo "$dy, $dm, $dd"; ?>), 
                null, 
                <?php echo (int)($t->progress ?? 0); ?>, 
                null
            ],
            <?php endforeach; ?>
        ]);

        var rowHeight = 46;
        var headerHeight = 70;
        var calculatedHeight = (<?php echo count($tasks); ?> * rowHeight) + headerHeight;

        var options = {
            width: chartWidth,
            height: calculatedHeight,
            backgroundColor: 'transparent',
            gantt: {
                trackHeight: 38,
                barHeight: 24,
                barCornerRadius: 6,
                shadowEnabled: false,
                innerGridTrack: { fill: '#ffffff' },
                innerGridDarkTrack: { fill: '#f8fafc' },
                arrow: {
                    color: '#94a3b8',
                    radius: 4,
                    width: 1.5,
                    spaceAfter: 6
                },
                labelStyle: {
                    fontName: 'Cairo, Tahoma, Arial',
                    fontSize: 12,
                    color: '#334155',
                    bold: true
                },
                palette: [
                    { "color": "#2563eb", "dark": "#1d4ed8", "light": "#dbeafe" }
                ]
            }
        };

        var chart = new google.visualization.Gantt(container);
        chart.draw(data, options);
        ganttDrawn = true;
    }

    /* تحميل مكتبة الرسوم مع الانتظار حتى يحمل الخط */
    function loadGantt() {
        google.charts.load('current', { 'packages': ['gantt'] });

        /* ننتظر خط Cairo يتحمل قبل الرسم */
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(function() {
                google.charts.setOnLoadCallback(function() {
                    initGantt();
                });
            });
        } else {
            /* fallback للمتصفحات القديمة */
            google.charts.setOnLoadCallback(function() {
                setTimeout(initGantt, 500);
            });
        }
    }

    loadGantt();

    /* إعادة الرسم عند تغيير حجم النافذة */
    var ganttResizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(ganttResizeTimer);
        ganttResizeTimer = setTimeout(function() {
            if (ganttDrawn) initGantt();
        }, 300);
    });
</script>
<?php endif; ?>