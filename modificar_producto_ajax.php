<?php
if (
    isset($_POST['idProducto']) && is_numeric($_POST['idProducto']) &&
    isset($_POST['nombreProducto']) && 
    isset($_POST['existencia']) && is_numeric($_POST['existencia']) &&
    isset($_POST['precio']) && is_numeric($_POST['precio'])
) {
    $idProducto = $_POST['idProducto'];
    $nombreProducto = $_POST['nombreProducto'];
    $existencia = $_POST['existencia'];
    $precio = $_POST['precio'];

    $sql = new mysqli("localhost", "root", "", "baristacafe");

    if ($sql->connect_error) {
        header("Content-Type: application/json");
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
        exit();
    }

    // Logic to modify the product
    $stmt = $sql->prepare("UPDATE producto SET nombreProducto = ?, existencia = ?, precio = ? WHERE idProducto = ?");
    $stmt->bind_param("siii", $nombreProducto, $existencia, $precio, $idProducto);

    if ($stmt->execute()) {
        // Success in modification
        header("Content-Type: application/json");
        echo json_encode(['success' => true]);
        exit();
    } else {
        // Error in modification
        header("Content-Type: application/json");
        echo json_encode(['success' => false, 'message' => 'Error al modificar el producto: ' . $stmt->error]);
        exit();
    }
}
else {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'message' => 'Datos de producto no proporcionados']);
    exit();
}
?>