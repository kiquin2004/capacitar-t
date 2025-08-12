<!DOCTYPE html>
<html lang="es" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Capacitar-T México - Capacitación Médica BLS, ACLS, PALS'; ?></title>
    <meta name="description" content="<?php echo isset($description) ? htmlspecialchars($description) : 'Centro líder en capacitación médica con cursos BLS, ACLS, PALS, Stop the Bleed certificados por AHA. Para profesionales médicos, padres, maestros y personal de brigadas.'; ?>">
    <meta name="keywords" content="<?php echo SITE_KEYWORDS; ?>">
    <meta name="author" content="<?php echo SITE_NAME; ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo htmlspecialchars($current_url ?? Router::currentUrl()); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo SITE_NAME; ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($current_url ?? Router::currentUrl()); ?>">
    <meta property="og:title" content="<?php echo isset($title) ? htmlspecialchars($title) : SITE_NAME; ?>">
    <meta property="og:description" content="<?php echo isset($description) ? htmlspecialchars($description) : SITE_DESCRIPTION; ?>">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/og-image-capacitar-t.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="es_MX">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@capacitartmx">
    <meta name="twitter:creator" content="@capacitartmx">
    <meta name="twitter:url" content="<?php echo htmlspecialchars($current_url ?? Router::currentUrl()); ?>">
    <meta name="twitter:title" content="<?php echo isset($title) ? htmlspecialchars($title) : SITE_NAME; ?>">
    <meta name="twitter:description" content="<?php echo isset($description) ? htmlspecialchars($description) : SITE_DESCRIPTION; ?>">
    <meta name="twitter:image" content="<?php echo SITE_URL; ?>/assets/images/twitter-card-capacitar-t.jpg">

    <!-- Medical Schema.org Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "MedicalOrganization",
        "name": "<?php echo SITE_NAME; ?>",
        "url": "<?php echo SITE_URL; ?>",
        "logo": "<?php echo SITE_URL; ?>/assets/images/logo-capacitar-t.png",
        "description": "<?php echo SITE_DESCRIPTION; ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Av. Universidad 1200",
            "addressLocality": "Ciudad de México",
            "addressRegion": "CDMX",
            "postalCode": "03100",
            "addressCountry": "MX"
        },
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "+52-55-1234-5678",
            "contactType": "customer service",
            "availableLanguage": ["Spanish", "English"]
        },
        "medicalSpecialty": [
            "Emergency Medicine",
            "Cardiology", 
            "Pediatrics",
            "First Aid Training"
        ],
        "hasCredential": {
            "@type": "EducationalOccupationalCredential",
            "credentialCategory": "American Heart Association Training Center"
        }
    }
    </script>

    <!-- Course Schema (if on course page) -->
    <?php if (isset($course)): ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Course",
        "name": "<?php echo htmlspecialchars($course['title']); ?>",
        "description": "<?php echo htmlspecialchars($course['short_description']); ?>",
        "provider": {
            "@type": "Organization",
            "name": "<?php echo SITE_NAME; ?>",
            "url": "<?php echo SITE_URL; ?>"
        },
        "courseCode": "<?php echo htmlspecialchars($course['course_code'] ?? ''); ?>",
        "educationalCredentialAwarded": "<?php echo htmlspecialchars($course['certification_type'] ?? ''); ?>",
        "timeRequired": "PT<?php echo $course['duration_hours']; ?>H",
        "occupationalCategory": "Healthcare",
        "audience": {
            "@type": "EducationalAudience",
            "audienceType": "<?php echo htmlspecialchars($course['target_audience'] ?? 'Healthcare Professionals'); ?>"
        },
        "offers": {
            "@type": "Offer",
            "price": "<?php echo $course['price']; ?>",
            "priceCurrency": "MXN"
        }
    }
    </script>
    <?php endif; ?>

    <!-- Favicon and Touch Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo SITE_URL; ?>/assets/images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo SITE_URL; ?>/assets/images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo SITE_URL; ?>/assets/images/favicons/favicon-16x16.png">
    <link rel="manifest" href="<?php echo SITE_URL; ?>/site.webmanifest">
    <link rel="mask-icon" href="<?php echo SITE_URL; ?>/assets/images/favicons/safari-pinned-tab.svg" color="#dc143c">
    <meta name="msapplication-TileColor" content="#dc143c">
    <meta name="theme-color" content="#dc143c">

    <!-- Preconnect to External Domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    
    <!-- Critical CSS (inline for performance) -->
    <style>
        /* Critical above-the-fold styles */
        :root{--primary-red:#dc143c;--primary-blue:#2c5aa0;--text-dark:#2d3748;--bg-white:#ffffff;--border-color:#e2e8f0;}
        body{margin:0;font-family:Inter,-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;color:var(--text-dark);}
        .header{background:var(--bg-white);position:fixed;top:0;left:0;right:0;z-index:1030;box-shadow:0 1px 3px rgba(0,0,0,0.1);}
        .navbar{display:flex;align-items:center;justify-content:space-between;padding:1rem;max-width:1200px;margin:0 auto;}
        .logo{font-size:1.5rem;font-weight:700;color:var(--primary-red);text-decoration:none;}
        .hero{background:linear-gradient(135deg,var(--primary-red) 0%,var(--primary-blue) 100%);color:white;padding:120px 0 80px;text-align:center;}
        .hero h1{font-size:3rem;margin:0 0 1.5rem 0;font-weight:600;}
        .hero p{font-size:1.2rem;margin:0 0 2rem 0;opacity:0.9;}
    </style>

    <!-- Web Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/main.css?v=<?php echo filemtime(__DIR__ . '/../../assets/css/main.css'); ?>">
    
    <!-- jQuery (loaded early for MVVM) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!-- Global Variables for JavaScript -->
    <script>
        window.SITE_URL = '<?php echo SITE_URL; ?>';
        window.isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        window.csrfToken = '<?php echo $_SESSION['_csrf_token'] ?? ''; ?>';
        window.userRole = '<?php echo $_SESSION['user_role'] ?? 'guest'; ?>';
        window.currentPage = '<?php echo basename($_SERVER['PHP_SELF'], '.php'); ?>';
    </script>

    <!-- Google Analytics -->
    <?php if (defined('GA_TRACKING_ID') && GA_TRACKING_ID): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo GA_TRACKING_ID; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo GA_TRACKING_ID; ?>', {
            send_page_view: false,
            custom_map: {
                'custom_dimension_1': 'user_type'
            }
        });
    </script>
    <?php endif; ?>

    <!-- Facebook Pixel -->
    <?php if (defined('FB_PIXEL_ID') && FB_PIXEL_ID): ?>
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?php echo FB_PIXEL_ID; ?>');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo FB_PIXEL_ID; ?>&ev=PageView&noscript=1"/></noscript>
    <?php endif; ?>
</head>
<body data-page="<?php echo $page ?? 'home'; ?>" class="loading">
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Saltar al contenido principal</a>

    <header class="header" role="banner">
        <nav class="navbar container" role="navigation" aria-label="Navegación principal">
            <a href="<?php echo SITE_URL; ?>/" class="logo" aria-label="Capacitar-T México - Inicio">
                <i class="fas fa-heartbeat" aria-hidden="true"></i>
                <span>Capacitar-T</span>
                <small style="font-size: 0.7em; font-weight: 400; color: var(--primary-blue);">México</small>
            </a>
            
            <ul class="nav-menu" role="menubar">
                <li role="none">
                    <a href="<?php echo SITE_URL; ?>/" role="menuitem" <?php echo Router::isActive('/') ? 'aria-current="page"' : ''; ?>>
                        <i class="fas fa-home" aria-hidden="true"></i> Inicio
                    </a>
                </li>
                
                <li class="dropdown" role="none">
                    <a href="<?php echo SITE_URL; ?>/cursos" role="menuitem" aria-expanded="false" aria-haspopup="true">
                        <i class="fas fa-graduation-cap" aria-hidden="true"></i> Cursos <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </a>
                    <ul class="dropdown-menu" role="menu" aria-label="Categorías de cursos">
                        <li role="none">
                            <a href="<?php echo SITE_URL; ?>/cursos/profesionales-medicos" role="menuitem">
                                <i class="fas fa-user-md" aria-hidden="true"></i>
                                <div>
                                    <strong>Profesionales Médicos</strong>
                                    <small>BLS, ACLS, PALS, Stop the Bleed</small>
                                </div>
                            </a>
                        </li>
                        <li role="none">
                            <a href="<?php echo SITE_URL; ?>/cursos/primeros-auxilios-comunitarios" role="menuitem">
                                <i class="fas fa-heart" aria-hidden="true"></i>
                                <div>
                                    <strong>Primeros Auxilios</strong>
                                    <small>Para padres, maestros, cuidadores</small>
                                </div>
                            </a>
                        </li>
                        <li role="none">
                            <a href="<?php echo SITE_URL; ?>/cursos/gestion-medica" role="menuitem">
                                <i class="fas fa-hospital" aria-hidden="true"></i>
                                <div>
                                    <strong>Gestión Médica</strong>
                                    <small>Consultorios, clínicas, urgencias</small>
                                </div>
                            </a>
                        </li>
                        <li role="separator" class="dropdown-divider"></li>
                        <li role="none">
                            <a href="<?php echo SITE_URL; ?>/cursos" role="menuitem">
                                <i class="fas fa-list" aria-hidden="true"></i> Ver Todos los Cursos
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="dropdown" role="none">
                    <a href="#" role="menuitem" aria-expanded="false" aria-haspopup="true">
                        <i class="fas fa-certificate" aria-hidden="true"></i> Certificaciones <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </a>
                    <ul class="dropdown-menu" role="menu" aria-label="Tipos de certificación">
                        <li role="none">
                            <a href="<?php echo SITE_URL; ?>/certificaciones/aha" role="menuitem">
                                <div class="certification-badge aha">AHA</div>
                                <div>
                                    <strong>American Heart Association</strong>
                                    <small>BLS, ACLS, PALS, Heartsaver</small>
                                </div>
                            </a>
                        </li>
                        <li role="none">
                            <a href="<?php echo SITE_URL; ?>/certificaciones/erc" role="menuitem">
                                <div class="certification-badge erc">ERC</div>
                                <div>
                                    <strong>European Resuscitation Council</strong>
                                    <small>Soporte vital europeo</small>
                                </div>
                            </a>
                        </li>
                        <li role="none">
                            <a href="<?php echo SITE_URL; ?>/certificaciones/capacitar-t" role="menuitem">
                                <div class="certification-badge">CT</div>
                                <div>
                                    <strong>Capacitar-T</strong>
                                    <small>Certificaciones propias</small>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li role="none">
                    <a href="<?php echo SITE_URL; ?>/nosotros" role="menuitem">
                        <i class="fas fa-users" aria-hidden="true"></i> Nosotros
                    </a>
                </li>
                
                <li role="none">
                    <a href="<?php echo SITE_URL; ?>/contacto" role="menuitem">
                        <i class="fas fa-envelope" aria-hidden="true"></i> Contacto
                    </a>
                </li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                <li class="dropdown user-dropdown" role="none">
                    <a href="#" role="menuitem" aria-expanded="false" aria-haspopup="true" class="user-menu">
                        <i class="fas fa-user-circle" aria-hidden="true"></i> 
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?>
                        <i class="fas fa-chevron-down" aria-hidden="true"></i>
                    </a>
                    <ul class="dropdown-menu" role="menu" aria-label="Menú de usuario">
                        <li role="none">
                            <a href="<?php echo SITE_URL; ?>/perfil" role="menuitem">
                                <i class="fas fa-user" aria-hidden="true"></i> Mi Perfil
                            </a>
                        </li>
                        <li role="none">
                            <a href="<?php echo SITE_URL; ?>/mis-cursos" role="menuitem">
                                <i class="fas fa-book" aria-hidden="true"></i> Mis Cursos
                            </a>
                        </li>
                        <li role="none">
                            <a href="<?php echo SITE_URL; ?>/certificados" role="menuitem">
                                <i class="fas fa-certificate" aria-hidden="true"></i> Certificados
                            </a>
                        </li>
                        <li role="separator" class="dropdown-divider"></li>
                        <li role="none">
                            <a href="<?php echo SITE_URL; ?>/logout" role="menuitem">
                                <i class="fas fa-sign-out-alt" aria-hidden="true"></i> Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </li>
                <?php else: ?>
                <li role="none">
                    <a href="<?php echo SITE_URL; ?>/login" role="menuitem" class="btn btn-outline">
                        <i class="fas fa-sign-in-alt" aria-hidden="true"></i> Iniciar Sesión
                    </a>
                </li>
                <li role="none">
                    <a href="<?php echo SITE_URL; ?>/registro" role="menuitem" class="btn btn-primary">
                        <i class="fas fa-user-plus" aria-hidden="true"></i> Registrarse
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            
            <button class="mobile-menu-toggle" aria-label="Abrir menú de navegación" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </nav>
    </header>

    <main id="main-content" class="main-content" role="main">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['flash_type'] ?? 'info'; ?>" role="alert" aria-live="polite">
                <div class="container">
                    <i class="fas fa-<?php echo $_SESSION['flash_type'] === 'success' ? 'check-circle' : ($_SESSION['flash_type'] === 'error' ? 'exclamation-triangle' : 'info-circle'); ?>" aria-hidden="true"></i>
                    <?php 
                    echo htmlspecialchars($_SESSION['flash_message']);
                    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
                    ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Creative Commons Attribution for Images -->
        <div class="cc-notice" style="display: none;" aria-hidden="true">
            <p>Las imágenes utilizadas en este sitio provienen de fuentes Creative Commons:</p>
            <ul>
                <li>Unsplash.com - Licencia Unsplash</li>
                <li>Pexels.com - Licencia Pexels</li>
                <li>Pixabay.com - Licencia Pixabay</li>
                <li>Wikimedia Commons - Licencias CC BY-SA</li>
            </ul>
        </div>