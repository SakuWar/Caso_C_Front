<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_GET['archivo']) || !isset($_GET['carpeta'])) {
    http_response_code(400);
    die('Parámetros faltantes');
}

$carpeta = basename($_GET['carpeta']);
$archivo = basename($_GET['archivo']);
$ruta = realpath("./descarga/$carpeta/$archivo");

if (!$ruta || !file_exists($ruta)) {
    http_response_code(404);
    die('Archivo no encontrado');
}

$passwordFile = "./descarga/$carpeta/.password";
if (file_exists($passwordFile)) {
    $sessionKey = 'auth_' . md5($carpeta);

    if (!isset($_SESSION[$sessionKey])) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['password'])) {
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Contraseña requerida</title>
                <link rel="stylesheet" href="estilo.css">
            </head>
            <body>
                <div class="content">
                    <div class="password-prompt">
                        <h2>🔒 Contraseña requerida</h2>
                        <form method="POST">
                            <input type="password" name="password" 
                                   placeholder="Ingresa la contraseña" 
                                   required 
                                   autofocus>
                            <button type="submit">Desbloquear</button>
                        </form>
                        <?php if (isset($_POST['password'])): ?>
                            <p style="color: #ff2b00; margin-top: 10px;">Contraseña incorrecta</p>
                        <?php endif; ?>
                    </div>
                </div>
            </body>
            </html>
            <?php
            exit;
        }

        $storedHash = file_get_contents($passwordFile);
        if (!password_verify($_POST['password'], $storedHash)) {
            $_SESSION['attempt'] = true;
            header("Location: ".$_SERVER['REQUEST_URI']);
            exit;
        }

        $_SESSION[$sessionKey] = true;
    }
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $archivo . '"');
header('Content-Length: ' . filesize($ruta));

readfile($ruta);
exit;
?>