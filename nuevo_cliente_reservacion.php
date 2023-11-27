<?php

$nombre = $_POST['nombre'];
$correo = $_POST['correo'];
$fechaHora = $_POST['fechaHora'];
$personas = $_POST['num_personas'];
$direccion = $_POST['direccion'];
$telefono1 = $_POST['telefono1'];
$telefono2 = $_POST['telefono2'];

$sql = new mysqli('localhost', 'root', '230403', 'baristacafe');

if ($sql->connect_errno) {
    echo json_encode(['success' => false, 'message' => 'No se pudo conectar a la base de datos ' . $sql->connect_error]);
    exit();
}

$stmtUsers = $sql->prepare("INSERT INTO cliente(CorreoElectronico, Direccion, NombreCliente) VALUES (?, ?, ?)");
$stmtUsers->bind_param("sss", $correo, $direccion, $nombre);

if (!$stmtUsers->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al registrar al usuario ' . $stmtUsers->error]);
    exit();
}

$stmtUsers->close();

$stmtPhone = $sql->prepare("INSERT INTO telefonosCliente(IdCliente, Telefonos) VALUES (?, ?)");
$stmtPhone->bind_param("is", $id_cliente, $telefono1);

if (!$stmtPhone->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al registrar el telefono 1' . $stmtPhone->error]);
    exit();
}

if (!empty($telefono2)) {
    $stmtPhone->bind_param("is", $id_cliente, $telefono2);
    if (!$stmtPhone->execute()) {
        echo json_encode(['success' => false, 'message' => 'Error al registrar el telefono 2' . $stmtPhone->error]);
        exit();
    }
}

$stmtPhone->close();

$stmtRsrv = $sql->prepare("SELECT reservacion.HoraReservacion, reservacion.NumeroMesa, mesa.Sillas FROM reservacion , mesa WHERE reservacion.NumeroMesa = mesa.NumeroMesa and reservacion.HoraReservacion = ? and mesa.Sillas >= ?;");
$stmtRsrv->bind_param("si", $fechaHora, $personas);
$stmtRsrv->execute();
$result = $stmtRsrv->get_result();

if ($result->num_rows > 0) {
    $stmtRsrv->close();
    $sql->close();
    echo json_encode(['success' => false, 'message' => 'Error, ya existe una reservación, **USUARIO REGISTRADO**  ' . $stmtRsrv->error]);
    exit();
} else {
    $stmtMesas = $sql->prepare("SELECT mesa.NumeroMesa FROM mesa WHERE mesa.Sillas >= ?;");
    $stmtMesas->bind_param("i", $personas);
    $stmtMesas->execute();
    $resultMesas = $stmtMesas->get_result();

    $row = $resultMesas->fetch_assoc();

    $stmtRsrvInsert = $sql->prepare("INSERT INTO reservacion (FechaReservacion, HoraReservacion, IdCliente, NumeroMesa) VALUES (?, ?, ?, ?)");
    $stmtRsrvInsert->bind_param("ssii", $fechaHora, $fechaHora, $id_cliente, $row['NumeroMesa']);

    if (!$stmtRsrvInsert->execute()) {
        echo json_encode(['success' => false, 'message' => 'Error al registrar la reservación ' . $stmtRsrvInsert->error]);
        exit();
    }

    $stmtRsrvInsert->close();
    $stmtMesas->close();
    $sql->close();
    echo json_encode(['success' => true]);
    exit();
}
