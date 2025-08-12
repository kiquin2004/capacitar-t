<?php
require_once 'includes/viewmodel_base.php';
require_once 'models/User.php';

class UserViewModel extends ViewModelBase {
    private $userModel;
    
    protected function init() {
        $this->userModel = new User();
        
        // Set common data
        $this->bind('site_name', SITE_NAME);
        $this->bind('current_url', Router::currentUrl());
    }
    
    public function register() {
        // If already authenticated, redirect to profile
        if ($this->isAuthenticated()) {
            $this->redirect('/perfil');
            return;
        }
        
        $this->setData([
            'title' => 'Crear Cuenta - Capacitar-T México',
            'description' => 'Crea tu cuenta gratuita y accede a los mejores cursos de capacitación médica',
            'form_data' => $_SESSION['form_data'] ?? [],
            'form_errors' => $_SESSION['form_errors'] ?? []
        ]);
        
        // Clear form data from session
        unset($_SESSION['form_data'], $_SESSION['form_errors']);
        
        $this->view('users/register');
    }
    
    public function registerSubmit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/registro');
            return;
        }
        
        $rules = [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|email|max:255',
            'phone' => 'required|max:20',
            'password' => 'required|min:8|max:255',
            'password_confirm' => 'required|same:password',
            'profession' => 'required|max:50',
            'birth_date' => 'required|date',
            'terms_accepted' => 'required'
        ];
        
        $data = $this->sanitize($_POST);
        
        if ($this->validate($data, $rules)) {
            // Check if email already exists
            if ($this->userModel->emailExists($data['email'])) {
                $this->redirect('/registro', 'El correo electrónico ya está registrado. <a href="/login">Inicia sesión</a>', 'error');
                return;
            }
            
            // Determine user type based on profession
            $userType = $this->determineUserType($data['profession']);
            
            // Create user account
            $userData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'profession' => $data['profession'],
                'birth_date' => $data['birth_date'],
                'user_type' => $userType,
                'is_student' => $this->isStudentProfession($data['profession']),
                'is_healthcare_professional' => $this->isHealthcareProfession($data['profession']),
                'email_verified' => false,
                'status' => 'active',
                'demographic_group' => $this->determineDemographic($data['birth_date']),
                'registration_ip' => $_SERVER['REMOTE_ADDR'],
                'registration_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $userId = $this->userModel->create($userData);
            
            if ($userId) {
                // Generate email verification token
                $verificationToken = $this->generateVerificationToken($userId);
                
                // Send welcome email with verification link
                $this->sendWelcomeEmail($userData, $verificationToken);
                
                // Auto-login the user
                $this->loginUser($userId, $userData);
                
                // Redirect based on redirect parameter or default to profile
                $redirectUrl = $this->validateRedirectUrl($_GET['redirect'] ?? '/perfil');
                $this->redirect($redirectUrl, 'Cuenta creada exitosamente. ¡Bienvenido a Capacitar-T!', 'success');
            } else {
                $this->redirect('/registro', 'Error al crear la cuenta. Inténtalo nuevamente.', 'error');
            }
        } else {
            $_SESSION['form_errors'] = $this->getErrors();
            $_SESSION['form_data'] = $data;
            $this->redirect('/registro');
        }
    }
    
    public function login() {
        if ($this->isAuthenticated()) {
            $this->redirect('/perfil');
            return;
        }
        
        $this->setData([
            'title' => 'Iniciar Sesión - Capacitar-T México',
            'description' => 'Accede a tu cuenta y continúa con tu capacitación médica',
            'redirect_url' => $_GET['redirect'] ?? '',
            'form_data' => $_SESSION['form_data'] ?? [],
            'form_errors' => $_SESSION['form_errors'] ?? []
        ]);
        
        unset($_SESSION['form_data'], $_SESSION['form_errors']);
        
        $this->view('users/login');
    }
    
    public function loginSubmit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
            return;
        }
        
        $rules = [
            'email' => 'required|email',
            'password' => 'required'
        ];
        
        $data = $this->sanitize($_POST);
        
        if ($this->validate($data, $rules)) {
            $user = $this->userModel->findByEmail($data['email']);
            
            if ($user && password_verify($data['password'], $user['password'])) {
                if ($user['status'] !== 'active') {
                    $this->redirect('/login', 'Tu cuenta está inactiva. Contacta soporte.', 'error');
                    return;
                }
                
                // Login successful
                $this->loginUser($user['id'], $user);
                
                // Update last login
                $this->userModel->updateLastLogin($user['id']);
                
                // Redirect to intended page or profile (validate redirect URL to prevent open redirects)
                $redirectUrl = $this->validateRedirectUrl($data['redirect_url'] ?? '/perfil');
                $this->redirect($redirectUrl, 'Bienvenido de vuelta, ' . htmlspecialchars($user['first_name'], ENT_QUOTES) . '!', 'success');
            } else {
                $this->redirect('/login', 'Credenciales incorrectas.', 'error');
            }
        } else {
            $_SESSION['form_errors'] = $this->getErrors();
            $_SESSION['form_data'] = $data;
            $this->redirect('/login');
        }
    }
    
    public function logout() {
        if ($this->isAuthenticated()) {
            $userName = $_SESSION['user_name'] ?? '';
            
            // Destroy session
            session_destroy();
            
            // Start new session for flash message
            session_start();
            session_regenerate_id(true);
            
            $this->redirect('/', 'Sesión cerrada exitosamente. ¡Hasta pronto' . ($userName ? ', ' . $userName : '') . '!', 'success');
        } else {
            $this->redirect('/');
        }
    }
    
    public function profile() {
        if (!$this->isAuthenticated()) {
            $this->redirect('/login?redirect=' . urlencode('/perfil'));
            return;
        }
        
        $userId = $this->getSession('user_id');
        $userProfile = $this->userModel->getDetailedProfile($userId);
        
        if (!$userProfile) {
            $this->redirect('/login', 'Sesión expirada. Inicia sesión nuevamente.', 'error');
            return;
        }
        
        // Get user's enrollments
        $enrollments = $this->userModel->getEnrollments($userId);
        
        // Get user's certificates
        $certificates = $this->userModel->getCertificates($userId);
        
        // Get achievement badges
        $achievements = $this->getUserAchievements($userId);
        
        $this->setData([
            'title' => 'Mi Perfil - ' . $userProfile['first_name'] . ' ' . $userProfile['last_name'],
            'description' => 'Gestiona tu perfil, cursos y certificaciones médicas',
            'user' => $userProfile,
            'enrollments' => $enrollments,
            'certificates' => $certificates,
            'achievements' => $achievements,
            'form_data' => $_SESSION['form_data'] ?? [],
            'form_errors' => $_SESSION['form_errors'] ?? []
        ]);
        
        unset($_SESSION['form_data'], $_SESSION['form_errors']);
        
        $this->view('users/profile');
    }
    
    public function updateProfile() {
        if (!$this->isAuthenticated()) {
            $this->redirect('/login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/perfil');
            return;
        }
        
        $rules = [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'phone' => 'required|max:20',
            'profession' => 'required|max:50',
            'birth_date' => 'required|date',
            'bio' => 'max:500',
            'linkedin_url' => 'url',
            'institution' => 'max:100'
        ];
        
        // Add password validation only if password is being changed
        if (!empty($_POST['new_password'])) {
            $rules['current_password'] = 'required';
            $rules['new_password'] = 'required|min:8';
            $rules['new_password_confirm'] = 'required|same:new_password';
        }
        
        $data = $this->sanitize($_POST);
        
        if ($this->validate($data, $rules)) {
            $userId = $this->getSession('user_id');
            
            // Verify current password if changing password
            if (!empty($data['new_password'])) {
                $currentUser = $this->userModel->getById($userId);
                if (!password_verify($data['current_password'], $currentUser['password'])) {
                    $this->redirect('/perfil', 'Contraseña actual incorrecta.', 'error');
                    return;
                }
                $data['password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
            }
            
            // Remove password fields that shouldn't be saved
            unset($data['current_password'], $data['new_password'], $data['new_password_confirm']);
            
            // Update user type based on new profession
            $data['user_type'] = $this->determineUserType($data['profession']);
            $data['is_student'] = $this->isStudentProfession($data['profession']);
            $data['is_healthcare_professional'] = $this->isHealthcareProfession($data['profession']);
            $data['demographic_group'] = $this->determineDemographic($data['birth_date']);
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            if ($this->userModel->update($userId, $data)) {
                // Update session data
                $_SESSION['user_name'] = $data['first_name'] . ' ' . $data['last_name'];
                
                $this->redirect('/perfil', 'Perfil actualizado exitosamente.', 'success');
            } else {
                $this->redirect('/perfil', 'Error al actualizar el perfil.', 'error');
            }
        } else {
            $_SESSION['form_errors'] = $this->getErrors();
            $_SESSION['form_data'] = $data;
            $this->redirect('/perfil');
        }
    }
    
    public function verifyEmail($token) {
        $verification = $this->userModel->getEmailVerification($token);
        
        if (!$verification || $verification['expires_at'] < date('Y-m-d H:i:s')) {
            $this->redirect('/', 'Token de verificación inválido o expirado.', 'error');
            return;
        }
        
        // Mark email as verified
        if ($this->userModel->verifyEmail($verification['user_id'])) {
            // Delete verification token
            $this->userModel->deleteEmailVerification($token);
            
            $this->redirect('/perfil', 'Email verificado exitosamente.', 'success');
        } else {
            $this->redirect('/', 'Error al verificar el email.', 'error');
        }
    }
    
    // Private helper methods
    private function loginUser($userId, $userData) {
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Set session data
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = $userData['email'];
        $_SESSION['user_name'] = $userData['first_name'] . ' ' . $userData['last_name'];
        $_SESSION['user_role'] = $userData['user_type'];
        $_SESSION['user_profession'] = $userData['profession'];
        $_SESSION['is_healthcare_professional'] = $userData['is_healthcare_professional'];
        $_SESSION['is_student'] = $userData['is_student'];
        $_SESSION['login_time'] = time();
        
        // Generate CSRF token
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
    }
    
    private function determineUserType($profession) {
        $medicalProfessions = ['doctor', 'nurse', 'medical_student', 'nursing_student', 'paramedic'];
        $adminProfessions = ['administrator', 'manager'];
        
        if (in_array($profession, $medicalProfessions)) {
            return 'medical_professional';
        } elseif (in_array($profession, $adminProfessions)) {
            return 'administrator';
        } else {
            return 'general_public';
        }
    }
    
    private function isStudentProfession($profession) {
        return in_array($profession, ['medical_student', 'nursing_student']);
    }
    
    private function isHealthcareProfession($profession) {
        return in_array($profession, ['doctor', 'nurse', 'medical_student', 'nursing_student', 'paramedic']);
    }
    
    private function determineDemographic($birthDate) {
        $age = date('Y') - date('Y', strtotime($birthDate));
        
        if ($age >= 40 && $age <= 55) {
            return 'GEN_X';
        } elseif ($age >= 25 && $age < 40) {
            return 'MILLENNIALS';
        } elseif ($age >= 18 && $age < 25) {
            return 'GEN_BETA';
        } else {
            return 'OTHER';
        }
    }
    
    private function generateVerificationToken($userId) {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $this->userModel->createEmailVerification($userId, $token, $expiresAt);
        
        return $token;
    }
    
    private function sendWelcomeEmail($userData, $verificationToken) {
        // Email service implementation
        $subject = "Bienvenido a Capacitar-T México";
        $verificationUrl = SITE_URL . "/verificar-email/" . $verificationToken;
        
        $message = "
            Hola {$userData['first_name']},
            
            ¡Bienvenido a Capacitar-T México!
            
            Tu cuenta ha sido creada exitosamente. Para completar el registro,
            por favor verifica tu email haciendo clic en el siguiente enlace:
            
            {$verificationUrl}
            
            Este enlace expira en 24 horas.
            
            Si no solicitaste esta cuenta, puedes ignorar este email.
            
            ¡Gracias por unirte a nosotros!
            
            El equipo de Capacitar-T México
        ";
        
        // Here you would send the actual email
        // mail($userData['email'], $subject, $message);
        
        return true;
    }
    
    private function getUserAchievements($userId) {
        // Get user achievements/badges
        return [
            [
                'id' => 'first_course',
                'name' => 'Primer Curso Completado',
                'description' => 'Completó su primer curso de capacitación',
                'icon' => 'fas fa-graduation-cap',
                'earned' => true,
                'earned_date' => '2024-01-15'
            ],
            [
                'id' => 'aha_certified',
                'name' => 'Certificado AHA',
                'description' => 'Obtuvo certificación de la American Heart Association',
                'icon' => 'fas fa-certificate',
                'earned' => true,
                'earned_date' => '2024-01-20'
            ],
            [
                'id' => 'course_series',
                'name' => 'Estudiante Dedicado',
                'description' => 'Completó 3 cursos en un año',
                'icon' => 'fas fa-medal',
                'earned' => false,
                'progress' => 2,
                'required' => 3
            ]
        ];
    }
}
?>