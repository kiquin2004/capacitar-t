<?php
require_once 'includes/viewmodel_base.php';
require_once 'models/Course.php';
require_once 'models/User.php';

class AdminViewModel extends ViewModelBase {
    private $courseModel;
    private $userModel;
    
    protected function init() {
        // Check admin authentication
        if (!$this->isAuthenticated() || $_SESSION['user_role'] !== 'admin') {
            $this->redirect('/login?redirect=' . urlencode($_SERVER['REQUEST_URI']));
            return;
        }
        
        $this->courseModel = new Course();
        $this->userModel = new User();
        
        $this->bind('site_name', SITE_NAME);
        $this->bind('current_url', Router::currentUrl());
    }
    
    public function dashboard() {
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent enrollments
        $recentEnrollments = $this->getRecentEnrollments(10);
        
        // Get course performance metrics
        $courseMetrics = $this->getCourseMetrics();
        
        // Get system alerts
        $alerts = $this->getSystemAlerts();
        
        $this->setData([
            'title' => 'Panel de Administración - Capacitar-T',
            'description' => 'Dashboard administrativo para gestión de cursos y estudiantes',
            'stats' => $stats,
            'recent_enrollments' => $recentEnrollments,
            'course_metrics' => $courseMetrics,
            'alerts' => $alerts
        ]);
        
        $this->view('admin/dashboard');
    }
    
    public function courses() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'search' => $search,
            'status' => $status,
            'limit' => $limit,
            'offset' => $offset
        ];
        
        $courses = $this->courseModel->getAdminList($filters);
        $totalCourses = $this->courseModel->getTotalCount($filters);
        $pagination = $this->paginate($totalCourses, $page, $limit);
        
        $this->setData([
            'title' => 'Gestión de Cursos - Admin',
            'description' => 'Administrar cursos médicos y certificaciones',
            'courses' => $courses,
            'pagination' => $pagination,
            'current_filters' => $filters,
            'total_courses' => $totalCourses
        ]);
        
        $this->view('admin/courses');
    }
    
    public function courseForm($courseId = null) {
        $course = null;
        $pageTitle = 'Nuevo Curso';
        
        if ($courseId) {
            $course = $this->courseModel->getById($courseId);
            if (!$course) {
                http_response_code(404);
                $this->view('errors/404');
                return;
            }
            $pageTitle = 'Editar: ' . $course['title'];
        }
        
        // Get form data
        $categories = $this->courseModel->getCategories();
        $instructors = $this->getInstructors();
        
        $this->setData([
            'title' => $pageTitle,
            'description' => 'Formulario de creación/edición de cursos',
            'course' => $course,
            'categories' => $categories,
            'instructors' => $instructors,
            'form_data' => $_SESSION['form_data'] ?? [],
            'form_errors' => $_SESSION['form_errors'] ?? []
        ]);
        
        unset($_SESSION['form_data'], $_SESSION['form_errors']);
        
        $this->view('admin/course-form');
    }
    
    public function saveCourse($courseId = null) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/admin/cursos');
            return;
        }
        
        $rules = [
            'title' => 'required|max:200',
            'short_description' => 'required|max:500',
            'full_description' => 'required',
            'course_code' => 'required|max:20',
            'category_id' => 'required|numeric',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|numeric|min:1',
            'max_participants' => 'required|numeric|min:1',
            'difficulty_level' => 'required|in:BASIC,INTERMEDIATE,ADVANCED',
            'certification_body' => 'required|in:AHA,ERC,ILCOR,CAPACITAR_T',
            'is_aha_certified' => 'boolean',
            'status' => 'required|in:draft,published,archived'
        ];
        
        $data = $this->sanitize($_POST);
        
        if ($this->validate($data, $rules)) {
            // Handle file uploads
            $featuredImage = $this->handleImageUpload('featured_image');
            if ($featuredImage) {
                $data['featured_image'] = $featuredImage;
            }
            
            // Process learning objectives
            if (!empty($data['learning_objectives_raw'])) {
                $objectives = array_filter(explode("\n", $data['learning_objectives_raw']));
                $data['learning_objectives'] = json_encode($objectives);
            }
            
            // Process target audiences
            if (!empty($data['target_audiences_raw'])) {
                $audiences = array_filter(explode("\n", $data['target_audiences_raw']));
                $data['target_audiences'] = json_encode($audiences);
            }
            
            // Generate slug from title
            if (!$courseId) {
                $data['slug'] = $this->generateSlug($data['title']);
            }
            
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            if ($courseId) {
                // Update existing course
                if ($this->courseModel->update($courseId, $data)) {
                    $this->redirect('/admin/cursos', 'Curso actualizado exitosamente.', 'success');
                } else {
                    $this->redirect('/admin/curso/' . $courseId, 'Error al actualizar el curso.', 'error');
                }
            } else {
                // Create new course
                $data['created_at'] = date('Y-m-d H:i:s');
                $newCourseId = $this->courseModel->create($data);
                
                if ($newCourseId) {
                    $this->redirect('/admin/curso/' . $newCourseId, 'Curso creado exitosamente.', 'success');
                } else {
                    $this->redirect('/admin/curso/nuevo', 'Error al crear el curso.', 'error');
                }
            }
        } else {
            $_SESSION['form_errors'] = $this->getErrors();
            $_SESSION['form_data'] = $data;
            
            if ($courseId) {
                $this->redirect('/admin/curso/' . $courseId);
            } else {
                $this->redirect('/admin/curso/nuevo');
            }
        }
    }
    
    public function users() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $userType = isset($_GET['user_type']) ? trim($_GET['user_type']) : '';
        
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'search' => $search,
            'user_type' => $userType,
            'limit' => $limit,
            'offset' => $offset
        ];
        
        $users = $this->userModel->getAdminList($filters);
        $totalUsers = $this->userModel->getTotalCount($filters);
        $pagination = $this->paginate($totalUsers, $page, $limit);
        
        $userStats = $this->getUserStats();
        
        $this->setData([
            'title' => 'Gestión de Usuarios - Admin',
            'description' => 'Administrar usuarios y perfiles',
            'users' => $users,
            'pagination' => $pagination,
            'current_filters' => $filters,
            'total_users' => $totalUsers,
            'user_stats' => $userStats
        ]);
        
        $this->view('admin/users');
    }
    
    public function enrollments() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $courseId = isset($_GET['course_id']) ? (int)$_GET['course_id'] : null;
        
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'status' => $status,
            'course_id' => $courseId,
            'limit' => $limit,
            'offset' => $offset
        ];
        
        $enrollments = $this->getEnrollmentsList($filters);
        $totalEnrollments = $this->getTotalEnrollments($filters);
        $pagination = $this->paginate($totalEnrollments, $page, $limit);
        
        $enrollmentStats = $this->getEnrollmentStats();
        $courses = $this->courseModel->getSelectOptions();
        
        $this->setData([
            'title' => 'Gestión de Inscripciones - Admin',
            'description' => 'Administrar inscripciones y pagos',
            'enrollments' => $enrollments,
            'pagination' => $pagination,
            'current_filters' => $filters,
            'total_enrollments' => $totalEnrollments,
            'enrollment_stats' => $enrollmentStats,
            'courses' => $courses
        ]);
        
        $this->view('admin/enrollments');
    }
    
    public function reports() {
        $reportType = $_GET['type'] ?? 'revenue';
        $period = $_GET['period'] ?? 'monthly';
        
        $reportData = $this->generateReport($reportType, $period);
        
        $this->setData([
            'title' => 'Reportes y Análisis - Admin',
            'description' => 'Reportes de ingresos, inscripciones y performance',
            'report_type' => $reportType,
            'period' => $period,
            'report_data' => $reportData
        ]);
        
        $this->view('admin/reports');
    }
    
    // Private helper methods
    private function getDashboardStats() {
        return [
            'total_courses' => $this->courseModel->getTotalCount(),
            'active_courses' => $this->courseModel->getTotalCount(['status' => 'published']),
            'total_users' => $this->userModel->getTotalCount(),
            'new_users_month' => $this->userModel->getNewUsersCount(30),
            'total_enrollments' => $this->getTotalEnrollments(),
            'pending_enrollments' => $this->getTotalEnrollments(['status' => 'pending_payment']),
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'completion_rate' => $this->getCompletionRate()
        ];
    }
    
    private function getRecentEnrollments($limit = 10) {
        $sql = "SELECT e.*, u.first_name, u.last_name, u.email, c.title as course_title, c.slug as course_slug
                FROM course_enrollments e
                JOIN users u ON e.user_id = u.id
                JOIN courses c ON e.course_id = c.id
                ORDER BY e.created_at DESC
                LIMIT ?";
                
        return $this->db->fetchAll($sql, [$limit]);
    }
    
    private function getCourseMetrics() {
        $sql = "SELECT c.id, c.title, c.slug,
                       COUNT(e.id) as total_enrollments,
                       AVG(r.rating) as avg_rating,
                       SUM(CASE WHEN e.status = 'completed' THEN 1 ELSE 0 END) as completed_count
                FROM courses c
                LEFT JOIN course_enrollments e ON c.id = e.course_id
                LEFT JOIN course_reviews r ON c.id = r.course_id
                WHERE c.status = 'published'
                GROUP BY c.id
                ORDER BY total_enrollments DESC
                LIMIT 10";
                
        return $this->db->fetchAll($sql);
    }
    
    private function getSystemAlerts() {
        $alerts = [];
        
        // Check for courses with low enrollment
        $lowEnrollmentCourses = $this->courseModel->getLowEnrollmentCourses();
        if (count($lowEnrollmentCourses) > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Cursos con baja inscripción',
                'message' => count($lowEnrollmentCourses) . ' cursos tienen menos de 5 inscripciones.',
                'action' => '/admin/cursos?filter=low_enrollment'
            ];
        }
        
        // Check for pending payments
        $pendingPayments = $this->getTotalEnrollments(['status' => 'pending_payment']);
        if ($pendingPayments > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Pagos pendientes',
                'message' => $pendingPayments . ' inscripciones con pago pendiente.',
                'action' => '/admin/inscripciones?status=pending_payment'
            ];
        }
        
        // Check for expiring certificates
        $expiringCertificates = $this->getExpiringCertificatesCount();
        if ($expiringCertificates > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Certificados por vencer',
                'message' => $expiringCertificates . ' certificados vencen en los próximos 30 días.',
                'action' => '/admin/certificados?filter=expiring'
            ];
        }
        
        return $alerts;
    }
    
    private function getInstructors() {
        $sql = "SELECT id, first_name, last_name FROM users WHERE user_type = 'instructor' AND status = 'active'";
        return $this->db->fetchAll($sql);
    }
    
    private function handleImageUpload($fieldName) {
        if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        $file = $_FILES[$fieldName];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }
        
        $uploadDir = 'assets/images/courses/';
        $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', $file['name']);
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $fileName;
        }
        
        return null;
    }
    
    private function generateSlug($title) {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Ensure uniqueness
        $counter = 1;
        $originalSlug = $slug;
        
        while ($this->courseModel->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    private function getEnrollmentsList($filters) {
        $sql = "SELECT e.*, u.first_name, u.last_name, u.email, c.title as course_title,
                       s.start_date, s.location, p.amount, p.status as payment_status
                FROM course_enrollments e
                JOIN users u ON e.user_id = u.id
                JOIN courses c ON e.course_id = c.id
                LEFT JOIN course_schedules s ON e.schedule_id = s.id
                LEFT JOIN payments p ON e.id = p.enrollment_id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND e.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['course_id'])) {
            $sql .= " AND e.course_id = ?";
            $params[] = $filters['course_id'];
        }
        
        $sql .= " ORDER BY e.created_at DESC";
        
        if (isset($filters['limit'])) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $filters['limit'];
            $params[] = $filters['offset'] ?? 0;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    private function getTotalEnrollments($filters = []) {
        $sql = "SELECT COUNT(*) as count FROM course_enrollments e WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND e.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['course_id'])) {
            $sql .= " AND e.course_id = ?";
            $params[] = $filters['course_id'];
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] ?? 0;
    }
    
    private function getEnrollmentStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending_payment' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                FROM course_enrollments";
                
        return $this->db->fetchOne($sql) ?? [];
    }
    
    private function getUserStats() {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN user_type = 'medical_professional' THEN 1 ELSE 0 END) as medical_professionals,
                    SUM(CASE WHEN user_type = 'general_public' THEN 1 ELSE 0 END) as general_public,
                    SUM(CASE WHEN is_student = 1 THEN 1 ELSE 0 END) as students,
                    SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as new_this_month
                FROM users
                WHERE status = 'active'";
                
        return $this->db->fetchOne($sql) ?? [];
    }
    
    private function getMonthlyRevenue() {
        $sql = "SELECT COALESCE(SUM(p.amount), 0) as revenue
                FROM payments p
                WHERE p.status = 'completed'
                AND p.processed_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                
        $result = $this->db->fetchOne($sql);
        return $result['revenue'] ?? 0;
    }
    
    private function getCompletionRate() {
        $sql = "SELECT 
                    COUNT(*) as total_enrollments,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_enrollments
                FROM course_enrollments
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)";
                
        $result = $this->db->fetchOne($sql);
        if ($result && $result['total_enrollments'] > 0) {
            return round(($result['completed_enrollments'] / $result['total_enrollments']) * 100, 1);
        }
        
        return 0;
    }
    
    private function getExpiringCertificatesCount() {
        $sql = "SELECT COUNT(*) as count
                FROM course_enrollments e
                JOIN courses c ON e.course_id = c.id
                WHERE e.certificate_issued = 1
                AND e.status = 'completed'
                AND DATE_ADD(e.completion_date, INTERVAL 2 YEAR) <= DATE_ADD(NOW(), INTERVAL 30 DAY)";
                
        $result = $this->db->fetchOne($sql);
        return $result['count'] ?? 0;
    }
    
    private function generateReport($type, $period) {
        // Placeholder for report generation
        // This would implement actual report generation logic
        return [
            'type' => $type,
            'period' => $period,
            'data' => [],
            'charts' => []
        ];
    }
}
?>