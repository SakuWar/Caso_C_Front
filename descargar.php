<?php
session_start(); // Iniciar sesión
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar parámetros
if (!isset($_GET['archivo']) || !isset($_GET['carpeta'])) {
    http_response_code(400);
    die('Parámetros faltantes');
}

// Sanitizar entradas
$carpeta = filter_var($_GET['carpeta'], FILTER_SANITIZE_STRING);
$archivo = basename(filter_var($_GET['archivo'], FILTER_SANITIZE_STRING));
$ruta = realpath("./descarga/$carpeta/$archivo");

// Validar ruta segura
if (!$ruta || !file_exists($ruta)) {
    http_response_code(404);
    die('Archivo no encontrado');
}

// Verificar contraseña si existe
$passwordFile = "./descarga/$carpeta/.password";
if (file_exists($passwordFile)) {
    // Verificar si ya está autenticado para esta carpeta
    $sessionKey = 'auth_' . md5($carpeta);
    
    if (!isset($_SESSION[$sessionKey])) {
        // Mostrar formulario si no hay POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['password'])) {
            echo '
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <title>Contraseña requerida</title>
                <link rel="stylesheet" href="estilo.css">
            </head>
            <body>
                <div class="password-prompt">
                    <form method="POST">
                        <h3>🔒 Carpeta protegida</h3>
                        <input type="hidden" name="carpeta" value="'.htmlspecialchars($carpeta).'">
                        <input type="hidden" name="archivo" value="'.htmlspecialchars($archivo).'">
                        <input type="password" name="password" placeholder="Ingresa la contraseña" required>
                        <button type="submit">Desbloquear</button>
                    </form>
                </div>
            </body>
            </html>
            ';
            exit;
        }
        
        // Validar contraseña
        $storedHash = file_get_contents($passwordFile);
        if (!password_verify($_POST['password'], $storedHash)) {
            http_response_code(401);
            die('Contraseña incorrecta');
        }
        
        // Marcar como autenticado
        $_SESSION[$sessionKey] = true;
    }
}

// Descargar archivo
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $archivo . '"');
header('Content-Length: ' . filesize($ruta));
ob_clean();
flush();
readfile($ruta);
exit;
?>