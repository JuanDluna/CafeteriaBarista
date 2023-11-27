<?php
session_start();

if (!isset($_SESSION['id_cliente'])) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'message' => 'No hay sesión activa']);
    exit();
}

if (
    isset($_POST['numeroMesa']) && is_numeric($_POST['numeroMesa']) &&
    isset($_POST['idCliente']) && is_numeric($_POST['idCliente']) &&
    isset($_POST['fechaReservacion']) &&
    isset($_POST['horaReservacion'])
) {
    $numeroMesa = $_POST['numeroMesa'];
    $idCliente = $_POST['idCliente'];
    $fechaReservacion = $_POST['fechaReservacion'];
    $horaReservacion = $_POST['horaReservacion'];

    $sql = new mysqli("localhost", "root", "230403", "baristacafe");

    if ($sql->connect_error) {
        header("Content-Type: application/json");
        echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
        exit();
    }

    // Lógica para eliminar la reservación
    $stmt = $sql->prepare("DELETE FROM reservacion WHERE NumeroMesa = ? AND IdCliente = ? AND FechaReservacion = ? AND HoraReservacion = ?");
    $stmt->bind_param("iiss", $numeroMesa, $idCliente, $fechaReservacion, $horaReservacion);

    if ($stmt->execute()) {
        // Éxito en la eliminación
        header("Content-Type: application/json");
        echo json_encode(['success' => true]);
        $stmt->close();
        $sql->close();
        exit();
    } else {
        // Error en la eliminación
        header("Content-Type: application/json");
        echo json_encode(['success' => false, 'message' => 'Error al eliminar la reservación: ' . $stmt->error]);
        $stmt->close();
        $sql->close();
        exit();
    }
} else {
    // Datos de la reservación no válidos
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'message' => 'Datos de la reservación no válidos']);
    exit();
}
