<?php
require_once 'includes/viewmodel_base.php';
require_once 'models/Course.php';

class CourseApiViewModel extends ViewModelBase {
    private $courseModel;
    
    protected function init() {
        $this->courseModel = new Course();
    }
    
    public function getAllCourses() {
        try {
            $filters = [
                'category_slug' => $_GET['category'] ?? null,
                'course_type' => $_GET['type'] ?? null,
                'search' => $_GET['search'] ?? null,
                'difficulty_level' => $_GET['difficulty'] ?? null,
                'certification_body' => $_GET['certification'] ?? null,
                'limit' => isset($_GET['limit']) ? intval($_GET['limit']) : null
            ];
            
            // Remove null values
            $filters = array_filter($filters, function($value) {
                return $value !== null && $value !== '';
            });
            
            $courses = $this->courseModel->getAllWithCategories($filters);
            
            $this->json([
                'success' => true,
                'data' => $courses,
                'total' => count($courses)
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'error' => 'Error al obtener los cursos',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getCourse($id) {
        try {
            if (!$id || !is_numeric($id)) {
                $this->json([
                    'success' => false,
                    'error' => 'ID de curso inválido'
                ], 400);
                return;
            }
            
            $course = $this->courseModel->getWithDetails($id);
            
            if (!$course) {
                $this->json([
                    'success' => false,
                    'error' => 'Curso no encontrado'
                ], 404);
                return;
            }
            
            $this->json([
                'success' => true,
                'data' => $course
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'error' => 'Error al obtener el curso',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getSchedules($courseId) {
        try {
            if (!$courseId || !is_numeric($courseId)) {
                $this->json([
                    'success' => false,
                    'error' => 'ID de curso inválido'
                ], 400);
                return;
            }
            
            $schedules = $this->courseModel->getUpcomingSchedules(null, $courseId);
            
            $this->json([
                'success' => true,
                'data' => $schedules,
                'total' => count($schedules)
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'error' => 'Error al obtener los horarios',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
?>