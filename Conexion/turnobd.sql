-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-02-2016 a las 21:28:03
-- Versión del servidor: 10.1.9-MariaDB
-- Versión de PHP: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `turnobd`
--
CREATE DATABASE IF NOT EXISTS `turnobd` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `turnobd`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `calificacion`
--

CREATE TABLE `calificacion` (
  `id` int(10) UNSIGNED NOT NULL,
  `idTurno` int(10) UNSIGNED NOT NULL,
  `calificacion` tinyint(3) UNSIGNED NOT NULL,
  `comentario` varchar(128) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(50) NOT NULL,
  `nombres` varchar(30) NOT NULL,
  `apellidos` varchar(30) NOT NULL,
  `telefono` varchar(10) DEFAULT NULL,
  `pass` varchar(50) NOT NULL,
  `idPush` varchar(200) DEFAULT NULL,
  `idFace` varchar(200) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE `departamento` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id` int(10) UNSIGNED NOT NULL,
  `idSucursal` int(10) UNSIGNED NOT NULL,
  `identificacion` varchar(12) NOT NULL,
  `email` varchar(30) DEFAULT NULL,
  `nombres` varchar(30) NOT NULL,
  `apellidos` varchar(30) NOT NULL,
  `telefono` varchar(12) DEFAULT NULL,
  `pass` varchar(30) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL,
  `estadoOnline` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id` int(10) UNSIGNED NOT NULL,
  `nit` varchar(15) NOT NULL,
  `razonSocial` varchar(50) NOT NULL,
  `email` varchar(20) DEFAULT NULL,
  `telefono` varchar(12) NOT NULL,
  `contacto` varchar(30) DEFAULT NULL,
  `promedio` tinyint(3) UNSIGNED NOT NULL,
  `logo` varchar(30) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estadoturno`
--

CREATE TABLE `estadoturno` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(15) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipio`
--

CREATE TABLE `municipio` (
  `id` int(10) UNSIGNED NOT NULL,
  `idDepartamento` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sector`
--

CREATE TABLE `sector` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` varchar(128) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `id` int(10) UNSIGNED NOT NULL,
  `idSector` int(10) UNSIGNED NOT NULL,
  `idEmpresa` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` varchar(128) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `serviciossucursal`
--

CREATE TABLE `serviciossucursal` (
  `id` int(10) UNSIGNED NOT NULL,
  `idServicio` int(10) UNSIGNED NOT NULL,
  `idSucursal` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `serviciosusuario`
--

CREATE TABLE `serviciosusuario` (
  `id` int(10) UNSIGNED NOT NULL,
  `idUsuario` int(10) UNSIGNED NOT NULL,
  `idServicio` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sucursal`
--

CREATE TABLE `sucursal` (
  `id` int(10) UNSIGNED NOT NULL,
  `idEmpresa` int(10) UNSIGNED NOT NULL,
  `idMunicipio` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `direccion` varchar(20) NOT NULL,
  `telefono` varchar(12) NOT NULL,
  `latitud` varchar(12) NOT NULL,
  `longitud` varchar(12) NOT NULL,
  `promedio` float DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipoturno`
--

CREATE TABLE `tipoturno` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(15) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `turno`
--

CREATE TABLE `turno` (
  `id` int(10) UNSIGNED NOT NULL,
  `idCliente` int(10) UNSIGNED NOT NULL,
  `idServicio` int(10) UNSIGNED NOT NULL,
  `idSucursal` int(10) UNSIGNED NOT NULL,
  `idEmpleado` int(10) UNSIGNED NOT NULL,
  `idTipoTurno` int(10) UNSIGNED NOT NULL,
  `idEstadoTurno` int(10) UNSIGNED NOT NULL,
  `fechaSolicitud` datetime NOT NULL,
  `fechaAtendida` datetime DEFAULT NULL,
  `turno` int(10) UNSIGNED NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `calificacion`
--
ALTER TABLE `calificacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idTurno` (`idTurno`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `departamento`
--
ALTER TABLE `departamento`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identificacion` (`identificacion`),
  ADD KEY `idSucursal` (`idSucursal`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `estadoturno`
--
ALTER TABLE `estadoturno`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `municipio`
--
ALTER TABLE `municipio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idDepartamento` (`idDepartamento`);

--
-- Indices de la tabla `sector`
--
ALTER TABLE `sector`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idSucursal` (`idSector`),
  ADD KEY `idSucursal_2` (`idEmpresa`);

--
-- Indices de la tabla `serviciossucursal`
--
ALTER TABLE `serviciossucursal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idServicio` (`idServicio`),
  ADD KEY `idSucursal` (`idSucursal`);

--
-- Indices de la tabla `serviciosusuario`
--
ALTER TABLE `serviciosusuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUsuario` (`idUsuario`),
  ADD KEY `idServicio` (`idServicio`);

--
-- Indices de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idEmpresa` (`idEmpresa`),
  ADD KEY `idMunicipio` (`idMunicipio`);

--
-- Indices de la tabla `tipoturno`
--
ALTER TABLE `tipoturno`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `turno`
--
ALTER TABLE `turno`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idCliente` (`idCliente`),
  ADD KEY `idServicio` (`idServicio`),
  ADD KEY `idSucursal` (`idSucursal`),
  ADD KEY `idUsuario` (`idEmpleado`),
  ADD KEY `idTipoTurno` (`idTipoTurno`),
  ADD KEY `idEstadoTurno` (`idEstadoTurno`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `calificacion`
--
ALTER TABLE `calificacion`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `departamento`
--
ALTER TABLE `departamento`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `estadoturno`
--
ALTER TABLE `estadoturno`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `municipio`
--
ALTER TABLE `municipio`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sector`
--
ALTER TABLE `sector`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `serviciossucursal`
--
ALTER TABLE `serviciossucursal`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `serviciosusuario`
--
ALTER TABLE `serviciosusuario`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `tipoturno`
--
ALTER TABLE `tipoturno`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `turno`
--
ALTER TABLE `turno`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `calificacion`
--
ALTER TABLE `calificacion`
  ADD CONSTRAINT `calificacion_ibfk_1` FOREIGN KEY (`idTurno`) REFERENCES `turno` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD CONSTRAINT `empleado_ibfk_1` FOREIGN KEY (`idSucursal`) REFERENCES `sucursal` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `municipio`
--
ALTER TABLE `municipio`
  ADD CONSTRAINT `municipio_ibfk_1` FOREIGN KEY (`idDepartamento`) REFERENCES `departamento` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD CONSTRAINT `servicio_ibfk_1` FOREIGN KEY (`idSector`) REFERENCES `sector` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `servicio_ibfk_2` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `serviciossucursal`
--
ALTER TABLE `serviciossucursal`
  ADD CONSTRAINT `serviciossucursal_ibfk_1` FOREIGN KEY (`idServicio`) REFERENCES `servicio` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `serviciossucursal_ibfk_2` FOREIGN KEY (`idSucursal`) REFERENCES `sucursal` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `serviciosusuario`
--
ALTER TABLE `serviciosusuario`
  ADD CONSTRAINT `serviciosusuario_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `empleado` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `serviciosusuario_ibfk_2` FOREIGN KEY (`idServicio`) REFERENCES `servicio` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `sucursal`
--
ALTER TABLE `sucursal`
  ADD CONSTRAINT `sucursal_ibfk_1` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sucursal_ibfk_2` FOREIGN KEY (`idMunicipio`) REFERENCES `municipio` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `turno`
--
ALTER TABLE `turno`
  ADD CONSTRAINT `turno_ibfk_1` FOREIGN KEY (`idCliente`) REFERENCES `cliente` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `turno_ibfk_2` FOREIGN KEY (`idServicio`) REFERENCES `servicio` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `turno_ibfk_3` FOREIGN KEY (`idSucursal`) REFERENCES `sucursal` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `turno_ibfk_4` FOREIGN KEY (`idEmpleado`) REFERENCES `empleado` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `turno_ibfk_5` FOREIGN KEY (`idTipoTurno`) REFERENCES `tipoturno` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `turno_ibfk_6` FOREIGN KEY (`idEstadoTurno`) REFERENCES `estadoturno` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
