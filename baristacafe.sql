SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `baristacafe` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `baristacafe`;

DELIMITER $$
DROP PROCEDURE IF EXISTS `HacerReservacion`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `HacerReservacion` (IN `p_IdCliente` INT, IN `p_FechaReservacion` DATE, IN `p_HoraReservacion` DATETIME, IN `p_NumPersonas` INT)   BEGIN
    DECLARE num_mesas_disponibles INT;

    -- Calcular el rango de tiempo de una hora
    DECLARE p_HoraFinal DATETIME;
    SET p_HoraFinal = DATE_ADD(p_HoraReservacion, INTERVAL 1 HOUR);

    -- Verificar mesas disponibles con suficientes sillas
    SELECT COUNT(*) INTO num_mesas_disponibles
    FROM Mesa m
    WHERE m.Sillas >= p_NumPersonas
    AND m.NumeroMesa NOT IN (
        SELECT r.NumeroMesa
        FROM Reservacion r
        WHERE r.FechaReservacion = p_FechaReservacion
        AND NOT (r.HoraReservacion >= p_HoraFinal OR DATE_ADD(r.HoraReservacion, INTERVAL 1 HOUR) <= p_HoraReservacion)
    );

    -- Para depuración: Verificar qué mesas están disponibles
    SELECT m.NumeroMesa
    FROM Mesa m
    WHERE m.Sillas >= p_NumPersonas
    AND m.NumeroMesa NOT IN (
        SELECT r.NumeroMesa
        FROM Reservacion r
        WHERE r.FechaReservacion = p_FechaReservacion
        AND NOT (r.HoraReservacion >= p_HoraFinal OR DATE_ADD(r.HoraReservacion, INTERVAL 1 HOUR) <= p_HoraReservacion)
    );

    -- Si hay mesas disponibles, hacer la reserva
    IF num_mesas_disponibles > 0 THEN
        INSERT INTO Reservacion (IdCliente, NumeroMesa, FechaReservacion, HoraReservacion)
        SELECT p_IdCliente, m.NumeroMesa, p_FechaReservacion, p_HoraReservacion
        FROM Mesa m
        WHERE m.Sillas >= p_NumPersonas
        AND m.NumeroMesa NOT IN (
            SELECT r.NumeroMesa
            FROM Reservacion r
            WHERE r.FechaReservacion = p_FechaReservacion
            AND NOT (r.HoraReservacion >= p_HoraFinal OR DATE_ADD(r.HoraReservacion, INTERVAL 1 HOUR) <= p_HoraReservacion)
        )
        LIMIT 1;

        SELECT 'Reserva realizada con éxito.' AS Mensaje;
    ELSE
        SELECT 'No hay mesas disponibles para la fecha y hora solicitadas.' AS Mensaje;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `InsertarCliente`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `InsertarCliente` (IN `p_NombreCliente` VARCHAR(255), IN `p_Direccion` VARCHAR(100), IN `p_CorreoElectronico` VARCHAR(50), OUT `p_IdCliente` INT)   BEGIN
    DECLARE cliente_id INT;

    -- Verificar si ya existe un cliente con los mismos datos
SELECT 
    IdCliente
INTO cliente_id FROM
    Cliente
WHERE
    NombreCliente = p_NombreCliente
        AND Direccion = p_Direccion
        AND CorreoElectronico = p_CorreoElectronico;

    -- Si ya existe, devolver ID -1 y salir
    IF cliente_id IS NOT NULL THEN
        SET p_IdCliente = -1;
    ELSE
        -- Si no existe, insertar el nuevo cliente
        INSERT INTO Cliente (NombreCliente, Direccion, CorreoElectronico)
        VALUES (p_NombreCliente, p_Direccion, p_CorreoElectronico);

        -- Obtener el ID del cliente recién insertado
        SET p_IdCliente = LAST_INSERT_ID();
    END IF;
END$$

DELIMITER ;

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE IF NOT EXISTS `cliente` (
  `IdCliente` int NOT NULL AUTO_INCREMENT,
  `NombreCliente` varchar(255) DEFAULT NULL,
  `Direccion` varchar(100) DEFAULT NULL,
  `CorreoElectronico` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`IdCliente`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

TRUNCATE TABLE `cliente`;
INSERT INTO `cliente` VALUES
(1, 'Juan De Luna', 'Clarisas #315', 'pablo.dluna@hotmail.com'),
(2, 'Uriel Acosta', 'Aquiles Serdán #304', 'uriel.ac@live.com.mx'),
(3, 'Ronaldo Martínez', 'Pasos #123', 'mjbhronaldo@gmail.com'),
(14, 'Pedro Parker', 'Algun lugar lejano', 'notspiderman@gmail.com');

DROP TABLE IF EXISTS `compraenlinea`;
CREATE TABLE IF NOT EXISTS `compraenlinea` (
  `IdCliente` int DEFAULT NULL,
  `IdProducto` int DEFAULT NULL,
  `Cantidad` int DEFAULT NULL,
  KEY `IdCliente` (`IdCliente`),
  KEY `IdProducto` (`IdProducto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

TRUNCATE TABLE `compraenlinea`;
DROP TABLE IF EXISTS `empleado`;
CREATE TABLE IF NOT EXISTS `empleado` (
  `IdEmpleado` int NOT NULL AUTO_INCREMENT,
  `NombreEmpleado` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`IdEmpleado`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

TRUNCATE TABLE `empleado`;
INSERT INTO `empleado` VALUES
(1, 'Juan Pérez'),
(2, 'Ana Sánchez'),
(3, 'Carlos Gómez');

DROP TABLE IF EXISTS `mesa`;
CREATE TABLE IF NOT EXISTS `mesa` (
  `NumeroMesa` int NOT NULL AUTO_INCREMENT,
  `Sillas` int DEFAULT NULL,
  PRIMARY KEY (`NumeroMesa`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

TRUNCATE TABLE `mesa`;
INSERT INTO `mesa` VALUES
(1, 4),
(2, 2),
(3, 6),
(4, 4),
(5, 2),
(6, 8),
(7, 6),
(8, 4),
(9, 2),
(10, 6),
(11, 4),
(12, 8);

DROP TABLE IF EXISTS `producto`;
CREATE TABLE IF NOT EXISTS `producto` (
  `IdProducto` int NOT NULL AUTO_INCREMENT,
  `NombreProducto` varchar(255) DEFAULT NULL,
  `Existencia` int DEFAULT NULL,
  `Precio` int DEFAULT NULL,
  PRIMARY KEY (`IdProducto`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

TRUNCATE TABLE `producto`;
INSERT INTO `producto` VALUES
(1, 'Rol de canela', 10, 25),
(2, 'Waffle tostado', 20, 50),
(3, 'Crepas saladas', 5, 40),
(4, 'Pancakes', 10, 35),
(5, 'Baguettes', 15, 75),
(6, 'Latte', 50, 35),
(7, 'Cafe americano', 35, 21),
(8, 'Chocolate caliente', 40, 31),
(9, 'Frappe Chai/Matcha', 68, 58),
(10, 'Limonada o Naranjada', 40, 27);

DROP TABLE IF EXISTS `reservacion`;
CREATE TABLE IF NOT EXISTS `reservacion` (
  `NumeroMesa` int DEFAULT NULL,
  `IdCliente` int DEFAULT NULL,
  `FechaReservacion` date DEFAULT NULL,
  `HoraReservacion` datetime DEFAULT NULL,
  KEY `NumeroMesa` (`NumeroMesa`),
  KEY `IdCliente` (`IdCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

TRUNCATE TABLE `reservacion`;
INSERT INTO `reservacion` VALUES
(2, 1, '2023-12-02', '2023-12-02 19:00:00'),
(4, 2, '2023-12-04', '2023-12-04 18:00:00'),
(5, 2, '2023-12-05', '2023-12-05 19:00:00'),
(6, 2, '2023-12-06', '2023-12-06 20:00:00'),
(7, 3, '2023-12-07', '2023-12-07 18:00:00'),
(8, 3, '2023-12-08', '2023-12-08 19:00:00'),
(9, 3, '2023-12-09', '2023-12-09 20:00:00'),
(1, 1, '2023-12-01', '2023-12-01 20:00:00'),
(2, 1, '2023-12-01', '2023-12-01 20:00:00'),
(3, 1, '2023-12-01', '2023-12-01 20:00:00');

DROP TABLE IF EXISTS `telefonoscliente`;
CREATE TABLE IF NOT EXISTS `telefonoscliente` (
  `IdCliente` int DEFAULT NULL,
  `Telefonos` bigint DEFAULT NULL,
  KEY `IdCliente` (`IdCliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

TRUNCATE TABLE `telefonoscliente`;
INSERT INTO `telefonoscliente` VALUES
(14, 1231456789);

DROP TABLE IF EXISTS `venderfisicamente`;
CREATE TABLE IF NOT EXISTS `venderfisicamente` (
  `IdCliente` int DEFAULT NULL,
  `IdEmpleado` int DEFAULT NULL,
  `Cantidad` int DEFAULT NULL,
  KEY `IdCliente` (`IdCliente`),
  KEY `IdEmpleado` (`IdEmpleado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

TRUNCATE TABLE `venderfisicamente`;

ALTER TABLE `compraenlinea`
  ADD CONSTRAINT `compraenlinea_ibfk_1` FOREIGN KEY (`IdCliente`) REFERENCES `cliente` (`IdCliente`),
  ADD CONSTRAINT `compraenlinea_ibfk_2` FOREIGN KEY (`IdProducto`) REFERENCES `producto` (`IdProducto`);

ALTER TABLE `reservacion`
  ADD CONSTRAINT `reservacion_ibfk_1` FOREIGN KEY (`NumeroMesa`) REFERENCES `mesa` (`NumeroMesa`),
  ADD CONSTRAINT `reservacion_ibfk_2` FOREIGN KEY (`IdCliente`) REFERENCES `cliente` (`IdCliente`);

ALTER TABLE `telefonoscliente`
  ADD CONSTRAINT `telefonoscliente_ibfk_1` FOREIGN KEY (`IdCliente`) REFERENCES `cliente` (`IdCliente`);

ALTER TABLE `venderfisicamente`
  ADD CONSTRAINT `venderfisicamente_ibfk_1` FOREIGN KEY (`IdCliente`) REFERENCES `cliente` (`IdCliente`),
  ADD CONSTRAINT `venderfisicamente_ibfk_2` FOREIGN KEY (`IdEmpleado`) REFERENCES `empleado` (`IdEmpleado`);
USE `phpmyadmin`;

TRUNCATE TABLE `pma__column_info`;
TRUNCATE TABLE `pma__table_uiprefs`;
TRUNCATE TABLE `pma__tracking`;
TRUNCATE TABLE `pma__column_info`;
TRUNCATE TABLE `pma__table_uiprefs`;
TRUNCATE TABLE `pma__tracking`;
TRUNCATE TABLE `pma__column_info`;
TRUNCATE TABLE `pma__table_uiprefs`;
TRUNCATE TABLE `pma__tracking`;
TRUNCATE TABLE `pma__column_info`;
TRUNCATE TABLE `pma__table_uiprefs`;
TRUNCATE TABLE `pma__tracking`;
TRUNCATE TABLE `pma__column_info`;
TRUNCATE TABLE `pma__table_uiprefs`;
TRUNCATE TABLE `pma__tracking`;
TRUNCATE TABLE `pma__column_info`;
TRUNCATE TABLE `pma__table_uiprefs`;
TRUNCATE TABLE `pma__tracking`;
TRUNCATE TABLE `pma__column_info`;
TRUNCATE TABLE `pma__table_uiprefs`;
TRUNCATE TABLE `pma__tracking`;
TRUNCATE TABLE `pma__column_info`;
TRUNCATE TABLE `pma__table_uiprefs`;
TRUNCATE TABLE `pma__tracking`;
TRUNCATE TABLE `pma__bookmark`;
TRUNCATE TABLE `pma__relation`;
TRUNCATE TABLE `pma__savedsearches`;
TRUNCATE TABLE `pma__central_columns`;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
