<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página No Encontrada | Capacitar-T México</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #dc143c 0%, #2c5aa0 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .error-container {
            text-align: center;
            max-width: 500px;
            padding: 2rem;
        }
        .error-code {
            font-size: 6rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        .error-title {
            font-size: 2rem;
            margin: 1rem 0;
        }
        .error-message {
            font-size: 1.1rem;
            margin: 1.5rem 0;
            opacity: 0.9;
        }
        .error-actions {
            margin-top: 2rem;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: rgba(255,255,255,0.2);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 0 0.5rem;
            transition: background 0.3s;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .medical-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.8;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="error-container">
        <div class="medical-icon">
            <i class="fas fa-heartbeat"></i>
        </div>
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Página No Encontrada</h2>
        <p class="error-message">
            La página que buscas no existe o ha sido movida. 
            Puede que el enlace esté incorrecto o la página haya sido eliminada.
        </p>
        <div class="error-actions">
            <a href="/" class="btn">
                <i class="fas fa-home"></i> Volver al Inicio
            </a>
            <a href="/cursos" class="btn">
                <i class="fas fa-graduation-cap"></i> Ver Cursos
            </a>
        </div>
    </div>
</body>
</html>