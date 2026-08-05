<?php
// المسار: app/controllers/JournalController.php

class JournalController extends Controller {
    
    private Journal $journalModel;
    private Account $accountModel;

    public function __construct() {
        // حماية الوصول: فقط من لديهم صلاحيات إدارية أو محاسبية
        $this->requireAnyRole(['admin', 'editor']);
        $this->journalModel = $this->model('Journal');
        $this->accountModel = $this->model('Account'); // سنحتاج دليل الحسابات لجلبه في الفورم
    }

    /**
     * عرض قائمة القيود اليومية
     */
    public function index(): void {
        $entries = $this->journalModel->getAllEntries();
        
        $data = [
            'title' => 'القيود اليومية',
            'entries' => $entries,
            'breadcrumb' => [
                ['label' => 'المالية والمحاسبة', 'url' => '#'],
                ['label' => 'القيود اليومية', 'url' => 'journal/index']
            ]
        ];
        
        ob_start();
        $this->view('journal/index', $data);
        $content = ob_get_clean();
        
        Layout::render($content, $data);
    }

    /**
     * إضافة قيد يومية جديد
     */
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

            // التحقق من صحة القيد المزدوج
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

            if (empty($data['entry_number']) || empty($data['entry_date'])) {
                $this->setFlash('error', 'يرجى تعبئة الحقول الأساسية للقيد.');
                $this->redirect('journal/create');
            }

            if (empty($lines)) {
                $this->setFlash('error', 'يجب إضافة سطرين على الأقل للقيد.');
                $this->redirect('journal/create');
            }

            if (round($totalDebit, 2) !== round($totalCredit, 2)) {
                $this->setFlash('error', 'القيد غير متزن! إجمالي المدين (' . $totalDebit . ') لا يساوي إجمالي الدائن (' . $totalCredit . ').');
                $this->redirect('journal/create');
            }

            if ($this->journalModel->createEntry($data, $lines)) {
                $this->setFlash('success', 'تم حفظ القيد اليومي بنجاح وتحديث أرصدة الحسابات.');
                $this->redirect('journal/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ القيد.');
                $this->redirect('journal/create');
            }
        } else {
            // توليد رقم قيد افتراضي
            $defaultEntryNumber = 'JE-' . date('Ymd') . '-' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            $accounts = $this->accountModel->getChartOfAccounts();
            
            $data = [
                'title' => 'إضافة قيد يومية',
                'accounts' => $accounts,
                'default_entry_number' => $defaultEntryNumber,
                'breadcrumb' => [
                    ['label' => 'القيود اليومية', 'url' => 'journal/index'],
                    ['label' => 'إضافة قيد', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('journal/create', $data);
            $content = ob_get_clean();
            
            Layout::render($content, $data);
        }
    }

    /**
     * عرض تفاصيل القيد
     */
    public function show(int $id): void {
        $entry = $this->journalModel->getEntryById($id);
        
        if (!$entry) {
            $this->setFlash('error', 'القيد غير موجود.');
            $this->redirect('journal/index');
        }

        $lines = $this->journalModel->getEntryLines($id);

        $data = [
            'title' => 'تفاصيل القيد: ' . $entry->entry_number,
            'entry' => $entry,
            'lines' => $lines,
            'breadcrumb' => [
                ['label' => 'القيود اليومية', 'url' => 'journal/index'],
                ['label' => 'تفاصيل القيد', 'url' => '#']
            ]
        ];

        ob_start();
        $this->view('journal/view', $data); // سنقوم بإنشاء هذا العرض لاحقاً
        $content = ob_get_clean();
        
        Layout::render($content, $data);
    }
}