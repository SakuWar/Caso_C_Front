<?php
// Habilitar reporte de errores para depuración (quitar en producción)
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

// Forzar descarga con headers adecuados
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $archivo . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($ruta));

// Limpiar buffer de salida
ob_clean();
flush();

// Leer archivo
readfile($ruta);
exit;
?>