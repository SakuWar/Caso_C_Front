<?php
// Inicia la sesión para manejar autenticación y datos entre solicitudes
session_start();

// Activa la visualización de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verifica que los parámetros 'archivo' y 'carpeta' estén presentes en la solicitud
if (!isset($_GET['archivo']) || !isset($_GET['carpeta'])) {
    http_response_code(400); // Devuelve un código de error 400 (solicitud incorrecta)
    die('Parámetros faltantes'); // Termina la ejecución con un mensaje de error
}

// Sanitiza las entradas para evitar ataques de path traversal
$carpeta = basename($_GET['carpeta']); // Obtiene solo el nombre de la carpeta
$archivo = basename($_GET['archivo']); // Obtiene solo el nombre del archivo
$ruta = realpath("./descarga/$carpeta/$archivo"); // Obtiene la ruta absoluta del archivo

// Verifica que la ruta sea válida y que el archivo exista
if (!$ruta || !file_exists($ruta)) {
    http_response_code(404); // Devuelve un código de error 404 (archivo no encontrado)
    die('Archivo no encontrado'); // Termina la ejecución con un mensaje de error
}

// Verifica si existe un archivo de contraseña asociado a la carpeta
$passwordFile = "./descarga/$carpeta/.password";
if (file_exists($passwordFile)) {
    $sessionKey = 'auth_' . md5($carpeta); // Genera una clave única para la sesión basada en la carpeta

    // Verifica si el usuario ya está autenticado para esta carpeta
    if (!isset($_SESSION[$sessionKey])) {
        // Si no se ha enviado una contraseña, muestra un formulario para ingresarla
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['password'])) {
            // Usa output buffering para evitar errores de encabezados
            ob_start();
            echo '<!DOCTYPE html><html lang="es"><head>...'; // Aquí iría el HTML del formulario
            ob_end_flush();
            exit; // Termina la ejecución hasta que se envíe la contraseña
        }

        // Valida la contraseña ingresada comparándola con el hash almacenado
        $storedHash = file_get_contents($passwordFile); // Lee el hash de la contraseña almacenada
        if (!password_verify($_POST['password'], $storedHash)) {
            http_response_code(401); // Devuelve un código de error 401 (no autorizado)
            die('Contraseña incorrecta'); // Termina la ejecución con un mensaje de error
        }

        // Si la contraseña es correcta, marca al usuario como autenticado en la sesión
        $_SESSION[$sessionKey] = true;
    }
}

// Configura las cabeceras para iniciar la descarga del archivo
header('Content-Description: File Transfer'); // Describe la transferencia del archivo
header('Content-Type: application/octet-stream'); // Indica que es un archivo binario
header('Content-Disposition: attachment; filename="' . $archivo . '"'); // Especifica el nombre del archivo para la descarga
header('Content-Length: ' . filesize($ruta)); // Especifica el tamaño del archivo

// Limpia el búfer de salida y fuerza la descarga del archivo
ob_clean();
flush();
readfile($ruta); // Envía el contenido del archivo al cliente
exit; // Termina la ejecución del script
?>