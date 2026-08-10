<?php
// app/controllers/RecruitmentController.php

class RecruitmentController extends Controller {
    
    private $recruitmentModel;
    private $employeeModel;

    public function __construct() {
        $this->requireAuth();
        $this->recruitmentModel = $this->model('Recruitment');
        $this->employeeModel = $this->model('Employee');
    }

    public function index() {
        $candidates = $this->recruitmentModel->getAllCandidates();
        
        $data = [
            'title' => 'Recruitment Module',
            'candidates' => $candidates,
            'breadcrumb' => [
                ['label' => 'HR', 'url' => '#'],
                ['label' => 'Recruitment', 'url' => 'recruitment/index']
            ]
        ];
        
        ob_start();
        $this->view('recruitment/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create() {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'name'             => trim($_POST['name'] ?? ''),
                'email'            => trim($_POST['email'] ?? ''),
                'phone'            => trim($_POST['phone'] ?? ''),
                'position_applied' => trim($_POST['position_applied'] ?? ''),
                'nationality'      => trim($_POST['nationality'] ?? ''),
                'expected_salary'  => (float)($_POST['expected_salary'] ?? 0),
                'source'           => trim($_POST['source'] ?? 'Direct'),
                'status'           => trim($_POST['status'] ?? 'Applied'),
                'interview_date'   => trim($_POST['interview_date'] ?? ''),
                'notes'            => trim($_POST['notes'] ?? '')
            ];

            if (empty($data['name']) || empty($data['position_applied'])) {
                $this->setFlash('error', 'Name and Position Applied are required.');
            } else {
                if ($this->recruitmentModel->createCandidate($data)) {
                    $this->setFlash('success', 'Candidate added successfully.');
                    $this->redirect('recruitment/index');
                    return;
                } else {
                    $this->setFlash('error', 'Error saving candidate.');
                }
            }
        }

        $data = [
            'title' => 'Add New Candidate',
            'breadcrumb' => [
                ['label' => 'Recruitment', 'url' => 'recruitment/index'],
                ['label' => 'Add', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('recruitment/create', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function updateStatus() {
        if ($this->isPost()) {
            $candidateId = (int)($_POST['candidate_id'] ?? 0);
            $status = trim($_POST['status'] ?? '');
            $score = (int)($_POST['interview_score'] ?? 0);
            
            if ($candidateId > 0 && !empty($status)) {
                $this->recruitmentModel->updateStatus($candidateId, $status, $score);
                $this->setFlash('success', 'Candidate status updated.');
            }
        }
        $this->redirect('recruitment/index');
    }

    public function hire($id = '') {
        $this->requireAnyRole(['admin', 'manager', 'super_admin']);
        if (empty($id) || !is_numeric($id)) $this->redirect('recruitment/index');
        
        $candidate = $this->recruitmentModel->getCandidateById((int)$id);
        if (!$candidate) {
            $this->setFlash('error', 'Candidate not found.');
            $this->redirect('recruitment/index');
        }

        // Automatic conversion from Candidate to Employee Profile
        $empData = [
            'employee_number'      => 'EMP-' . date('ym') . rand(100, 999),
            'full_name'            => $candidate->name,
            'name_ar'              => null,
            'name_en'              => $candidate->name,
            'gender'               => 'Male', // Default
            'dob'                  => null,
            'nationality'          => $candidate->nationality,
            'marital_status'       => 'Single',
            'blood_group'          => null,
            'personal_email'       => $candidate->email,
            'personal_mobile'      => $candidate->phone,
            'emergency_contact'    => null,
            'emergency_relation'   => null,
            'emergency_phone'      => null,
            'job_title'            => $candidate->position_applied,
            'position'             => $candidate->position_applied,
            'employee_category'    => 'New Hire',
            'employment_type'      => 'Full-time',
            'date_of_joining'      => date('Y-m-d'),
            'probation_start_date' => date('Y-m-d'),
            'probation_end_date'   => date('Y-m-d', strtotime('+90 days')),
            'employment_status'    => 'Active',
            'work_location'        => 'HQ',
            'cost_center'          => null,
            'project_assignment'   => null,
            'employee_grade'       => null,
            'bank_name'            => null,
            'account_holder'       => null,
            'iban'                 => null,
            'salary_payment_method'=> 'Bank Transfer',
            'basic_salary'         => $candidate->expected_salary,
            'housing_allowance'    => 0,
            'transport_allowance'  => 0,
            'other_allowances'     => 0
        ];

        $newEmpId = $this->employeeModel->createEmployee($empData);
        
        if ($newEmpId) {
            $this->recruitmentModel->updateStatus((int)$id, 'Hired', $candidate->interview_score);
            $this->setFlash('success', 'Candidate hired! Employee profile created automatically.');
            $this->redirect('employee/show/' . $newEmpId);
        } else {
            $this->setFlash('error', 'Failed to create employee profile.');
            $this->redirect('recruitment/index');
        }
    }

    public function delete($id = '') {
        $this->requireRole('admin');
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $this->recruitmentModel->deleteCandidate((int)$id);
            $this->setFlash('success', 'Candidate deleted.');
        }
        $this->redirect('recruitment/index');
    }

    public function importExcel() {
        if ($this->isPost()) {
            $this->setFlash('success', 'Excel file received successfully. (Requires PhpSpreadsheet library setup for processing).');
        }
        $this->redirect('recruitment/index');
    }
}