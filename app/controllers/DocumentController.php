<?php
// المسار: app/controllers/DocumentController.php

class DocumentController extends Controller {
    
    /** @var Document */
    private Document $docModel;

    public function __construct() {
        $this->requireAuth();
        $this->docModel = $this->model('Document');
    }

    /**
     * عرض قائمة الوثائق
     */
    public function index(): void {
        $userId = Session::getUserId();
        $userRole = Session::getUserRole();
        
        $documents = $this->docModel->getAllDocuments($userId, $userRole);
        
        $data = [
            'title'     => 'نظام إدارة الوثائق (DMS)',
            'documents' => $documents,
            'flash'     => $this->getFlash(),
            'breadcrumb'=> [
                ['label' => 'الأصول والعقود', 'url' => '#'],
                ['label' => 'إدارة الوثائق', 'url' => 'document/index']
            ]
        ];
        
        ob_start();
        $this->view('documents/index', $data);
        $content = ob_get_clean();
        
        Layout::render($content, $data);
    }

    /**
     * رفع وحفظ وثيقة جديدة
     */
    public function create(): void {
        if ($this->isPost()) {
            $title = trim($_POST['title'] ?? '');
            $accessLevel = trim($_POST['access_level'] ?? 'private');
            
            // إعداد مجلد الرفع
            $uploadDir = dirname(APP_ROOT) . '/public/uploads/documents/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (empty($title)) {
                $this->setFlash('error', 'يرجى إدخال عنوان للوثيقة.');
                $this->redirect('document/create');
            }

            if (isset($_FILES['document_file']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['document_file']['tmp_name'];
                $fileName = $_FILES['document_file']['name'];
                $fileSize = $_FILES['document_file']['size'];
                
                // استخراج امتداد الملف
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                // الامتدادات المسموحة
                $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'png', 'jpg', 'jpeg', 'zip', 'rar'];
                
                if (!in_array($fileExtension, $allowedExtensions)) {
                    $this->setFlash('error', 'صيغة الملف غير مسموحة. الرجاء رفع ملفات PDF, Office, صور، أو ملفات مضغوطة فقط.');
                    $this->redirect('document/create');
                }

                // تحديد الحد الأقصى للملف (مثلاً 10 ميجابايت)
                if ($fileSize > 10 * 1024 * 1024) {
                    $this->setFlash('error', 'حجم الملف يتجاوز الحد المسموح (10MB).');
                    $this->redirect('document/create');
                }

                // توليد اسم فريد للملف لمنع التضارب
                $newFileName = uniqid('DOC_') . '_' . time() . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;

                // نقل الملف
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $data = [
                        'title'       => $title,
                        'file_name'   => $newFileName,
                        'file_type'   => $fileExtension,
                        'file_size'   => $fileSize,
                        'folder_path' => '/uploads/documents/',
                        'uploaded_by' => Session::getUserId(),
                        'access_level'=> $accessLevel
                    ];

                    if ($this->docModel->saveDocumentInfo($data)) {
                        $this->setFlash('success', 'تم رفع الوثيقة وأرشفتها بنجاح.');
                        $this->redirect('document/index');
                    } else {
                        // في حال فشل الحفظ بالداتابيز، نحذف الملف المرفوع
                        unlink($destPath);
                        $this->setFlash('error', 'حدث خطأ أثناء حفظ بيانات الوثيقة.');
                    }
                } else {
                    $this->setFlash('error', 'حدث خطأ أثناء نقل الملف إلى المجلد الوجهة.');
                }
            } else {
                $this->setFlash('error', 'يرجى اختيار ملف لرفعه.');
            }
            $this->redirect('document/create');
        } else {
            $data = [
                'title' => 'رفع وثيقة جديدة',
                'flash' => $this->getFlash(),
                'breadcrumb'=> [
                    ['label' => 'إدارة الوثائق', 'url' => 'document/index'],
                    ['label' => 'أرشفة وثيقة', 'url' => '#']
                ]
            ];
            
            ob_start();
            $this->view('documents/create', $data);
            $content = ob_get_clean();
            
            Layout::render($content, $data);
        }
    }

    /**
     * تنزيل الملف
     */
    public function download(string $id = ''): void {
        if (empty($id) || !is_numeric($id)) {
            $this->redirect('document/index');
        }

        $doc = $this->docModel->getDocumentById((int)$id);

        if (!$doc) {
            $this->setFlash('error', 'الوثيقة غير موجودة.');
            $this->redirect('document/index');
        }

        // تحقق من الصلاحيات إذا كان الملف خاصاً (Private)
        if ($doc->access_level === 'private' && $doc->uploaded_by !== Session::getUserId() && Session::getUserRole() !== 'admin') {
            $this->setFlash('error', 'ليس لديك صلاحية لتنزيل هذه الوثيقة.');
            $this->redirect('document/index');
        }

        $filePath = dirname(APP_ROOT) . '/public' . $doc->folder_path . $doc->file_name;

        if (file_exists($filePath)) {
            // إجبار المتصفح على التنزيل
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($doc->file_name) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            flush();
            readfile($filePath);
            exit;
        } else {
            $this->setFlash('error', 'الملف الفعلي غير موجود على الخادم.');
            $this->redirect('document/index');
        }
    }

    /**
     * حذف الوثيقة (من قاعدة البيانات ومن القرص)
     */
    public function delete(string $id = ''): void {
        if ($this->isPost() && !empty($id) && is_numeric($id)) {
            $docId = (int)$id;
            $doc = $this->docModel->getDocumentById($docId);

            if ($doc) {
                // تحقق من الصلاحيات للحذف
                if (Session::getUserRole() === 'admin' || $doc->uploaded_by === Session::getUserId()) {
                    
                    $filePath = dirname(APP_ROOT) . '/public' . $doc->folder_path . $doc->file_name;
                    
                    // حذف من الداتابيز
                    if ($this->docModel->delete($docId)) {
                        // حذف الملف الفعلي من السيرفر
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                        $this->setFlash('success', 'تم حذف الوثيقة بنجاح.');
                    } else {
                        $this->setFlash('error', 'فشل في حذف الوثيقة من قاعدة البيانات.');
                    }
                } else {
                    $this->setFlash('error', 'ليس لديك صلاحية لحذف هذا الملف.');
                }
            }
        }
        $this->redirect('document/index');
    }
}