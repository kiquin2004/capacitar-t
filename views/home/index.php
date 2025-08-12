<?php include VIEW_PATH . '/includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content container">
        <div class="hero-text">
            <h1>Capacitación Médica de <span class="highlight">Excelencia</span></h1>
            <p class="hero-subtitle">Centro líder en México para cursos BLS, ACLS, PALS, Stop the Bleed y Heartsaver certificados por la American Heart Association</p>
            <div class="hero-actions">
                <a href="#course-lines" class="btn btn-primary btn-large">
                    <i class="fas fa-graduation-cap" aria-hidden="true"></i> Explorar Cursos
                </a>
                <a href="/contacto" class="btn btn-outline btn-large">
                    <i class="fas fa-phone" aria-hidden="true"></i> Contacto Inmediato
                </a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number" data-target="<?php echo $stats['professionals_trained']; ?>"><?php echo number_format($stats['professionals_trained']); ?></span>
                    <span class="stat-label">Profesionales Capacitados</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="<?php echo $stats['certifications_issued']; ?>"><?php echo number_format($stats['certifications_issued']); ?></span>
                    <span class="stat-label">Certificaciones Emitidas</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-target="<?php echo $stats['years_experience']; ?>"><?php echo $stats['years_experience']; ?></span>
                    <span class="stat-label">Años de Experiencia</span>
                </div>
            </div>
        </div>
        <div class="hero-visual">
            <img src="<?php echo SITE_URL; ?>/assets/images/hero/medical-training-hero.jpg" 
                 alt="Capacitación médica profesional con maniquíes de práctica" 
                 class="hero-image lazy" 
                 data-src="<?php echo SITE_URL; ?>/assets/images/hero/medical-training-hero.jpg">
            <div class="aha-certification-badge">
                <img src="<?php echo SITE_URL; ?>/assets/images/badges/aha-training-center.png" 
                     alt="American Heart Association Training Center" 
                     class="certification-logo">
            </div>
        </div>
    </div>
</section>

<!-- Three Main Course Lines Section -->
<section id="course-lines" class="course-lines-section">
    <div class="container">
        <div class="section-header text-center">
            <h2>Nuestras Líneas de Capacitación</h2>
            <p class="section-subtitle">Programas especializados diseñados para diferentes perfiles profesionales</p>
        </div>

        <div class="course-lines-grid">
            <?php foreach ($course_lines as $line): ?>
            <div class="course-line-card" data-line="<?php echo $line['id']; ?>">
                <div class="card-icon" style="color: <?php echo $line['color']; ?>">
                    <i class="<?php echo $line['icon']; ?>" aria-hidden="true"></i>
                </div>
                <div class="card-content">
                    <h3><?php echo htmlspecialchars($line['title']); ?></h3>
                    <p class="card-subtitle"><?php echo htmlspecialchars($line['subtitle']); ?></p>
                    <p class="card-description"><?php echo htmlspecialchars($line['description']); ?></p>
                    
                    <div class="course-highlights">
                        <h4>Cursos Disponibles:</h4>
                        <ul class="course-list">
                            <?php foreach ($line['courses'] as $course): ?>
                            <li><i class="fas fa-check" aria-hidden="true"></i> <?php echo htmlspecialchars($course); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="target-info">
                        <strong>Dirigido a:</strong> <?php echo htmlspecialchars($line['target_audience']); ?>
                    </div>

                    <div class="certification-info">
                        <strong>Certificaciones:</strong> <?php echo htmlspecialchars($line['certifications']); ?>
                    </div>

                    <div class="card-actions">
                        <a href="/cursos/<?php echo str_replace('_', '-', $line['id']); ?>" class="btn btn-primary">
                            <i class="fas fa-arrow-right" aria-hidden="true"></i> Ver Cursos
                        </a>
                        <a href="/contacto?interes=<?php echo $line['id']; ?>" class="btn btn-outline">
                            <i class="fas fa-envelope" aria-hidden="true"></i> Información
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Courses Section -->
<section class="featured-courses-section">
    <div class="container">
        <div class="section-header">
            <h2>Cursos Destacados</h2>
            <p class="section-subtitle">Los cursos más solicitados por nuestros estudiantes</p>
        </div>

        <div class="featured-courses-grid">
            <?php foreach ($featured_courses as $course): ?>
            <div class="course-card" data-course-slug="<?php echo $course['slug']; ?>">
                <div class="course-image">
                    <img src="<?php echo SITE_URL; ?>/assets/images/courses/<?php echo $course['featured_image'] ?? 'default-course.jpg'; ?>" 
                         alt="<?php echo htmlspecialchars($course['title']); ?>" 
                         class="lazy" 
                         data-src="<?php echo SITE_URL; ?>/assets/images/courses/<?php echo $course['featured_image'] ?? 'default-course.jpg'; ?>">
                    
                    <div class="course-badges">
                        <?php if ($course['is_aha_certified']): ?>
                        <span class="badge aha-badge">AHA</span>
                        <?php endif; ?>
                        
                        <?php if ($course['difficulty_level']): ?>
                        <span class="badge difficulty-badge"><?php echo htmlspecialchars($course['difficulty_level']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="course-content">
                    <div class="course-category">
                        <i class="fas fa-tag" aria-hidden="true"></i>
                        <?php echo htmlspecialchars($course['category_name']); ?>
                    </div>

                    <h3 class="course-title"><?php echo htmlspecialchars($course['title']); ?></h3>
                    <p class="course-description"><?php echo htmlspecialchars($course['short_description']); ?></p>

                    <div class="course-meta">
                        <div class="course-duration">
                            <i class="fas fa-clock" aria-hidden="true"></i>
                            <?php echo $course['duration_hours']; ?> horas
                        </div>
                        
                        <div class="course-modality">
                            <i class="fas fa-users" aria-hidden="true"></i>
                            <?php echo htmlspecialchars($course['modality']); ?>
                        </div>
                        
                        <div class="course-rating">
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?php echo $i <= $course['average_rating'] ? '' : '-o'; ?>" aria-hidden="true"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-count">(<?php echo $course['total_reviews']; ?>)</span>
                        </div>
                    </div>

                    <div class="course-price">
                        <?php if ($course['original_price'] > $course['price']): ?>
                        <span class="original-price">$<?php echo number_format($course['original_price']); ?></span>
                        <?php endif; ?>
                        <span class="current-price">$<?php echo number_format($course['price']); ?> MXN</span>
                    </div>

                    <div class="course-actions">
                        <a href="/curso/<?php echo $course['slug']; ?>" class="btn btn-primary">
                            <i class="fas fa-info-circle" aria-hidden="true"></i> Ver Detalles
                        </a>
                        
                        <?php if ($course['next_schedule_date']): ?>
                        <a href="/curso/<?php echo $course['slug']; ?>#horarios" class="btn btn-outline">
                            <i class="fas fa-calendar" aria-hidden="true"></i> 
                            <?php echo date('d M', strtotime($course['next_schedule_date'])); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="section-footer text-center">
            <a href="/cursos" class="btn btn-secondary btn-large">
                <i class="fas fa-th-list" aria-hidden="true"></i> Ver Todos los Cursos
            </a>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-choose-us-section">
    <div class="container">
        <div class="section-header text-center">
            <h2>¿Por Qué Elegirnos?</h2>
            <p class="section-subtitle">Más de 15 años formando profesionales en emergencias médicas</p>
        </div>

        <div class="benefits-grid">
            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="fas fa-certificate text-primary" aria-hidden="true"></i>
                </div>
                <h3>Certificación AHA</h3>
                <p>Somos Centro de Entrenamiento oficial de la American Heart Association con instructores certificados.</p>
            </div>

            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="fas fa-users-cog text-success" aria-hidden="true"></i>
                </div>
                <h3>Grupos Reducidos</h3>
                <p>Máximo 16 participantes por curso para garantizar atención personalizada y práctica intensiva.</p>
            </div>

            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="fas fa-hospital text-info" aria-hidden="true"></i>
                </div>
                <h3>Simulación Real</h3>
                <p>Maniquíes de alta fidelidad y escenarios clínicos reales para una experiencia de aprendizaje inmersiva.</p>
            </div>

            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="fas fa-clock text-warning" aria-hidden="true"></i>
                </div>
                <h3>Horarios Flexibles</h3>
                <p>Cursos disponibles entre semana, fines de semana y modalidades intensivas para adaptarse a tu agenda.</p>
            </div>

            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="fas fa-handshake text-secondary" aria-hidden="true"></i>
                </div>
                <h3>Seguimiento Continuo</h3>
                <p>Acompañamiento post-certificación y recordatorios de renovación para mantener tus credenciales actualizadas.</p>
            </div>

            <div class="benefit-item">
                <div class="benefit-icon">
                    <i class="fas fa-medal text-primary" aria-hidden="true"></i>
                </div>
                <h3>Reconocimiento Internacional</h3>
                <p>Certificaciones válidas mundialmente que fortalecen tu perfil profesional en el sector salud.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-header text-center">
            <h2>Lo Que Dicen Nuestros Estudiantes</h2>
            <p class="section-subtitle">Testimonios reales de profesionales capacitados</p>
        </div>

        <div class="testimonials-carousel">
            <?php foreach ($testimonials as $testimonial): ?>
            <div class="testimonial-card" data-demographic="<?php echo $testimonial['demographic']; ?>">
                <div class="testimonial-content">
                    <div class="testimonial-text">
                        <blockquote>
                            "<?php echo htmlspecialchars($testimonial['text']); ?>"
                        </blockquote>
                    </div>
                    
                    <div class="testimonial-rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star<?php echo $i <= $testimonial['rating'] ? '' : '-o'; ?>" aria-hidden="true"></i>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="testimonial-author">
                    <img src="<?php echo SITE_URL; ?>/<?php echo $testimonial['image']; ?>" 
                         alt="<?php echo htmlspecialchars($testimonial['name']); ?>" 
                         class="author-photo lazy" 
                         data-src="<?php echo SITE_URL; ?>/<?php echo $testimonial['image']; ?>">
                    <div class="author-info">
                        <h4 class="author-name"><?php echo htmlspecialchars($testimonial['name']); ?></h4>
                        <p class="author-position"><?php echo htmlspecialchars($testimonial['position']); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="testimonial-navigation">
            <button class="testimonial-nav" data-direction="prev" aria-label="Testimonio anterior">
                <i class="fas fa-chevron-left" aria-hidden="true"></i>
            </button>
            <button class="testimonial-nav" data-direction="next" aria-label="Siguiente testimonio">
                <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <div class="cta-text">
                <h2>¿Listo para Certificarte?</h2>
                <p>Únete a miles de profesionales que han confiado en nosotros para su capacitación médica. Comienza tu camino hacia la excelencia profesional hoy mismo.</p>
            </div>
            <div class="cta-actions">
                <a href="/registro" class="btn btn-primary btn-large">
                    <i class="fas fa-user-plus" aria-hidden="true"></i> Crear Cuenta Gratuita
                </a>
                <a href="/contacto" class="btn btn-outline btn-large">
                    <i class="fas fa-phone" aria-hidden="true"></i> Hablar con Asesor
                </a>
            </div>
        </div>
        
        <div class="cta-emergency-contact">
            <div class="emergency-info">
                <i class="fas fa-phone-alt" aria-hidden="true"></i>
                <div>
                    <strong>¿Necesitas capacitación urgente?</strong>
                    <p>WhatsApp: <a href="https://wa.me/5255876543210">+52 55 8765-4321</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include VIEW_PATH . '/includes/footer.php'; ?>