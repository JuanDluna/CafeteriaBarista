-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 28-11-2023 a las 03:41:33
-- Versión del servidor: 8.0.33
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `baristacafe`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `HacerReservacion` (IN `p_IdCliente` INT, IN `p_FechaReservacion` DATE, IN `p_HoraReservacion` DATETIME, IN `p_NumPersonas` INT, OUT `Mensaje` VARCHAR(255))   BEGIN
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

        SET Mensaje = 'Reserva realizada con éxito.';
    ELSE
        SET Mensaje = 'No hay mesas disponibles para la fecha y hora solicitadas.';
    END IF;
END$$

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `IdCliente` int NOT NULL,
  `NombreCliente` varchar(255) DEFAULT NULL,
  `Direccion` varchar(100) DEFAULT NULL,
  `CorreoElectronico` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`IdCliente`, `NombreCliente`, `Direccion`, `CorreoElectronico`) VALUES
(1, 'Juan De Luna', 'Clarisas #315', 'pablo.dluna@hotmail.com'),
(2, 'Uriel Acosta', 'Aquiles Serdán #304', 'uriel.ac@live.com.mx'),
(3, 'Ronaldo Martínez', 'Pasos #123', 'mjbhronaldo@gmail.com'),
(14, 'Pedro Parker', 'Algun lugar lejano', 'notspiderman@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compraenlinea`
--

CREATE TABLE `compraenlinea` (
  `IdCliente` int DEFAULT NULL,
  `IdProducto` int DEFAULT NULL,
  `Cantidad` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `IdEmpleado` int NOT NULL,
  `NombreEmpleado` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`IdEmpleado`, `NombreEmpleado`) VALUES
(1, 'Juan Pérez'),
(2, 'Ana Sánchez'),
(3, 'Carlos Gómez');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesa`
--

CREATE TABLE `mesa` (
  `NumeroMesa` int NOT NULL,
  `Sillas` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `mesa`
--

INSERT INTO `mesa` (`NumeroMesa`, `Sillas`) VALUES
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `IdProducto` int NOT NULL,
  `NombreProducto` varchar(255) DEFAULT NULL,
  `Existencia` int DEFAULT NULL,
  `Precio` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`IdProducto`, `NombreProducto`, `Existencia`, `Precio`) VALUES
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservacion`
--

CREATE TABLE `reservacion` (
  `NumeroMesa` int DEFAULT NULL,
  `IdCliente` int DEFAULT NULL,
  `FechaReservacion` date DEFAULT NULL,
  `HoraReservacion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `reservacion`
--

INSERT INTO `reservacion` (`NumeroMesa`, `IdCliente`, `FechaReservacion`, `HoraReservacion`) VALUES
(2, 1, '2023-12-02', '2023-12-02 19:00:00'),
(4, 2, '2023-12-04', '2023-12-04 18:00:00'),
(5, 2, '2023-12-05', '2023-12-05 19:00:00'),
(6, 2, '2023-12-06', '2023-12-06 20:00:00'),
(7, 3, '2023-12-07', '2023-12-07 18:00:00'),
(8, 3, '2023-12-08', '2023-12-08 19:00:00'),
(9, 3, '2023-12-09', '2023-12-09 20:00:00'),
(1, 1, '2023-12-01', '2023-12-01 21:15:00'),
(3, 1, '2023-12-25', '2023-12-25 20:30:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonoscliente`
--

CREATE TABLE `telefonoscliente` (
  `IdCliente` int DEFAULT NULL,
  `Telefonos` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `telefonoscliente`
--

INSERT INTO `telefonoscliente` (`IdCliente`, `Telefonos`) VALUES
(14, 1231456789);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venderfisicamente`
--

CREATE TABLE `venderfisicamente` (
  `IdCliente` int DEFAULT NULL,
  `IdEmpleado` int DEFAULT NULL,
  `Cantidad` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`IdCliente`);

--
-- Indices de la tabla `compraenlinea`
--
ALTER TABLE `compraenlinea`
  ADD KEY `IdCliente` (`IdCliente`),
  ADD KEY `IdProducto` (`IdProducto`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`IdEmpleado`);

--
-- Indices de la tabla `mesa`
--
ALTER TABLE `mesa`
  ADD PRIMARY KEY (`NumeroMesa`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`IdProducto`);

--
-- Indices de la tabla `reservacion`
--
ALTER TABLE `reservacion`
  ADD KEY `NumeroMesa` (`NumeroMesa`),
  ADD KEY `IdCliente` (`IdCliente`);

--
-- Indices de la tabla `telefonoscliente`
--
ALTER TABLE `telefonoscliente`
  ADD KEY `IdCliente` (`IdCliente`);

--
-- Indices de la tabla `venderfisicamente`
--
ALTER TABLE `venderfisicamente`
  ADD KEY `IdCliente` (`IdCliente`),
  ADD KEY `IdEmpleado` (`IdEmpleado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `IdCliente` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `IdEmpleado` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `mesa`
--
ALTER TABLE `mesa`
  MODIFY `NumeroMesa` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `IdProducto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `compraenlinea`
--
ALTER TABLE `compraenlinea`
  ADD CONSTRAINT `compraenlinea_ibfk_1` FOREIGN KEY (`IdCliente`) REFERENCES `cliente` (`IdCliente`),
  ADD CONSTRAINT `compraenlinea_ibfk_2` FOREIGN KEY (`IdProducto`) REFERENCES `producto` (`IdProducto`);

--
-- Filtros para la tabla `reservacion`
--
ALTER TABLE `reservacion`
  ADD CONSTRAINT `reservacion_ibfk_1` FOREIGN KEY (`NumeroMesa`) REFERENCES `mesa` (`NumeroMesa`),
  ADD CONSTRAINT `reservacion_ibfk_2` FOREIGN KEY (`IdCliente`) REFERENCES `cliente` (`IdCliente`);

--
-- Filtros para la tabla `telefonoscliente`
--
ALTER TABLE `telefonoscliente`
  ADD CONSTRAINT `telefonoscliente_ibfk_1` FOREIGN KEY (`IdCliente`) REFERENCES `cliente` (`IdCliente`);

--
-- Filtros para la tabla `venderfisicamente`
--
ALTER TABLE `venderfisicamente`
  ADD CONSTRAINT `venderfisicamente_ibfk_1` FOREIGN KEY (`IdCliente`) REFERENCES `cliente` (`IdCliente`),
  ADD CONSTRAINT `venderfisicamente_ibfk_2` FOREIGN KEY (`IdEmpleado`) REFERENCES `empleado` (`IdEmpleado`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
