<?php
require_once 'includes/viewmodel_base.php';
require_once 'models/Course.php';
require_once 'models/User.php';

class EnrollmentViewModel extends ViewModelBase {
    private $courseModel;
    private $userModel;
    
    protected function init() {
        $this->courseModel = new Course();
        $this->userModel = new User();
        
        // Set common data
        $this->bind('site_name', SITE_NAME);
        $this->bind('current_url', Router::currentUrl());
    }
    
    public function enroll($courseId) {
        // Check if user is authenticated
        if (!$this->isAuthenticated()) {
            $this->redirect('/login?redirect=' . urlencode('/inscripcion/' . $courseId));
            return;
        }
        
        $course = $this->courseModel->getById($courseId);
        if (!$course) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }
        
        $userId = $this->getSession('user_id');
        $scheduleId = $_GET['schedule'] ?? null;
        
        // Check if user can enroll
        $enrollmentCheck = $this->userModel->canEnroll($userId, $courseId);
        if (!$enrollmentCheck['can_enroll']) {
            $this->redirect('/curso/' . $course['slug'], $enrollmentCheck['reason'], 'error');
            return;
        }
        
        // Get available schedules
        $schedules = $this->courseModel->getAvailableSchedules($courseId);
        
        // Get selected schedule if provided
        $selectedSchedule = null;
        if ($scheduleId) {
            $selectedSchedule = $this->courseModel->getScheduleById($scheduleId);
        }
        
        // Get user profile for pre-filling form
        $userProfile = $this->userModel->getProfile($userId);
        
        // Calculate pricing
        $pricing = $this->calculatePricing($course, $userProfile);
        
        // Get payment methods
        $paymentMethods = $this->getAvailablePaymentMethods();
        
        $this->setData([
            'title' => 'Inscripción - ' . $course['title'],
            'description' => 'Inscríbete al curso ' . $course['title'] . ' y obtén tu certificación médica',
            'course' => $course,
            'schedules' => $schedules,
            'selected_schedule' => $selectedSchedule,
            'user_profile' => $userProfile,
            'pricing' => $pricing,
            'payment_methods' => $paymentMethods
        ]);
        
        $this->view('enrollment/form');
    }
    
    public function enrollSubmit($courseId) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/inscripcion/' . $courseId);
            return;
        }
        
        if (!$this->isAuthenticated()) {
            $this->redirect('/login');
            return;
        }
        
        $course = $this->courseModel->getById($courseId);
        if (!$course) {
            http_response_code(404);
            return;
        }
        
        $userId = $this->getSession('user_id');
        
        $rules = [
            'schedule_id' => 'required|numeric',
            'payment_method' => 'required|in:credit_card,paypal,bank_transfer,installments',
            'emergency_contact_name' => 'required|max:100',
            'emergency_contact_phone' => 'required|max:20',
            'medical_conditions' => 'max:1000',
            'dietary_restrictions' => 'max:500',
            'terms_accepted' => 'required'
        ];
        
        $data = $this->sanitize($_POST);
        
        if ($this->validate($data, $rules)) {
            try {
                // Start database transaction
                $this->db->beginTransaction();
                
                // Create enrollment record
                $enrollmentData = [
                    'user_id' => $userId,
                    'course_id' => $courseId,
                    'schedule_id' => $data['schedule_id'],
                    'enrollment_date' => date('Y-m-d H:i:s'),
                    'status' => 'pending_payment',
                    'emergency_contact_name' => $data['emergency_contact_name'],
                    'emergency_contact_phone' => $data['emergency_contact_phone'],
                    'medical_conditions' => $data['medical_conditions'] ?? '',
                    'dietary_restrictions' => $data['dietary_restrictions'] ?? '',
                    'special_requirements' => $data['special_requirements'] ?? '',
                    'payment_method' => $data['payment_method'],
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
                ];
                
                $enrollmentId = $this->createEnrollment($enrollmentData);
                
                // Process payment
                $paymentResult = $this->processPayment($enrollmentId, $data);
                
                if ($paymentResult['success']) {
                    // Update enrollment status
                    $this->updateEnrollmentStatus($enrollmentId, 'confirmed');
                    
                    // Send confirmation emails
                    $this->sendEnrollmentConfirmation($enrollmentId);
                    
                    // Commit transaction
                    $this->db->commit();
                    
                    $this->redirect('/inscripcion/confirmacion/' . $enrollmentId, 'Inscripción realizada exitosamente', 'success');
                } else {
                    // Rollback transaction
                    $this->db->rollback();
                    
                    $this->redirect('/inscripcion/' . $courseId, 'Error en el pago: ' . $paymentResult['message'], 'error');
                }
                
            } catch (Exception $e) {
                $this->db->rollback();
                error_log("Enrollment error: " . $e->getMessage());
                $this->redirect('/inscripcion/' . $courseId, 'Ocurrió un error. Inténtalo nuevamente.', 'error');
            }
        } else {
            $_SESSION['form_errors'] = $this->getErrors();
            $_SESSION['form_data'] = $data;
            $this->redirect('/inscripcion/' . $courseId);
        }
    }
    
    public function confirmation($enrollmentId) {
        if (!$this->isAuthenticated()) {
            $this->redirect('/login');
            return;
        }
        
        $userId = $this->getSession('user_id');
        $enrollment = $this->getEnrollmentDetails($enrollmentId, $userId);
        
        if (!$enrollment) {
            http_response_code(404);
            $this->view('errors/404');
            return;
        }
        
        // Generate QR code for certificate verification
        $qrCode = $this->generateQRCode($enrollmentId);
        
        $this->setData([
            'title' => 'Confirmación de Inscripción',
            'description' => 'Tu inscripción ha sido confirmada exitosamente',
            'enrollment' => $enrollment,
            'qr_code' => $qrCode
        ]);
        
        $this->view('enrollment/confirmation');
    }
    
    // Private helper methods
    private function calculatePricing($course, $userProfile) {
        $basePrice = $course['price'];
        $discounts = [];
        $total = $basePrice;
        
        // Student discount
        if ($userProfile['is_student']) {
            $discounts[] = [
                'type' => 'student',
                'name' => 'Descuento Estudiante',
                'amount' => $basePrice * 0.15,
                'percentage' => 15
            ];
            $total -= $basePrice * 0.15;
        }
        
        // Healthcare professional discount
        if ($userProfile['is_healthcare_professional']) {
            $discounts[] = [
                'type' => 'healthcare',
                'name' => 'Descuento Personal de Salud',
                'amount' => $basePrice * 0.10,
                'percentage' => 10
            ];
            $total -= $basePrice * 0.10;
        }
        
        // Group discount (if part of institutional enrollment)
        if ($userProfile['institutional_group_size'] >= 5) {
            $discounts[] = [
                'type' => 'group',
                'name' => 'Descuento Grupal',
                'amount' => $basePrice * 0.20,
                'percentage' => 20
            ];
            $total -= $basePrice * 0.20;
        }
        
        // Early bird discount (if course is more than 30 days away)
        $courseDate = strtotime($course['next_schedule_date']);
        if ($courseDate > time() + (30 * 24 * 60 * 60)) {
            $discounts[] = [
                'type' => 'early_bird',
                'name' => 'Descuento Reserva Temprana',
                'amount' => $basePrice * 0.08,
                'percentage' => 8
            ];
            $total -= $basePrice * 0.08;
        }
        
        // Tax calculation (16% IVA in Mexico)
        $tax = $total * 0.16;
        $finalTotal = $total + $tax;
        
        return [
            'base_price' => $basePrice,
            'discounts' => $discounts,
            'subtotal' => $total,
            'tax' => $tax,
            'tax_rate' => 16,
            'total' => $finalTotal
        ];
    }
    
    private function getAvailablePaymentMethods() {
        return [
            [
                'id' => 'credit_card',
                'name' => 'Tarjeta de Crédito/Débito',
                'description' => 'Visa, MasterCard, American Express',
                'icon' => 'fas fa-credit-card',
                'processing_fee' => 0.035, // 3.5%
                'available' => true
            ],
            [
                'id' => 'paypal',
                'name' => 'PayPal',
                'description' => 'Pago seguro con PayPal',
                'icon' => 'fab fa-paypal',
                'processing_fee' => 0.045, // 4.5%
                'available' => true
            ],
            [
                'id' => 'bank_transfer',
                'name' => 'Transferencia Bancaria',
                'description' => 'SPEI o depósito bancario',
                'icon' => 'fas fa-university',
                'processing_fee' => 0,
                'available' => true,
                'requires_validation' => true
            ],
            [
                'id' => 'installments',
                'name' => 'Pagos a Meses',
                'description' => '3, 6 o 12 meses sin intereses',
                'icon' => 'fas fa-calendar-alt',
                'processing_fee' => 0.02, // 2%
                'available' => true,
                'min_amount' => 2000
            ]
        ];
    }
    
    private function createEnrollment($data) {
        $sql = "INSERT INTO course_enrollments (" . implode(', ', array_keys($data)) . ") 
                VALUES (" . implode(', ', array_fill(0, count($data), '?')) . ")";
        
        if ($this->db->execute($sql, array_values($data))) {
            return $this->db->lastInsertId();
        }
        
        throw new Exception("Error creating enrollment");
    }
    
    private function processPayment($enrollmentId, $data) {
        // This would integrate with actual payment processors
        // For now, simulate payment processing
        
        $paymentMethod = $data['payment_method'];
        
        switch ($paymentMethod) {
            case 'credit_card':
                return $this->processCreditCardPayment($enrollmentId, $data);
            case 'paypal':
                return $this->processPayPalPayment($enrollmentId, $data);
            case 'bank_transfer':
                return $this->processBankTransferPayment($enrollmentId, $data);
            case 'installments':
                return $this->processInstallmentPayment($enrollmentId, $data);
            default:
                return ['success' => false, 'message' => 'Método de pago no válido'];
        }
    }
    
    private function processCreditCardPayment($enrollmentId, $data) {
        // Integrate with Stripe, Conekta, or other payment processor
        // For demo purposes, simulate success
        
        $paymentData = [
            'enrollment_id' => $enrollmentId,
            'payment_method' => 'credit_card',
            'amount' => $data['amount'],
            'currency' => 'MXN',
            'status' => 'completed',
            'transaction_id' => 'cc_' . uniqid(),
            'processed_at' => date('Y-m-d H:i:s'),
            'card_last_four' => substr($data['card_number'], -4),
            'card_brand' => $this->detectCardBrand($data['card_number'])
        ];
        
        $this->createPaymentRecord($paymentData);
        
        return ['success' => true, 'transaction_id' => $paymentData['transaction_id']];
    }
    
    private function processPayPalPayment($enrollmentId, $data) {
        // Integrate with PayPal API
        // Simulate success for demo
        
        $paymentData = [
            'enrollment_id' => $enrollmentId,
            'payment_method' => 'paypal',
            'amount' => $data['amount'],
            'currency' => 'MXN',
            'status' => 'completed',
            'transaction_id' => 'pp_' . uniqid(),
            'processed_at' => date('Y-m-d H:i:s'),
            'paypal_email' => $data['paypal_email'] ?? ''
        ];
        
        $this->createPaymentRecord($paymentData);
        
        return ['success' => true, 'transaction_id' => $paymentData['transaction_id']];
    }
    
    private function processBankTransferPayment($enrollmentId, $data) {
        // For bank transfers, create pending payment that requires manual validation
        
        $paymentData = [
            'enrollment_id' => $enrollmentId,
            'payment_method' => 'bank_transfer',
            'amount' => $data['amount'],
            'currency' => 'MXN',
            'status' => 'pending_validation',
            'transaction_id' => 'bt_' . uniqid(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->createPaymentRecord($paymentData);
        
        return ['success' => true, 'transaction_id' => $paymentData['transaction_id'], 'requires_validation' => true];
    }
    
    private function processInstallmentPayment($enrollmentId, $data) {
        // Create installment payment plan
        
        $totalAmount = $data['amount'];
        $installments = $data['installment_months'];
        $monthlyAmount = $totalAmount / $installments;
        
        // Create main payment record
        $paymentData = [
            'enrollment_id' => $enrollmentId,
            'payment_method' => 'installments',
            'amount' => $totalAmount,
            'currency' => 'MXN',
            'status' => 'partial',
            'transaction_id' => 'inst_' . uniqid(),
            'processed_at' => date('Y-m-d H:i:s'),
            'installment_plan' => json_encode([
                'total_installments' => $installments,
                'monthly_amount' => $monthlyAmount,
                'start_date' => date('Y-m-d')
            ])
        ];
        
        $this->createPaymentRecord($paymentData);
        
        return ['success' => true, 'transaction_id' => $paymentData['transaction_id'], 'is_installment' => true];
    }
    
    private function createPaymentRecord($data) {
        $sql = "INSERT INTO payments (" . implode(', ', array_keys($data)) . ") 
                VALUES (" . implode(', ', array_fill(0, count($data), '?')) . ")";
        
        return $this->db->execute($sql, array_values($data));
    }
    
    private function updateEnrollmentStatus($enrollmentId, $status) {
        $sql = "UPDATE course_enrollments SET status = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->execute($sql, [$status, $enrollmentId]);
    }
    
    private function sendEnrollmentConfirmation($enrollmentId) {
        // Send confirmation email to student
        // Send notification to instructors/admin
        // This would integrate with an email service
        return true;
    }
    
    private function getEnrollmentDetails($enrollmentId, $userId) {
        $sql = "SELECT e.*, c.title as course_title, c.slug as course_slug, 
                       s.start_date, s.end_date, s.location,
                       u.first_name, u.last_name, u.email
                FROM course_enrollments e
                JOIN courses c ON e.course_id = c.id
                JOIN course_schedules s ON e.schedule_id = s.id
                JOIN users u ON e.user_id = u.id
                WHERE e.id = ? AND e.user_id = ?";
                
        return $this->db->fetchOne($sql, [$enrollmentId, $userId]);
    }
    
    private function generateQRCode($enrollmentId) {
        // Generate QR code for certificate verification
        // This would use a QR code library
        $verificationUrl = SITE_URL . '/verificar-certificado/' . $enrollmentId;
        
        // For demo, return the URL that would be encoded
        return $verificationUrl;
    }
    
    private function detectCardBrand($cardNumber) {
        $cardNumber = preg_replace('/\D/', '', $cardNumber);
        
        if (preg_match('/^4/', $cardNumber)) {
            return 'Visa';
        } elseif (preg_match('/^5[1-5]/', $cardNumber)) {
            return 'MasterCard';
        } elseif (preg_match('/^3[47]/', $cardNumber)) {
            return 'American Express';
        }
        
        return 'Unknown';
    }
}
?>