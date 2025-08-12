<?php include VIEW_PATH . '/includes/header.php'; ?>

<section class="auth-section">
    <div class="container">
        <div class="auth-container register-container">
            <div class="auth-form-container">
                <div class="auth-header">
                    <h1>Crear Cuenta Gratuita</h1>
                    <p>Únete a miles de profesionales médicos que confían en nosotros</p>
                </div>

                <?php if (!empty($form_errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                    <ul>
                        <?php foreach ($form_errors as $field => $errors): ?>
                            <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <form class="auth-form register-form" method="POST" action="/registro">
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3>Información Personal</h3>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="first_name">Nombre(s) *</label>
                                <input type="text" 
                                       id="first_name" 
                                       name="first_name" 
                                       value="<?php echo htmlspecialchars($form_data['first_name'] ?? ''); ?>" 
                                       required 
                                       class="form-control"
                                       placeholder="Tu nombre">
                            </div>

                            <div class="form-group">
                                <label for="last_name">Apellidos *</label>
                                <input type="text" 
                                       id="last_name" 
                                       name="last_name" 
                                       value="<?php echo htmlspecialchars($form_data['last_name'] ?? ''); ?>" 
                                       required 
                                       class="form-control"
                                       placeholder="Tus apellidos">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Correo Electrónico *</label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-envelope" aria-hidden="true"></i>
                                </div>
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" 
                                       required 
                                       class="form-control"
                                       placeholder="tu@email.com">
                            </div>
                            <small class="form-help">Usaremos este email para contactarte sobre tus cursos</small>
                        </div>

                        <div class="form-group">
                            <label for="phone">Teléfono *</label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-phone" aria-hidden="true"></i>
                                </div>
                                <input type="tel" 
                                       id="phone" 
                                       name="phone" 
                                       value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>" 
                                       required 
                                       class="form-control"
                                       placeholder="+52 55 1234 5678">
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="birth_date">Fecha de Nacimiento *</label>
                                <input type="date" 
                                       id="birth_date" 
                                       name="birth_date" 
                                       value="<?php echo $form_data['birth_date'] ?? ''; ?>" 
                                       required 
                                       class="form-control"
                                       max="<?php echo date('Y-m-d', strtotime('-18 years')); ?>">
                            </div>

                            <div class="form-group">
                                <label for="profession">Profesión *</label>
                                <select id="profession" name="profession" required class="form-control">
                                    <option value="">Selecciona tu profesión</option>
                                    <optgroup label="Estudiantes">
                                        <option value="medical_student" <?php echo ($form_data['profession'] ?? '') == 'medical_student' ? 'selected' : ''; ?>>Estudiante de Medicina</option>
                                        <option value="nursing_student" <?php echo ($form_data['profession'] ?? '') == 'nursing_student' ? 'selected' : ''; ?>>Estudiante de Enfermería</option>
                                    </optgroup>
                                    <optgroup label="Profesionales de la Salud">
                                        <option value="doctor" <?php echo ($form_data['profession'] ?? '') == 'doctor' ? 'selected' : ''; ?>>Médico</option>
                                        <option value="nurse" <?php echo ($form_data['profession'] ?? '') == 'nurse' ? 'selected' : ''; ?>>Enfermero(a)</option>
                                        <option value="paramedic" <?php echo ($form_data['profession'] ?? '') == 'paramedic' ? 'selected' : ''; ?>>Paramédico</option>
                                        <option value="dentist" <?php echo ($form_data['profession'] ?? '') == 'dentist' ? 'selected' : ''; ?>>Dentista</option>
                                        <option value="pharmacist" <?php echo ($form_data['profession'] ?? '') == 'pharmacist' ? 'selected' : ''; ?>>Farmacéutico</option>
                                    </optgroup>
                                    <optgroup label="Educación y Cuidado">
                                        <option value="teacher" <?php echo ($form_data['profession'] ?? '') == 'teacher' ? 'selected' : ''; ?>>Maestro(a)</option>
                                        <option value="parent" <?php echo ($form_data['profession'] ?? '') == 'parent' ? 'selected' : ''; ?>>Padre/Madre de Familia</option>
                                        <option value="childcare" <?php echo ($form_data['profession'] ?? '') == 'childcare' ? 'selected' : ''; ?>>Cuidador Infantil</option>
                                        <option value="coach" <?php echo ($form_data['profession'] ?? '') == 'coach' ? 'selected' : ''; ?>>Entrenador Deportivo</option>
                                    </optgroup>
                                    <optgroup label="Administración">
                                        <option value="administrator" <?php echo ($form_data['profession'] ?? '') == 'administrator' ? 'selected' : ''; ?>>Administrador Médico</option>
                                        <option value="manager" <?php echo ($form_data['profession'] ?? '') == 'manager' ? 'selected' : ''; ?>>Gerente de Clínica</option>
                                        <option value="receptionist" <?php echo ($form_data['profession'] ?? '') == 'receptionist' ? 'selected' : ''; ?>>Recepcionista Médico</option>
                                    </optgroup>
                                    <optgroup label="Industria y Seguridad">
                                        <option value="safety_officer" <?php echo ($form_data['profession'] ?? '') == 'safety_officer' ? 'selected' : ''; ?>>Oficial de Seguridad Industrial</option>
                                        <option value="first_aid_brigade" <?php echo ($form_data['profession'] ?? '') == 'first_aid_brigade' ? 'selected' : ''; ?>>Brigadista de Primeros Auxilios</option>
                                    </optgroup>
                                    <option value="other" <?php echo ($form_data['profession'] ?? '') == 'other' ? 'selected' : ''; ?>>Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Account Security -->
                    <div class="form-section">
                        <h3>Seguridad de la Cuenta</h3>
                        
                        <div class="form-group">
                            <label for="password">Contraseña *</label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-lock" aria-hidden="true"></i>
                                </div>
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       required 
                                       class="form-control"
                                       placeholder="Mínimo 8 caracteres"
                                       minlength="8">
                                <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="password-strength" id="password-strength">
                                <div class="strength-bar">
                                    <div class="strength-fill"></div>
                                </div>
                                <small class="strength-text">Escribe tu contraseña</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password_confirm">Confirmar Contraseña *</label>
                            <div class="input-group">
                                <div class="input-icon">
                                    <i class="fas fa-lock" aria-hidden="true"></i>
                                </div>
                                <input type="password" 
                                       id="password_confirm" 
                                       name="password_confirm" 
                                       required 
                                       class="form-control"
                                       placeholder="Repite tu contraseña">
                                <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Marketing -->
                    <div class="form-section">
                        <div class="form-group">
                            <label class="checkbox-label required">
                                <input type="checkbox" name="terms_accepted" required>
                                <span class="checkmark"></span>
                                Acepto los <a href="/terminos-condiciones" target="_blank">Términos y Condiciones</a> 
                                y las <a href="/politicas-privacidad" target="_blank">Políticas de Privacidad</a> *
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="marketing_emails" value="1" checked>
                                <span class="checkmark"></span>
                                Quiero recibir información sobre nuevos cursos y promociones especiales
                            </label>
                        </div>

                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="newsletter_medical" value="1">
                                <span class="checkmark"></span>
                                Suscribirme al boletín médico mensual con casos clínicos y actualizaciones
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-large btn-block">
                        <i class="fas fa-user-plus" aria-hidden="true"></i>
                        Crear Mi Cuenta Gratuita
                    </button>
                </form>

                <div class="auth-divider">
                    <span>o regístrate con</span>
                </div>

                <div class="social-login">
                    <button class="btn btn-social btn-google" onclick="registerWithGoogle()">
                        <i class="fab fa-google" aria-hidden="true"></i>
                        Google
                    </button>
                    
                    <button class="btn btn-social btn-facebook" onclick="registerWithFacebook()">
                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                        Facebook
                    </button>
                </div>

                <div class="auth-footer">
                    <p>
                        ¿Ya tienes cuenta? 
                        <a href="/login">Iniciar sesión</a>
                    </p>
                </div>
            </div>

            <div class="auth-benefits">
                <div class="benefits-content">
                    <h2>¿Qué Obtienes?</h2>
                    
                    <div class="benefit-list">
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-graduation-cap text-primary" aria-hidden="true"></i>
                            </div>
                            <div class="benefit-text">
                                <h3>Cursos Certificados AHA</h3>
                                <p>Acceso a cursos BLS, ACLS, PALS certificados por la American Heart Association</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-users text-success" aria-hidden="true"></i>
                            </div>
                            <div class="benefit-text">
                                <h3>Grupos Reducidos</h3>
                                <p>Máximo 16 participantes para garantizar atención personalizada</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-hospital text-info" aria-hidden="true"></i>
                            </div>
                            <div class="benefit-text">
                                <h3>Simulación de Alta Fidelidad</h3>
                                <p>Práctica con maniquíes médicos de última generación</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-clock text-warning" aria-hidden="true"></i>
                            </div>
                            <div class="benefit-text">
                                <h3>Horarios Flexibles</h3>
                                <p>Cursos disponibles entre semana, fines de semana y modalidad intensiva</p>
                            </div>
                        </div>
                    </div>

                    <div class="stats-preview">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <span class="stat-number">5,000+</span>
                                <span class="stat-label">Profesionales Capacitados</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">15</span>
                                <span class="stat-label">Años de Experiencia</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">4.9</span>
                                <span class="stat-label">Calificación Promedio</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="security-badges">
                    <div class="security-item">
                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                        <span>Datos Seguros SSL</span>
                    </div>
                    <div class="security-item">
                        <i class="fas fa-user-shield" aria-hidden="true"></i>
                        <span>Privacidad Garantizada</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password strength checker
    const passwordInput = document.getElementById('password');
    const strengthIndicator = document.getElementById('password-strength');
    
    if (passwordInput && strengthIndicator) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            updateStrengthIndicator(strength);
        });
    }
    
    // Password confirmation validation
    const passwordConfirm = document.getElementById('password_confirm');
    if (passwordConfirm) {
        passwordConfirm.addEventListener('input', function() {
            const password = passwordInput.value;
            const confirm = this.value;
            
            if (confirm && password !== confirm) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // Toggle password visibility
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    });
    
    // Phone number formatting
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            
            if (value.length >= 10) {
                if (value.startsWith('52')) {
                    value = value.substring(2);
                }
                
                if (value.length === 10) {
                    value = `+52 ${value.substring(0, 2)} ${value.substring(2, 6)} ${value.substring(6)}`;
                }
            }
            
            this.value = value;
        });
    }
    
    // Form submission
    const form = document.querySelector('.register-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirm = passwordConfirm.value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden.');
                return;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 8 caracteres.');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando cuenta...';
            submitBtn.disabled = true;
        });
    }
});

function calculatePasswordStrength(password) {
    let score = 0;
    
    if (password.length >= 8) score++;
    if (password.length >= 12) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    
    return score;
}

function updateStrengthIndicator(score) {
    const strengthFill = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');
    
    const levels = [
        { text: 'Muy débil', class: 'very-weak', width: '16%' },
        { text: 'Débil', class: 'weak', width: '32%' },
        { text: 'Regular', class: 'fair', width: '48%' },
        { text: 'Buena', class: 'good', width: '64%' },
        { text: 'Fuerte', class: 'strong', width: '80%' },
        { text: 'Muy fuerte', class: 'very-strong', width: '100%' }
    ];
    
    const level = levels[Math.min(score, levels.length - 1)];
    
    strengthFill.style.width = level.width;
    strengthFill.className = 'strength-fill ' + level.class;
    strengthText.textContent = level.text;
}

// Social registration functions (placeholder)
function registerWithGoogle() {
    alert('Funcionalidad de registro con Google se implementará próximamente');
}

function registerWithFacebook() {
    alert('Funcionalidad de registro con Facebook se implementará próximamente');
}
</script>

<?php include VIEW_PATH . '/includes/footer.php'; ?>