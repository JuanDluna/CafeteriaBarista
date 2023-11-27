<?php
session_start();

if (!isset($_SESSION['id_empleado'])) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'message' => 'No hay sesión activa']);
    exit();
}

if (
    isset($_POST['idProducto']) && is_numeric($_POST['idProducto'])
) {
    $idProducto = $_POST['idProducto'];

    $sql = new mysqli("localhost", "root", "230403", "baristacafe");

    if ($sql->connect_error) {
        header("Content-Type: application/json");
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
        exit();
    }

    // Logic to delete the product
    $stmt = $sql->prepare("DELETE FROM producto WHERE IdProducto = ?");
    $stmt->bind_param("i", $idProducto);

    if ($stmt->execute()) {
        // Success in deletion
        header("Content-Type: application/json");
        echo json_encode(['success' => true]);
        exit();
    } else {
        // Error in deletion
        header("Content-Type: application/json");
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto: ' . $stmt->error]);
        exit();
    }
}
else {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'message' => 'Datos de producto no proporcionados']);
    exit();
}
?>