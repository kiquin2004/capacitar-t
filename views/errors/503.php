<?php include VIEW_PATH . '/includes/header.php'; ?>

<div class="error-page-container">
    <div class="container">
        <div class="error-content text-center">
            <div class="error-icon">
                <i class="fas fa-tools text-primary" style="font-size: 5rem;"></i>
            </div>
            
            <h1 class="error-code"><?php echo $error_code; ?></h1>
            <h2 class="error-title"><?php echo htmlspecialchars($error_title); ?></h2>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            
            <div class="maintenance-info">
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: 75%">
                        Actualización en progreso...
                    </div>
                </div>
                
                <p class="text-muted">
                    <i class="fas fa-clock"></i> 
                    Tiempo estimado de finalización: <strong>15-30 minutos</strong>
                </p>
            </div>
            
            <div class="error-suggestions">
                <h4>Mientras esperamos:</h4>
                <ul class="list-unstyled">
                    <?php foreach ($error_suggestions as $suggestion): ?>
                    <li><i class="fas fa-info-circle text-info"></i> <?php echo htmlspecialchars($suggestion); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="social-links mb-4">
                <h5>Síguenos para actualizaciones:</h5>
                <div class="social-buttons">
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="#" class="btn btn-outline-info btn-sm">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                    <a href="#" class="btn btn-outline-success btn-sm">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                </div>
            </div>
            
            <div class="error-actions">
                <button onclick="location.reload()" class="btn btn-primary">
                    <i class="fas fa-sync-alt"></i> Recargar Página
                </button>
                <a href="/contacto" class="btn btn-outline-secondary">
                    <i class="fas fa-envelope"></i> Contactar Soporte
                </a>
            </div>
            
            <div class="emergency-contact mt-4">
                <div class="alert alert-warning">
                    <h5><i class="fas fa-exclamation-triangle"></i> ¿Emergencia médica?</h5>
                    <p class="mb-0">Para capacitación de emergencia, contáctanos por WhatsApp: 
                    <a href="https://wa.me/5255876543210" target="_blank">+52 55 8765-4321</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-page-container {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 0;
}

.error-content {
    max-width: 600px;
    padding: 2rem;
}

.error-icon {
    margin-bottom: 2rem;
    animation: spin 2s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.error-code {
    font-size: 4rem;
    font-weight: bold;
    color: #6f42c1;
    margin-bottom: 1rem;
}

.error-title {
    color: #495057;
    margin-bottom: 1rem;
}

.error-message {
    color: #6c757d;
    font-size: 1.1rem;
    margin-bottom: 2rem;
}

.maintenance-info {
    background: #e3f2fd;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.error-suggestions {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.error-suggestions ul li {
    text-align: left;
    padding: 0.5rem 0;
}

.social-buttons .btn {
    margin: 0.25rem;
}

.error-actions .btn {
    margin: 0.5rem;
}

.emergency-contact {
    max-width: 400px;
    margin: 0 auto;
}
</style>

<?php include VIEW_PATH . '/includes/footer.php'; ?>