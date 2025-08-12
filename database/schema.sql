CREATE DATABASE IF NOT EXISTS capacitar_t_mx CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE capacitar_t_mx;

-- Users table with enhanced medical professional fields
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    profession ENUM('Médico', 'Enfermero(a)', 'Paramédico', 'Estudiante de Medicina', 'Estudiante de Enfermería', 'Personal de Brigada', 'Maestro(a)', 'Padre/Madre de Familia', 'Administrador de Salud', 'Personal de Urgencias', 'Otro') DEFAULT 'Otro',
    institution VARCHAR(200),
    license_number VARCHAR(50), -- Medical license or professional ID
    specialization VARCHAR(100),
    experience_years TINYINT UNSIGNED,
    role ENUM('student', 'professional', 'instructor', 'admin') DEFAULT 'student',
    status ENUM('active', 'inactive', 'pending', 'suspended') DEFAULT 'pending',
    email_verified BOOLEAN DEFAULT FALSE,
    profile_image VARCHAR(255),
    birth_date DATE,
    gender ENUM('M', 'F', 'Otro', 'Prefiero no decir'),
    target_demographic ENUM('GEN_X', 'MILLENNIALS', 'GEN_BETA') GENERATED ALWAYS AS (
        CASE 
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 25 THEN 'GEN_BETA'
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 26 AND 41 THEN 'MILLENNIALS'
            WHEN TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 42 THEN 'GEN_X'
            ELSE NULL
        END
    ) VIRTUAL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_profession (profession),
    INDEX idx_demographic (target_demographic),
    INDEX idx_status (status)
);

-- Course categories with specific medical focus
CREATE TABLE course_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    slug VARCHAR(100) UNIQUE NOT NULL,
    course_type ENUM('MEDICAL_PROFESSIONAL', 'COMMUNITY_FIRST_AID', 'MEDICAL_MANAGEMENT') NOT NULL,
    icon VARCHAR(50),
    color VARCHAR(7) DEFAULT '#007bff',
    target_demographic SET('GEN_X', 'MILLENNIALS', 'GEN_BETA') DEFAULT 'GEN_X,MILLENNIALS,GEN_BETA',
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (course_type),
    INDEX idx_active (is_active)
);

-- Medical courses with certification details
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    short_description TEXT,
    full_description LONGTEXT,
    course_code VARCHAR(20) UNIQUE, -- e.g., BLS-001, ACLS-002, PALS-003
    duration_hours DECIMAL(4,2) NOT NULL,
    duration_days TINYINT DEFAULT 1,
    max_participants INT DEFAULT 16, -- Smaller groups for medical training
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'MXN',
    difficulty_level ENUM('BASIC', 'INTERMEDIATE', 'ADVANCED', 'INSTRUCTOR') DEFAULT 'BASIC',
    certification_type VARCHAR(100),
    certification_body ENUM('AHA', 'ERC', 'ILCOR', 'CAPACITAR_T', 'CUSTOM') DEFAULT 'CAPACITAR_T',
    certification_validity_years TINYINT DEFAULT 2,
    prerequisites TEXT,
    learning_objectives TEXT,
    target_audience TEXT,
    medical_specialization SET('CARDIOLOGY', 'PEDIATRICS', 'EMERGENCY', 'TRAUMA', 'CRITICAL_CARE', 'GENERAL') DEFAULT 'GENERAL',
    includes_hands_on BOOLEAN DEFAULT TRUE,
    includes_mannequins BOOLEAN DEFAULT TRUE,
    includes_aed_training BOOLEAN DEFAULT FALSE,
    featured_image VARCHAR(255),
    cc_image_source VARCHAR(100), -- Creative Commons source
    cc_image_license VARCHAR(50),  -- CC license type
    gallery_images JSON,
    video_url VARCHAR(255),
    status ENUM('draft', 'published', 'archived', 'under_review') DEFAULT 'draft',
    featured BOOLEAN DEFAULT FALSE,
    accreditation_points DECIMAL(4,2) DEFAULT 0, -- CME credits
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES course_categories(id),
    INDEX idx_course_code (course_code),
    INDEX idx_certification (certification_body),
    INDEX idx_specialization (medical_specialization),
    INDEX idx_status (status),
    INDEX idx_featured (featured)
);

-- Detailed course modules with medical content
CREATE TABLE course_modules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    duration_minutes INT NOT NULL,
    sort_order INT DEFAULT 0,
    module_type ENUM('theory', 'practical', 'assessment', 'simulation') DEFAULT 'theory',
    is_hands_on BOOLEAN DEFAULT FALSE,
    equipment_needed TEXT,
    learning_outcomes TEXT,
    assessment_criteria TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    INDEX idx_type (module_type)
);

-- Equipment and materials needed
CREATE TABLE course_equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    equipment_name VARCHAR(100) NOT NULL,
    quantity_needed INT DEFAULT 1,
    is_provided BOOLEAN DEFAULT TRUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Course schedules with enhanced medical training details
CREATE TABLE course_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    instructor_id INT,
    co_instructor_id INT,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    address TEXT,
    room_details VARCHAR(100),
    available_spots INT NOT NULL,
    registered_count INT DEFAULT 0,
    waitlist_count INT DEFAULT 0,
    min_participants INT DEFAULT 6,
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled', 'postponed') DEFAULT 'scheduled',
    notes TEXT,
    special_requirements TEXT,
    language ENUM('es', 'en', 'bilingual') DEFAULT 'es',
    certification_fee DECIMAL(8,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (instructor_id) REFERENCES users(id),
    FOREIGN KEY (co_instructor_id) REFERENCES users(id),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_status (status)
);

-- Course enrollments with medical certification tracking
CREATE TABLE course_enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    schedule_id INT NOT NULL,
    enrollment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled', 'no_show', 'failed') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'partial', 'refunded') DEFAULT 'pending',
    payment_method ENUM('credit_card', 'debit_card', 'transfer', 'cash', 'corporate') DEFAULT 'credit_card',
    payment_reference VARCHAR(100),
    amount_paid DECIMAL(10,2) DEFAULT 0,
    completion_date DATE,
    final_score DECIMAL(5,2),
    practical_score DECIMAL(5,2),
    theory_score DECIMAL(5,2),
    certificate_issued BOOLEAN DEFAULT FALSE,
    certificate_number VARCHAR(50) UNIQUE,
    certificate_issue_date DATE,
    certificate_expiry_date DATE,
    notes TEXT,
    special_needs TEXT,
    dietary_restrictions TEXT,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (schedule_id) REFERENCES course_schedules(id),
    UNIQUE KEY unique_enrollment (user_id, schedule_id),
    INDEX idx_certificate (certificate_number),
    INDEX idx_status (status)
);

-- Instructor profiles and qualifications
CREATE TABLE instructors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    bio TEXT,
    qualifications TEXT,
    certifications JSON, -- Array of certifications
    specializations JSON, -- Medical specializations
    years_teaching INT DEFAULT 0,
    years_clinical INT DEFAULT 0,
    languages_spoken SET('es', 'en', 'fr', 'de', 'it') DEFAULT 'es',
    instructor_level ENUM('certified', 'senior', 'master', 'director') DEFAULT 'certified',
    is_active BOOLEAN DEFAULT TRUE,
    hourly_rate DECIMAL(8,2),
    availability JSON, -- Schedule availability
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_active (is_active),
    INDEX idx_level (instructor_level)
);

-- Contact form submissions with course interests
CREATE TABLE contact_submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    profession VARCHAR(100),
    institution VARCHAR(200),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    course_interest VARCHAR(100),
    preferred_schedule ENUM('morning', 'afternoon', 'evening', 'weekend', 'flexible') DEFAULT 'flexible',
    group_size INT,
    is_corporate_inquiry BOOLEAN DEFAULT FALSE,
    urgency_level ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('new', 'contacted', 'in_progress', 'closed', 'spam') DEFAULT 'new',
    assigned_to INT, -- Staff member handling inquiry
    ip_address VARCHAR(45),
    user_agent TEXT,
    source VARCHAR(50), -- How they found us
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    INDEX idx_status (status),
    INDEX idx_course_interest (course_interest)
);

-- Newsletter subscriptions
CREATE TABLE newsletter_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    first_name VARCHAR(100),
    interests JSON, -- Course categories of interest
    subscription_source VARCHAR(50),
    demographic ENUM('GEN_X', 'MILLENNIALS', 'GEN_BETA'),
    status ENUM('active', 'inactive', 'unsubscribed', 'bounced') DEFAULT 'active',
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL,
    last_email_sent TIMESTAMP NULL,
    INDEX idx_status (status),
    INDEX idx_demographic (demographic)
);

-- Insert course categories
INSERT INTO course_categories (name, description, slug, course_type, icon, color, target_demographic, sort_order) VALUES
('Profesionales Médicos', 'Cursos especializados para estudiantes de medicina, enfermería, paramédicos y brigadas de primeros auxilios en fábricas y centros de trabajo', 'profesionales-medicos', 'MEDICAL_PROFESSIONAL', 'fas fa-user-md', '#e74c3c', 'GEN_X,MILLENNIALS,GEN_BETA', 1),
('Primeros Auxilios Comunitarios', 'Cursos de primeros auxilios para padres, maestros y personal de parques, piscinas y campamentos de verano', 'primeros-auxilios-comunitarios', 'COMMUNITY_FIRST_AID', 'fas fa-heart', '#27ae60', 'GEN_X,MILLENNIALS', 2),
('Gestión Médica', 'Cursos de administración y gestión de consultorios médicos, clínicas, salas de urgencias y recepciones', 'gestion-medica', 'MEDICAL_MANAGEMENT', 'fas fa-hospital', '#3498db', 'GEN_X,MILLENNIALS', 3);

-- Insert specific medical courses
INSERT INTO courses (category_id, title, slug, short_description, full_description, course_code, duration_hours, duration_days, price, difficulty_level, certification_type, certification_body, medical_specialization, includes_aed_training, featured) VALUES
-- Professional Medical Courses
(1, 'Soporte Vital Básico (BLS) - AHA', 'bls-basico', 'Curso de reanimación cardiopulmonar básica certificado por la American Heart Association', 'Curso integral de Soporte Vital Básico que incluye RCP para adultos, niños y bebés, uso de DEA, y manejo de atragantamiento. Certificación válida por 2 años.', 'BLS-001', 4.00, 1, 1800.00, 'BASIC', 'BLS Provider Card', 'AHA', 'CARDIOLOGY,EMERGENCY', TRUE, TRUE),

(1, 'Soporte Cardíaco Avanzado (ACLS) - AHA', 'acls-avanzado', 'Curso avanzado de manejo de paro cardíaco y emergencias cardiovasculares', 'Curso avanzado que cubre algoritmos de ACLS, farmacología en emergencias, interpretación de ECG, manejo de vías aéreas avanzadas y liderazgo de equipo de reanimación.', 'ACLS-001', 12.00, 2, 4500.00, 'ADVANCED', 'ACLS Provider Card', 'AHA', 'CARDIOLOGY,CRITICAL_CARE,EMERGENCY', TRUE, TRUE),

(1, 'Soporte Pediátrico Avanzado (PALS) - AHA', 'pals-pediatrico', 'Curso especializado en emergencias pediátricas y neonatales', 'Curso especializado en reconocimiento y manejo de emergencias respiratorias y cardiovasculares en pacientes pediátricos y neonatos.', 'PALS-001', 14.00, 2, 4800.00, 'ADVANCED', 'PALS Provider Card', 'AHA', 'PEDIATRICS,CRITICAL_CARE,EMERGENCY', TRUE, TRUE),

(1, 'Stop the Bleed - Control de Hemorragias', 'stop-the-bleed-profesional', 'Técnicas profesionales para control de hemorragias traumáticas', 'Curso intensivo para profesionales de la salud en control inmediato de hemorragias traumáticas, uso de torniquetes, agentes hemostáticos y técnicas de compresión avanzada.', 'STB-PRO-001', 6.00, 1, 2200.00, 'INTERMEDIATE', 'Stop the Bleed Provider', 'CAPACITAR_T', 'TRAUMA,EMERGENCY', FALSE, TRUE),

(1, 'Heartsaver AED para Profesionales', 'heartsaver-profesional', 'RCP y DEA para profesionales en entornos laborales', 'Curso diseñado para personal de brigadas de emergencia en fábricas y centros de trabajo. Incluye RCP, DEA y primeros auxilios ocupacionales.', 'HS-PRO-001', 5.00, 1, 1600.00, 'BASIC', 'Heartsaver AED Certificate', 'AHA', 'GENERAL,EMERGENCY', TRUE, FALSE),

-- Community First Aid Courses
(2, 'RCP Básico para Padres y Maestros', 'bls-infantil', 'Soporte vital básico enfocado en niños y bebés para no profesionales', 'Curso práctico de RCP pediátrico, manejo de atragantamiento y uso de DEA, especialmente diseñado para padres, maestros y cuidadores.', 'BLS-PED-001', 4.00, 1, 1200.00, 'BASIC', 'Heartsaver Pediatric Certificate', 'AHA', 'PEDIATRICS', TRUE, TRUE),

(2, 'Stop the Bleed Comunitario', 'stop-the-bleed-comunidad', 'Control básico de hemorragias para ciudadanos', 'Curso básico de control de hemorragias para padres, maestros y personal no médico. Técnicas simples y efectivas para emergencias cotidianas.', 'STB-COM-001', 3.00, 1, 800.00, 'BASIC', 'Stop the Bleed Certificate', 'CAPACITAR_T', 'GENERAL', FALSE, TRUE),

(2, 'Primeros Auxilios Acuáticos', 'primeros-auxilios-acuaticos', 'Emergencias médicas en piscinas y centros acuáticos', 'Curso especializado para personal de piscinas, parques acuáticos y campamentos. Incluye rescate acuático básico, RCP en ambiente húmedo y manejo de trauma cervical.', 'AQ-FA-001', 8.00, 1, 1500.00, 'INTERMEDIATE', 'Aquatic First Aid Certificate', 'CAPACITAR_T', 'EMERGENCY', TRUE, TRUE),

-- Medical Management Courses
(3, 'Administración Eficiente de Consultorios', 'gestion-consultorios', 'Optimización de procesos administrativos en consultorios médicos', 'Curso integral de gestión administrativa, financiera y operativa para consultorios médicos privados y clínicas pequeñas.', 'ADM-CONS-001', 16.00, 2, 3200.00, 'INTERMEDIATE', 'Certificate in Medical Office Management', 'CAPACITAR_T', 'GENERAL', FALSE, FALSE),

(3, 'Gestión de Servicios de Urgencias', 'administracion-urgencias', 'Optimización de flujos en salas de emergencia', 'Metodologías para mejorar eficiencia operativa, reducir tiempos de espera y optimizar recursos en servicios de urgencias hospitalarias.', 'ADM-URG-001', 20.00, 3, 4200.00, 'ADVANCED', 'Emergency Department Management Certificate', 'CAPACITAR_T', 'EMERGENCY', FALSE, FALSE);

-- Insert course modules for BLS course
INSERT INTO course_modules (course_id, title, description, duration_minutes, sort_order, module_type, is_hands_on, learning_outcomes) VALUES
(1, 'Introducción y Conceptos Básicos', 'Fundamentos del soporte vital básico y cadena de supervivencia', 45, 1, 'theory', FALSE, 'Identificar situaciones que requieren RCP, conocer la cadena de supervivencia'),
(1, 'RCP en Adultos', 'Técnicas de compresión torácica y ventilación de rescate', 90, 2, 'practical', TRUE, 'Realizar RCP de alta calidad en adultos'),
(1, 'Uso del DEA', 'Desfibrilación externa automatizada', 60, 3, 'practical', TRUE, 'Operar un DEA de manera segura y efectiva'),
(1, 'RCP Pediátrico', 'Adaptaciones para bebés y niños', 75, 4, 'practical', TRUE, 'Aplicar técnicas de RCP apropiadas para la edad'),
(1, 'Evaluación Práctica', 'Demostración de habilidades adquiridas', 30, 5, 'assessment', TRUE, 'Demostrar competencia en todas las habilidades de BLS');

-- Insert sample schedules
INSERT INTO course_schedules (course_id, start_date, end_date, start_time, end_time, location, address, available_spots, min_participants) VALUES
(1, '2025-09-15', '2025-09-15', '08:00:00', '12:00:00', 'Centro de Simulación Médica UNAM', 'Ciudad Universitaria, Coyoacán, CDMX', 16, 8),
(1, '2025-09-22', '2025-09-22', '13:00:00', '17:00:00', 'Hospital General de México', 'Dr. Balmis 148, Doctores, CDMX', 16, 8),
(2, '2025-10-05', '2025-10-06', '09:00:00', '18:00:00', 'Instituto Nacional de Cardiología', 'Juan Badiano 1, Sección XVI, CDMX', 12, 6),
(3, '2025-10-12', '2025-10-13', '08:30:00', '17:30:00', 'Hospital Infantil de México', 'Dr. Márquez 162, Doctores, CDMX', 12, 6),
(6, '2025-09-28', '2025-09-28', '09:00:00', '13:00:00', 'Escuela Primaria Benito Juárez', 'Av. Universidad 1200, Del Valle, CDMX', 20, 12);

-- Insert sample equipment
INSERT INTO course_equipment (course_id, equipment_name, quantity_needed, description) VALUES
(1, 'Maniquí de RCP Adulto', 4, 'Maniquíes Resusci Anne para práctica de RCP'),
(1, 'Maniquí de RCP Infantil', 2, 'Maniquíes pediátricos para práctica'),
(1, 'Desfibrilador de Entrenamiento', 2, 'DEA de práctica con electrodos de entrenamiento'),
(1, 'Mascarilla de Bolsillo', 16, 'Dispositivos de barrera para ventilación de rescate'),
(2, 'Monitor Simulador de ECG', 2, 'Simuladores de ritmos cardíacos para ACLS');