<?php
require_once 'includes/viewmodel_base.php';

class ErrorViewModel extends ViewModelBase {
    
    protected function init() {
        // Set common error page data
        $this->bind('site_name', SITE_NAME);
        $this->bind('site_url', SITE_URL);
    }
    
    public function notFound() {
        http_response_code(404);
        
        $this->setData([
            'title' => '404 - Página No Encontrada - ' . SITE_NAME,
            'meta_description' => 'La página que buscas no se encuentra disponible. Explora nuestros cursos de capacitación médica.',
            'error_code' => 404,
            'error_title' => 'Página No Encontrada',
            'error_message' => 'Lo sentimos, la página que buscas no se encuentra disponible.',
            'error_suggestions' => [
                'Verifica que la URL esté escrita correctamente',
                'Regresa a la página principal',
                'Explora nuestros cursos disponibles',
                'Usa el buscador para encontrar lo que necesitas'
            ]
        ]);
        
        $this->view('errors/404');
    }
    
    public function forbidden() {
        http_response_code(403);
        
        $this->setData([
            'title' => '403 - Acceso Denegado - ' . SITE_NAME,
            'meta_description' => 'No tienes permisos para acceder a esta página.',
            'error_code' => 403,
            'error_title' => 'Acceso Denegado',
            'error_message' => 'No tienes permisos suficientes para acceder a esta página.',
            'error_suggestions' => [
                'Inicia sesión con una cuenta autorizada',
                'Contacta al administrador si necesitas acceso',
                'Regresa a la página principal',
                'Explora las páginas públicas disponibles'
            ]
        ]);
        
        $this->view('errors/403');
    }
    
    public function serverError() {
        http_response_code(500);
        
        $this->setData([
            'title' => '500 - Error del Servidor - ' . SITE_NAME,
            'meta_description' => 'Ha ocurrido un error interno del servidor. Por favor intenta más tarde.',
            'error_code' => 500,
            'error_title' => 'Error Interno del Servidor',
            'error_message' => 'Ha ocurrido un error interno. Nuestro equipo técnico ha sido notificado.',
            'error_suggestions' => [
                'Intenta recargar la página en unos minutos',
                'Verifica tu conexión a internet',
                'Contacta al soporte técnico si el problema persiste',
                'Regresa a la página principal'
            ]
        ]);
        
        $this->view('errors/500');
    }
    
    public function badRequest() {
        http_response_code(400);
        
        $this->setData([
            'title' => '400 - Solicitud Incorrecta - ' . SITE_NAME,
            'meta_description' => 'La solicitud enviada no es válida o está mal formada.',
            'error_code' => 400,
            'error_title' => 'Solicitud Incorrecta',
            'error_message' => 'La solicitud enviada no es válida. Por favor verifica los datos e intenta nuevamente.',
            'error_suggestions' => [
                'Verifica que todos los campos estén correctamente llenos',
                'Asegúrate de usar el formato correcto',
                'Intenta enviar la información nuevamente',
                'Contacta al soporte si el problema continúa'
            ]
        ]);
        
        $this->view('errors/400');
    }
    
    public function maintenance() {
        http_response_code(503);
        
        $this->setData([
            'title' => '503 - Sitio en Mantenimiento - ' . SITE_NAME,
            'meta_description' => 'El sitio está temporalmente en mantenimiento. Regresa pronto.',
            'error_code' => 503,
            'error_title' => 'Sitio en Mantenimiento',
            'error_message' => 'Estamos realizando mejoras al sitio. Regresa en unos minutos.',
            'error_suggestions' => [
                'Intenta acceder nuevamente en unos minutos',
                'Síguenos en redes sociales para actualizaciones',
                'Contacta por WhatsApp para urgencias',
                'El mantenimiento será breve'
            ]
        ]);
        
        $this->view('errors/503');
    }
    
    // API error responses
    public function apiNotFound() {
        $this->json([
            'success' => false,
            'error' => 'Endpoint no encontrado',
            'code' => 404,
            'message' => 'El endpoint solicitado no existe'
        ], 404);
    }
    
    public function apiMethodNotAllowed() {
        $this->json([
            'success' => false,
            'error' => 'Método no permitido',
            'code' => 405,
            'message' => 'El método HTTP usado no está permitido para este endpoint'
        ], 405);
    }
    
    public function apiServerError($message = null) {
        $this->json([
            'success' => false,
            'error' => 'Error interno del servidor',
            'code' => 500,
            'message' => $message ?? 'Ha ocurrido un error interno'
        ], 500);
    }
}
?>