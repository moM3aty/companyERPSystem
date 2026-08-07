<?php
// app/views/activity_logs/index.php
$logs = $data['logs'] ?? [];
$limit = $data['limit'] ?? 500;
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-user-secret text-primary"></i> سجل التدقيق ونشاط النظام (Audit Trail)</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">مراقبة وتتبع دقيق للعمليات الحساسة التي يقوم بها الموظفون داخل النظام لضمان الأمان والنزاهة.</p>
    </div>
    
    <div class="d-flex gap-2">
        <form action="<?php echo URLROOT; ?>/activityLog/index" method="GET" class="d-flex align-items-center gap-2 bg-white border rounded p-1">
            <span class="text-muted ms-2" style="font-size: 12px; font-weight:600;">عرض آخر:</span>
            <select name="limit" class="form-control border-0" onchange="this.form.submit()" style="padding: 4px; width: 100px; font-family: monospace;">
                <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100 حركة</option>
                <option value="500" <?php echo $limit == 500 ? 'selected' : ''; ?>>500 حركة</option>
                <option value="1000" <?php echo $limit == 1000 ? 'selected' : ''; ?>>1000 حركة</option>
            </select>
        </form>
        <button onclick="window.print()" class="btn btn-secondary">
            <i class="fas fa-print"></i> طباعة السجل
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="bg-light">
                    <tr>
                        <th style="width: 15%;">التاريخ والوقت</th>
                        <th style="width: 20%;">المستخدم / IP</th>
                        <th style="width: 15%;">نوع العملية</th>
                        <th style="width: 15%;">الوحدة المعنية</th>
                        <th style="width: 35%;">التفاصيل الدقيقة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($logs)): foreach($logs as $log): 
                        $actionColor = match($log->action) {
                            'CREATE' => 'badge-success',
                            'UPDATE' => 'badge-info',
                            'DELETE' => 'badge-danger',
                            'LOGIN'  => 'badge-primary',
                            default  => 'badge-secondary'
                        };
                        $actionIcon = match($log->action) {
                            'CREATE' => 'fa-plus',
                            'UPDATE' => 'fa-pen',
                            'DELETE' => 'fa-trash',
                            'LOGIN'  => 'fa-right-to-bracket',
                            default  => 'fa-bolt'
                        };
                    ?>
                    <tr>
                        <td class="font-monospace text-muted" style="font-size:12px;">
                            <i class="far fa-clock text-primary"></i> <?php echo date('Y-m-d H:i:s', strtotime($log->created_at)); ?>
                        </td>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($log->user_name ?? 'النظام الآلي'); ?></div>
                            <div class="font-monospace text-muted" style="font-size:10px;"><i class="fas fa-network-wired"></i> IP: <?php echo htmlspecialchars($log->ip_address ?? 'Local/Unknown'); ?></div>
                        </td>
                        <td>
                            <span class="badge <?php echo $actionColor; ?>">
                                <i class="fas <?php echo $actionIcon; ?>"></i> <?php echo htmlspecialchars($log->action); ?>
                            </span>
                        </td>
                        <td class="fw-bold text-body" style="font-size:13px;">
                            <i class="fas fa-cube text-muted"></i> <?php echo htmlspecialchars($log->module); ?>
                            <?php if($log->record_id): ?>
                                <span class="badge badge-secondary font-monospace" style="font-size:10px;">ID: <?php echo $log->record_id; ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted" style="font-size:13px;">
                            <?php echo htmlspecialchars($log->description); ?>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding:60px;">
                            <i class="fas fa-shield-halved fs-1 mb-3 opacity-50 d-block"></i>
                            لا توجد حركات مسجلة حالياً في النظام.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    @media print {
        .d-print-none, .sidebar, .topbar { display: none !important; }
        .main-content { margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        body { background: #fff !important; }
    }
</style>