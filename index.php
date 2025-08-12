<?php
// Load session configuration BEFORE starting session
require_once 'config/session.php';
session_start();
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/router.php';
require_once 'includes/viewmodel_base.php';

$router = new Router();

// Home routes
$router->get('/', 'HomeViewModel@index');
$router->get('/inicio', 'HomeViewModel@index');
$router->get('/nosotros', 'HomeViewModel@about');
$router->get('/contacto', 'HomeViewModel@contact');
$router->post('/contacto', 'HomeViewModel@contactSubmit');

// Course routes - Professional Medical Training
$router->get('/cursos', 'CourseViewModel@index');
$router->get('/cursos/profesionales-medicos', 'CourseViewModel@medicalProfessionals');
$router->get('/cursos/primeros-auxilios-comunitarios', 'CourseViewModel@communityFirstAid');
$router->get('/cursos/gestion-medica', 'CourseViewModel@medicalManagement');

// Specific course routes
$router->get('/curso/{slug}', 'CourseViewModel@show');
$router->get('/cursos/bls-basico', 'CourseViewModel@basicLifeSupport');
$router->get('/cursos/acls-avanzado', 'CourseViewModel@advancedCardiacLifeSupport');
$router->get('/cursos/pals-pediatrico', 'CourseViewModel@pediatricAdvancedLifeSupport');
$router->get('/cursos/stop-the-bleed', 'CourseViewModel@stopTheBleed');
$router->get('/cursos/heartsaver', 'CourseViewModel@heartSaver');
$router->get('/cursos/bls-infantil', 'CourseViewModel@pediatricBLS');
$router->get('/cursos/primeros-auxilios-acuaticos', 'CourseViewModel@aquaticFirstAid');
$router->get('/cursos/gestion-consultorios', 'CourseViewModel@officeManagement');
$router->get('/cursos/administracion-urgencias', 'CourseViewModel@emergencyManagement');

// User routes
$router->get('/registro', 'UserViewModel@register');
$router->post('/registro', 'UserViewModel@registerSubmit');
$router->get('/login', 'UserViewModel@login');
$router->post('/login', 'UserViewModel@loginSubmit');
$router->get('/logout', 'UserViewModel@logout');
$router->get('/perfil', 'UserViewModel@profile');

// Enrollment routes
$router->get('/inscripcion/{courseId}', 'EnrollmentViewModel@enroll');
$router->post('/inscripcion/{courseId}', 'EnrollmentViewModel@enrollSubmit');

// Enrollment routes
$router->get('/inscripcion/confirmacion/{enrollmentId}', 'EnrollmentViewModel@confirmation');

// User profile routes
$router->post('/perfil/actualizar', 'UserViewModel@updateProfile');
$router->get('/verificar-email/{token}', 'UserViewModel@verifyEmail');

// Admin routes (protected)
$router->get('/admin/dashboard', 'AdminViewModel@dashboard');
$router->get('/admin/cursos', 'AdminViewModel@courses');
$router->get('/admin/curso/nuevo', 'AdminViewModel@courseForm');
$router->get('/admin/curso/{courseId}', 'AdminViewModel@courseForm');
$router->post('/admin/curso/guardar', 'AdminViewModel@saveCourse');
$router->post('/admin/curso/{courseId}/guardar', 'AdminViewModel@saveCourse');
$router->get('/admin/usuarios', 'AdminViewModel@users');
$router->get('/admin/inscripciones', 'AdminViewModel@enrollments');
$router->get('/admin/reportes', 'AdminViewModel@reports');

// API routes for MVVM binding
$router->get('/api/courses', 'CourseApiViewModel@getAllCourses');
$router->get('/api/course/{id}', 'CourseApiViewModel@getCourse');
$router->get('/api/schedules/{courseId}', 'CourseApiViewModel@getSchedules');
$router->get('/api/dashboard-data', 'AdminApiViewModel@dashboardData');

// Calendar and scheduling routes
$router->get('/api/calendar/{courseId}', 'CalendarViewModel@getCourseSchedules');
$router->post('/api/calendar/add-to-calendar', 'CalendarViewModel@addToCalendar');

$router->resolve();
?>