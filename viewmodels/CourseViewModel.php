<?php
require_once 'includes/viewmodel_base.php';
require_once 'models/Course.php';

class CourseViewModel extends ViewModelBase {
    private $courseModel;
    
    protected function init() {
        $this->courseModel = new Course();
        
        // Set common data
        $this->bind('site_name', SITE_NAME);
        $this->bind('current_url', Router::currentUrl());
    }
    
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $categorySlug = isset($_GET['category']) ? trim($_GET['category']) : '';
        $difficultyLevel = isset($_GET['difficulty']) ? trim($_GET['difficulty']) : '';
        $certificationBody = isset($_GET['certification']) ? trim($_GET['certification']) : '';
        $priceRange = isset($_GET['price_range']) ? trim($_GET['price_range']) : '';
        
        $limit = 12;
        $offset = ($page - 1) * $limit;
        
        $filters = [
            'search' => $search,
            'category_slug' => $categorySlug,
            'difficulty_level' => $difficultyLevel,
            'certification_body' => $certificationBody,
            'price_range' => $priceRange,
            'limit' => $limit,
            'offset' => $offset
        ];
        
        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== '' && $value !== null;
        });
        
        $courses = $this->courseModel->getAllWithCategories($filters);
        $totalCourses = $this->courseModel->getTotalCount($filters);
        $pagination = $this->paginate($totalCourses, $page, $limit);
        
        // Get filter options
        $categories = $this->getCategories();
        $difficultyLevels = CERTIFICATION_LEVELS;
        $certificationBodies = ['AHA', 'ERC', 'ILCOR', 'CAPACITAR_T'];
        
        $this->setData([
            'title' => 'Catálogo de Cursos - Capacitación Médica',
            'description' => 'Explora nuestro catálogo completo de cursos médicos: BLS, ACLS, PALS, Stop the Bleed y más',
            'courses' => $courses,
            'categories' => $categories,
            'difficulty_levels' => $difficultyLevels,
            'certification_bodies' => $certificationBodies,
            'pagination' => $pagination,
            'current_filters' => [
                'search' => $search,
                'category' => $categorySlug,
                'difficulty' => $difficultyLevel,
                'certification' => $certificationBody,
                'price_range' => $priceRange
            ],
            'total_courses' => $totalCourses
        ]);
        
        $this->view('courses/index');
    }
    
    public function medicalProfessionals() {
        $courses = $this->courseModel->getByType('MEDICAL_PROFESSIONAL');
        $categoryInfo = $this->getCategoryInfo('MEDICAL_PROFESSIONAL');
        
        $this->setData([
            'title' => 'Cursos para Profesionales Médicos - BLS, ACLS, PALS',
            'description' => 'Certificaciones AHA para estudiantes de medicina, enfermería, paramédicos y brigadas de emergencia',
            'courses' => $courses,
            'category_info' => $categoryInfo,
            'specializations' => MEDICAL_SPECIALIZATIONS
        ]);
        
        $this->view('courses/category');
    }
    
    public function communityFirstAid() {
        $courses = $this->courseModel->getByType('COMMUNITY_FIRST_AID');
        $categoryInfo = $this->getCategoryInfo('COMMUNITY_FIRST_AID');
        
        $this->setData([
            'title' => 'Primeros Auxilios para Padres y Maestros',
            'description' => 'Cursos de primeros auxilios para parques, piscinas, campamentos y centros educativos',
            'courses' => $courses,
            'category_info' => $categoryInfo,
            'target_venues' => [
                'Parques infantiles',
                'Piscinas y centros acuáticos',
                'Campamentos de verano',
                'Guarderías y colegios',
                'Centros deportivos'
            ]
        ]);
        
        $this->view('courses/category');
    }
    
    public function medicalManagement() {
        $courses = $this->courseModel->getByType('MEDICAL_MANAGEMENT');
        $categoryInfo = $this->getCategoryInfo('MEDICAL_MANAGEMENT');
        
        $this->setData([
            'title' => 'Gestión de Consultorios y Clínicas Médicas',
            'description' => 'Cursos de administración para consultorios, clínicas, urgencias y recepciones médicas',
            'courses' => $courses,
            'category_info' => $categoryInfo,
            'management_areas' => [
                'Administración de consultorios',
                'Gestión de urgencias',
                'Atención al paciente',
                'Sistemas de calidad',
                'Recursos humanos'
            ]
        ]);
        
        $this->view('courses/category');
    }
    
    public function show($slug) {
        $course = $this->courseModel->getBySlug($slug);
        
        if (!$course) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }
        
        // Get course details
        $modules = $this->courseModel->getModules($course['id']);
        $equipment = $this->courseModel->getEquipment($course['id']);
        $schedules = $this->courseModel->getSchedules($course['id']);
        $relatedCourses = $this->courseModel->getRelated($course['id'], $course['category_id'], 3);
        
        // Check if user can enroll (if logged in)
        $canEnroll = true;
        $enrollmentMessage = '';
        
        if ($this->isAuthenticated()) {
            $userId = $this->getSession('user_id');
            $userModel = new User();
            $enrollmentCheck = $userModel->canEnroll($userId, $course['id']);
            $canEnroll = $enrollmentCheck['can_enroll'];
            $enrollmentMessage = $enrollmentCheck['reason'] ?? '';
        }
        
        // Track course view for analytics
        $this->trackCourseView($course['id']);
        
        $this->setData([
            'title' => $course['title'] . ' - Capacitar-T México',
            'description' => $course['short_description'],
            'course' => $course,
            'modules' => $modules,
            'equipment' => $equipment,
            'schedules' => $schedules,
            'related_courses' => $relatedCourses,
            'can_enroll' => $canEnroll,
            'enrollment_message' => $enrollmentMessage,
            'breadcrumbs' => $this->getBreadcrumbs($course)
        ]);
        
        $this->view('courses/detail');
    }
    
    // Specific course methods
    public function basicLifeSupport() {
        $this->showCourseByCode('BLS-001');
    }
    
    public function advancedCardiacLifeSupport() {
        $this->showCourseByCode('ACLS-001');
    }
    
    public function pediatricAdvancedLifeSupport() {
        $this->showCourseByCode('PALS-001');
    }
    
    public function stopTheBleed() {
        $courses = $this->courseModel->search('stop the bleed');
        
        $this->setData([
            'title' => 'Stop the Bleed - Control de Hemorragias',
            'description' => 'Aprende técnicas efectivas para controlar hemorragias y salvar vidas en situaciones de emergencia',
            'courses' => $courses,
            'course_info' => [
                'what_is' => 'Stop the Bleed es una campaña nacional para enseñar a personas sin formación médica cómo ayudar en una emergencia hemorrágica antes de que lleguen los profesionales.',
                'who_should_take' => 'Cualquier persona mayor de 10 años puede aprender estas técnicas que salvan vidas.',
                'techniques' => ['Presión directa', 'Vendajes de presión', 'Uso de torniquetes', 'Identificación de hemorragias'],
                'duration' => '1-3 horas según el nivel',
                'certification' => 'Certificado Stop the Bleed'
            ]
        ]);
        
        $this->view('courses/stop-the-bleed');
    }
    
    public function heartSaver() {
        $this->showCourseByCode('HS-PRO-001');
    }
    
    public function pediatricBLS() {
        $this->showCourseByCode('BLS-PED-001');
    }
    
    public function aquaticFirstAid() {
        $this->showCourseByCode('AQ-FA-001');
    }
    
    public function officeManagement() {
        $this->showCourseByCode('ADM-CONS-001');
    }
    
    public function emergencyManagement() {
        $this->showCourseByCode('ADM-URG-001');
    }
    
    // Helper methods
    private function showCourseByCode($courseCode) {
        $sql = "SELECT * FROM courses WHERE course_code = ? AND status = 'published'";
        $course = $this->db->fetchOne($sql, [$courseCode]);
        
        if ($course) {
            $this->redirect('/curso/' . $course['slug']);
        } else {
            http_response_code(404);
            $this->view('errors/404');
        }
    }
    
    private function getCategories() {
        return $this->db->fetchAll(
            "SELECT * FROM course_categories WHERE is_active = 1 ORDER BY sort_order, name"
        );
    }
    
    private function getCategoryInfo($courseType) {
        $info = [
            'MEDICAL_PROFESSIONAL' => [
                'title' => 'Profesionales Médicos',
                'description' => 'Cursos certificados por la American Heart Association para estudiantes y profesionales de la salud',
                'icon' => 'fas fa-user-md',
                'color' => '#e74c3c',
                'features' => [
                    'Certificaciones AHA reconocidas internacionalmente',
                    'Simulación con maniquíes de alta fidelidad',
                    'Casos clínicos reales',
                    'Validez de 2 años',
                    'Grupos reducidos (máximo 16 participantes)',
                    'Material didáctico incluido'
                ],
                'target_audience' => [
                    'Estudiantes de medicina',
                    'Estudiantes de enfermería',
                    'Paramédicos',
                    'Personal de brigadas industriales',
                    'Técnicos en emergencias médicas'
                ]
            ],
            'COMMUNITY_FIRST_AID' => [
                'title' => 'Primeros Auxilios Comunitarios',
                'description' => 'Capacitación práctica para padres, maestros y personal no médico',
                'icon' => 'fas fa-heart',
                'color' => '#27ae60',
                'features' => [
                    'Enfoque práctico y sencillo',
                    'Técnicas fáciles de recordar',
                    'Escenarios cotidianos',
                    'Certificado de participación',
                    'Material de consulta incluido',
                    'Horarios flexibles'
                ],
                'target_audience' => [
                    'Padres de familia',
                    'Maestros y educadores',
                    'Personal de guarderías',
                    'Entrenadores deportivos',
                    'Personal de centros recreativos'
                ]
            ],
            'MEDICAL_MANAGEMENT' => [
                'title' => 'Gestión Médica',
                'description' => 'Optimización de procesos administrativos y operativos en servicios de salud',
                'icon' => 'fas fa-hospital',
                'color' => '#3498db',
                'features' => [
                    'Metodologías probadas',
                    'Casos de estudio reales',
                    'Herramientas digitales',
                    'Certificado profesional',
                    'Networking con expertos',
                    'Seguimiento post-curso'
                ],
                'target_audience' => [
                    'Directores de clínicas',
                    'Administradores médicos',
                    'Personal administrativo',
                    'Jefes de enfermería',
                    'Coordinadores de servicios'
                ]
            ]
        ];
        
        return $info[$courseType] ?? null;
    }
    
    private function getBreadcrumbs($course) {
        return [
            ['title' => 'Inicio', 'url' => '/'],
            ['title' => 'Cursos', 'url' => '/cursos'],
            ['title' => $course['category_name'], 'url' => '/cursos/' . $course['category_slug']],
            ['title' => $course['title'], 'url' => '']
        ];
    }
    
    private function trackCourseView($courseId) {
        // Track course view for analytics
        $sql = "INSERT INTO course_views (course_id, user_id, ip_address, user_agent, viewed_at) 
                VALUES (?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE view_count = view_count + 1";
        
        $userId = $this->isAuthenticated() ? $this->getSession('user_id') : null;
        
        try {
            $this->db->execute($sql, [
                $courseId, 
                $userId, 
                $_SERVER['REMOTE_ADDR'], 
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        } catch (Exception $e) {
            // Log error but don't break the page
            error_log("Error tracking course view: " . $e->getMessage());
        }
    }
}
?>