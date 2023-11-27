-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 27-11-2023 a las 01:45:14
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
-

CREATE TABLE `cliente` (
  `IdCliente` int NOT NULL,
  `NombreCliente` varchar(255) DEFAULT NULL,
  `Direccion` varchar(100) DEFAULT NULL,
  `CorreoElectronico` varchar(50) DEFAULT NULL
) 


INSERT INTO `cliente` (`IdCliente`, `NombreCliente`, `Direccion`, `CorreoElectronico`) VALUES
(1, 'Juan De Luna', 'Clarisas #315', 'pablo.dluna@hotmail.com'),
(2, 'Uriel Acosta', 'Aquiles Serdán #304', 'uriel.ac@live.com.mx'),
(3, 'Ronaldo Martínez', 'Pasos #123', 'mjbhronaldo@gmail.com');



INSERT INTO `empleado` (`IdEmpleado`, `NombreEmpleado`) VALUES
(1, 'Juan Pérez'),
(2, 'Ana Sánchez'),
(3, 'Carlos Gómez');



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




CREATE TABLE `producto` (
  `IdProducto` int NOT NULL,
  `NombreProducto` varchar(255) DEFAULT NULL,
  `Existencia` int DEFAULT NULL,
  `Precio` int DEFAULT NULL
) 



CREATE TABLE `reservacion` (
  `NumeroMesa` int DEFAULT NULL,
  `IdCliente` int DEFAULT NULL,
  `FechaReservacion` date DEFAULT NULL,
  `HoraReservacion` datetime DEFAULT NULL
) 



CREATE TABLE `telefonoscliente` (
  `IdCliente` int DEFAULT NULL,
  `Telefonos` bigint DEFAULT NULL
) 



CREATE TABLE `venderfisicamente` (
  `IdCliente` int DEFAULT NULL,
  `IdEmpleado` int DEFAULT NULL,
  `Cantidad` int DEFAULT NULL
) 


ALTER TABLE `cliente`
  ADD PRIMARY KEY (`IdCliente`);

ALTER TABLE `compraenlinea`
  ADD KEY `IdCliente` (`IdCliente`),
  ADD KEY `IdProducto` (`IdProducto`);


ALTER TABLE `empleado`
  ADD PRIMARY KEY (`IdEmpleado`);


ALTER TABLE `mesa`
  ADD PRIMARY KEY (`NumeroMesa`);


ALTER TABLE `producto`
  ADD PRIMARY KEY (`IdProducto`);


ALTER TABLE `reservacion`
  ADD KEY `NumeroMesa` (`NumeroMesa`),
  ADD KEY `IdCliente` (`IdCliente`);

ALTER TABLE `telefonoscliente`
  ADD KEY `IdCliente` (`IdCliente`);


ALTER TABLE `venderfisicamente`
  ADD KEY `IdCliente` (`IdCliente`),
  ADD KEY `IdEmpleado` (`IdEmpleado`);



ALTER TABLE `cliente`
  MODIFY `IdCliente` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;


ALTER TABLE `empleado`
  MODIFY `IdEmpleado` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `mesa`
  MODIFY `NumeroMesa` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;


ALTER TABLE `producto`
  MODIFY `IdProducto` int NOT NULL AUTO_INCREMENT;



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
COMMIT;


