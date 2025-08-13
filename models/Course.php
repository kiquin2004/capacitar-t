<?php
require_once 'models/BaseModel.php';

class Course extends BaseModel {
    protected $table = 'courses';
    
    public function __construct() {
        parent::__construct();
    }

    // Get all courses with category information
    public function getAllWithCategories($filters = []) {
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color,
                       cat.icon as category_icon, cat.slug as category_slug, cat.course_type
                FROM courses c 
                JOIN course_categories cat ON c.category_id = cat.id 
                WHERE c.status = 'published'";
        
        $params = [];
        
        if (!empty($filters['category_slug'])) {
            $sql .= " AND cat.slug = ?";
            $params[] = $filters['category_slug'];
        }
        
        if (!empty($filters['course_type'])) {
            $sql .= " AND cat.course_type = ?";
            $params[] = $filters['course_type'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (c.title LIKE ? OR c.short_description LIKE ? OR c.target_audience LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        if (!empty($filters['difficulty_level'])) {
            $sql .= " AND c.difficulty_level = ?";
            $params[] = $filters['difficulty_level'];
        }
        
        if (!empty($filters['certification_body'])) {
            $sql .= " AND c.certification_body = ?";
            $params[] = $filters['certification_body'];
        }
        
        $sql .= " ORDER BY c.featured DESC, c.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . intval($filters['limit']);
            if (!empty($filters['offset'])) {
                $sql .= " OFFSET " . intval($filters['offset']);
            }
        }
        
        return $this->db->fetchAll($sql, $params);
    }

    // Get featured courses
    public function getFeatured($limit = 6) {
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color,
                       cat.icon as category_icon, cat.slug as category_slug, cat.course_type
                FROM courses c 
                JOIN course_categories cat ON c.category_id = cat.id 
                WHERE c.featured = 1 AND c.status = 'published' 
                ORDER BY c.created_at DESC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$limit]);
    }

    // Get courses by type (MEDICAL_PROFESSIONAL, COMMUNITY_FIRST_AID, MEDICAL_MANAGEMENT)
    public function getByType($courseType) {
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color,
                       cat.icon as category_icon, cat.slug as category_slug, cat.course_type
                FROM courses c 
                JOIN course_categories cat ON c.category_id = cat.id 
                WHERE cat.course_type = ? AND c.status = 'published'
                ORDER BY c.featured DESC, c.sort_order, c.created_at DESC";
        
        return $this->db->fetchAll($sql, [$courseType]);
    }

    // Get course by slug with full details
    public function getBySlug($slug) {
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color,
                       cat.icon as category_icon, cat.slug as category_slug, cat.course_type
                FROM courses c 
                JOIN course_categories cat ON c.category_id = cat.id 
                WHERE c.slug = ? AND c.status = 'published'";
        
        return $this->db->fetchOne($sql, [$slug]);
    }

    // Get course with full details including modules, equipment, schedules
    public function getWithDetails($courseId) {
        $course = $this->findById($courseId);
        if (!$course) {
            return null;
        }
        
        // Get category information
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color,
                       cat.icon as category_icon, cat.slug as category_slug, cat.course_type
                FROM courses c 
                JOIN course_categories cat ON c.category_id = cat.id 
                WHERE c.id = ?";
        
        $course = $this->db->fetchOne($sql, [$courseId]);
        
        if ($course) {
            // Get modules
            $course['modules'] = $this->getModules($courseId);
            
            // Get equipment
            $course['equipment'] = $this->getEquipment($courseId);
            
            // Get upcoming schedules
            $course['schedules'] = $this->getSchedules($courseId);
            
            // Get related courses
            $course['related'] = $this->getRelated($courseId, $course['category_id']);
        }
        
        return $course;
    }

    // Get course modules
    public function getModules($courseId) {
        $sql = "SELECT * FROM course_modules 
                WHERE course_id = ? 
                ORDER BY sort_order, id";
        
        return $this->db->fetchAll($sql, [$courseId]);
    }

    // Get course equipment
    public function getEquipment($courseId) {
        $sql = "SELECT * FROM course_equipment 
                WHERE course_id = ? 
                ORDER BY equipment_name";
        
        return $this->db->fetchAll($sql, [$courseId]);
    }

    // Get course schedules
    public function getSchedules($courseId, $includeInstructor = true) {
        $sql = "SELECT cs.*, 
                       u.first_name as instructor_first_name, u.last_name as instructor_last_name,
                       u2.first_name as co_instructor_first_name, u2.last_name as co_instructor_last_name";
        
        if ($includeInstructor) {
            $sql .= " FROM course_schedules cs
                      LEFT JOIN users u ON cs.instructor_id = u.id
                      LEFT JOIN users u2 ON cs.co_instructor_id = u2.id";
        } else {
            $sql .= " FROM course_schedules cs";
        }
        
        $sql .= " WHERE cs.course_id = ? AND cs.start_date >= CURDATE() AND cs.status = 'scheduled'
                  ORDER BY cs.start_date, cs.start_time";
        
        return $this->db->fetchAll($sql, [$courseId]);
    }

    // Get upcoming schedules across all courses or for specific course
    public function getUpcomingSchedules($limit = 6, $courseId = null) {
        $sql = "SELECT cs.*, c.title as course_title, c.slug as course_slug,
                       c.course_code, c.duration_hours, c.price,
                       cat.name as category_name, cat.color as category_color,
                       CONCAT(u.first_name, ' ', u.last_name) as instructor_name
                FROM course_schedules cs
                JOIN courses c ON cs.course_id = c.id
                JOIN course_categories cat ON c.category_id = cat.id
                LEFT JOIN users u ON cs.instructor_id = u.id
                WHERE cs.start_date >= CURDATE() AND cs.status = 'scheduled' AND c.status = 'published'";
        
        $params = [];
        
        if ($courseId !== null) {
            $sql .= " AND cs.course_id = ?";
            $params[] = $courseId;
        }
        
        $sql .= " ORDER BY cs.start_date, cs.start_time";
        
        if ($limit !== null) {
            $sql .= " LIMIT ?";
            $params[] = $limit;
        }
        
        return $this->db->fetchAll($sql, $params);
    }

    // Get courses by medical specialization
    public function getBySpecialization($specialization) {
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color
                FROM courses c 
                JOIN course_categories cat ON c.category_id = cat.id 
                WHERE FIND_IN_SET(?, c.medical_specialization) > 0 AND c.status = 'published'
                ORDER BY c.featured DESC, c.created_at DESC";
        
        return $this->db->fetchAll($sql, [$specialization]);
    }

    // Get course statistics
    public function getStats() {
        $sql = "SELECT 
                    COUNT(*) as total_courses,
                    COUNT(CASE WHEN featured = 1 THEN 1 END) as featured_courses,
                    COUNT(CASE WHEN difficulty_level = 'BASIC' THEN 1 END) as basic_courses,
                    COUNT(CASE WHEN difficulty_level = 'INTERMEDIATE' THEN 1 END) as intermediate_courses,
                    COUNT(CASE WHEN difficulty_level = 'ADVANCED' THEN 1 END) as advanced_courses,
                    COUNT(CASE WHEN certification_body = 'AHA' THEN 1 END) as aha_courses,
                    AVG(price) as average_price,
                    AVG(duration_hours) as average_duration
                FROM courses 
                WHERE status = 'published'";
        
        return $this->db->fetchOne($sql);
    }

    // Search courses with advanced filters
    public function search($searchTerm, $filters = [], $page = 1, $limit = 12) {
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color,
                       cat.icon as category_icon, cat.slug as category_slug, cat.course_type,
                       MATCH(c.title, c.short_description, c.full_description) AGAINST(? IN NATURAL LANGUAGE MODE) as relevance
                FROM courses c 
                JOIN course_categories cat ON c.category_id = cat.id 
                WHERE c.status = 'published' AND (
                    MATCH(c.title, c.short_description, c.full_description) AGAINST(? IN NATURAL LANGUAGE MODE)
                    OR c.title LIKE ? 
                    OR c.course_code LIKE ?
                )";
        
        $params = [$searchTerm, $searchTerm, '%' . $searchTerm . '%', '%' . $searchTerm . '%'];
        
        // Apply additional filters
        if (!empty($filters['course_type'])) {
            $sql .= " AND cat.course_type = ?";
            $params[] = $filters['course_type'];
        }
        
        if (!empty($filters['difficulty_level'])) {
            $sql .= " AND c.difficulty_level = ?";
            $params[] = $filters['difficulty_level'];
        }
        
        if (!empty($filters['price_range'])) {
            switch ($filters['price_range']) {
                case 'under_1000':
                    $sql .= " AND c.price < 1000";
                    break;
                case '1000_3000':
                    $sql .= " AND c.price BETWEEN 1000 AND 3000";
                    break;
                case 'over_3000':
                    $sql .= " AND c.price > 3000";
                    break;
            }
        }
        
        $sql .= " ORDER BY relevance DESC, c.featured DESC, c.created_at DESC
                  LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }

    // Get total count for pagination
    public function getTotalCount($filters = []) {
        $sql = "SELECT COUNT(*) as total
                FROM courses c 
                JOIN course_categories cat ON c.category_id = cat.id 
                WHERE c.status = 'published'";
        
        $params = [];
        
        if (!empty($filters['category_slug'])) {
            $sql .= " AND cat.slug = ?";
            $params[] = $filters['category_slug'];
        }
        
        if (!empty($filters['course_type'])) {
            $sql .= " AND cat.course_type = ?";
            $params[] = $filters['course_type'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (c.title LIKE ? OR c.short_description LIKE ? OR c.target_audience LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['total'];
    }

    // Get related courses
    public function getRelated($courseId, $categoryId, $limit = 3) {
        $sql = "SELECT c.*, cat.name as category_name, cat.color as category_color
                FROM courses c 
                JOIN course_categories cat ON c.category_id = cat.id 
                WHERE c.category_id = ? AND c.id != ? AND c.status = 'published'
                ORDER BY c.featured DESC, c.created_at DESC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$categoryId, $courseId, $limit]);
    }

    // Check course availability
    public function isAvailable($courseId) {
        $sql = "SELECT COUNT(*) as available_schedules
                FROM course_schedules 
                WHERE course_id = ? AND start_date >= CURDATE() AND status = 'scheduled' 
                AND available_spots > registered_count";
        
        $result = $this->db->fetchOne($sql, [$courseId]);
        return $result['available_schedules'] > 0;
    }
}
?>