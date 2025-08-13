<?php
require_once 'includes/viewmodel_base.php';
require_once 'models/Course.php';

class CalendarViewModel extends ViewModelBase {
    private $courseModel;
    
    protected function init() {
        $this->courseModel = new Course();
    }
    
    public function getCourseSchedules($courseId) {
        try {
            if (!$courseId || !is_numeric($courseId)) {
                $this->json([
                    'success' => false,
                    'error' => 'ID de curso inválido'
                ], 400);
                return;
            }
            
            $schedules = $this->courseModel->getUpcomingSchedules(null, $courseId);
            
            // Format schedules for calendar display
            $calendarEvents = [];
            foreach ($schedules as $schedule) {
                $calendarEvents[] = [
                    'id' => $schedule['id'],
                    'title' => $schedule['course_title'] ?? 'Curso',
                    'start' => $schedule['start_date'] . 'T' . $schedule['start_time'],
                    'end' => $schedule['end_date'] . 'T' . $schedule['end_time'],
                    'location' => $schedule['location'],
                    'available_spots' => $schedule['available_spots'],
                    'registered_count' => $schedule['registered_count'],
                    'price' => $schedule['price'] ?? 0,
                    'instructor' => $schedule['instructor_name'] ?? null,
                    'status' => $schedule['status'],
                    'backgroundColor' => $this->getEventColor($schedule['status']),
                    'borderColor' => $this->getEventColor($schedule['status']),
                    'textColor' => '#ffffff'
                ];
            }
            
            $this->json([
                'success' => true,
                'data' => $calendarEvents,
                'total' => count($calendarEvents)
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'error' => 'Error al obtener horarios del curso',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function addToCalendar() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input || !isset($input['schedule_id'])) {
                $this->json([
                    'success' => false,
                    'error' => 'Datos de horario requeridos'
                ], 400);
                return;
            }
            
            $scheduleId = intval($input['schedule_id']);
            $format = $input['format'] ?? 'google'; // google, outlook, ics
            
            // Get schedule details
            $schedule = $this->getScheduleDetails($scheduleId);
            
            if (!$schedule) {
                $this->json([
                    'success' => false,
                    'error' => 'Horario no encontrado'
                ], 404);
                return;
            }
            
            $calendarUrl = $this->generateCalendarUrl($schedule, $format);
            
            $this->json([
                'success' => true,
                'data' => [
                    'url' => $calendarUrl,
                    'format' => $format,
                    'schedule' => $schedule
                ]
            ]);
            
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'error' => 'Error al generar enlace de calendario',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    private function getEventColor($status) {
        switch ($status) {
            case 'scheduled':
                return '#28a745'; // Green
            case 'in_progress':
                return '#007bff'; // Blue
            case 'completed':
                return '#6c757d'; // Gray
            case 'cancelled':
                return '#dc3545'; // Red
            case 'postponed':
                return '#fd7e14'; // Orange
            default:
                return '#6c757d'; // Gray
        }
    }
    
    private function getScheduleDetails($scheduleId) {
        try {
            $sql = "SELECT 
                        cs.id, cs.start_date, cs.end_date, cs.start_time, cs.end_time,
                        cs.location, cs.address, cs.available_spots, cs.registered_count,
                        c.title as course_title, c.short_description, c.price,
                        CONCAT(i.first_name, ' ', i.last_name) as instructor_name
                    FROM course_schedules cs
                    JOIN courses c ON cs.course_id = c.id
                    LEFT JOIN users i ON cs.instructor_id = i.id
                    WHERE cs.id = ?";
            
            return $this->db->fetchOne($sql, [$scheduleId]);
            
        } catch (Exception $e) {
            error_log("Error getting schedule details: " . $e->getMessage());
            return null;
        }
    }
    
    private function generateCalendarUrl($schedule, $format) {
        $title = urlencode($schedule['course_title']);
        $startDateTime = date('Ymd\THis', strtotime($schedule['start_date'] . ' ' . $schedule['start_time']));
        $endDateTime = date('Ymd\THis', strtotime($schedule['end_date'] . ' ' . $schedule['end_time']));
        
        $description = urlencode(
            $schedule['short_description'] . "\n\n" .
            "Instructor: " . ($schedule['instructor_name'] ?? 'Por confirmar') . "\n" .
            "Ubicación: " . $schedule['location']
        );
        
        $location = urlencode($schedule['location'] . ', ' . ($schedule['address'] ?? ''));
        
        switch ($format) {
            case 'google':
                return "https://calendar.google.com/calendar/render?action=TEMPLATE" .
                       "&text={$title}" .
                       "&dates={$startDateTime}/{$endDateTime}" .
                       "&details={$description}" .
                       "&location={$location}";
            
            case 'outlook':
                return "https://outlook.live.com/calendar/0/deeplink/compose?subject={$title}" .
                       "&startdt={$startDateTime}" .
                       "&enddt={$endDateTime}" .
                       "&body={$description}" .
                       "&location={$location}";
            
            case 'ics':
                // For ICS format, we would generate and return a .ics file
                // This is a simplified URL that points to an ICS generator endpoint
                return SITE_URL . "/api/calendar/ics/{$schedule['id']}";
            
            default:
                return null;
        }
    }
}
?>