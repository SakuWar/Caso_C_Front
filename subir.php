<?php
// Establece el tipo de contenido de la respuesta como JSON
header('Content-Type: application/json');

// Inicializa un arreglo para la respuesta con valores predeterminados
$response = ['success' => false, 'message' => ''];

try {
    // Obtiene el nombre de la carpeta desde los datos enviados por POST
    $carpetaNombre = $_POST['nombre'];
    $carpetaRuta = "./descarga/" . $carpetaNombre;

    // Verifica si la carpeta no existe y la crea con permisos 0755
    if (!file_exists($carpetaRuta)) {
        mkdir($carpetaRuta, 0755, true);
    }

    // Verifica si no se recibió ningún archivo en la solicitud
    if (empty($_FILES['archivo'])) {
        throw new Exception("No se recibió ningún archivo."); // Lanza una excepción si no hay archivo
    }

    // Obtiene la información del archivo subido
    $archivo = $_FILES['archivo'];
    $nombreArchivo = str_replace(' ', '_', basename($archivo['name'])); // Reemplaza espacios en el nombre del archivo
    $destino = $carpetaRuta . '/' . $nombreArchivo; // Define la ruta de destino del archivo

    // Mueve el archivo subido a la carpeta de destino
    if (move_uploaded_file($archivo['tmp_name'], $destino)) {
        $response['success'] = true; // Indica que la subida fue exitosa
        $response['message'] = "Archivo subido correctamente.";
    } else {
        throw new Exception("Error al mover el archivo."); // Lanza una excepción si ocurre un error al mover el archivo
    }
} catch (Exception $e) {
    // Captura cualquier excepción y guarda el mensaje de error en la respuesta
    $response['message'] = $e->getMessage();
}

// Devuelve la respuesta como JSON
echo json_encode($response);
?>