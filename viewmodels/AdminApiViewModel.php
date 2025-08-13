<?php
require_once 'includes/viewmodel_base.php';
require_once 'models/Course.php';
require_once 'models/User.php';

class AdminApiViewModel extends ViewModelBase {
    private $courseModel;
    private $userModel;
    
    protected function init() {
        $this->courseModel = new Course();
        $this->userModel = new User();
        
        // Require admin authentication for all admin API endpoints
        $this->requireAuth();
        $this->requireAdminRole();
    }
    
    public function dashboardData() {
        try {
            // Get comprehensive dashboard statistics
            $courseStats = $this->courseModel->getStats();
            $userStats = $this->userModel->getStats();
            $enrollmentStats = $this->getEnrollmentStats();
            $revenueStats = $this->getRevenueStats();
            
            $dashboardData = [
                'courses' => [
                    'total' => $courseStats['total_courses'] ?? 0,
                    'published' => $courseStats['published_courses'] ?? 0,
                    'draft' => $courseStats['draft_courses'] ?? 0,
                    'featured' => $courseStats['featured_courses'] ?? 0
                ],
                'users' => [
                    'total' => $userStats['total_users'] ?? 0,
                    'active' => $userStats['active_users'] ?? 0,
                    'professionals' => $userStats['professionals'] ?? 0,
                    'students' => $userStats['students'] ?? 0,
                    'new_this_month' => $userStats['new_this_month'] ?? 0
                ],
                'enrollments' => [
                    'total' => $enrollmentStats['total'] ?? 0,
                    'confirmed' => $enrollmentStats['confirmed'] ?? 0,
                    'completed' => $enrollmentStats['completed'] ?? 0,
                    'pending' => $enrollmentStats['pending'] ?? 0,
                    'this_month' => $enrollmentStats['this_month'] ?? 0
                ],
                'revenue' => [
                    'total' => $revenueStats['total'] ?? 0,
                    'this_month' => $revenueStats['this_month'] ?? 0,
                    'last_month' => $revenueStats['last_month'] ?? 0,
                    'growth_rate' => $revenueStats['growth_rate'] ?? 0
                ],
                'recent_activities' => $this->getRecentActivities(),
                'top_courses' => $this->getTopCourses(),
                'upcoming_schedules' => $this->courseModel->getUpcomingSchedules(10)
            ];
            
            $this->json([
                'success' => true,
                'data' => $dashboardData
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'error' => 'Error al obtener datos del dashboard',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    private function requireAdminRole() {
        $user = $this->getSession('user');
        if (!$user || $user['role'] !== 'admin') {
            $this->json([
                'success' => false,
                'error' => 'Acceso denegado. Se requieren permisos de administrador.'
            ], 403);
            exit;
        }
    }
    
    private function getEnrollmentStats() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN DATE(enrollment_date) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) THEN 1 ELSE 0 END) as this_month
                    FROM course_enrollments";
            
            return $this->db->fetchOne($sql) ?: [];
            
        } catch (Exception $e) {
            error_log("Error getting enrollment stats: " . $e->getMessage());
            return [];
        }
    }
    
    private function getRevenueStats() {
        try {
            $sql = "SELECT 
                        SUM(amount_paid) as total,
                        SUM(CASE WHEN DATE(enrollment_date) >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH) THEN amount_paid ELSE 0 END) as this_month,
                        SUM(CASE WHEN DATE(enrollment_date) >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) 
                                 AND DATE(enrollment_date) < DATE_SUB(CURDATE(), INTERVAL 1 MONTH) THEN amount_paid ELSE 0 END) as last_month
                    FROM course_enrollments 
                    WHERE payment_status = 'paid'";
            
            $stats = $this->db->fetchOne($sql) ?: [];
            
            // Calculate growth rate
            if ($stats && $stats['last_month'] > 0) {
                $stats['growth_rate'] = (($stats['this_month'] - $stats['last_month']) / $stats['last_month']) * 100;
            } else {
                $stats['growth_rate'] = 0;
            }
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Error getting revenue stats: " . $e->getMessage());
            return [];
        }
    }
    
    private function getRecentActivities() {
        try {
            $sql = "SELECT 
                        'enrollment' as type,
                        CONCAT(u.first_name, ' ', u.last_name) as description,
                        c.title as details,
                        e.enrollment_date as created_at
                    FROM course_enrollments e
                    JOIN users u ON e.user_id = u.id
                    JOIN course_schedules cs ON e.schedule_id = cs.id
                    JOIN courses c ON cs.course_id = c.id
                    ORDER BY e.enrollment_date DESC
                    LIMIT 10";
            
            return $this->db->fetchAll($sql) ?: [];
            
        } catch (Exception $e) {
            error_log("Error getting recent activities: " . $e->getMessage());
            return [];
        }
    }
    
    private function getTopCourses() {
        try {
            $sql = "SELECT 
                        c.title,
                        c.slug,
                        COUNT(e.id) as enrollment_count,
                        AVG(e.amount_paid) as avg_revenue,
                        SUM(e.amount_paid) as total_revenue
                    FROM courses c
                    LEFT JOIN course_schedules cs ON c.id = cs.course_id
                    LEFT JOIN course_enrollments e ON cs.id = e.schedule_id
                    WHERE c.status = 'published'
                    GROUP BY c.id
                    ORDER BY enrollment_count DESC, total_revenue DESC
                    LIMIT 5";
            
            return $this->db->fetchAll($sql) ?: [];
            
        } catch (Exception $e) {
            error_log("Error getting top courses: " . $e->getMessage());
            return [];
        }
    }
}
?>