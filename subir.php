<?php
header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

try {
    $carpetaNombre = $_POST['nombre'];
    $carpetaRuta = "./descarga/" . $carpetaNombre;

    if (!file_exists($carpetaRuta)) {
        mkdir($carpetaRuta, 0755, true);
    }

    if (empty($_FILES['archivo'])) {
        throw new Exception("No se recibió ningún archivo.");
    }

    $archivo = $_FILES['archivo'];
    $nombreArchivo = str_replace(' ', '_', basename($archivo['name']));
    $destino = $carpetaRuta . '/' . $nombreArchivo;

    if (move_uploaded_file($archivo['tmp_name'], $destino)) {
        $response['success'] = true;
        $response['message'] = "Archivo subido correctamente.";
    } else {
        throw new Exception("Error al mover el archivo.");
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>