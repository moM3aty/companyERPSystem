<?php
class AuthController extends Controller {
    
    public function index() {
        if(isset($_SESSION['user_id'])) {
            header('Location: ' . URL_ROOT . '/dashboard');
            exit();
        }
        $this->view('auth/login');
    }

    public function login() {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => ''
            ];

            $userModel = $this->model('User');
            $user = $userModel->findUserByEmail($data['email']);

            if($user) {
                if(password_verify($data['password'], $user->password)) {
                    // ========== إصلاح الجلسة: تجديد المعرف ==========
                    session_regenerate_id(true);
                    
                    $_SESSION['user_id'] = $user->id;
                    $_SESSION['user_name'] = $user->name;
                    $_SESSION['user_role'] = $user->role;
                    
                    header('Location: ' . URL_ROOT . '/dashboard');
                    exit();
                } else {
                    $data['password_err'] = 'كلمة المرور غير صحيحة';
                    $this->view('auth/login', $data);
                }
            } else {
                $data['email_err'] = 'البريد الإلكتروني غير مسجل';
                $this->view('auth/login', $data);
            }
        } else {
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => ''
            ];
            $this->view('auth/login', $data);
        }
    }

    public function logout() {
        // ========== إصلاح الجلسة: تدمير الجلسة بشكل كامل ==========
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: ' . URL_ROOT . '/auth/login');
        exit();
    }
}