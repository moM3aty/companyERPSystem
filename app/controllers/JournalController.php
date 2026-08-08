<?php
// app/controllers/JournalController.php

class JournalController extends Controller {
    
    private Journal $journalModel;
    private Account $accountModel;

    public function __construct() {
        $this->requireAnyRole(['admin', 'editor']);
        $this->journalModel = $this->model('Journal');
        $this->accountModel = $this->model('Account'); 
    }

    public function index(): void {
        $entries = $this->journalModel->getAllEntries();
        
        $data = [
            'title' => 'القيود اليومية',
            'entries' => $entries,
            'breadcrumb' => [
                ['label' => 'المحاسبة', 'url' => '#'],
                ['label' => 'القيود اليومية', 'url' => 'journal/index']
            ]
        ];
        
        ob_start();
        $this->view('journal/index', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function create(): void {
        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'entry_number' => trim($_POST['entry_number'] ?? ''),
                'entry_date' => trim($_POST['entry_date'] ?? date('Y-m-d')),
                'description' => trim($_POST['description'] ?? ''),
                'reference_type' => trim($_POST['reference_type'] ?? ''),
                'reference_id' => !empty($_POST['reference_id']) ? (int)$_POST['reference_id'] : null,
            ];

            $accountIds = $_POST['account_id'] ?? [];
            $debits = $_POST['debit'] ?? [];
            $credits = $_POST['credit'] ?? [];
            $lineDescriptions = $_POST['line_description'] ?? [];

            $totalDebit = 0;
            $totalCredit = 0;
            $lines = [];

            for ($i = 0; $i < count($accountIds); $i++) {
                if (!empty($accountIds[$i])) {
                    $debit = (float)($debits[$i] ?? 0);
                    $credit = (float)($credits[$i] ?? 0);
                    
                    if ($debit > 0 || $credit > 0) {
                        $totalDebit += $debit;
                        $totalCredit += $credit;
                        $lines[] = [
                            'account_id' => (int)$accountIds[$i],
                            'debit' => $debit,
                            'credit' => $credit,
                            'description' => trim($lineDescriptions[$i] ?? '')
                        ];
                    }
                }
            }

            if (empty($data['entry_number']) || empty($data['description'])) {
                $this->setFlash('error', 'يرجى تعبئة الحقول الأساسية للقيد.');
                $this->redirect('journal/create');
            } elseif (empty($lines) || count($lines) < 2) {
                $this->setFlash('error', 'يجب إضافة سطرين على الأقل للقيد.');
                $this->redirect('journal/create');
            } elseif (round($totalDebit, 2) !== round($totalCredit, 2)) {
                $this->setFlash('error', 'القيد غير متزن! المجموع المدين لا يساوي الدائن.');
                $this->redirect('journal/create');
            } else {
                if ($this->journalModel->createEntry($data, $lines)) {
                    $this->setFlash('success', 'تم حفظ القيد وتحديث أرصدة الحسابات بنجاح.');
                    $this->redirect('journal/index');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ القيد.');
                    $this->redirect('journal/create');
                }
            }
        } else {
            $defaultEntryNumber = 'JE-' . date('Ymd') . '-' . str_pad((string)random_int(1, 999), 3, '0', STR_PAD_LEFT);
            $accounts = $this->accountModel->getChartOfAccounts();
            
            $data = [
                'title' => 'إضافة قيد يومية',
                'accounts' => $accounts,
                'default_entry_number' => $defaultEntryNumber,
                'breadcrumb' => [
                    ['label' => 'القيود', 'url' => 'journal/index'],
                    ['label' => 'إضافة قيد', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('journal/create', $data);
            $content = ob_get_clean();
            Layout::render($content, $data);
        }
    }

    public function edit(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('journal/index');
        
        $entryId = (int)$id;
        $entry = $this->journalModel->getEntryById($entryId);
        
        if (!$entry) {
            $this->setFlash('error', 'القيد غير موجود.');
            $this->redirect('journal/index');
        }

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'entry_date' => trim($_POST['entry_date'] ?? date('Y-m-d')),
                'description' => trim($_POST['description'] ?? ''),
                'reference_type' => trim($_POST['reference_type'] ?? ''),
                'reference_id' => !empty($_POST['reference_id']) ? (int)$_POST['reference_id'] : null,
            ];

            $accountIds = $_POST['account_id'] ?? [];
            $debits = $_POST['debit'] ?? [];
            $credits = $_POST['credit'] ?? [];
            $lineDescriptions = $_POST['line_description'] ?? [];

            $totalDebit = 0;
            $totalCredit = 0;
            $lines = [];

            for ($i = 0; $i < count($accountIds); $i++) {
                if (!empty($accountIds[$i])) {
                    $debit = (float)($debits[$i] ?? 0);
                    $credit = (float)($credits[$i] ?? 0);
                    
                    if ($debit > 0 || $credit > 0) {
                        $totalDebit += $debit;
                        $totalCredit += $credit;
                        $lines[] = [
                            'account_id' => (int)$accountIds[$i],
                            'debit' => $debit,
                            'credit' => $credit,
                            'description' => trim($lineDescriptions[$i] ?? '')
                        ];
                    }
                }
            }

            if (empty($data['description'])) {
                $this->setFlash('error', 'يرجى تعبئة الحقول الأساسية للقيد.');
            } elseif (empty($lines) || count($lines) < 2) {
                $this->setFlash('error', 'يجب إضافة سطرين على الأقل للقيد.');
            } elseif (round($totalDebit, 2) !== round($totalCredit, 2)) {
                $this->setFlash('error', 'القيد غير متزن! المجموع المدين لا يساوي الدائن.');
            } else {
                if ($this->journalModel->updateEntry($entryId, $data, $lines)) {
                    $this->setFlash('success', 'تم تعديل القيد وتحديث الأرصدة بنجاح.');
                    $this->redirect('journal/index');
                    return;
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء تعديل القيد في قاعدة البيانات.');
                }
            }
        }

        $accounts = $this->accountModel->getChartOfAccounts();
        $lines = $this->journalModel->getEntryLines($entryId);
        
        $data = [
            'title' => 'تعديل قيد يومية',
            'entry' => $entry,
            'lines' => $lines,
            'accounts' => $accounts,
            'breadcrumb' => [
                ['label' => 'القيود', 'url' => 'journal/index'],
                ['label' => 'تعديل قيد', 'url' => '#']
            ]
        ];
        
        ob_start();
        $this->view('journal/edit', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }

    public function show(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) $this->redirect('journal/index');
        
        $entryId = (int)$id;
        $entry = $this->journalModel->getEntryById($entryId);
        
        if (!$entry) {
            $this->setFlash('error', 'القيد غير موجود.');
            $this->redirect('journal/index');
        }

        $lines = $this->journalModel->getEntryLines($entryId);

        $data = [
            'title' => 'تفاصيل القيد',
            'entry' => $entry,
            'lines' => $lines,
            'breadcrumb' => [
                ['label' => 'القيود', 'url' => 'journal/index'],
                ['label' => 'التفاصيل', 'url' => '#']
            ]
        ];

        ob_start();
        $this->view('journal/view', $data);
        $content = ob_get_clean();
        Layout::render($content, $data);
    }
}