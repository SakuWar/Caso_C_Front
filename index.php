<?php
session_start();
$carpetaNombre = isset($_GET['nombre']) ? $_GET['nombre'] : '';
$carpetaRuta = "./descarga/" . $carpetaNombre;

try {
    if (!file_exists($carpetaRuta)) {
        mkdir($carpetaRuta, 0755, true);
        $mensaje = "Carpeta '$carpetaNombre' creada con éxito.";
    } else {
        $mensaje = "La carpeta '$carpetaNombre' ya existe.";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_FILES['archivo'])) {
            $archivos = $_FILES['archivo'];
            $uploadSuccess = true;
            
            foreach ($archivos['name'] as $key => $name) {
                if ($archivos['error'][$key] === UPLOAD_ERR_OK) {
                    $nombreArchivo = str_replace(' ', '_', basename($name));
                    $destino = $carpetaRuta . '/' . $nombreArchivo;
                    
                    if (!move_uploaded_file($archivos['tmp_name'][$key], $destino)) {
                        $uploadSuccess = false;
                    }
                }
            }
            
            if ($uploadSuccess) {
                $mensaje = "Archivos subidos con éxito.";
            } else {
                throw new Exception("Error al subir algunos archivos.");
            }
        }
    }

    if (isset($_POST['eliminarArchivo'])) {
        $archivoAEliminar = $_POST['eliminarArchivo'];
        $archivoRutaAEliminar = $carpetaRuta . '/' . $archivoAEliminar;

        if (file_exists($archivoRutaAEliminar)) {
            if (unlink($archivoRutaAEliminar)) {
                $mensaje = "Archivo '$archivoAEliminar' eliminado con éxito.";
            } else {
                throw new Exception("Error al eliminar el archivo.");
            }
        } else {
            throw new Exception("El archivo '$archivoAEliminar' no existe.");
        }
    }

    function obtenerIcono($archivo) {
        $extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
        $iconos = [
            'pdf' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0730c5" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="8" fill="#0730c5">PDF</text></svg>',
            
            'doc' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0730c5" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="8" fill="#0730c5">DOC</text></svg>',
            
            'xls' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0730c5" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="8" fill="#0730c5">XLS</text></svg>',
            
            'zip' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0730c5" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="8" fill="#0730c5">ZIP</text></svg>',
            
            'jpg' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0730c5" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="8" fill="#0730c5">JPG</text></svg>',
            
            'png' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0730c5" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="8" fill="#0730c5">PNG</text></svg>',
            
            'mp3' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0730c5" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="8" fill="#0730c5">MP3</text></svg>',
            
            'mp4' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0730c5" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="8" fill="#0730c5">MP4</text></svg>',
            
            'exe' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0730c5" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="8" fill="#0730c5">EXE</text></svg>',
            
            'psd' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0730c5" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="8" fill="#0730c5">PSD</text></svg>',
            
            'ai' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0730c5" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="8" fill="#0730c5">AI</text></svg>',
            
            'txt' => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0730c5" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="8" fill="#0730c5">TXT</text></svg>'
        ];
        
        // Icono genérico para extensiones no listadas
        return $iconos[$extension] ?? '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0730c5" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle" font-size="8" fill="#0730c5">'.strtoupper($extension).'</text></svg>';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['set_password'])) {
        if (!empty($_POST['password'])) {
            $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            file_put_contents($carpetaRuta . '/.password', $hash);
            $mensaje = "Contraseña establecida correctamente.";
        }
    }

} catch (Exception $e) {
    $mensaje = "Error: " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compartir archivos</title>
    <script src="parametro.js"></script>
    <link rel="stylesheet" href="estilo.css">
</head>

<body>
    <h1>Compartir archivos <sup class="beta">BETA</sup></h1>
    <div class="content">
        <h3>Sube tus archivos y comparte este enlace temporal: <span>ibu.pe/?nombre=<?php echo $carpetaNombre;?></span></h3>

        <?php if (!file_exists($carpetaRuta . '/.password')): ?>
        <div class="password-form">
            <form method="POST">
                <label><input type="checkbox" id="habilitarPassword" onclick="togglePassword()"> Proteger con contraseña</label>
                <div id="passwordField" style="display:none;">
                    <input type="password" name="password" placeholder="Ingresa una contraseña" required>
                    <button type="submit" name="set_password">Aplicar</button>
                </div>
            </form>
        </div>
        <script>
            function togglePassword() {
                const checkbox = document.getElementById('habilitarPassword');
                const field = document.getElementById('passwordField');
                field.style.display = checkbox.checked ? 'block' : 'none';
            }
        </script>
        <?php endif; ?>

        <div class="container">
            <div class="drop-area" id="drop-area">
                <form action="" id="form" method="POST" enctype="multipart/form-data">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" style="fill:#0730c5;transform: ;msFilter:;"><path d="M13 19v-4h3l-4-5-4 5h3v4z"></path><path d="M7 19h2v-2H7c-1.654 0-3-1.346-3-3 0-1.404 1.199-2.756 2.673-3.015l.581-.102.192-.558C8.149 8.274 9.895 7 12 7c2.757 0 5 2.243 5 5v1h1c1.103 0 2 .897 2 2s-.897 2-2 2h-3v2h3c2.206 0 4-1.794 4-4a4.01 4.01 0 0 0-3.056-3.888C18.507 7.67 15.56 5 12 5 9.244 5 6.85 6.611 5.757 9.15 3.609 9.792 2 11.82 2 14c0 2.757 2.243 5 5 5z"></path></svg> <br>
                    <input type="file" class="file-input" name="archivo[]" id="archivo" multiple onchange="document.getElementById('form').submit()">
                    <label> Arrastra tus archivos aquí<br>o</label>
                    <p><b>Abre el explorador</b></p> 
                    
                </form>
            </div>

            <div class="container2">
               

                <div id="file-list" class="pila">
                    <?php
                    $targetDir = $carpetaRuta;

                    $files = scandir($targetDir);
                    $files = array_diff($files, array('.', '..', '.password'));

                    if (count($files) > 0) {
                        echo " <h3 style='margin-bottom:10px;'>Archivos Subidos:</h3>";

                        foreach ($files as $file) {
                            echo "<div class='archivos_subidos'>
                                <div class='file-info'>
                                    <div class='file-icon'>".obtenerIcono($file)."</div>
                                        <a href='descargar.php?carpeta=".urlencode($carpetaNombre)."&archivo=".urlencode($file)."' class='boton-descargar'>$file</a>
                                    </div>
                            <form action='' method='POST' style='display:inline;'>
                                <input type='hidden' name='eliminarArchivo' value='$file'>
                                <button type='submit' class='btn_delete'>
                                    <svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-trash' width='24' height='24' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'>
                                        <path stroke='none' d='M0 0h24v24H0z' fill='none'/>
                                        <path d='M4 7l16 0' />
                                        <path d='M10 11l0 6' />
                                        <path d='M14 11l0 6' />
                                        <path d='M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12' />
                                        <path d='M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3' />
                                    </svg>
                                </button>
                            </form>
                        </div>";
                        }
                    } else {
                        echo "No se han subido archivos.";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- <script src="parametro.js"></script> -->

</body>

</html>
