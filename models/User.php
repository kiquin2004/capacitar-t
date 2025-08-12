<?php
require_once 'models/BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Create user with password hashing
    public function createUser($data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        // Set default values
        $data['status'] = $data['status'] ?? 'pending';
        $data['role'] = $data['role'] ?? 'student';
        $data['email_verified'] = false;
        
        return $this->create($data);
    }
    
    // Find user by email
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
        return $this->db->fetchOne($sql, [$email]);
    }
    
    // Authenticate user
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'active') {
                // Update last login
                $this->update($user['id'], [
                    'last_login' => date('Y-m-d H:i:s'),
                    'login_attempts' => 0
                ]);
                
                return $user;
            } else {
                return ['error' => 'Cuenta inactiva. Contacta al administrador.'];
            }
        } else {
            // Increment login attempts
            if ($user) {
                $attempts = ($user['login_attempts'] ?? 0) + 1;
                $this->update($user['id'], ['login_attempts' => $attempts]);
                
                if ($attempts >= 5) {
                    $this->update($user['id'], ['status' => 'suspended']);
                    return ['error' => 'Cuenta suspendida por múltiples intentos fallidos.'];
                }
            }
            
            return false;
        }
    }
    
    // Get user profile with enrollment statistics
    public function getProfile($userId) {
        $sql = "SELECT u.*, 
                       COUNT(ce.id) as total_enrollments,
                       COUNT(CASE WHEN ce.status = 'completed' THEN 1 END) as completed_courses,
                       COUNT(CASE WHEN ce.status = 'confirmed' THEN 1 END) as upcoming_courses,
                       COUNT(CASE WHEN ce.certificate_issued = 1 THEN 1 END) as certificates_earned
                FROM users u
                LEFT JOIN course_enrollments ce ON u.id = ce.user_id
                WHERE u.id = ?
                GROUP BY u.id";
        
        return $this->db->fetchOne($sql, [$userId]);
    }
    
    // Get user's enrollments
    public function getEnrollments($userId, $status = null) {
        $sql = "SELECT ce.*, 
                       c.title as course_title, c.slug as course_slug, c.course_code,
                       cs.start_date, cs.end_date, cs.start_time, cs.end_time, cs.location,
                       cat.name as category_name, cat.color as category_color
                FROM course_enrollments ce
                JOIN course_schedules cs ON ce.schedule_id = cs.id
                JOIN courses c ON cs.course_id = c.id
                JOIN course_categories cat ON c.category_id = cat.id
                WHERE ce.user_id = ?";
        
        $params = [$userId];
        
        if ($status) {
            $sql .= " AND ce.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY cs.start_date DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Get user's certificates
    public function getCertificates($userId) {
        $sql = "SELECT ce.certificate_number, ce.certificate_issue_date, ce.certificate_expiry_date,
                       ce.final_score, c.title as course_title, c.certification_type,
                       c.certification_body, c.course_code
                FROM course_enrollments ce
                JOIN course_schedules cs ON ce.schedule_id = cs.id
                JOIN courses c ON cs.course_id = c.id
                WHERE ce.user_id = ? AND ce.certificate_issued = 1
                ORDER BY ce.certificate_issue_date DESC";
        
        return $this->db->fetchAll($sql, [$userId]);
    }
    
    // Update user password
    public function changePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        return $this->update($userId, [
            'password' => $hashedPassword,
            'password_changed_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    // Verify email address
    public function verifyEmail($userId) {
        return $this->update($userId, [
            'email_verified' => true,
            'email_verified_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    // Get users by profession
    public function getByProfession($profession) {
        return $this->findAll(['profession' => $profession, 'status' => 'active'], 'created_at DESC');
    }
    
    // Get users by demographic
    public function getByDemographic($demographic) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE target_demographic = ? AND status = 'active'
                ORDER BY created_at DESC";
        
        return $this->db->fetchAll($sql, [$demographic]);
    }
    
    // Get user statistics
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total_users,
                    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_users,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_users,
                    COUNT(CASE WHEN role = 'professional' THEN 1 END) as professionals,
                    COUNT(CASE WHEN role = 'instructor' THEN 1 END) as instructors,
                    COUNT(CASE WHEN target_demographic = 'GEN_X' THEN 1 END) as gen_x,
                    COUNT(CASE WHEN target_demographic = 'MILLENNIALS' THEN 1 END) as millennials,
                    COUNT(CASE WHEN target_demographic = 'GEN_BETA' THEN 1 END) as gen_beta,
                    COUNT(CASE WHEN email_verified = 1 THEN 1 END) as verified_emails
                FROM {$this->table}";
        
        return $this->db->fetchOne($sql);
    }
    
    // Search users
    public function search($searchTerm, $filters = []) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR institution LIKE ?)";
        
        $searchParam = '%' . $searchTerm . '%';
        $params = [$searchParam, $searchParam, $searchParam, $searchParam];
        
        if (!empty($filters['profession'])) {
            $sql .= " AND profession = ?";
            $params[] = $filters['profession'];
        }
        
        if (!empty($filters['role'])) {
            $sql .= " AND role = ?";
            $params[] = $filters['role'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND status = ?";
            $params[] = $filters['status'];
        }
        
        $sql .= " ORDER BY last_name, first_name";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    // Update last activity
    public function updateLastActivity($userId) {
        return $this->update($userId, ['last_activity' => date('Y-m-d H:i:s')]);
    }
    
    // Get recent registrations
    public function getRecentRegistrations($days = 7, $limit = 10) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
                ORDER BY created_at DESC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$days, $limit]);
    }
    
    // Check if user can enroll in course
    public function canEnroll($userId, $courseId) {
        // Check if user already enrolled
        $sql = "SELECT COUNT(*) as count FROM course_enrollments ce
                JOIN course_schedules cs ON ce.schedule_id = cs.id
                WHERE ce.user_id = ? AND cs.course_id = ? 
                AND ce.status IN ('pending', 'confirmed', 'completed')";
        
        $result = $this->db->fetchOne($sql, [$userId, $courseId]);
        
        if ($result['count'] > 0) {
            return ['can_enroll' => false, 'reason' => 'Ya estás inscrito en este curso'];
        }
        
        // Check prerequisites (if needed)
        // This would require additional business logic
        
        return ['can_enroll' => true];
    }
    
    // Get user's learning path recommendations
    public function getRecommendations($userId) {
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color
                FROM courses c
                JOIN course_categories cat ON c.category_id = cat.id
                JOIN users u ON u.id = ?
                WHERE c.status = 'published'
                AND c.target_audience LIKE CONCAT('%', u.profession, '%')
                AND c.id NOT IN (
                    SELECT cs.course_id FROM course_enrollments ce
                    JOIN course_schedules cs ON ce.schedule_id = cs.id
                    WHERE ce.user_id = ? AND ce.status IN ('completed', 'confirmed')
                )
                ORDER BY c.featured DESC, c.created_at DESC
                LIMIT 6";
        
        return $this->db->fetchAll($sql, [$userId, $userId]);
    }
    
    // Check if user is instructor
    public function isInstructor($userId) {
        $sql = "SELECT COUNT(*) as count FROM instructors WHERE user_id = ? AND is_active = 1";
        $result = $this->db->fetchOne($sql, [$userId]);
        return $result['count'] > 0;
    }
    
    // Get instructor profile
    public function getInstructorProfile($userId) {
        $sql = "SELECT i.*, u.first_name, u.last_name, u.email, u.profession
                FROM instructors i
                JOIN users u ON i.user_id = u.id
                WHERE i.user_id = ? AND i.is_active = 1";
        
        return $this->db->fetchOne($sql, [$userId]);
    }
}
?>