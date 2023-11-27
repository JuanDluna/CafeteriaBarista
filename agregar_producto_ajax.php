<?php
if (
    isset($_POST['nombreProducto']) && 
    isset($_POST['existencia']) && is_numeric($_POST['existencia']) &&
    isset($_POST['precio']) && is_numeric($_POST['precio'])
) {
    $nombreProducto = $_POST['nombreProducto'];
    $existencia = $_POST['existencia']; 
    $precio = $_POST['precio'];

    $sql = new mysqli("localhost", "root", "", "baristacafe");

    if ($sql->connect_error) {
        header("Content-Type: application/json");
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
        exit();
    }

    // Logic to add the product
    $stmt = $sql->prepare("INSERT INTO producto (nombreProducto, existencia, precio) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $nombreProducto, $existencia, $precio);

    if ($stmt->execute()) {
        // Success in addition
        header("Content-Type: application/json");
        echo json_encode(['success' => true]);
        exit();
    } else {
        // Error in addition
        header("Content-Type: application/json");
        echo json_encode(['success' => false, 'message' => 'Error al agregar el producto: ' + $stmt->error]);
        exit();
    }
}
else {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'message' => 'Datos de producto no proporcionados']);
    exit();
}
?>