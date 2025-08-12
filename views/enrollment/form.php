<?php include VIEW_PATH . '/includes/header.php'; ?>

<section class="enrollment-hero">
    <div class="container">
        <div class="enrollment-header">
            <div class="course-info-summary">
                <h1>Inscripción al Curso</h1>
                <div class="course-summary-card">
                    <img src="<?php echo SITE_URL; ?>/assets/images/courses/<?php echo $course['featured_image'] ?? 'default-course.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($course['title']); ?>" 
                         class="course-thumb">
                    
                    <div class="course-summary-info">
                        <h2><?php echo htmlspecialchars($course['title']); ?></h2>
                        <p><?php echo htmlspecialchars($course['short_description']); ?></p>
                        
                        <div class="course-badges">
                            <?php if ($course['is_aha_certified']): ?>
                            <span class="badge aha-certified">AHA Certified</span>
                            <?php endif; ?>
                            <span class="badge duration"><?php echo $course['duration_hours']; ?> horas</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="enrollment-form-section">
    <div class="container">
        <div class="enrollment-grid">
            <div class="enrollment-form-container">
                <form class="enrollment-form" method="POST" action="/inscripcion/<?php echo $course['id']; ?>">
                    <input type="hidden" name="_csrf_token" value="<?php echo $_SESSION['_csrf_token']; ?>">
                    
                    <!-- Step 1: Schedule Selection -->
                    <div class="form-step active" id="step-schedule">
                        <div class="step-header">
                            <h3>
                                <span class="step-number">1</span>
                                Selecciona tu Horario
                            </h3>
                            <p>Elige la fecha y modalidad que mejor se adapte a tu agenda</p>
                        </div>

                        <div class="schedules-selection">
                            <?php foreach ($schedules as $schedule): ?>
                            <div class="schedule-option">
                                <input type="radio" 
                                       name="schedule_id" 
                                       value="<?php echo $schedule['id']; ?>" 
                                       id="schedule-<?php echo $schedule['id']; ?>"
                                       <?php echo ($selected_schedule && $selected_schedule['id'] == $schedule['id']) ? 'checked' : ''; ?>
                                       required>
                                
                                <label for="schedule-<?php echo $schedule['id']; ?>" class="schedule-card">
                                    <div class="schedule-date">
                                        <span class="day"><?php echo date('d', strtotime($schedule['start_date'])); ?></span>
                                        <span class="month"><?php echo date('M Y', strtotime($schedule['start_date'])); ?></span>
                                    </div>
                                    
                                    <div class="schedule-details">
                                        <h4><?php echo htmlspecialchars($schedule['modality']); ?></h4>
                                        <div class="schedule-time">
                                            <i class="fas fa-clock" aria-hidden="true"></i>
                                            <?php echo date('H:i', strtotime($schedule['start_time'])); ?> - 
                                            <?php echo date('H:i', strtotime($schedule['end_time'])); ?>
                                        </div>
                                        
                                        <div class="schedule-location">
                                            <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                            <?php echo htmlspecialchars($schedule['location']); ?>
                                        </div>
                                        
                                        <div class="schedule-instructor">
                                            <i class="fas fa-user-tie" aria-hidden="true"></i>
                                            Instructor: <?php echo htmlspecialchars($schedule['instructor_name']); ?>
                                        </div>
                                        
                                        <div class="schedule-availability">
                                            <?php if ($schedule['available_spots'] > 0): ?>
                                            <span class="availability available">
                                                <i class="fas fa-check-circle" aria-hidden="true"></i>
                                                <?php echo $schedule['available_spots']; ?> lugares disponibles
                                            </span>
                                            <?php else: ?>
                                            <span class="availability full">
                                                <i class="fas fa-exclamation-circle" aria-hidden="true"></i>
                                                Curso lleno
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="schedule-select-indicator">
                                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                                    </div>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn btn-primary next-step" data-next="step-personal">
                                Continuar <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Personal Information -->
                    <div class="form-step" id="step-personal">
                        <div class="step-header">
                            <h3>
                                <span class="step-number">2</span>
                                Información Personal
                            </h3>
                            <p>Confirma y completa tu información personal</p>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="first_name">Nombre(s) *</label>
                                <input type="text" 
                                       id="first_name" 
                                       name="first_name" 
                                       value="<?php echo htmlspecialchars($user_profile['first_name'] ?? ''); ?>" 
                                       required 
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="last_name">Apellidos *</label>
                                <input type="text" 
                                       id="last_name" 
                                       name="last_name" 
                                       value="<?php echo htmlspecialchars($user_profile['last_name'] ?? ''); ?>" 
                                       required 
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="email">Correo Electrónico *</label>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($user_profile['email'] ?? ''); ?>" 
                                       required 
                                       class="form-control"
                                       readonly>
                            </div>

                            <div class="form-group">
                                <label for="phone">Teléfono *</label>
                                <input type="tel" 
                                       id="phone" 
                                       name="phone" 
                                       value="<?php echo htmlspecialchars($user_profile['phone'] ?? ''); ?>" 
                                       required 
                                       class="form-control"
                                       placeholder="+52 55 1234 5678">
                            </div>

                            <div class="form-group">
                                <label for="birth_date">Fecha de Nacimiento *</label>
                                <input type="date" 
                                       id="birth_date" 
                                       name="birth_date" 
                                       value="<?php echo $user_profile['birth_date'] ?? ''; ?>" 
                                       required 
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="profession">Profesión *</label>
                                <select id="profession" name="profession" required class="form-control">
                                    <option value="">Selecciona tu profesión</option>
                                    <option value="medical_student" <?php echo ($user_profile['profession'] == 'medical_student') ? 'selected' : ''; ?>>Estudiante de Medicina</option>
                                    <option value="nursing_student" <?php echo ($user_profile['profession'] == 'nursing_student') ? 'selected' : ''; ?>>Estudiante de Enfermería</option>
                                    <option value="doctor" <?php echo ($user_profile['profession'] == 'doctor') ? 'selected' : ''; ?>>Médico</option>
                                    <option value="nurse" <?php echo ($user_profile['profession'] == 'nurse') ? 'selected' : ''; ?>>Enfermero(a)</option>
                                    <option value="paramedic" <?php echo ($user_profile['profession'] == 'paramedic') ? 'selected' : ''; ?>>Paramédico</option>
                                    <option value="teacher" <?php echo ($user_profile['profession'] == 'teacher') ? 'selected' : ''; ?>>Maestro(a)</option>
                                    <option value="parent" <?php echo ($user_profile['profession'] == 'parent') ? 'selected' : ''; ?>>Padre/Madre de Familia</option>
                                    <option value="administrator" <?php echo ($user_profile['profession'] == 'administrator') ? 'selected' : ''; ?>>Administrador Médico</option>
                                    <option value="other" <?php echo ($user_profile['profession'] == 'other') ? 'selected' : ''; ?>>Otro</option>
                                </select>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="form-section">
                            <h4>Contacto de Emergencia</h4>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="emergency_contact_name">Nombre del Contacto *</label>
                                    <input type="text" 
                                           id="emergency_contact_name" 
                                           name="emergency_contact_name" 
                                           required 
                                           class="form-control">
                                </div>

                                <div class="form-group">
                                    <label for="emergency_contact_phone">Teléfono del Contacto *</label>
                                    <input type="tel" 
                                           id="emergency_contact_phone" 
                                           name="emergency_contact_phone" 
                                           required 
                                           class="form-control"
                                           placeholder="+52 55 1234 5678">
                                </div>

                                <div class="form-group">
                                    <label for="emergency_contact_relationship">Relación</label>
                                    <select id="emergency_contact_relationship" name="emergency_contact_relationship" class="form-control">
                                        <option value="">Selecciona relación</option>
                                        <option value="spouse">Esposo(a)</option>
                                        <option value="parent">Padre/Madre</option>
                                        <option value="child">Hijo(a)</option>
                                        <option value="sibling">Hermano(a)</option>
                                        <option value="friend">Amigo(a)</option>
                                        <option value="colleague">Colega</option>
                                        <option value="other">Otro</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Medical Information -->
                        <div class="form-section">
                            <h4>Información Médica</h4>
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label for="medical_conditions">Condiciones Médicas</label>
                                    <textarea id="medical_conditions" 
                                              name="medical_conditions" 
                                              class="form-control" 
                                              rows="3" 
                                              placeholder="Menciona cualquier condición médica relevante, alergias o medicamentos que tomes"></textarea>
                                </div>

                                <div class="form-group full-width">
                                    <label for="dietary_restrictions">Restricciones Alimentarias</label>
                                    <textarea id="dietary_restrictions" 
                                              name="dietary_restrictions" 
                                              class="form-control" 
                                              rows="2" 
                                              placeholder="Menciona alergias alimentarias o restricciones dietéticas"></textarea>
                                </div>

                                <div class="form-group full-width">
                                    <label for="special_requirements">Requerimientos Especiales</label>
                                    <textarea id="special_requirements" 
                                              name="special_requirements" 
                                              class="form-control" 
                                              rows="2" 
                                              placeholder="Necesidades de accesibilidad, adaptaciones especiales, etc."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn btn-secondary prev-step" data-prev="step-schedule">
                                <i class="fas fa-arrow-left" aria-hidden="true"></i> Anterior
                            </button>
                            <button type="button" class="btn btn-primary next-step" data-next="step-payment">
                                Continuar <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Payment -->
                    <div class="form-step" id="step-payment">
                        <div class="step-header">
                            <h3>
                                <span class="step-number">3</span>
                                Información de Pago
                            </h3>
                            <p>Completa tu inscripción con el método de pago de tu preferencia</p>
                        </div>

                        <!-- Payment Method Selection -->
                        <div class="payment-methods">
                            <?php foreach ($payment_methods as $method): ?>
                            <?php if ($method['available']): ?>
                            <div class="payment-method-option">
                                <input type="radio" 
                                       name="payment_method" 
                                       value="<?php echo $method['id']; ?>" 
                                       id="payment-<?php echo $method['id']; ?>"
                                       required>
                                
                                <label for="payment-<?php echo $method['id']; ?>" class="payment-method-card">
                                    <div class="payment-method-info">
                                        <div class="payment-icon">
                                            <i class="<?php echo $method['icon']; ?>" aria-hidden="true"></i>
                                        </div>
                                        <div class="payment-details">
                                            <h4><?php echo htmlspecialchars($method['name']); ?></h4>
                                            <p><?php echo htmlspecialchars($method['description']); ?></p>
                                            <?php if ($method['processing_fee'] > 0): ?>
                                            <small class="processing-fee">
                                                Comisión: <?php echo ($method['processing_fee'] * 100); ?>%
                                            </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="payment-select-indicator">
                                        <i class="fas fa-check-circle" aria-hidden="true"></i>
                                    </div>
                                </label>
                            </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <!-- Credit Card Form -->
                        <div class="payment-form" id="credit-card-form" style="display: none;">
                            <div class="form-grid">
                                <div class="form-group full-width">
                                    <label for="card_number">Número de Tarjeta *</label>
                                    <input type="text" 
                                           id="card_number" 
                                           name="card_number" 
                                           class="form-control" 
                                           placeholder="1234 5678 9012 3456"
                                           maxlength="19">
                                </div>

                                <div class="form-group">
                                    <label for="card_expiry">Fecha de Vencimiento *</label>
                                    <input type="text" 
                                           id="card_expiry" 
                                           name="card_expiry" 
                                           class="form-control" 
                                           placeholder="MM/YY"
                                           maxlength="5">
                                </div>

                                <div class="form-group">
                                    <label for="card_cvv">CVV *</label>
                                    <input type="text" 
                                           id="card_cvv" 
                                           name="card_cvv" 
                                           class="form-control" 
                                           placeholder="123"
                                           maxlength="4">
                                </div>

                                <div class="form-group full-width">
                                    <label for="card_holder_name">Nombre del Titular *</label>
                                    <input type="text" 
                                           id="card_holder_name" 
                                           name="card_holder_name" 
                                           class="form-control"
                                           placeholder="Como aparece en la tarjeta">
                                </div>
                            </div>
                        </div>

                        <!-- Installments Form -->
                        <div class="payment-form" id="installments-form" style="display: none;">
                            <div class="form-group">
                                <label for="installment_months">Número de Mensualidades</label>
                                <select id="installment_months" name="installment_months" class="form-control">
                                    <option value="3">3 meses sin intereses</option>
                                    <option value="6">6 meses sin intereses</option>
                                    <option value="12">12 meses sin intereses</option>
                                </select>
                            </div>
                        </div>

                        <!-- Bank Transfer Form -->
                        <div class="payment-form" id="bank-transfer-form" style="display: none;">
                            <div class="bank-info">
                                <h4>Datos Bancarios para Transferencia</h4>
                                <div class="bank-details">
                                    <p><strong>Banco:</strong> BBVA Bancomer</p>
                                    <p><strong>Cuenta:</strong> 0123456789</p>
                                    <p><strong>CLABE:</strong> 012345678901234567</p>
                                    <p><strong>Beneficiario:</strong> Capacitar-T México S.C.</p>
                                </div>
                                <div class="transfer-instructions">
                                    <p><strong>Instrucciones:</strong></p>
                                    <ol>
                                        <li>Realiza la transferencia por el monto total</li>
                                        <li>Envía el comprobante a: pagos@capacitar-t.com.mx</li>
                                        <li>Tu inscripción se confirmará en 24-48 horas</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn btn-secondary prev-step" data-prev="step-personal">
                                <i class="fas fa-arrow-left" aria-hidden="true"></i> Anterior
                            </button>
                            <button type="button" class="btn btn-primary next-step" data-next="step-confirmation">
                                Continuar <i class="fas fa-arrow-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Confirmation -->
                    <div class="form-step" id="step-confirmation">
                        <div class="step-header">
                            <h3>
                                <span class="step-number">4</span>
                                Confirmación
                            </h3>
                            <p>Revisa tu información antes de completar la inscripción</p>
                        </div>

                        <div class="confirmation-summary">
                            <div class="course-summary">
                                <h4>Resumen del Curso</h4>
                                <div class="summary-item">
                                    <span class="label">Curso:</span>
                                    <span class="value"><?php echo htmlspecialchars($course['title']); ?></span>
                                </div>
                                <div class="summary-item">
                                    <span class="label">Duración:</span>
                                    <span class="value"><?php echo $course['duration_hours']; ?> horas</span>
                                </div>
                                <div class="summary-item">
                                    <span class="label">Horario:</span>
                                    <span class="value" id="selected-schedule-summary">Por seleccionar</span>
                                </div>
                            </div>

                            <div class="terms-conditions">
                                <div class="form-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="terms_accepted" required>
                                        <span class="checkmark"></span>
                                        He leído y acepto los <a href="/terminos-condiciones" target="_blank">términos y condiciones</a>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="privacy_accepted" required>
                                        <span class="checkmark"></span>
                                        Acepto las <a href="/politicas-privacidad" target="_blank">políticas de privacidad</a>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="marketing_emails" value="1">
                                        <span class="checkmark"></span>
                                        Deseo recibir información sobre nuevos cursos y promociones
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="step-actions">
                            <button type="button" class="btn btn-secondary prev-step" data-prev="step-payment">
                                <i class="fas fa-arrow-left" aria-hidden="true"></i> Anterior
                            </button>
                            <button type="submit" class="btn btn-success btn-large">
                                <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                                Completar Inscripción
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Pricing Sidebar -->
            <div class="pricing-sidebar">
                <div class="pricing-card">
                    <h3>Resumen de Pago</h3>
                    
                    <div class="pricing-breakdown">
                        <div class="price-item">
                            <span class="label">Precio del curso:</span>
                            <span class="value">$<?php echo number_format($pricing['base_price']); ?></span>
                        </div>

                        <?php if (!empty($pricing['discounts'])): ?>
                        <?php foreach ($pricing['discounts'] as $discount): ?>
                        <div class="price-item discount">
                            <span class="label"><?php echo htmlspecialchars($discount['name']); ?> (-<?php echo $discount['percentage']; ?>%):</span>
                            <span class="value">-$<?php echo number_format($discount['amount']); ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="price-item subtotal">
                            <span class="label">Subtotal:</span>
                            <span class="value">$<?php echo number_format($pricing['subtotal']); ?></span>
                        </div>

                        <div class="price-item tax">
                            <span class="label">IVA (<?php echo $pricing['tax_rate']; ?>%):</span>
                            <span class="value">$<?php echo number_format($pricing['tax']); ?></span>
                        </div>

                        <div class="price-item total">
                            <span class="label">Total:</span>
                            <span class="value">$<?php echo number_format($pricing['total']); ?> MXN</span>
                        </div>
                    </div>

                    <input type="hidden" name="amount" value="<?php echo $pricing['total']; ?>">

                    <div class="pricing-benefits">
                        <h4>Incluye:</h4>
                        <ul>
                            <li><i class="fas fa-check" aria-hidden="true"></i> Material didáctico</li>
                            <li><i class="fas fa-check" aria-hidden="true"></i> Certificación oficial</li>
                            <li><i class="fas fa-check" aria-hidden="true"></i> Práctica con equipos</li>
                            <li><i class="fas fa-check" aria-hidden="true"></i> Coffee break</li>
                            <li><i class="fas fa-check" aria-hidden="true"></i> Soporte post-curso</li>
                        </ul>
                    </div>
                </div>

                <!-- Security Information -->
                <div class="security-info">
                    <h4>Compra Segura</h4>
                    <div class="security-features">
                        <div class="security-item">
                            <i class="fas fa-shield-alt" aria-hidden="true"></i>
                            <span>Transacciones seguras SSL</span>
                        </div>
                        <div class="security-item">
                            <i class="fas fa-lock" aria-hidden="true"></i>
                            <span>Datos protegidos</span>
                        </div>
                        <div class="security-item">
                            <i class="fas fa-undo" aria-hidden="true"></i>
                            <span>Política de reembolso</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Custom JavaScript for enrollment form -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const enrollmentForm = document.querySelector('.enrollment-form');
    const steps = document.querySelectorAll('.form-step');
    const nextButtons = document.querySelectorAll('.next-step');
    const prevButtons = document.querySelectorAll('.prev-step');
    
    // Step navigation
    nextButtons.forEach(button => {
        button.addEventListener('click', function() {
            const nextStep = this.getAttribute('data-next');
            showStep(nextStep);
        });
    });
    
    prevButtons.forEach(button => {
        button.addEventListener('click', function() {
            const prevStep = this.getAttribute('data-prev');
            showStep(prevStep);
        });
    });
    
    function showStep(stepId) {
        steps.forEach(step => step.classList.remove('active'));
        document.getElementById(stepId).classList.add('active');
        
        // Update progress indicator if you have one
        updateProgress(stepId);
    }
    
    function updateProgress(stepId) {
        const stepNumber = {
            'step-schedule': 1,
            'step-personal': 2,
            'step-payment': 3,
            'step-confirmation': 4
        };
        
        // Update progress bar or indicators here
    }
    
    // Payment method handling
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const paymentForms = document.querySelectorAll('.payment-form');
    
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            paymentForms.forEach(form => form.style.display = 'none');
            
            const selectedForm = document.getElementById(this.value + '-form');
            if (selectedForm) {
                selectedForm.style.display = 'block';
            }
        });
    });
    
    // Form validation and submission
    enrollmentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        submitButton.disabled = true;
        
        // Simulate processing time
        setTimeout(() => {
            this.submit();
        }, 2000);
    });
});
</script>

<?php include VIEW_PATH . '/includes/footer.php'; ?>