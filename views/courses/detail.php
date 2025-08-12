<?php include VIEW_PATH . '/includes/header.php'; ?>

<!-- Course Hero Section -->
<section class="course-hero">
    <div class="container">
        <div class="course-hero-content">
            <div class="course-hero-text">
                <div class="course-breadcrumbs">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <?php foreach ($breadcrumbs as $crumb): ?>
                            <?php if (empty($crumb['url'])): ?>
                            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($crumb['title']); ?></li>
                            <?php else: ?>
                            <li class="breadcrumb-item"><a href="<?php echo $crumb['url']; ?>"><?php echo htmlspecialchars($crumb['title']); ?></a></li>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                </div>

                <div class="course-badges-top">
                    <?php if ($course['is_aha_certified']): ?>
                    <span class="badge aha-certified">
                        <img src="<?php echo SITE_URL; ?>/assets/images/badges/aha-small.png" alt="AHA">
                        Certificado AHA
                    </span>
                    <?php endif; ?>
                    
                    <?php if ($course['certification_body'] === 'ERC'): ?>
                    <span class="badge erc-certified">
                        <img src="<?php echo SITE_URL; ?>/assets/images/badges/erc-small.png" alt="ERC">
                        Certificado ERC
                    </span>
                    <?php endif; ?>
                    
                    <span class="badge difficulty-<?php echo strtolower($course['difficulty_level']); ?>">
                        <?php echo htmlspecialchars($course['difficulty_level']); ?>
                    </span>
                </div>

                <h1 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h1>
                <p class="course-subtitle"><?php echo htmlspecialchars($course['short_description']); ?></p>

                <div class="course-meta-top">
                    <div class="meta-item">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                        <span><?php echo $course['duration_hours']; ?> horas académicas</span>
                    </div>
                    
                    <div class="meta-item">
                        <i class="fas fa-users" aria-hidden="true"></i>
                        <span>Máximo <?php echo $course['max_participants']; ?> participantes</span>
                    </div>
                    
                    <div class="meta-item">
                        <i class="fas fa-certificate" aria-hidden="true"></i>
                        <span>Validez <?php echo CERTIFICATION_VALIDITY_YEARS; ?> años</span>
                    </div>
                    
                    <div class="meta-item rating">
                        <div class="stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star<?php echo $i <= $course['average_rating'] ? '' : '-o'; ?>" aria-hidden="true"></i>
                            <?php endfor; ?>
                        </div>
                        <span>(<?php echo $course['total_reviews']; ?> reseñas)</span>
                    </div>
                </div>

                <div class="course-price-section">
                    <?php if ($course['original_price'] > $course['price']): ?>
                    <span class="original-price">$<?php echo number_format($course['original_price']); ?> MXN</span>
                    <span class="discount-percent">-<?php echo round((($course['original_price'] - $course['price']) / $course['original_price']) * 100); ?>%</span>
                    <?php endif; ?>
                    <span class="current-price">$<?php echo number_format($course['price']); ?> MXN</span>
                </div>

                <div class="course-actions-top">
                    <?php if ($can_enroll): ?>
                    <button class="btn btn-primary btn-large enroll-btn" data-course-id="<?php echo $course['id']; ?>">
                        <i class="fas fa-graduation-cap" aria-hidden="true"></i> Inscribirme Ahora
                    </button>
                    <?php else: ?>
                    <button class="btn btn-secondary btn-large" disabled>
                        <i class="fas fa-info-circle" aria-hidden="true"></i> <?php echo $enrollment_message; ?>
                    </button>
                    <?php endif; ?>
                    
                    <button class="btn btn-outline btn-large share-btn">
                        <i class="fas fa-share-alt" aria-hidden="true"></i> Compartir
                    </button>
                    
                    <button class="btn btn-outline btn-large" onclick="window.print()">
                        <i class="fas fa-print" aria-hidden="true"></i> Imprimir
                    </button>
                </div>
            </div>

            <div class="course-hero-visual">
                <div class="course-image-container">
                    <img src="<?php echo SITE_URL; ?>/assets/images/courses/<?php echo $course['featured_image'] ?? 'default-course-large.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($course['title']); ?>" 
                         class="course-featured-image">
                    
                    <?php if ($course['has_video_preview']): ?>
                    <div class="video-play-overlay">
                        <button class="play-video-btn" data-video-url="<?php echo $course['preview_video_url']; ?>">
                            <i class="fas fa-play" aria-hidden="true"></i>
                            <span>Vista Previa del Curso</span>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Medical Equipment Showcase -->
                <?php if (!empty($equipment)): ?>
                <div class="equipment-showcase">
                    <h4>Equipo Médico Especializado</h4>
                    <div class="equipment-grid">
                        <?php foreach (array_slice($equipment, 0, 4) as $item): ?>
                        <div class="equipment-item">
                            <img src="<?php echo SITE_URL; ?>/assets/images/equipment/<?php echo $item['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                 class="equipment-image">
                            <span class="equipment-name"><?php echo htmlspecialchars($item['name']); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Course Content Navigation -->
<nav class="course-nav sticky-nav">
    <div class="container">
        <ul class="course-nav-links">
            <li><a href="#descripcion" class="nav-link active">Descripción</a></li>
            <li><a href="#modulos" class="nav-link">Módulos</a></li>
            <li><a href="#certificacion" class="nav-link">Certificación</a></li>
            <li><a href="#horarios" class="nav-link">Horarios</a></li>
            <li><a href="#instructores" class="nav-link">Instructores</a></li>
            <li><a href="#requisitos" class="nav-link">Requisitos</a></li>
            <li><a href="#resenas" class="nav-link">Reseñas</a></li>
        </ul>
    </div>
</nav>

<!-- Course Description Section -->
<section id="descripcion" class="course-section">
    <div class="container">
        <div class="section-grid">
            <div class="main-content">
                <div class="course-description">
                    <h2>Descripción del Curso</h2>
                    <div class="description-content">
                        <?php echo $course['full_description']; ?>
                    </div>
                </div>

                <!-- Learning Objectives -->
                <div class="learning-objectives">
                    <h3>Objetivos de Aprendizaje</h3>
                    <ul class="objectives-list">
                        <?php foreach ($course['learning_objectives'] as $objective): ?>
                        <li>
                            <i class="fas fa-check-circle text-success" aria-hidden="true"></i>
                            <?php echo htmlspecialchars($objective); ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Target Audience -->
                <div class="target-audience">
                    <h3>¿Para Quién es Este Curso?</h3>
                    <div class="audience-grid">
                        <?php foreach ($course['target_audiences'] as $audience): ?>
                        <div class="audience-item">
                            <i class="fas fa-user-check" aria-hidden="true"></i>
                            <span><?php echo htmlspecialchars($audience); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="sidebar">
                <!-- Course Quick Info -->
                <div class="info-card">
                    <h4>Información del Curso</h4>
                    <ul class="info-list">
                        <li>
                            <span class="info-label">Duración:</span>
                            <span class="info-value"><?php echo $course['duration_hours']; ?> horas</span>
                        </li>
                        <li>
                            <span class="info-label">Modalidad:</span>
                            <span class="info-value"><?php echo htmlspecialchars($course['modality']); ?></span>
                        </li>
                        <li>
                            <span class="info-label">Certificación:</span>
                            <span class="info-value"><?php echo htmlspecialchars($course['certification_type']); ?></span>
                        </li>
                        <li>
                            <span class="info-label">Idioma:</span>
                            <span class="info-value">Español</span>
                        </li>
                        <li>
                            <span class="info-label">Material:</span>
                            <span class="info-value">Incluido</span>
                        </li>
                        <li>
                            <span class="info-label">Validez:</span>
                            <span class="info-value"><?php echo CERTIFICATION_VALIDITY_YEARS; ?> años</span>
                        </li>
                    </ul>
                </div>

                <!-- Contact Card -->
                <div class="contact-card">
                    <h4>¿Tienes Dudas?</h4>
                    <p>Nuestro equipo te ayudará a resolver cualquier consulta</p>
                    <div class="contact-options">
                        <a href="https://wa.me/525587654321" class="btn btn-success btn-block">
                            <i class="fab fa-whatsapp" aria-hidden="true"></i> WhatsApp
                        </a>
                        <a href="tel:+525512345678" class="btn btn-outline btn-block">
                            <i class="fas fa-phone" aria-hidden="true"></i> Llamar
                        </a>
                        <a href="/contacto" class="btn btn-outline btn-block">
                            <i class="fas fa-envelope" aria-hidden="true"></i> Email
                        </a>
                    </div>
                </div>

                <!-- Social Sharing -->
                <div class="sharing-card">
                    <h4>Compartir Curso</h4>
                    <div class="social-sharing">
                        <a href="#" class="share-btn facebook" data-platform="facebook">
                            <i class="fab fa-facebook-f" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="share-btn twitter" data-platform="twitter">
                            <i class="fab fa-twitter" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="share-btn linkedin" data-platform="linkedin">
                            <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="share-btn whatsapp" data-platform="whatsapp">
                            <i class="fab fa-whatsapp" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Course Modules Section -->
<section id="modulos" class="course-section bg-light">
    <div class="container">
        <h2 class="section-title">Módulos del Curso</h2>
        <div class="modules-accordion">
            <?php foreach ($modules as $index => $module): ?>
            <div class="module-item">
                <div class="module-header" data-toggle="collapse" data-target="#module-<?php echo $index; ?>">
                    <h3>
                        <span class="module-number"><?php echo $index + 1; ?></span>
                        <?php echo htmlspecialchars($module['title']); ?>
                    </h3>
                    <div class="module-meta">
                        <span class="module-duration">
                            <i class="fas fa-clock" aria-hidden="true"></i>
                            <?php echo $module['duration_minutes']; ?> min
                        </span>
                        <i class="fas fa-chevron-down collapse-icon" aria-hidden="true"></i>
                    </div>
                </div>
                
                <div class="module-content collapse" id="module-<?php echo $index; ?>">
                    <div class="module-description">
                        <?php echo $module['description']; ?>
                    </div>
                    
                    <?php if (!empty($module['topics'])): ?>
                    <div class="module-topics">
                        <h4>Temas a tratar:</h4>
                        <ul>
                            <?php foreach ($module['topics'] as $topic): ?>
                            <li><?php echo htmlspecialchars($topic); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($module['practical_activities'])): ?>
                    <div class="practical-activities">
                        <h4>Actividades Prácticas:</h4>
                        <ul>
                            <?php foreach ($module['practical_activities'] as $activity): ?>
                            <li>
                                <i class="fas fa-hands" aria-hidden="true"></i>
                                <?php echo htmlspecialchars($activity); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Certification Section -->
<section id="certificacion" class="course-section">
    <div class="container">
        <div class="certification-showcase">
            <div class="certification-content">
                <h2>Certificación Oficial</h2>
                <p class="certification-description">
                    Al completar exitosamente este curso, recibirás una certificación oficial reconocida 
                    internacionalmente con validez de <?php echo CERTIFICATION_VALIDITY_YEARS; ?> años.
                </p>

                <div class="certification-features">
                    <div class="feature-item">
                        <i class="fas fa-globe-americas text-primary" aria-hidden="true"></i>
                        <h4>Reconocimiento Internacional</h4>
                        <p>Válida en más de 150 países</p>
                    </div>
                    
                    <div class="feature-item">
                        <i class="fas fa-digital-signature text-success" aria-hidden="true"></i>
                        <h4>Certificado Digital</h4>
                        <p>Descarga inmediata y verificación online</p>
                    </div>
                    
                    <div class="feature-item">
                        <i class="fas fa-calendar-check text-info" aria-hidden="true"></i>
                        <h4>Seguimiento de Renovación</h4>
                        <p>Te recordamos cuando necesites renovar</p>
                    </div>
                </div>
            </div>

            <div class="certification-visual">
                <div class="certificate-preview">
                    <img src="<?php echo SITE_URL; ?>/assets/images/certificates/sample-certificate.jpg" 
                         alt="Ejemplo de certificado" 
                         class="certificate-sample">
                    
                    <div class="certification-logos">
                        <?php if ($course['is_aha_certified']): ?>
                        <img src="<?php echo SITE_URL; ?>/assets/images/certifications/aha-logo.png" 
                             alt="American Heart Association" 
                             class="cert-logo">
                        <?php endif; ?>
                        
                        <?php if ($course['certification_body'] === 'ERC'): ?>
                        <img src="<?php echo SITE_URL; ?>/assets/images/certifications/erc-logo.png" 
                             alt="European Resuscitation Council" 
                             class="cert-logo">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Schedules Section -->
<section id="horarios" class="course-section bg-light">
    <div class="container">
        <h2 class="section-title">Próximos Horarios Disponibles</h2>
        
        <?php if (!empty($schedules)): ?>
        <div class="schedules-grid">
            <?php foreach ($schedules as $schedule): ?>
            <div class="schedule-card">
                <div class="schedule-header">
                    <div class="schedule-date">
                        <span class="day"><?php echo date('d', strtotime($schedule['start_date'])); ?></span>
                        <span class="month"><?php echo date('M', strtotime($schedule['start_date'])); ?></span>
                    </div>
                    <div class="schedule-info">
                        <h4><?php echo htmlspecialchars($schedule['modality']); ?></h4>
                        <p class="schedule-time">
                            <?php echo date('H:i', strtotime($schedule['start_time'])); ?> - 
                            <?php echo date('H:i', strtotime($schedule['end_time'])); ?>
                        </p>
                    </div>
                </div>

                <div class="schedule-details">
                    <div class="detail-item">
                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                        <span><?php echo htmlspecialchars($schedule['location']); ?></span>
                    </div>
                    
                    <div class="detail-item">
                        <i class="fas fa-users" aria-hidden="true"></i>
                        <span>
                            <?php echo $schedule['enrolled_count']; ?>/<?php echo $schedule['max_participants']; ?> inscritos
                        </span>
                    </div>
                    
                    <div class="detail-item">
                        <i class="fas fa-user-tie" aria-hidden="true"></i>
                        <span>Instructor: <?php echo htmlspecialchars($schedule['instructor_name']); ?></span>
                    </div>
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

                <div class="schedule-actions">
                    <?php if ($schedule['available_spots'] > 0 && $can_enroll): ?>
                    <button class="btn btn-primary enroll-btn" 
                            data-course-id="<?php echo $course['id']; ?>" 
                            data-schedule-id="<?php echo $schedule['id']; ?>">
                        <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                        Inscribirme
                    </button>
                    <?php else: ?>
                    <button class="btn btn-secondary" disabled>
                        <i class="fas fa-times" aria-hidden="true"></i>
                        No Disponible
                    </button>
                    <?php endif; ?>
                    
                    <button class="btn btn-outline add-to-calendar" 
                            data-schedule='<?php echo json_encode($schedule); ?>'>
                        <i class="fas fa-calendar-plus" aria-hidden="true"></i>
                        Agregar al Calendario
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="no-schedules">
            <i class="fas fa-calendar-times" aria-hidden="true"></i>
            <h3>Próximamente nuevas fechas</h3>
            <p>Estamos programando nuevos horarios. ¡Contáctanos para más información!</p>
            <a href="/contacto" class="btn btn-primary">
                <i class="fas fa-envelope" aria-hidden="true"></i>
                Contactar
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Related Courses Section -->
<?php if (!empty($related_courses)): ?>
<section class="related-courses-section">
    <div class="container">
        <h2 class="section-title">Cursos Relacionados</h2>
        <div class="related-courses-grid">
            <?php foreach ($related_courses as $related): ?>
            <div class="course-card compact">
                <div class="course-image">
                    <img src="<?php echo SITE_URL; ?>/assets/images/courses/<?php echo $related['featured_image'] ?? 'default-course.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($related['title']); ?>">
                    
                    <?php if ($related['is_aha_certified']): ?>
                    <span class="badge aha-badge">AHA</span>
                    <?php endif; ?>
                </div>
                
                <div class="course-content">
                    <h3><?php echo htmlspecialchars($related['title']); ?></h3>
                    <p><?php echo htmlspecialchars($related['short_description']); ?></p>
                    
                    <div class="course-meta">
                        <span class="duration">
                            <i class="fas fa-clock" aria-hidden="true"></i>
                            <?php echo $related['duration_hours']; ?>h
                        </span>
                        <span class="price">$<?php echo number_format($related['price']); ?></span>
                    </div>
                    
                    <a href="/curso/<?php echo $related['slug']; ?>" class="btn btn-outline btn-small">
                        Ver Curso
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php include VIEW_PATH . '/includes/footer.php'; ?>