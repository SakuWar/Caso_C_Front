<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar parámetros
if (!isset($_GET['archivo']) || !isset($_GET['carpeta'])) {
    http_response_code(400);
    die('Parámetros faltantes');
}

// Sanitizar entradas correctamente
$carpeta = basename($_GET['carpeta']); // Usar basename para evitar traversal
$archivo = basename($_GET['archivo']); // Basename elimina rutas
$ruta = realpath("./descarga/$carpeta/$archivo");

// Validar ruta segura
if (!$ruta || !file_exists($ruta)) {
    http_response_code(404);
    die('Archivo no encontrado');
}

// Verificar contraseña si existe
$passwordFile = "./descarga/$carpeta/.password";
if (file_exists($passwordFile)) {
    $sessionKey = 'auth_' . md5($carpeta);
    
    if (!isset($_SESSION[$sessionKey])) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['password'])) {
            // Usar output buffering para evitar errores de headers
            ob_start();
            echo '<!DOCTYPE html><html lang="es"><head>...'; // Tu HTML aquí
            ob_end_flush();
            exit;
        }
        
        // Validar contraseña
        $storedHash = file_get_contents($passwordFile);
        if (!password_verify($_POST['password'], $storedHash)) {
            http_response_code(401);
            die('Contraseña incorrecta');
        }
        
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