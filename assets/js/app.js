/**
 * Capacitar-T.com.mx - Medical Training Platform
 * MVVM Architecture with jQuery
 * Optimized for 40+, Millennials, and Gen Beta demographics
 */

// MVVM ViewModel Base Class
class ViewModelBase {
    constructor() {
        this.data = {};
        this.bindings = new Map();
        this.init();
    }

    // Initialize ViewModel - override in subclasses
    init() {}

    // Data binding methods
    bind(key, value) {
        this.data[key] = value;
        this.notifyBindings(key, value);
    }

    get(key) {
        return this.data[key];
    }

    // Register data binding
    registerBinding(key, callback) {
        if (!this.bindings.has(key)) {
            this.bindings.set(key, []);
        }
        this.bindings.get(key).push(callback);
    }

    // Notify all bindings when data changes
    notifyBindings(key, value) {
        if (this.bindings.has(key)) {
            this.bindings.get(key).forEach(callback => callback(value));
        }
    }

    // Command pattern for user interactions
    executeCommand(commandName, ...args) {
        if (typeof this[commandName] === 'function') {
            this[commandName](...args);
        }
    }
}

// Main Application ViewModel
class AppViewModel extends ViewModelBase {
    init() {
        this.setupEventListeners();
        this.initializeComponents();
        this.setupAccessibility();
        this.trackUserDemographic();
    }

    setupEventListeners() {
        // Mobile menu toggle
        $(document).on('click', '.mobile-menu-toggle', () => {
            this.toggleMobileMenu();
        });

        // Header scroll effect
        $(window).on('scroll', () => {
            this.handleHeaderScroll();
        });

        // Smooth scrolling for anchor links
        $(document).on('click', 'a[href^="#"]', (e) => {
            this.smoothScrollToAnchor(e);
        });

        // Course card interactions
        $(document).on('click', '.course-card', (e) => {
            this.handleCourseCardClick(e);
        });

        // Form submissions
        $(document).on('submit', 'form', (e) => {
            this.handleFormSubmission(e);
        });

        // Search functionality
        $(document).on('input', '.search-input', (e) => {
            this.handleSearch(e);
        });

        // Filter interactions
        $(document).on('change', '.filter-select, .filter-checkbox', (e) => {
            this.handleFilterChange(e);
        });

        // Close mobile menu when clicking outside
        $(document).on('click', (e) => {
            if (!$(e.target).closest('.navbar').length) {
                this.closeMobileMenu();
            }
        });
    }

    initializeComponents() {
        // Initialize statistics counter animation
        this.initStatsCounter();
        
        // Initialize course carousel/slider
        this.initCourseCarousel();
        
        // Initialize testimonials rotation
        this.initTestimonials();
        
        // Initialize course filters
        this.initCourseFilters();
        
        // Initialize contact form enhancements
        this.initContactForm();
        
        // Initialize lazy loading for images
        this.initLazyLoading();
        
        // Initialize tooltips
        this.initTooltips();
    }

    setupAccessibility() {
        // Add ARIA labels and improve keyboard navigation
        this.enhanceKeyboardNavigation();
        this.addAriaLabels();
        this.setupFocusManagement();
    }

    trackUserDemographic() {
        // Detect user preferences and adapt interface accordingly
        const userAgent = navigator.userAgent;
        const screenSize = window.innerWidth;
        
        // Demographic-based adaptations
        if (screenSize >= 1200) {
            this.bind('layout', 'desktop');
        } else if (screenSize >= 768) {
            this.bind('layout', 'tablet');
        } else {
            this.bind('layout', 'mobile');
        }

        // Track for analytics
        this.trackEvent('user_demographic', {
            screen_size: screenSize,
            user_agent: userAgent.substring(0, 100)
        });
    }

    // Mobile Menu Methods
    toggleMobileMenu() {
        const $menu = $('.nav-menu');
        const $toggle = $('.mobile-menu-toggle');
        
        $menu.toggleClass('active');
        $toggle.toggleClass('active');
        
        // Update toggle button animation
        const $spans = $toggle.find('span');
        if ($menu.hasClass('active')) {
            $spans.eq(0).css('transform', 'rotate(45deg) translate(6px, 6px)');
            $spans.eq(1).css('opacity', '0');
            $spans.eq(2).css('transform', 'rotate(-45deg) translate(6px, -6px)');
        } else {
            $spans.css({
                'transform': 'none',
                'opacity': '1'
            });
        }

        // Prevent body scroll when menu is open
        $('body').toggleClass('menu-open', $menu.hasClass('active'));
    }

    closeMobileMenu() {
        $('.nav-menu').removeClass('active');
        $('.mobile-menu-toggle').removeClass('active');
        $('body').removeClass('menu-open');
        
        // Reset toggle button
        $('.mobile-menu-toggle span').css({
            'transform': 'none',
            'opacity': '1'
        });
    }

    // Header scroll effect
    handleHeaderScroll() {
        const scrollTop = $(window).scrollTop();
        const $header = $('.header');
        
        if (scrollTop > 100) {
            $header.addClass('scrolled');
        } else {
            $header.removeClass('scrolled');
        }
    }

    // Smooth scrolling
    smoothScrollToAnchor(e) {
        const href = $(e.currentTarget).attr('href');
        
        if (href.startsWith('#')) {
            e.preventDefault();
            const target = $(href);
            
            if (target.length) {
                const offsetTop = target.offset().top - 100; // Account for fixed header
                
                $('html, body').animate({
                    scrollTop: offsetTop
                }, 800, 'easeInOutCubic');
            }
        }
    }

    // Statistics Counter Animation
    initStatsCounter() {
        const $stats = $('.stat-number');
        let animated = false;

        const animateStats = () => {
            if (animated) return;
            animated = true;

            $stats.each(function() {
                const $this = $(this);
                const target = parseInt($this.text().replace(/,/g, ''));
                let current = 0;
                const increment = target / 60; // 60 frames for 1-second animation
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    
                    // Format number with commas for readability (important for 40+ demographic)
                    $this.text(Math.floor(current).toLocaleString());
                }, 16); // ~60 FPS
            });
        };

        // Trigger animation when stats section is visible
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateStats();
                }
            });
        }, { threshold: 0.5 });

        $('.stats-section').each(function() {
            observer.observe(this);
        });
    }

    // Course Carousel/Slider
    initCourseCarousel() {
        const $carousel = $('.course-carousel');
        if ($carousel.length === 0) return;

        let currentIndex = 0;
        const $slides = $carousel.find('.course-slide');
        const totalSlides = $slides.length;
        const slideDuration = 5000; // 5 seconds per slide

        if (totalSlides <= 1) return;

        // Auto-advance slides
        const autoAdvance = () => {
            currentIndex = (currentIndex + 1) % totalSlides;
            this.showSlide(currentIndex);
        };

        let intervalId = setInterval(autoAdvance, slideDuration);

        // Pause on hover (accessibility)
        $carousel.on('mouseenter', () => {
            clearInterval(intervalId);
        }).on('mouseleave', () => {
            intervalId = setInterval(autoAdvance, slideDuration);
        });

        // Manual navigation
        $(document).on('click', '.carousel-nav button', (e) => {
            const direction = $(e.currentTarget).data('direction');
            currentIndex = direction === 'next' 
                ? (currentIndex + 1) % totalSlides
                : (currentIndex - 1 + totalSlides) % totalSlides;
            
            this.showSlide(currentIndex);
            
            // Reset auto-advance
            clearInterval(intervalId);
            intervalId = setInterval(autoAdvance, slideDuration);
        });

        // Keyboard navigation
        $carousel.on('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                this.showSlide(currentIndex);
            } else if (e.key === 'ArrowRight') {
                currentIndex = (currentIndex + 1) % totalSlides;
                this.showSlide(currentIndex);
            }
        });

        // Initialize first slide
        this.showSlide(0);
    }

    showSlide(index) {
        const $carousel = $('.course-carousel');
        const $slides = $carousel.find('.course-slide');
        const $indicators = $carousel.find('.carousel-indicator');

        $slides.removeClass('active').eq(index).addClass('active');
        $indicators.removeClass('active').eq(index).addClass('active');

        // Update ARIA attributes
        $slides.attr('aria-hidden', 'true').eq(index).attr('aria-hidden', 'false');
    }

    // Testimonials rotation
    initTestimonials() {
        const $testimonials = $('.testimonial-card');
        if ($testimonials.length <= 3) return; // Don't rotate if 3 or fewer testimonials

        let currentSet = 0;
        const testimonialsPerPage = 3;
        const totalSets = Math.ceil($testimonials.length / testimonialsPerPage);

        const showTestimonialSet = (setIndex) => {
            const startIndex = setIndex * testimonialsPerPage;
            const endIndex = startIndex + testimonialsPerPage;

            $testimonials.hide().slice(startIndex, endIndex).show();
            
            // Update indicators
            $('.testimonial-indicator').removeClass('active').eq(setIndex).addClass('active');
        };

        // Auto-rotate every 8 seconds
        setInterval(() => {
            currentSet = (currentSet + 1) % totalSets;
            showTestimonialSet(currentSet);
        }, 8000);

        // Manual navigation
        $(document).on('click', '.testimonial-nav button', (e) => {
            const direction = $(e.currentTarget).data('direction');
            currentSet = direction === 'next' 
                ? (currentSet + 1) % totalSets
                : (currentSet - 1 + totalSets) % totalSets;
            showTestimonialSet(currentSet);
        });

        // Initialize first set
        showTestimonialSet(0);
    }

    // Course Filters
    initCourseFilters() {
        const $filterContainer = $('.course-filters');
        if ($filterContainer.length === 0) return;

        // Initialize filter state
        this.bind('activeFilters', {
            category: '',
            difficulty: '',
            certification: '',
            priceRange: ''
        });

        // Apply filters when changed
        this.registerBinding('activeFilters', (filters) => {
            this.applyFilters(filters);
        });

        // Search with debouncing
        let searchTimeout;
        $(document).on('input', '.course-search', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = $(e.target).val();
                this.filterCoursesBySearch(searchTerm);
            }, 300);
        });
    }

    handleFilterChange(e) {
        const $filter = $(e.target);
        const filterType = $filter.data('filter-type');
        const filterValue = $filter.val();
        
        const currentFilters = this.get('activeFilters');
        currentFilters[filterType] = filterValue;
        
        this.bind('activeFilters', currentFilters);
        
        // Track filter usage
        this.trackEvent('filter_used', {
            type: filterType,
            value: filterValue
        });
    }

    applyFilters(filters) {
        const $courses = $('.course-card');
        let visibleCount = 0;

        $courses.each(function() {
            const $course = $(this);
            let shouldShow = true;

            // Check each filter
            Object.keys(filters).forEach(filterType => {
                const filterValue = filters[filterType];
                if (!filterValue) return;

                const courseValue = $course.data(filterType.replace('_', '-'));
                if (courseValue !== filterValue) {
                    shouldShow = false;
                }
            });

            if (shouldShow) {
                $course.show();
                visibleCount++;
            } else {
                $course.hide();
            }
        });

        // Update results count
        $('.results-count').text(`${visibleCount} cursos encontrados`);
        
        // Show no results message if needed
        $('.no-results').toggle(visibleCount === 0);
    }

    filterCoursesBySearch(searchTerm) {
        const $courses = $('.course-card');
        let visibleCount = 0;

        if (!searchTerm.trim()) {
            $courses.show();
            visibleCount = $courses.length;
        } else {
            const terms = searchTerm.toLowerCase().split(' ');
            
            $courses.each(function() {
                const $course = $(this);
                const searchableText = [
                    $course.find('.course-title').text(),
                    $course.find('.course-description').text(),
                    $course.find('.course-category').text()
                ].join(' ').toLowerCase();

                const matches = terms.every(term => searchableText.includes(term));
                
                if (matches) {
                    $course.show();
                    visibleCount++;
                } else {
                    $course.hide();
                }
            });
        }

        $('.results-count').text(`${visibleCount} cursos encontrados`);
        $('.no-results').toggle(visibleCount === 0);
    }

    // Contact Form Enhancements
    initContactForm() {
        const $form = $('.contact-form');
        if ($form.length === 0) return;

        // Progressive enhancement for better UX
        $form.find('.form-control').on('blur', (e) => {
            this.validateField($(e.target));
        });

        // Auto-suggest course interest based on previous page
        const referrer = document.referrer;
        if (referrer.includes('/curso/')) {
            const courseName = this.extractCourseNameFromUrl(referrer);
            if (courseName) {
                $form.find('#course_interest').val(courseName);
            }
        }

        // Phone number formatting (Mexican format)
        $form.find('input[type="tel"]').on('input', (e) => {
            this.formatPhoneNumber($(e.target));
        });
    }

    validateField($field) {
        const value = $field.val().trim();
        const fieldType = $field.attr('type') || 'text';
        const isRequired = $field.prop('required');
        
        // Remove existing error
        $field.removeClass('error').siblings('.field-error').remove();

        let isValid = true;
        let errorMessage = '';

        if (isRequired && !value) {
            isValid = false;
            errorMessage = 'Este campo es requerido';
        } else if (fieldType === 'email' && value && !this.isValidEmail(value)) {
            isValid = false;
            errorMessage = 'Ingresa un email válido';
        } else if (fieldType === 'tel' && value && !this.isValidPhone(value)) {
            isValid = false;
            errorMessage = 'Ingresa un teléfono válido';
        }

        if (!isValid) {
            $field.addClass('error');
            $field.after(`<div class="field-error">${errorMessage}</div>`);
        }

        return isValid;
    }

    formatPhoneNumber($field) {
        let value = $field.val().replace(/\D/g, '');
        
        if (value.length >= 10) {
            // Mexican format: +52 55 1234 5678
            if (value.startsWith('52')) {
                value = value.substring(2);
            }
            
            if (value.length === 10) {
                value = `+52 ${value.substring(0, 2)} ${value.substring(2, 6)} ${value.substring(6)}`;
            }
        }
        
        $field.val(value);
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    isValidPhone(phone) {
        // Mexican phone number validation
        return /^\+52\s\d{2}\s\d{4}\s\d{4}$/.test(phone) || /^\d{10}$/.test(phone.replace(/\D/g, ''));
    }

    // Form Submission Handler
    handleFormSubmission(e) {
        const $form = $(e.target);
        
        if ($form.hasClass('no-validate')) {
            return; // Skip validation for certain forms
        }

        let isValid = true;
        
        // Validate all required fields
        $form.find('.form-control[required]').each((index, field) => {
            if (!this.validateField($(field))) {
                isValid = false;
            }
        });

        if (!isValid) {
            e.preventDefault();
            
            // Focus on first error field
            const $firstError = $form.find('.form-control.error').first();
            if ($firstError.length) {
                $firstError.focus();
                $firstError[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
            
            return false;
        }

        // Show loading state
        const $submitBtn = $form.find('button[type="submit"]');
        const originalText = $submitBtn.text();
        
        $submitBtn.prop('disabled', true)
                 .addClass('loading')
                 .text('Enviando...');

        // Track form submission
        this.trackEvent('form_submission', {
            form_type: $form.data('form-type') || 'contact',
            page: window.location.pathname
        });
    }

    // Course Card Interactions
    handleCourseCardClick(e) {
        const $card = $(e.currentTarget);
        const courseSlug = $card.data('course-slug');
        
        if (courseSlug && !$(e.target).closest('.btn, .course-actions').length) {
            // Navigate to course detail page
            window.location.href = `/curso/${courseSlug}`;
            
            // Track course interest
            this.trackEvent('course_viewed', {
                course_slug: courseSlug,
                source: 'course_card'
            });
        }
    }

    // Search Handler
    handleSearch(e) {
        const searchTerm = $(e.target).val();
        const searchType = $(e.target).data('search-type') || 'courses';
        
        if (searchTerm.length >= 2) {
            this.performSearch(searchTerm, searchType);
        } else if (searchTerm.length === 0) {
            this.clearSearch();
        }
    }

    performSearch(term, type) {
        // Implement real-time search with AJAX
        const searchData = {
            q: term,
            type: type,
            action: 'search'
        };

        $.ajax({
            url: '/api/search',
            method: 'GET',
            data: searchData,
            success: (response) => {
                this.displaySearchResults(response.results, type);
            },
            error: () => {
                console.warn('Search request failed');
            }
        });

        // Track search
        this.trackEvent('search_performed', {
            term: term,
            type: type
        });
    }

    displaySearchResults(results, type) {
        const $resultsContainer = $('.search-results');
        
        if (results.length === 0) {
            $resultsContainer.html('<p class="no-results">No se encontraron resultados</p>');
            return;
        }

        const resultsHtml = results.map(result => {
            if (type === 'courses') {
                return this.buildCourseResultHtml(result);
            }
            return this.buildGenericResultHtml(result);
        }).join('');

        $resultsContainer.html(resultsHtml);
    }

    buildCourseResultHtml(course) {
        return `
            <div class="search-result-item" data-course-slug="${course.slug}">
                <div class="result-icon">
                    <i class="${course.category_icon}"></i>
                </div>
                <div class="result-content">
                    <h4>${course.title}</h4>
                    <p>${course.short_description}</p>
                    <div class="result-meta">
                        <span class="category">${course.category_name}</span>
                        <span class="price">$${course.price.toLocaleString()}</span>
                    </div>
                </div>
            </div>
        `;
    }

    // Lazy Loading Implementation
    initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const lazyImageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        lazyImageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                lazyImageObserver.observe(img);
            });
        }
    }

    // Tooltip Enhancement
    initTooltips() {
        $(document).on('mouseenter', '[data-tooltip]', function() {
            const $element = $(this);
            const tooltipText = $element.data('tooltip');
            
            if (!tooltipText) return;

            const $tooltip = $('<div class="tooltip"></div>').text(tooltipText);
            $('body').append($tooltip);

            const elementRect = this.getBoundingClientRect();
            const tooltipRect = $tooltip[0].getBoundingClientRect();

            $tooltip.css({
                position: 'fixed',
                top: elementRect.top - tooltipRect.height - 10,
                left: elementRect.left + (elementRect.width - tooltipRect.width) / 2,
                zIndex: 1070
            });

            $tooltip.addClass('show');
        });

        $(document).on('mouseleave', '[data-tooltip]', function() {
            $('.tooltip').remove();
        });
    }

    // Accessibility Enhancements
    enhanceKeyboardNavigation() {
        // Skip to main content link
        $('body').prepend('<a href="#main-content" class="skip-link">Saltar al contenido principal</a>');

        // Keyboard navigation for dropdowns
        $(document).on('keydown', '.dropdown', function(e) {
            const $dropdown = $(this);
            const $menu = $dropdown.find('.dropdown-menu');
            const $items = $menu.find('a');

            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $dropdown.toggleClass('active');
            } else if (e.key === 'Escape') {
                $dropdown.removeClass('active');
                $dropdown.find('> a').focus();
            } else if (e.key === 'ArrowDown') {
                e.preventDefault();
                const currentIndex = $items.index(document.activeElement);
                const nextIndex = (currentIndex + 1) % $items.length;
                $items.eq(nextIndex).focus();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const currentIndex = $items.index(document.activeElement);
                const prevIndex = (currentIndex - 1 + $items.length) % $items.length;
                $items.eq(prevIndex).focus();
            }
        });
    }

    addAriaLabels() {
        // Add missing ARIA labels for better screen reader support
        $('.course-card').attr('role', 'article');
        $('.nav-menu').attr('role', 'navigation');
        $('.testimonial-card').attr('role', 'testimonial');
        
        // Add aria-expanded to dropdown toggles
        $('.dropdown > a').attr('aria-expanded', 'false');
        
        $(document).on('click', '.dropdown > a', function() {
            const expanded = $(this).parent().hasClass('active');
            $(this).attr('aria-expanded', expanded);
        });
    }

    setupFocusManagement() {
        // Trap focus in modal dialogs when they're open
        $(document).on('keydown', '.modal.active', function(e) {
            if (e.key === 'Tab') {
                const $modal = $(this);
                const $focusableElements = $modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
                
                if ($focusableElements.length === 0) return;

                const firstElement = $focusableElements.first();
                const lastElement = $focusableElements.last();

                if (e.shiftKey && document.activeElement === firstElement[0]) {
                    e.preventDefault();
                    lastElement.focus();
                } else if (!e.shiftKey && document.activeElement === lastElement[0]) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        });
    }

    // Analytics and Tracking
    trackEvent(eventName, properties = {}) {
        // Generic event tracking - integrate with your analytics service
        if (typeof gtag !== 'undefined') {
            gtag('event', eventName, properties);
        }
        
        if (typeof fbq !== 'undefined') {
            fbq('track', eventName, properties);
        }

        // Console log for development
        if (window.location.hostname === 'localhost') {
            console.log('Track Event:', eventName, properties);
        }
    }

    trackPageView(page = window.location.pathname) {
        this.trackEvent('page_view', {
            page: page,
            title: document.title,
            demographic: this.get('layout')
        });
    }

    // Utility Methods
    extractCourseNameFromUrl(url) {
        const match = url.match(/\/curso\/([^/]+)/);
        return match ? match[1].replace(/-/g, ' ') : null;
    }

    clearSearch() {
        $('.search-results').empty();
        $('.course-card').show();
        $('.results-count').text('');
    }

    // Public API for external interactions
    showNotification(message, type = 'info') {
        const $notification = $(`
            <div class="notification notification-${type}">
                <div class="notification-content">
                    <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                    <span>${message}</span>
                </div>
                <button class="notification-close" aria-label="Cerrar">&times;</button>
            </div>
        `);

        $('body').append($notification);
        
        setTimeout(() => $notification.addClass('show'), 100);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            $notification.removeClass('show');
            setTimeout(() => $notification.remove(), 300);
        }, 5000);

        // Manual close
        $notification.on('click', '.notification-close', () => {
            $notification.removeClass('show');
            setTimeout(() => $notification.remove(), 300);
        });
    }

    getNotificationIcon(type) {
        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-triangle',
            'warning': 'exclamation-circle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }
}

// Course Detail ViewModel
class CourseDetailViewModel extends ViewModelBase {
    init() {
        this.setupCourseInteractions();
        this.initScheduleSelection();
        this.setupModuleAccordion();
    }

    setupCourseInteractions() {
        // Enrollment button handler
        $(document).on('click', '.enroll-btn', (e) => {
            this.handleEnrollment(e);
        });

        // Share functionality
        $(document).on('click', '.share-btn', (e) => {
            this.handleShare(e);
        });

        // Add to calendar
        $(document).on('click', '.add-to-calendar', (e) => {
            this.handleAddToCalendar(e);
        });
    }

    handleEnrollment(e) {
        const $button = $(e.currentTarget);
        const scheduleId = $button.data('schedule-id');
        const courseId = $button.data('course-id');
        
        if (!this.isUserLoggedIn()) {
            this.showLoginPrompt();
            return;
        }

        this.startEnrollmentProcess(courseId, scheduleId);
    }

    startEnrollmentProcess(courseId, scheduleId) {
        // Implementation for enrollment process
        window.location.href = `/inscripcion/${courseId}?schedule=${scheduleId}`;
    }

    showLoginPrompt() {
        const modal = `
            <div class="modal login-prompt-modal">
                <div class="modal-content">
                    <h3>Inicia Sesión para Inscribirte</h3>
                    <p>Para inscribirte en este curso necesitas tener una cuenta.</p>
                    <div class="modal-actions">
                        <a href="/login" class="btn btn-primary">Iniciar Sesión</a>
                        <a href="/registro" class="btn btn-secondary">Crear Cuenta</a>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(modal);
        $('.modal').addClass('active');
    }

    isUserLoggedIn() {
        return window.isLoggedIn || false;
    }
}

// Initialize Application
$(document).ready(function() {
    // Initialize main app ViewModel
    window.appViewModel = new AppViewModel();
    
    // Initialize page-specific ViewModels based on current page
    const currentPage = $('body').data('page');
    
    if (currentPage === 'course-detail') {
        window.courseDetailViewModel = new CourseDetailViewModel();
    }
    
    // Track initial page view
    window.appViewModel.trackPageView();
    
    // Add loading complete class for CSS animations
    setTimeout(() => {
        $('body').addClass('loaded');
    }, 100);
    
    console.log('Capacitar-T.com.mx MVVM Application Initialized');
});

// jQuery Easing for smooth animations
$.extend($.easing, {
    easeInOutCubic: function (x, t, b, c, d) {
        if ((t/=d/2) < 1) return c/2*t*t*t + b;
        return c/2*((t-=2)*t*t + 2) + b;
    }
});

// Global error handling
window.onerror = function(msg, url, lineNo, columnNo, error) {
    console.error('JavaScript Error:', {
        message: msg,
        source: url,
        line: lineNo,
        column: columnNo,
        error: error
    });
    
    // Track errors for debugging
    if (window.appViewModel) {
        window.appViewModel.trackEvent('javascript_error', {
            message: msg.substring(0, 100),
            source: url.split('/').pop(),
            line: lineNo
        });
    }
    
    return false;
};

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { AppViewModel, CourseDetailViewModel };
}