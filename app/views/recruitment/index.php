<?php
// app/views/recruitment/index.php
$candidates = $data['candidates'] ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-4 d-print-none">
    <div>
        <h3 class="mb-0 text-dark"><i class="fas fa-user-tie text-primary"></i> Recruitment Module</h3>
        <p class="text-muted mt-1" style="font-size: 13px;">Manage candidates from Application to Hired status.</p>
    </div>
    
    <div class="d-flex gap-2">
        <form action="<?php echo URLROOT; ?>/recruitment/importExcel" method="POST" enctype="multipart/form-data" id="excelForm" style="display:none;">
            <input type="file" id="excelUpload" name="excel_file" accept=".xlsx, .xls, .csv" onchange="document.getElementById('excelForm').submit()">
        </form>
        
        <button class="btn btn-success" onclick="document.getElementById('excelUpload').click()">
            <i class="fas fa-file-excel"></i> Import Excel
        </button>
        
        <button class="btn btn-dark" onclick="window.print()">
            <i class="fas fa-file-pdf"></i> Export PDF
        </button>
        
        <a href="<?php echo URLROOT; ?>/recruitment/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Candidate
        </a>
    </div>
</div>

<?php 
    $flash = Session::getFlash();
    if ($flash): 
?>
    <div class="flash-msg flash-<?php echo $flash['type']; ?>">
        <i class="fas fa-<?php echo $flash['type'] === 'success' ? 'circle-check' : 'circle-xmark'; ?>"></i>
        <?php echo htmlspecialchars($flash['message']); ?>
    </div>
<?php endif; ?>

<div class="d-none d-print-block mb-4 text-center pb-3 border-bottom">
    <h2 class="fw-black text-dark mb-1">Recruitment & Candidates Report</h2>
    <h5 class="text-muted font-monospace">Date: <?php echo date('Y-m-d'); ?></h5>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>Candidate Name</th>
                        <th>Position Applied</th>
                        <th class="text-center">Interview Date</th>
                        <th class="text-center">Score</th>
                        <th class="text-center">Status</th>
                        <th class="text-center d-print-none">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidates as $c) : 
                        $statusClasses = [
                            'Applied' => 'badge-secondary', 'Screening' => 'badge-info', 'Interview' => 'badge-warning',
                            'Offered' => 'badge-primary', 'Hired' => 'badge-success', 'Rejected' => 'badge-danger'
                        ];
                        $statusClass = $statusClasses[$c->status] ?? 'badge-secondary';
                    ?>
                    <tr>
                        <td>
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($c->name); ?></div>
                            <div class="text-muted font-monospace mt-1" style="font-size: 11px;"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($c->phone ?? '—'); ?></div>
                        </td>
                        <td class="fw-bold text-primary"><?php echo htmlspecialchars($c->position_applied); ?></td>
                        <td class="text-center text-muted font-monospace fs-6">
                            <?php echo !empty($c->interview_date) ? date('Y-m-d H:i', strtotime($c->interview_date)) : '—'; ?>
                        </td>
                        <td class="text-center">
                            <?php if($c->status === 'Interview' || $c->status === 'Hired'): ?>
                                <span class="badge badge-dark fs-6"><i class="fas fa-star text-warning"></i> <?php echo $c->interview_score; ?>/100</span>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <span class="badge <?php echo $statusClass; ?>"><?php echo $c->status; ?></span>
                        </td>
                        <td class="text-center d-print-none">
                            <form action="<?php echo URLROOT; ?>/recruitment/updateStatus" method="POST" class="d-flex align-items-center justify-content-center gap-1">
                                <input type="hidden" name="candidate_id" value="<?php echo $c->id; ?>">
                                <select name="status" class="form-control form-control-sm" style="width: auto; font-size: 11px; padding: 2px 5px;" onchange="this.form.submit()">
                                    <option value="" disabled selected>Update Status</option>
                                    <option value="Interview">Interview</option>
                                    <option value="Offered">Offer</option>
                                    <option value="Rejected">Reject</option>
                                </select>
                                
                                <?php if($c->status !== 'Hired'): ?>
                                    <a href="<?php echo URLROOT; ?>/recruitment/hire/<?php echo $c->id; ?>" class="btn btn-success btn-sm text-white ms-1" style="padding: 2px 8px; font-size: 11px;" title="Hire and create Employee Profile" onclick="return confirm('Hire this candidate and create an employee profile automatically?');"><i class="fas fa-user-check"></i> Hire</a>
                                <?php endif; ?>

                                <?php if(Session::hasRole('admin')): ?>
                                    <button type="submit" formaction="<?php echo URLROOT; ?>/recruitment/delete/<?php echo $c->id; ?>" class="btn-icon delete ms-1" title="Delete" onclick="return confirm('Confirm delete?');"><i class="fas fa-trash"></i></button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($candidates)) : ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted p-5">
                            <i class="fas fa-user-clock fs-1 mb-3 opacity-50 d-block"></i>
                            No candidates found.
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
    body { background: #fff !important; }
    .d-print-none, .sidebar, .topbar { display: none !important; }
    .main-content { margin: 0 !important; }
    .card { box-shadow: none !important; border: 1px solid var(--border-color) !important; page-break-inside: avoid; }
    .d-print-block { display: block !important; }
}
</style>