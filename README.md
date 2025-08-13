# 🏥 Capacitar-T.com.mx - Medical Training Platform

Sistema integral de capacitación médica especializado en cursos BLS, ACLS, PALS y certificaciones AHA para profesionales de la salud, padres, maestros y personal de brigadas.

## 📋 Características
 "Testing a change "
- **Arquitectura MVVM en PHP** - Separación clara de lógica y presentación
- **Cursos Médicos Certificados AHA** - BLS, ACLS, PALS, Stop the Bleed, Heartsaver
- **Tres Líneas Especializadas:**
  - Profesionales Médicos (médicos, enfermeros, estudiantes, paramédicos)
  - Primeros Auxilios Comunitarios (padres, maestros, cuidadores)
  - Gestión Médica (administración de consultorios y clínicas)
- **Sistema de Inscripciones** - Con pagos integrados y confirmación automática
- **Panel Administrativo** - Dashboard completo para gestión de cursos y usuarios
- **Target Demographics** - Optimizado para Gen X (40+), Millennials y Gen Beta
- **Creative Commons** - Sistema de atribución automática para imágenes

## 🚀 Instalación

### Prerrequisitos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Extensiones PHP: PDO, PDO_MySQL, JSON, OpenSSL

### 1. Configuración de Base de Datos
```sql
CREATE DATABASE capacitar_t_mx CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
```

### 2. Importar Esquema y Datos
```bash
mysql -u root -p capacitar_t_mx < database/schema.sql
mysql -u root -p capacitar_t_mx < database/seed_data.sql
```

### 3. Configuración del Sitio
Editar `config/config.php`:
```php
// Actualizar estos valores según tu entorno
define('DB_HOST', 'localhost');
define('DB_NAME', 'capacitar_t_mx');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_password');
define('SITE_URL', 'http://tu-dominio.com');
```

### 4. Permisos de Archivos
```bash
chmod -R 755 .
chmod -R 777 assets/uploads/
chmod -R 777 tmp/
```

## 🔐 Acceso de Administrador

### Credenciales por Defecto:
- **Email:** admin@capacitar-t.com.mx
- **Password:** password
- **URL:** http://tu-dominio.com/admin/dashboard

## 📁 Estructura del Proyecto

```
capacitar-t.com.mx/
├── config/                    # Configuración
│   ├── config.php            # Configuración principal
│   └── database.php          # Conexión a BD
├── database/                 # Base de datos
│   ├── schema.sql           # Estructura de tablas
│   └── seed_data.sql        # Datos de ejemplo
├── models/                  # Modelos de datos
│   ├── BaseModel.php        # Modelo base
│   ├── Course.php          # Gestión de cursos
│   └── User.php            # Gestión de usuarios
├── viewmodels/             # Controladores MVVM
│   ├── HomeViewModel.php    # Página principal
│   ├── CourseViewModel.php  # Cursos médicos
│   ├── UserViewModel.php    # Usuarios
│   ├── EnrollmentViewModel.php # Inscripciones
│   └── AdminViewModel.php   # Administración
├── views/                  # Templates
│   ├── includes/           # Componentes comunes
│   ├── home/              # Página principal
│   ├── courses/           # Páginas de cursos
│   ├── users/             # Login/registro
│   ├── enrollment/        # Inscripciones
│   └── admin/             # Panel admin
├── assets/                # Recursos estáticos
│   ├── css/main.css       # CSS médico
│   ├── js/app.js         # JavaScript MVVM
│   └── images/           # Imágenes CC
└── includes/             # Utilidades
    ├── router.php        # Sistema de rutas
    └── viewmodel_base.php # Base MVVM
```

## 🎯 Uso del Sistema

### Usuarios Finales
1. **Registro:** Crear cuenta gratuita con profesión específica
2. **Explorar Cursos:** Navegar por las tres líneas de capacitación
3. **Inscripción:** Proceso multi-step con pago integrado
4. **Certificación:** Descargar certificados AHA automáticamente

### Administradores
1. **Dashboard:** Estadísticas en tiempo real
2. **Gestión de Cursos:** CRUD completo de cursos médicos
3. **Usuarios:** Administrar perfiles y inscripciones
4. **Reportes:** Analytics de ingresos y performance

## 🏥 Cursos Disponibles

### Profesionales Médicos
- **BLS (Basic Life Support)** - $2,500 MXN
- **ACLS (Advanced Cardiac Life Support)** - $4,500 MXN  
- **PALS (Pediatric Advanced Life Support)** - $4,800 MXN
- **Stop the Bleed Profesional** - $800 MXN
- **Heartsaver AED** - $1,200 MXN

### Primeros Auxilios Comunitarios
- RCP Pediátrico para padres
- Stop the Bleed para maestros
- Primeros auxilios acuáticos
- Emergencias en campamentos

### Gestión Médica
- Administración de consultorios
- Gestión de urgencias
- Sistemas de calidad médica
- Atención al paciente

## 💳 Métodos de Pago

- Tarjetas de crédito/débito (Visa, MasterCard, AMEX)
- PayPal
- Transferencia bancaria/SPEI
- Pagos a meses sin intereses (3, 6, 12 meses)

## 🎨 Creative Commons

El sitio utiliza imágenes Creative Commons de:
- **Unsplash.com** - Licencia Unsplash
- **Pexels.com** - Licencia Pexels  
- **Pixabay.com** - Licencia Pixabay
- **Wikimedia Commons** - Licencias CC BY-SA

## 📊 Demographics Target

### Gen X (40-55 años)
- Tipografía clara y legible
- Navegación intuitiva
- Colores profesionales

### Millennials (25-40 años)
- Diseño moderno y responsivo
- Integración con redes sociales
- Proceso de pago ágil

### Gen Beta (18-25 años)
- Interactividad con jQuery
- Formularios dinámicos
- Mobile-first design

## 🔧 Troubleshooting

### Error: "Undefined constant VIEW_PATH"
- Verificar que `config/config.php` esté incluido antes que otros archivos
- Verificar permisos de lectura en todos los archivos

### Error 404 en rutas
- Verificar configuración de mod_rewrite en Apache
- Verificar que la URL base esté correctamente configurada

### Problemas de base de datos
- Verificar credenciales en `config/config.php`
- Verificar que las tablas estén creadas correctamente
- Verificar permisos de usuario MySQL

## 📞 Soporte

Para soporte técnico o preguntas sobre implementación:
- **Email:** soporte@capacitar-t.com.mx
- **WhatsApp:** +52 55 8765-4321
- **Horario:** Lunes a Viernes 8:00-18:00, Sábados 9:00-15:00

## 📄 Licencia

Este proyecto está desarrollado específicamente para Capacitar-T México. Todos los derechos reservados.

Las imágenes utilizadas provienen de fuentes Creative Commons con atribución apropiada.

---

**Capacitar-T México** - Centro líder en capacitación médica con certificaciones AHA reconocidas internacionalmente.