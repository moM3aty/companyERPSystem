<?php
// app/controllers/AccountingController.php

class AccountingController extends Controller {
    
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URL_ROOT . '/auth/login');
            exit();
        }
    }

    private function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type'    => $type,
            'message' => $message
        ];
    }

    private function getFlash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    /**
     * الصفحة الرئيسية للمحاسبة
     */
    public function index() {
        $accountingModel = $this->model('Accounting');

        // ========================================
        // 1) حذف مصروف (POST مع ?delete=ID)
        // ========================================
        if (isset($_GET['delete']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int) $_GET['delete'];

            if ($id > 0) {
                $expense = $accountingModel->getExpenseById($id);
                if ($expense) {
                    if ($accountingModel->deleteExpense($id)) {
                        $this->setFlash('success', 'تم حذف المصروف "' . $expense->description . '" بنجاح');
                    } else {
                        $this->setFlash('error', 'حدث خطأ في قاعدة البيانات أثناء الحذف');
                    }
                } else {
                    $this->setFlash('warning', 'المصروف المطلوب غير موجود');
                }
            } else {
                $this->setFlash('error', 'معرّف المصروف غير صالح');
            }

            header('Location: ' . URL_ROOT . '/accounting/index');
            exit();
        }

        // ========================================
        // 2) إضافة مصروف جديد (POST)
        // ========================================
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_GET['delete'])) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $description = trim($_POST['description'] ?? '');
            $amount      = trim($_POST['amount'] ?? '');
            $category    = trim($_POST['category'] ?? 'أخرى');

            $errors = [];

            if (empty($description)) {
                $errors[] = 'بيان المصروف مطلوب';
            } elseif (mb_strlen($description) > 255) {
                $errors[] = 'بيان المصروف طويل جداً (أقصى 255 حرف)';
            }

            if (empty($amount) || !is_numeric($amount)) {
                $errors[] = 'يرجى إدخال مبلغ صحيح';
            } elseif (floatval($amount) <= 0) {
                $errors[] = 'المبلغ يجب أن يكون أكبر من صفر';
            } elseif (floatval($amount) > 99999999.99) {
                $errors[] = 'المبلغ يتجاوز الحد المسموح';
            }

            // القوائم المسموحة
            $allowedCategories = ['تشغيلية', 'رواتب', 'إيجار', 'كهرباء وماء', 'صيانة', 'تسويق', 'نقل وشحن', 'أخرى'];
            if (!in_array($category, $allowedCategories)) {
                $category = 'أخرى';
            }

            if (empty($errors)) {
                $data = [
                    'description' => $description,
                    'amount'      => floatval($amount),
                    'category'    => $category
                ];

                if ($accountingModel->addExpense($data)) {
                    $this->setFlash('success', 'تم تسجيل المصروف "' . $description . '" بقيمة ' . number_format($data['amount'], 2) . ' ر.س');
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء حفظ المصروف في قاعدة البيانات');
                }
            } else {
                $this->setFlash('error', implode(' | ', $errors));
            }

            header('Location: ' . URL_ROOT . '/accounting/index');
            exit();
        }

        // ========================================
        // 3) عرض القائمة مع البحث (GET)
        // ========================================
        $search = trim($_GET['search'] ?? '');

        if (!empty($search)) {
            $expenses = $accountingModel->searchExpenses($search);
        } else {
            $expenses = $accountingModel->getExpenses();
        }

        $totalSales   = $accountingModel->getTotalSales();
        $totalExpenses = $accountingModel->getTotalExpenses();

        $data = [
            'title'         => 'المحاسبة والأرباح',
            'expenses'      => $expenses,
            'total_sales'   => $totalSales,
            'total_expenses' => $totalExpenses,
            'net_profit'    => $totalSales - $totalExpenses,
            'search'        => $search,
            'flash'         => $this->getFlash()
        ];

        $this->view('accounting/index', $data);
    }
}