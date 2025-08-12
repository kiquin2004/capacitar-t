<?php include VIEW_PATH . '/includes/header.php'; ?>

<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-form-container">
                <div class="auth-header">
                    <h1>Iniciar Sesión</h1>
                    <p>Accede a tu cuenta y continúa tu capacitación médica</p>
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

                <form class="auth-form" method="POST" action="/login">
                    <input type="hidden" name="redirect_url" value="<?php echo htmlspecialchars($redirect_url); ?>">
                    
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
                    </div>

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
                                   placeholder="Tu contraseña">
                            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember_me">
                            <span class="checkmark"></span>
                            Recordarme
                        </label>
                        
                        <a href="/recuperar-password" class="forgot-password">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-large btn-block">
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i>
                        Iniciar Sesión
                    </button>
                </form>

                <div class="auth-divider">
                    <span>o continúa con</span>
                </div>

                <div class="social-login">
                    <button class="btn btn-social btn-google" onclick="loginWithGoogle()">
                        <i class="fab fa-google" aria-hidden="true"></i>
                        Google
                    </button>
                    
                    <button class="btn btn-social btn-facebook" onclick="loginWithFacebook()">
                        <i class="fab fa-facebook-f" aria-hidden="true"></i>
                        Facebook
                    </button>
                </div>

                <div class="auth-footer">
                    <p>
                        ¿No tienes cuenta? 
                        <a href="/registro<?php echo $redirect_url ? '?redirect=' . urlencode($redirect_url) : ''; ?>">
                            Crear cuenta gratuita
                        </a>
                    </p>
                </div>
            </div>

            <div class="auth-benefits">
                <div class="benefits-content">
                    <h2>¿Por qué crear una cuenta?</h2>
                    
                    <div class="benefit-list">
                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-graduation-cap text-primary" aria-hidden="true"></i>
                            </div>
                            <div class="benefit-text">
                                <h3>Acceso a Cursos Exclusivos</h3>
                                <p>Inscríbete en cursos certificados por la American Heart Association</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-certificate text-success" aria-hidden="true"></i>
                            </div>
                            <div class="benefit-text">
                                <h3>Certificaciones Digitales</h3>
                                <p>Descarga tus certificados al instante y compártelos profesionalmente</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-calendar-check text-info" aria-hidden="true"></i>
                            </div>
                            <div class="benefit-text">
                                <h3>Seguimiento de Progreso</h3>
                                <p>Rastrea tu avance y recibe recordatorios de renovación</p>
                            </div>
                        </div>

                        <div class="benefit-item">
                            <div class="benefit-icon">
                                <i class="fas fa-users text-warning" aria-hidden="true"></i>
                            </div>
                            <div class="benefit-text">
                                <h3>Comunidad Médica</h3>
                                <p>Conéctate con otros profesionales de la salud</p>
                            </div>
                        </div>
                    </div>

                    <div class="testimonial-preview">
                        <blockquote>
                            "La plataforma más completa para capacitación médica en México. 
                            Los cursos son excelentes y la certificación AHA es muy valorada."
                        </blockquote>
                        <cite>
                            <strong>Dra. María Rodríguez</strong>
                            <span>Médico de Urgencias</span>
                        </cite>
                    </div>
                </div>

                <div class="security-badges">
                    <div class="security-item">
                        <i class="fas fa-shield-alt" aria-hidden="true"></i>
                        <span>Datos Seguros SSL</span>
                    </div>
                    <div class="security-item">
                        <i class="fas fa-lock" aria-hidden="true"></i>
                        <span>Información Protegida</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Demo Mode Notice -->
<?php if (defined('DEMO_MODE') && DEMO_MODE): ?>
<div class="demo-notice">
    <div class="container">
        <div class="demo-content">
            <i class="fas fa-info-circle" aria-hidden="true"></i>
            <div>
                <strong>Modo Demo</strong>
                <p>Usa <code>demo@capacitar-t.com.mx</code> con contraseña <code>demo123</code> para probar</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.querySelector('#password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
    
    // Form validation
    const form = document.querySelector('.auth-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Por favor completa todos los campos requeridos.');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Iniciando sesión...';
            submitBtn.disabled = true;
            
            // Reset button after 3 seconds if form doesn't submit
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            }, 3000);
        });
    }
});

// Social login functions (placeholder for actual implementation)
function loginWithGoogle() {
    alert('Funcionalidad de Google Login se implementará próximamente');
}

function loginWithFacebook() {
    alert('Funcionalidad de Facebook Login se implementará próximamente');
}
</script>

<?php include VIEW_PATH . '/includes/footer.php'; ?>