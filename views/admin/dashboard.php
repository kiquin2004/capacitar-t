<?php include VIEW_PATH . '/includes/header.php'; ?>

<div class="admin-layout">
    <!-- Admin Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h2>
                <i class="fas fa-user-shield" aria-hidden="true"></i>
                Panel Admin
            </h2>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item active">
                    <a href="/admin/dashboard" class="nav-link">
                        <i class="fas fa-tachometer-alt" aria-hidden="true"></i>
                        Dashboard
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/admin/cursos" class="nav-link">
                        <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                        Cursos
                        <span class="badge"><?php echo $stats['total_courses']; ?></span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/admin/usuarios" class="nav-link">
                        <i class="fas fa-users" aria-hidden="true"></i>
                        Usuarios
                        <span class="badge"><?php echo $stats['total_users']; ?></span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/admin/inscripciones" class="nav-link">
                        <i class="fas fa-clipboard-list" aria-hidden="true"></i>
                        Inscripciones
                        <?php if ($stats['pending_enrollments'] > 0): ?>
                        <span class="badge badge-warning"><?php echo $stats['pending_enrollments']; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/admin/reportes" class="nav-link">
                        <i class="fas fa-chart-bar" aria-hidden="true"></i>
                        Reportes
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/admin/configuracion" class="nav-link">
                        <i class="fas fa-cog" aria-hidden="true"></i>
                        Configuración
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <a href="/" class="btn btn-outline btn-small">
                <i class="fas fa-home" aria-hidden="true"></i>
                Ver Sitio Web
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="admin-main">
        <!-- Admin Header -->
        <header class="admin-header">
            <div class="header-left">
                <h1>Dashboard Administrativo</h1>
                <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            </div>
            
            <div class="header-right">
                <div class="header-actions">
                    <button class="btn btn-outline" onclick="refreshDashboard()">
                        <i class="fas fa-sync-alt" aria-hidden="true"></i>
                        Actualizar
                    </button>
                    
                    <a href="/admin/curso/nuevo" class="btn btn-primary">
                        <i class="fas fa-plus" aria-hidden="true"></i>
                        Nuevo Curso
                    </a>
                </div>
                
                <div class="user-menu-admin">
                    <button class="user-menu-toggle">
                        <i class="fas fa-user-circle" aria-hidden="true"></i>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </button>
                    <div class="user-menu-dropdown">
                        <a href="/perfil">Mi Perfil</a>
                        <a href="/logout">Cerrar Sesión</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="admin-content">
            <!-- System Alerts -->
            <?php if (!empty($alerts)): ?>
            <section class="alerts-section">
                <?php foreach ($alerts as $alert): ?>
                <div class="alert alert-<?php echo $alert['type']; ?> alert-dismissible">
                    <div class="alert-content">
                        <strong><?php echo htmlspecialchars($alert['title']); ?></strong>
                        <p><?php echo htmlspecialchars($alert['message']); ?></p>
                    </div>
                    <?php if (isset($alert['action'])): ?>
                    <div class="alert-actions">
                        <a href="<?php echo $alert['action']; ?>" class="btn btn-small btn-outline">
                            Ver Detalles
                        </a>
                    </div>
                    <?php endif; ?>
                    <button class="alert-dismiss" aria-label="Cerrar alerta">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </button>
                </div>
                <?php endforeach; ?>
            </section>
            <?php endif; ?>

            <!-- Stats Grid -->
            <section class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon text-primary">
                        <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['total_courses']); ?></h3>
                        <p>Total Cursos</p>
                        <small class="stat-meta">
                            <?php echo $stats['active_courses']; ?> activos
                        </small>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon text-success">
                        <i class="fas fa-users" aria-hidden="true"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['total_users']); ?></h3>
                        <p>Usuarios Registrados</p>
                        <small class="stat-meta">
                            +<?php echo $stats['new_users_month']; ?> este mes
                        </small>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon text-info">
                        <i class="fas fa-clipboard-check" aria-hidden="true"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($stats['total_enrollments']); ?></h3>
                        <p>Inscripciones</p>
                        <small class="stat-meta">
                            <?php echo $stats['completion_rate']; ?>% completadas
                        </small>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon text-warning">
                        <i class="fas fa-dollar-sign" aria-hidden="true"></i>
                    </div>
                    <div class="stat-content">
                        <h3>$<?php echo number_format($stats['monthly_revenue']); ?></h3>
                        <p>Ingresos del Mes</p>
                        <small class="stat-meta">
                            MXN
                        </small>
                    </div>
                </div>
            </section>

            <!-- Main Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Recent Enrollments -->
                <section class="dashboard-card">
                    <div class="card-header">
                        <h2>Inscripciones Recientes</h2>
                        <a href="/admin/inscripciones" class="btn btn-small btn-outline">
                            Ver Todas
                        </a>
                    </div>
                    
                    <div class="card-content">
                        <?php if (!empty($recent_enrollments)): ?>
                        <div class="enrollment-list">
                            <?php foreach ($recent_enrollments as $enrollment): ?>
                            <div class="enrollment-item">
                                <div class="enrollment-user">
                                    <div class="user-avatar">
                                        <i class="fas fa-user" aria-hidden="true"></i>
                                    </div>
                                    <div class="user-info">
                                        <strong><?php echo htmlspecialchars($enrollment['first_name'] . ' ' . $enrollment['last_name']); ?></strong>
                                        <small><?php echo htmlspecialchars($enrollment['email']); ?></small>
                                    </div>
                                </div>
                                
                                <div class="enrollment-course">
                                    <a href="/curso/<?php echo $enrollment['course_slug']; ?>" target="_blank">
                                        <?php echo htmlspecialchars($enrollment['course_title']); ?>
                                    </a>
                                </div>
                                
                                <div class="enrollment-status">
                                    <span class="status-badge status-<?php echo $enrollment['status']; ?>">
                                        <?php echo ucfirst($enrollment['status']); ?>
                                    </span>
                                </div>
                                
                                <div class="enrollment-date">
                                    <small><?php echo date('d/m/Y H:i', strtotime($enrollment['created_at'])); ?></small>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-clipboard-list" aria-hidden="true"></i>
                            <p>No hay inscripciones recientes</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Course Performance -->
                <section class="dashboard-card">
                    <div class="card-header">
                        <h2>Rendimiento de Cursos</h2>
                        <a href="/admin/reportes?type=courses" class="btn btn-small btn-outline">
                            Reporte Completo
                        </a>
                    </div>
                    
                    <div class="card-content">
                        <?php if (!empty($course_metrics)): ?>
                        <div class="course-metrics">
                            <?php foreach (array_slice($course_metrics, 0, 5) as $course): ?>
                            <div class="metric-item">
                                <div class="metric-course">
                                    <a href="/curso/<?php echo $course['slug']; ?>" target="_blank">
                                        <strong><?php echo htmlspecialchars($course['title']); ?></strong>
                                    </a>
                                </div>
                                
                                <div class="metric-stats">
                                    <div class="metric-stat">
                                        <span class="stat-label">Inscripciones:</span>
                                        <span class="stat-value"><?php echo $course['total_enrollments']; ?></span>
                                    </div>
                                    
                                    <div class="metric-stat">
                                        <span class="stat-label">Completados:</span>
                                        <span class="stat-value"><?php echo $course['completed_count']; ?></span>
                                    </div>
                                    
                                    <?php if ($course['avg_rating']): ?>
                                    <div class="metric-stat">
                                        <span class="stat-label">Rating:</span>
                                        <span class="stat-value">
                                            <?php echo number_format($course['avg_rating'], 1); ?>
                                            <div class="stars-small">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?php echo $i <= $course['avg_rating'] ? '' : '-o'; ?>" aria-hidden="true"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-chart-line" aria-hidden="true"></i>
                            <p>No hay métricas disponibles</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Quick Actions -->
                <section class="dashboard-card">
                    <div class="card-header">
                        <h2>Acciones Rápidas</h2>
                    </div>
                    
                    <div class="card-content">
                        <div class="quick-actions">
                            <a href="/admin/curso/nuevo" class="quick-action">
                                <div class="action-icon">
                                    <i class="fas fa-plus" aria-hidden="true"></i>
                                </div>
                                <div class="action-content">
                                    <strong>Nuevo Curso</strong>
                                    <small>Crear curso médico</small>
                                </div>
                            </a>
                            
                            <a href="/admin/usuarios?filter=new" class="quick-action">
                                <div class="action-icon">
                                    <i class="fas fa-user-plus" aria-hidden="true"></i>
                                </div>
                                <div class="action-content">
                                    <strong>Nuevos Usuarios</strong>
                                    <small>Revisar registros</small>
                                </div>
                            </a>
                            
                            <a href="/admin/inscripciones?status=pending_payment" class="quick-action">
                                <div class="action-icon">
                                    <i class="fas fa-credit-card" aria-hidden="true"></i>
                                </div>
                                <div class="action-content">
                                    <strong>Pagos Pendientes</strong>
                                    <small>Verificar pagos</small>
                                </div>
                            </a>
                            
                            <a href="/admin/reportes" class="quick-action">
                                <div class="action-icon">
                                    <i class="fas fa-download" aria-hidden="true"></i>
                                </div>
                                <div class="action-content">
                                    <strong>Exportar Datos</strong>
                                    <small>Generar reportes</small>
                                </div>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- System Status -->
                <section class="dashboard-card">
                    <div class="card-header">
                        <h2>Estado del Sistema</h2>
                    </div>
                    
                    <div class="card-content">
                        <div class="system-status">
                            <div class="status-item">
                                <div class="status-indicator status-good"></div>
                                <div class="status-info">
                                    <strong>Base de Datos</strong>
                                    <small>Funcionando correctamente</small>
                                </div>
                            </div>
                            
                            <div class="status-item">
                                <div class="status-indicator status-good"></div>
                                <div class="status-info">
                                    <strong>Email Service</strong>
                                    <small>Operativo</small>
                                </div>
                            </div>
                            
                            <div class="status-item">
                                <div class="status-indicator status-warning"></div>
                                <div class="status-info">
                                    <strong>Respaldos</strong>
                                    <small>Último: hace 2 días</small>
                                </div>
                            </div>
                            
                            <div class="status-item">
                                <div class="status-indicator status-good"></div>
                                <div class="status-info">
                                    <strong>SSL Certificate</strong>
                                    <small>Válido hasta 2025</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
</div>

<!-- Dashboard Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize dashboard
    initDashboard();
    
    // Auto-refresh every 5 minutes
    setInterval(refreshDashboardData, 300000);
});

function initDashboard() {
    // Initialize user menu
    const userMenuToggle = document.querySelector('.user-menu-toggle');
    const userMenuDropdown = document.querySelector('.user-menu-dropdown');
    
    if (userMenuToggle && userMenuDropdown) {
        userMenuToggle.addEventListener('click', function() {
            userMenuDropdown.classList.toggle('active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.user-menu-admin')) {
                userMenuDropdown.classList.remove('active');
            }
        });
    }
    
    // Initialize alert dismissal
    const alertDismissButtons = document.querySelectorAll('.alert-dismiss');
    alertDismissButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.alert').style.display = 'none';
        });
    });
}

function refreshDashboard() {
    // Show loading state
    const refreshBtn = document.querySelector('button[onclick="refreshDashboard()"]');
    const originalText = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
    refreshBtn.disabled = true;
    
    // Simulate refresh (in real app, this would make AJAX call)
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function refreshDashboardData() {
    // AJAX call to refresh dashboard data without page reload
    fetch('/admin/api/dashboard-data')
        .then(response => response.json())
        .then(data => {
            // Update dashboard elements with new data
            updateDashboardStats(data.stats);
            updateRecentEnrollments(data.recent_enrollments);
        })
        .catch(error => {
            console.error('Error refreshing dashboard data:', error);
        });
}

function updateDashboardStats(stats) {
    // Update stat cards with new values
    const statCards = document.querySelectorAll('.stat-card h3');
    if (statCards.length >= 4) {
        statCards[0].textContent = stats.total_courses.toLocaleString();
        statCards[1].textContent = stats.total_users.toLocaleString();
        statCards[2].textContent = stats.total_enrollments.toLocaleString();
        statCards[3].textContent = '$' + stats.monthly_revenue.toLocaleString();
    }
}

function updateRecentEnrollments(enrollments) {
    // Update recent enrollments list
    // This would be implemented based on the data structure
}
</script>

<?php include VIEW_PATH . '/includes/footer.php'; ?>