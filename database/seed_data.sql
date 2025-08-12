-- Datos de ejemplo para Capacitar-T.com.mx
-- Este archivo inicializa la base de datos con información médica real

-- Categorías de cursos
INSERT INTO course_categories (name, slug, description, icon, course_type, sort_order, is_active) VALUES
('Profesionales Médicos', 'profesionales-medicos', 'Cursos certificados AHA para personal de salud', 'fas fa-user-md', 'MEDICAL_PROFESSIONAL', 1, 1),
('Primeros Auxilios Comunitarios', 'primeros-auxilios-comunitarios', 'Capacitación para padres, maestros y cuidadores', 'fas fa-heart', 'COMMUNITY_FIRST_AID', 2, 1),
('Gestión Médica', 'gestion-medica', 'Administración de servicios de salud', 'fas fa-hospital', 'MEDICAL_MANAGEMENT', 3, 1);

-- Cursos principales con información médica real
INSERT INTO courses (
    title, slug, short_description, full_description, course_code, category_id, 
    price, original_price, duration_hours, max_participants, difficulty_level, 
    certification_body, certification_type, is_aha_certified, modality, status,
    learning_objectives, target_audiences, featured_image, created_at, updated_at
) VALUES
-- BLS Básico
(
    'BLS - Basic Life Support (Soporte Vital Básico)',
    'bls-basico',
    'Certificación AHA en soporte vital básico para profesionales de la salud y personal de emergencias.',
    '<h3>Descripción del Curso</h3>
    <p>El curso BLS (Basic Life Support) está diseñado para profesionales de la salud y personas que necesitan saber realizar RCP de alta calidad, usar un DEA y resolver atragantamientos de manera segura, oportuna y efectiva.</p>
    
    <h4>Contenido del Curso:</h4>
    <ul>
        <li>RCP de alta calidad para adultos, niños y bebés</li>
        <li>Uso del desfibrilador externo automático (DEA)</li>
        <li>Manejo de vía aérea con dispositivos de barrera</li>
        <li>Técnicas de trabajo en equipo durante emergencias</li>
        <li>Manejo de atragantamientos en todas las edades</li>
    </ul>',
    'BLS-001',
    1, -- Profesionales Médicos
    2500.00, 3000.00, 4, 16, 'BASIC', 'AHA', 'BLS Provider', 1, 'Presencial',
    'published',
    '["Realizar RCP de alta calidad en adultos, niños y bebés", "Usar correctamente el DEA", "Manejar obstrucciones de vía aérea", "Trabajar efectivamente en equipo durante emergencias"]',
    '["Médicos", "Enfermeros", "Estudiantes de medicina", "Estudiantes de enfermería", "Paramédicos", "Personal de brigadas de emergencia"]',
    'bls-course-aha.jpg',
    NOW(), NOW()
),

-- ACLS Avanzado
(
    'ACLS - Advanced Cardiovascular Life Support',
    'acls-avanzado',
    'Certificación AHA en soporte cardiovascular avanzado para profesionales médicos especializados.',
    '<h3>Descripción del Curso</h3>
    <p>El curso ACLS mejora la calidad de atención para el paciente adulto en paro cardiorrespiratorio o con otras emergencias cardiopulmonares.</p>
    
    <h4>Contenido del Curso:</h4>
    <ul>
        <li>Manejo avanzado de vía aérea</li>
        <li>Algoritmos de ACLS para paros cardíacos</li>
        <li>Farmacología en emergencias cardiovasculares</li>
        <li>Interpretación de ritmos cardíacos</li>
        <li>Cardioversión y desfibrilación</li>
        <li>Manejo de síndromes coronarios agudos</li>
    </ul>',
    'ACLS-001',
    1, -- Profesionales Médicos
    4500.00, 5500.00, 8, 12, 'ADVANCED', 'AHA', 'ACLS Provider', 1, 'Presencial',
    'published',
    '["Manejar algoritmos de paro cardíaco", "Interpretar electrocardiogramas básicos", "Administrar medicamentos de emergencia", "Realizar intubación endotraqueal", "Liderar equipos de reanimación"]',
    '["Médicos", "Enfermeros de cuidados críticos", "Paramédicos avanzados", "Residentes de medicina", "Personal de urgencias"]',
    'acls-course-aha.jpg',
    NOW(), NOW()
),

-- PALS Pediátrico
(
    'PALS - Pediatric Advanced Life Support',
    'pals-pediatrico',
    'Certificación AHA en soporte vital avanzado pediátrico para emergencias en niños y bebés.',
    '<h3>Descripción del Curso</h3>
    <p>El curso PALS está dirigido a profesionales de la salud que responden a emergencias en bebés y niños, incluyendo personal de emergencias prehospitalarias, enfermeros, médicos y otros profesionales de la salud.</p>
    
    <h4>Contenido del Curso:</h4>
    <ul>
        <li>Evaluación pediátrica sistemática</li>
        <li>RCP de alta calidad para bebés y niños</li>
        <li>Manejo de vía aérea en pediatría</li>
        <li>Algoritmos de paro pediátrico</li>
        <li>Farmacología pediátrica de emergencia</li>
        <li>Casos clínicos pediátricos</li>
    </ul>',
    'PALS-001',
    1, -- Profesionales Médicos
    4800.00, 5800.00, 8, 12, 'ADVANCED', 'AHA', 'PALS Provider', 1, 'Presencial',
    'published',
    '["Evaluar sistemáticamente a pacientes pediátricos", "Realizar RCP pediátrica de alta calidad", "Manejar emergencias respiratorias", "Calcular dosis pediátricas de medicamentos", "Trabajar en equipo en emergencias pediátricas"]',
    '["Pediatras", "Enfermeros pediátricos", "Personal de urgencias pediátricas", "Paramédicos", "Anestesiólogos"]',
    'pals-course-aha.jpg',
    NOW(), NOW()
),

-- Stop the Bleed Profesional
(
    'Stop the Bleed - Control de Hemorragias',
    'stop-the-bleed-profesional',
    'Certificación en técnicas de control de hemorragias para situaciones de emergencia y trauma.',
    '<h3>Descripción del Curso</h3>
    <p>Stop the Bleed es una campaña nacional para enseñar a las personas técnicas básicas de control de hemorragias. Este curso está diseñado tanto para profesionales como para el público general.</p>
    
    <h4>Contenido del Curso:</h4>
    <ul>
        <li>Identificación de hemorragias que amenazan la vida</li>
        <li>Presión directa efectiva</li>
        <li>Uso correcto de torniquetes</li>
        <li>Vendajes de presión</li>
        <li>Cuidados durante el transporte</li>
        <li>Seguridad del rescatista</li>
    </ul>',
    'STB-001',
    2, -- Primeros Auxilios Comunitarios
    800.00, 1000.00, 2, 20, 'BASIC', 'AHA', 'Stop the Bleed Certificate', 1, 'Presencial',
    'published',
    '["Reconocer hemorragias que amenazan la vida", "Aplicar presión directa efectiva", "Usar torniquetes correctamente", "Realizar vendajes de presión", "Mantener la seguridad personal"]',
    '["Maestros", "Padres de familia", "Personal de seguridad", "Entrenadores deportivos", "Público general"]',
    'stop-the-bleed-course.jpg',
    NOW(), NOW()
),

-- Heartsaver AED
(
    'Heartsaver AED - RCP y Desfibrilación',
    'heartsaver-aed',
    'Certificación AHA en RCP y uso de DEA para el público general y trabajadores.',
    '<h3>Descripción del Curso</h3>
    <p>El curso Heartsaver AED está diseñado para personas con poca o ninguna formación médica que necesitan una tarjeta de curso de finalización por requisitos laborales, reglamentarios o por otros motivos.</p>
    
    <h4>Contenido del Curso:</h4>
    <ul>
        <li>RCP para adultos, niños y bebés</li>
        <li>Uso del desfibrilador externo automático (DEA)</li>
        <li>Manejo de atragantamientos</li>
        <li>Primeros auxilios básicos</li>
        <li>Emergencias médicas comunes</li>
    </ul>',
    'HS-AED-001',
    2, -- Primeros Auxilios Comunitarios
    1200.00, 1500.00, 3, 20, 'BASIC', 'AHA', 'Heartsaver AED', 1, 'Presencial',
    'published',
    '["Realizar RCP efectiva", "Usar correctamente el DEA", "Manejar obstrucciones de vía aérea", "Proporcionar primeros auxilios básicos", "Reconocer emergencias médicas"]',
    '["Maestros", "Cuidadores infantiles", "Entrenadores deportivos", "Personal de oficina", "Empleados industriales"]',
    'heartsaver-aed-course.jpg',
    NOW(), NOW()
);

-- Horarios de cursos (próximas fechas)
INSERT INTO course_schedules (
    course_id, instructor_id, start_date, end_date, start_time, end_time,
    location, available_spots, registered_count, status
) VALUES
-- BLS Horarios
(1, 1, '2024-12-15', '2024-12-15', '09:00:00', '13:00:00', 
 'Centro de Capacitación Capacitar-T, Av. Universidad 1200, CDMX', 16, 8, 'scheduled'),
(1, 2, '2024-12-20', '2024-12-20', '14:00:00', '18:00:00', 
 'Centro de Capacitación Capacitar-T, Av. Universidad 1200, CDMX', 16, 12, 'scheduled'),
(1, 1, '2025-01-08', '2025-01-08', '09:00:00', '13:00:00', 
 'Centro de Capacitación Capacitar-T, Av. Universidad 1200, CDMX', 16, 3, 'scheduled'),

-- ACLS Horarios
(2, 1, '2024-12-18', '2024-12-19', '09:00:00', '17:00:00', 
 'Centro de Capacitación Capacitar-T, Av. Universidad 1200, CDMX', 12, 6, 'scheduled'),
(2, 1, '2025-01-15', '2025-01-16', '09:00:00', '17:00:00', 
 'Centro de Capacitación Capacitar-T, Av. Universidad 1200, CDMX', 12, 2, 'scheduled'),

-- PALS Horarios
(3, 3, '2024-12-22', '2024-12-23', '09:00:00', '17:00:00', 
 'Centro de Capacitación Capacitar-T, Av. Universidad 1200, CDMX', 12, 4, 'scheduled');

-- Usuarios de ejemplo (incluyendo administradores)
INSERT INTO users (
    first_name, last_name, email, phone, password, profession, birth_date, 
    role, email_verified, status
) VALUES
('Roberto', 'Martínez Silva', 'admin@capacitar-t.com.mx', '+52 55 1234 5678', 
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
 'Médico', '1975-05-15', 'admin', 1, 'active'),

('Patricia', 'González López', 'instructor@capacitar-t.com.mx', '+52 55 8765 4321',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Enfermero(a)', '1985-08-22', 'instructor', 1, 'active'),

('María Elena', 'Rodríguez', 'maria.rodriguez@hospital.com', '+52 55 9876 5432',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Médico', '1980-03-10', 'professional', 1, 'active'),

('Carlos', 'Mendoza Herrera', 'carlos.mendoza@ejemplo.com', '+52 55 5555 1234',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Enfermero(a)', '1988-11-05', 'professional', 1, 'active'),

('Ana Sofía', 'Herrera Vázquez', 'ana.herrera@guarderia.com', '+52 55 4444 5678',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Maestro(a)', '1990-07-18', 'student', 1, 'active');

-- Módulos de cursos con contenido médico detallado
INSERT INTO course_modules (course_id, title, description, duration_minutes, sort_order, learning_outcomes, equipment_needed) VALUES
-- BLS Módulos
(1, 'Introducción al BLS', 
 'Fundamentos del soporte vital básico y conceptos generales de emergencias cardiovasculares.',
 30, 1,
 '["Cadena de supervivencia", "Epidemiología del paro cardíaco", "Aspectos legales", "Seguridad del rescatista"]',
 '["Evaluación de la escena", "Activación del sistema de emergencias"]'),

(1, 'RCP de Alta Calidad en Adultos',
 'Técnicas de reanimación cardiopulmonar en pacientes adultos con énfasis en calidad.',
 90, 2,
 '["Anatomía cardiopulmonar básica", "Técnica de compresiones", "Ventilación de rescate", "Ciclos de RCP"]',
 '["Práctica en maniquí adulto", "Evaluación de profundidad y frecuencia", "Trabajo en equipo"]'),

(1, 'Uso del DEA',
 'Desfibrilación externa automática: principios, uso seguro y mantenimiento.',
 60, 3,
 '["Principios de desfibrilación", "Tipos de DEA", "Seguridad eléctrica", "Mantenimiento preventivo"]',
 '["Simulacro con DEA", "Colocación de electrodos", "Análisis de ritmos"]'),

(1, 'RCP Pediátrica y Manejo de Atragantamientos',
 'Técnicas específicas para bebés y niños, incluyendo obstrucción de vía aérea.',
 60, 4,
 '["Diferencias anatómicas pediátricas", "Técnica de RCP en niños", "RCP en bebés", "Maniobras anti-atragantamiento"]',
 '["Práctica con maniquís pediátricos", "Simulación de atragantamientos", "Casos clínicos integrados"]');

-- Equipos médicos utilizados en los cursos
INSERT INTO course_equipment (course_id, equipment_name, description, quantity_needed, is_provided) VALUES
(1, 'Maniquí de RCP Adulto Laerdal', 'Maniquí de entrenamiento con feedback de calidad de compresiones', 8, 1),
(1, 'Maniquí Pediátrico', 'Maniquí para práctica de RCP en niños', 4, 1),
(1, 'Maniquí de Bebé', 'Maniquí para entrenamiento en RCP infantil', 4, 1),
(1, 'DEA de Entrenamiento', 'Desfibrilador externo automático para prácticas', 4, 1),
(1, 'Máscaras de Barrera', 'Dispositivos de protección para ventilación de rescate', 20, 1),

(2, 'Maniquí Avanzado con Simulación', 'Maniquí de alta fidelidad con monitorización', 4, 1),
(2, 'Monitor Desfibrilador', 'Equipo real de monitorización y desfibrilación', 2, 1),
(2, 'Laringoscopio', 'Instrumento para intubación endotraqueal', 6, 1),
(2, 'Medicamentos de Simulación', 'Viales de práctica para administración de fármacos', 1, 1);

-- Inscripciones de ejemplo
INSERT INTO course_enrollments (
    user_id, schedule_id, enrollment_date, status, emergency_contact_name, 
    emergency_contact_phone, special_needs, payment_method
) VALUES
(3, 1, NOW(), 'confirmed', 'Juan Rodríguez', '+52 55 1111 2222', '', 'credit_card'),
(4, 1, NOW(), 'confirmed', 'Carmen Mendoza', '+52 55 3333 4444', '', 'credit_card'),
(5, 2, NOW(), 'pending', 'Pedro Herrera', '+52 55 5555 6666', '', 'transfer'),
(3, 3, NOW(), 'confirmed', 'Juan Rodríguez', '+52 55 1111 2222', '', 'credit_card');




-- Actualizar contadores de horarios
UPDATE course_schedules SET
    registered_count = (
        SELECT COUNT(*) FROM course_enrollments 
        WHERE schedule_id = course_schedules.id AND status IN ('confirmed', 'completed')
    );