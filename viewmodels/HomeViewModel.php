<?php
require_once 'includes/viewmodel_base.php';
require_once 'models/Course.php';
require_once 'models/User.php';

class HomeViewModel extends ViewModelBase {
    private $courseModel;
    private $userModel;
    
    protected function init() {
        $this->courseModel = new Course();
        $this->userModel = new User();
        
        // Set common data for all views
        $this->bind('site_name', SITE_NAME);
        $this->bind('current_url', Router::currentUrl());
    }
    
    public function index() {
        // Get featured courses
        $featuredCourses = $this->courseModel->getFeatured(6);
        
        // Get upcoming schedules
        $upcomingSchedules = $this->courseModel->getUpcomingSchedules(8);
        
        // Get courses by type for the three main lines
        $medicalProfessionalCourses = $this->courseModel->getByType('MEDICAL_PROFESSIONAL');
        $communityFirstAidCourses = $this->courseModel->getByType('COMMUNITY_FIRST_AID');
        $medicalManagementCourses = $this->courseModel->getByType('MEDICAL_MANAGEMENT');
        
        // Get statistics for the stats section
        $courseStats = $this->courseModel->getStats();
        $userStats = $this->userModel->getStats();
        
        // Combine stats
        $stats = [
            'total_courses' => $courseStats['total_courses'] ?? 0,
            'active_students' => $userStats['active_users'] ?? 0,
            'professionals_trained' => $userStats['professionals'] ?? 0,
            'certifications_issued' => $this->getCertificationsCount(),
            'average_rating' => 4.8, // This would come from reviews
            'years_experience' => 15
        ];
        
        // Testimonials data
        $testimonials = $this->getTestimonials();
        
        // Set view data
        $this->setData([
            'title' => 'Inicio - Capacitación Médica y Primeros Auxilios',
            'description' => 'Centro líder en capacitación médica con cursos BLS, ACLS, PALS, Stop the Bleed y Heartsaver certificados por AHA',
            'featured_courses' => $featuredCourses,
            'upcoming_schedules' => $upcomingSchedules,
            'medical_professional_courses' => $medicalProfessionalCourses,
            'community_first_aid_courses' => $communityFirstAidCourses,
            'medical_management_courses' => $medicalManagementCourses,
            'stats' => $stats,
            'testimonials' => $testimonials,
            'course_lines' => $this->getCourseLines()
        ]);
        
        $this->view('home/index');
    }
    
    public function about() {
        // About page data
        $this->setData([
            'title' => 'Acerca de Nosotros - Capacitar-T México',
            'description' => 'Conoce nuestra historia, misión y equipo de instructores certificados en capacitación médica',
            'team_members' => $this->getTeamMembers(),
            'certifications' => $this->getCertificationBodies(),
            'milestones' => $this->getMilestones()
        ]);
        
        $this->view('home/about');
    }
    
    public function contact() {
        // Get course categories for contact form
        $categories = $this->courseModel->findAll(['status' => 'published'], 'sort_order');
        
        $this->setData([
            'title' => 'Contacto - Capacitar-T México',
            'description' => 'Ponte en contacto con nosotros para información sobre cursos médicos y primeros auxilios',
            'categories' => $categories,
            'contact_info' => $this->getContactInfo()
        ]);
        
        $this->view('home/contact');
    }
    
    public function contactSubmit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/contacto');
            return;
        }
        
        $rules = [
            'name' => 'required|max:100',
            'email' => 'required|email|max:255',
            'subject' => 'required|max:200',
            'message' => 'required|max:1000'
        ];
        
        $data = $this->sanitize($_POST);
        
        if ($this->validate($data, $rules)) {
            // Create contact submission
            $contactData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? '',
                'profession' => $data['profession'] ?? '',
                'institution' => $data['institution'] ?? '',
                'subject' => $data['subject'],
                'message' => $data['message'],
                'course_interest' => $data['course_interest'] ?? '',
                'preferred_schedule' => $data['preferred_schedule'] ?? 'flexible',
                'group_size' => $data['group_size'] ?? null,
                'is_corporate_inquiry' => isset($data['is_corporate']),
                'urgency_level' => $data['urgency_level'] ?? 'medium',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'source' => $data['source'] ?? 'website'
            ];
            
            $sql = "INSERT INTO contact_submissions (" . implode(', ', array_keys($contactData)) . ") 
                    VALUES (" . implode(', ', array_fill(0, count($contactData), '?')) . ")";
            
            if ($this->db->execute($sql, array_values($contactData))) {
                // Send notification email (implement email service)
                $this->sendContactNotification($contactData);
                
                $this->redirect('/contacto', 'Gracias por contactarnos. Te responderemos pronto.', 'success');
            } else {
                $this->redirect('/contacto', 'Ocurrió un error al enviar el mensaje. Intenta nuevamente.', 'error');
            }
        } else {
            $_SESSION['form_errors'] = $this->getErrors();
            $_SESSION['form_data'] = $data;
            $this->redirect('/contacto');
        }
    }
    
    // Private helper methods
    private function getCertificationsCount() {
        $sql = "SELECT COUNT(*) as count FROM course_enrollments WHERE certificate_issued = 1";
        $result = $this->db->fetchOne($sql);
        return $result['count'] ?? 0;
    }
    
    private function getTestimonials() {
        return [
            [
                'name' => 'Dra. María Elena Rodríguez',
                'position' => 'Médico de Urgencias - Hospital General CDMX',
                'image' => 'assets/images/testimonials/dra-rodriguez.jpg', // CC image
                'text' => 'El curso de ACLS cambió completamente mi confianza durante las emergencias cardiovasculares. Los instructores son excepcionales y la metodología muy práctica.',
                'rating' => 5,
                'demographic' => 'GEN_X'
            ],
            [
                'name' => 'Enf. Carlos Mendoza',
                'position' => 'Enfermero Jefe - UCI Pediátrica',
                'image' => 'assets/images/testimonials/enf-mendoza.jpg',
                'text' => 'PALS me dio las herramientas necesarias para manejar emergencias pediátricas con seguridad. Altamente recomendado para todo el personal de salud.',
                'rating' => 5,
                'demographic' => 'MILLENNIALS'
            ],
            [
                'name' => 'Lic. Ana Sofía Herrera',
                'position' => 'Directora - Guardería Arcoíris',
                'image' => 'assets/images/testimonials/lic-herrera.jpg',
                'text' => 'Como madre y directora de guardería, el curso de primeros auxilios pediátricos me tranquilizó muchísimo. Ahora sé cómo actuar ante cualquier emergencia.',
                'rating' => 5,
                'demographic' => 'MILLENNIALS'
            ]
        ];
    }
    
    private function getCourseLines() {
        return [
            [
                'id' => 'medical_professionals',
                'title' => 'Profesionales Médicos',
                'subtitle' => 'Certificaciones AHA para personal de salud',
                'description' => 'Cursos especializados para estudiantes de medicina, enfermería, paramédicos y miembros de brigadas de primeros auxilios en fábricas y centros de trabajo.',
                'icon' => 'fas fa-user-md',
                'color' => '#e74c3c',
                'courses' => ['BLS Básico', 'ACLS Avanzado', 'PALS Pediátrico', 'Stop the Bleed Profesional', 'Heartsaver AED'],
                'target_audience' => 'Estudiantes de medicina y enfermería, paramédicos, personal de brigadas industriales',
                'certifications' => 'American Heart Association (AHA)',
                'image' => 'assets/images/course-lines/medical-professionals.jpg'
            ],
            [
                'id' => 'community_first_aid',
                'title' => 'Primeros Auxilios Comunitarios',
                'subtitle' => 'Para padres, maestros y cuidadores',
                'description' => 'Cursos de primeros auxilios diseñados para padres, maestros y personal de parques infantiles, piscinas y campamentos de verano.',
                'icon' => 'fas fa-heart',
                'color' => '#27ae60',
                'courses' => ['RCP Pediátrico', 'Stop the Bleed Básico', 'Primeros Auxilios Acuáticos', 'Emergencias Infantiles'],
                'target_audience' => 'Padres de familia, maestros, personal de guarderías y centros recreativos',
                'certifications' => 'Heartsaver AHA / Capacitar-T',
                'image' => 'assets/images/course-lines/community-first-aid.jpg'
            ],
            [
                'id' => 'medical_management',
                'title' => 'Gestión Médica',
                'subtitle' => 'Administración de servicios de salud',
                'description' => 'Cursos especializados en gestión y administración de consultorios médicos, clínicas, salas de urgencias y áreas de recepción.',
                'icon' => 'fas fa-hospital',
                'color' => '#3498db',
                'courses' => ['Administración de Consultorios', 'Gestión de Urgencias', 'Atención al Paciente', 'Sistemas de Calidad'],
                'target_audience' => 'Administradores médicos, directores de clínica, personal administrativo',
                'certifications' => 'Capacitar-T / Instituciones de Salud',
                'image' => 'assets/images/course-lines/medical-management.jpg'
            ]
        ];
    }
    
    private function getTeamMembers() {
        return [
            [
                'name' => 'Dr. Roberto Martínez Silva',
                'position' => 'Director Médico y Fundador',
                'specialization' => 'Medicina de Emergencias',
                'credentials' => 'MD, FACEP, Instructor AHA',
                'experience' => '20 años',
                'image' => 'assets/images/team/dr-martinez.jpg',
                'bio' => 'Especialista en medicina de emergencias con más de 20 años de experiencia en enseñanza médica.'
            ],
            [
                'name' => 'Enf. Patricia González López',
                'position' => 'Coordinadora de Educación',
                'specialization' => 'Enfermería en Cuidados Críticos',
                'credentials' => 'RN, BLS/ACLS/PALS Instructor',
                'experience' => '15 años',
                'image' => 'assets/images/team/enf-gonzalez.jpg',
                'bio' => 'Enfermera especializada en cuidados críticos y educación médica continua.'
            ]
        ];
    }
    
    private function getCertificationBodies() {
        return [
            [
                'name' => 'American Heart Association',
                'acronym' => 'AHA',
                'logo' => 'assets/images/certifications/aha-logo.png',
                'description' => 'Organización líder mundial en reanimación cardiopulmonar',
                'courses' => ['BLS', 'ACLS', 'PALS', 'Heartsaver']
            ],
            [
                'name' => 'European Resuscitation Council',
                'acronym' => 'ERC',
                'logo' => 'assets/images/certifications/erc-logo.png',
                'description' => 'Consejo Europeo de Resucitación',
                'courses' => ['Soporte Vital Básico', 'Soporte Vital Avanzado']
            ]
        ];
    }
    
    private function getMilestones() {
        return [
            ['year' => 2009, 'event' => 'Fundación de Capacitar-T'],
            ['year' => 2012, 'event' => 'Certificación como Centro AHA'],
            ['year' => 2015, 'event' => '1,000 profesionales capacitados'],
            ['year' => 2018, 'event' => 'Expansión a cursos comunitarios'],
            ['year' => 2020, 'event' => 'Modalidades virtuales e híbridas'],
            ['year' => 2023, 'event' => '5,000 profesionales certificados']
        ];
    }
    
    private function getContactInfo() {
        return [
            'address' => 'Av. Universidad 1200, Col. Del Valle Sur, CDMX',
            'phone' => '+52 55 1234 5678',
            'whatsapp' => '+52 55 8765 4321',
            'email' => 'info@capacitar-t.com.mx',
            'hours' => 'Lunes a Viernes: 8:00 - 18:00, Sábados: 9:00 - 15:00',
            'social_media' => [
                'facebook' => 'https://facebook.com/capacitart.mx',
                'instagram' => 'https://instagram.com/capacitart.mx',
                'linkedin' => 'https://linkedin.com/company/capacitar-t',
                'youtube' => 'https://youtube.com/@capacitartmexico'
            ]
        ];
    }
    
    private function sendContactNotification($contactData) {
        // Email notification implementation
        // This would integrate with an email service like PHPMailer, SendGrid, etc.
        
        $subject = "Nuevo contacto: " . $contactData['subject'];
        $message = "
            Nuevo mensaje de contacto recibido:
            
            Nombre: {$contactData['name']}
            Email: {$contactData['email']}
            Teléfono: {$contactData['phone']}
            Profesión: {$contactData['profession']}
            Institución: {$contactData['institution']}
            
            Asunto: {$contactData['subject']}
            
            Mensaje:
            {$contactData['message']}
            
            Curso de interés: {$contactData['course_interest']}
            Horario preferido: {$contactData['preferred_schedule']}
            Tamaño del grupo: {$contactData['group_size']}
            Es consulta corporativa: " . ($contactData['is_corporate_inquiry'] ? 'Sí' : 'No') . "
            Nivel de urgencia: {$contactData['urgency_level']}
        ";
        
        // Here you would send the email
        // mail(FROM_EMAIL, $subject, $message);
        
        return true;
    }
}
?>