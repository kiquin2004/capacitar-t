<?php include VIEW_PATH . '/includes/header.php'; ?>

<div class="error-page-container">
    <div class="container">
        <div class="error-content text-center">
            <div class="error-icon">
                <i class="fas fa-exclamation-triangle text-danger" style="font-size: 5rem;"></i>
            </div>
            
            <h1 class="error-code"><?php echo $error_code; ?></h1>
            <h2 class="error-title"><?php echo htmlspecialchars($error_title); ?></h2>
            <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            
            <div class="error-suggestions">
                <h4>¿Qué puedes hacer?</h4>
                <ul class="list-unstyled">
                    <?php foreach ($error_suggestions as $suggestion): ?>
                    <li><i class="fas fa-info-circle text-info"></i> <?php echo htmlspecialchars($suggestion); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="error-actions">
                <a href="/" class="btn btn-primary">
                    <i class="fas fa-home"></i> Ir al Inicio
                </a>
                <a href="javascript:history.back()" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Regresar
                </a>
                <a href="/contacto" class="btn btn-outline-secondary">
                    <i class="fas fa-life-ring"></i> Soporte Técnico
                </a>
            </div>
            
            <div class="emergency-contact mt-4">
                <div class="alert alert-info">
                    <h5><i class="fas fa-phone-alt"></i> ¿Necesitas ayuda urgente?</h5>
                    <p class="mb-0">WhatsApp: <a href="https://wa.me/5255876543210" target="_blank">+52 55 8765-4321</a></p>
                    <p class="mb-0">Email: <a href="mailto:soporte@capacitar-t.com.mx">soporte@capacitar-t.com.mx</a></p>
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
}

.error-code {
    font-size: 4rem;
    font-weight: bold;
    color: #dc3545;
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

.error-actions .btn {
    margin: 0.5rem;
}

.emergency-contact {
    max-width: 400px;
    margin: 0 auto;
}
</style>

<?php include VIEW_PATH . '/includes/footer.php'; ?>