<?php
// المسار: app/controllers/FixedAssetController.php

class FixedAssetController extends Controller {
    
    /** @var FixedAsset */
    private FixedAsset $assetModel;

    public function __construct() {
        $this->requireAuth();
        $this->assetModel = $this->model('FixedAsset');
    }

    /**
     * عرض قائمة الأصول وحساب الإهلاك
     */
    public function index(): void {
        $assets = $this->assetModel->getAllAssets();
        
        // حساب القيمة الدفترية الحالية لكل أصل (Book Value) باستخدام طريقة القسط الثابت
        $currentDate = new DateTime();
        
        foreach ($assets as &$asset) {
            $purchaseDate = new DateTime($asset->purchase_date);
            // حساب الفرق بالسنوات (مع الكسور)
            $interval = $purchaseDate->diff($currentDate);
            $yearsElapsed = $interval->y + ($interval->m / 12) + ($interval->d / 365.25);
            
            if ($yearsElapsed < 0) $yearsElapsed = 0; // إذا كان الشراء في المستقبل
            if ($yearsElapsed > $asset->useful_life_years) $yearsElapsed = $asset->useful_life_years; // لا يمكن إهلاك أكثر من العمر الإنتاجي
            
            // قسط الإهلاك السنوي = (التكلفة - الخردة) / العمر الإنتاجي
            $annualDepreciation = ($asset->purchase_cost - $asset->salvage_value) / $asset->useful_life_years;
            
            // مجمع الإهلاك
            $accumulatedDepreciation = $annualDepreciation * $yearsElapsed;
            
            // القيمة الدفترية
            $asset->book_value = $asset->purchase_cost - $accumulatedDepreciation;
            $asset->accumulated_depreciation = $accumulatedDepreciation;
        }

        $data = [
            'title' => 'الأصول الثابتة',
            'assets' => $assets,
            'flash' => $this->getFlash()
        ];
        
        ob_start();
        $this->view('assets/index', $data);
        $content = ob_get_clean();
        
        Layout::render($content, $data);
    }

    /**
     * إضافة أصل ثابت جديد
     */
    public function create(): void {
        $this->requireAnyRole(['admin', 'editor', 'manager']);

        if ($this->isPost()) {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'asset_tag'         => trim($_POST['asset_tag'] ?? ''),
                'name'              => trim($_POST['name'] ?? ''),
                'category'          => trim($_POST['category'] ?? 'other'),
                'purchase_date'     => trim($_POST['purchase_date'] ?? date('Y-m-d')),
                'purchase_cost'     => (float)($_POST['purchase_cost'] ?? 0.0),
                'salvage_value'     => (float)($_POST['salvage_value'] ?? 0.0),
                'useful_life_years' => (int)($_POST['useful_life_years'] ?? 1),
                'location'          => trim($_POST['location'] ?? ''),
                'status'            => trim($_POST['status'] ?? 'active'),
                'notes'             => trim($_POST['notes'] ?? ''),
                'created_by'        => Session::getUserId()
            ];

            if (empty($data['name']) || $data['purchase_cost'] <= 0 || $data['useful_life_years'] < 1) {
                $this->setFlash('error', 'يرجى إدخال اسم الأصل، التكلفة، والعمر الإنتاجي بشكل صحيح.');
                $this->redirect('asset/create');
            }

            if ($this->assetModel->createAsset($data)) {
                $this->setFlash('success', 'تم تسجيل الأصل الثابت بنجاح وبدء حساب إهلاكه.');
                $this->redirect('asset/index');
            } else {
                $this->setFlash('error', 'حدث خطأ أثناء حفظ بيانات الأصل. تأكد أن الرمز التسلسلي غير مكرر.');
                $this->redirect('asset/create');
            }
        } else {
            $data = [
                'title' => 'تسجيل أصل ثابت',
                'flash' => $this->getFlash()
            ];
            
            ob_start();
            $this->view('assets/create', $data);
            $content = ob_get_clean();
            
            Layout::render($content, $data);
        }
    }
}