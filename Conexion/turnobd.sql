-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-02-2016 a las 22:54:18
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

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id`, `email`, `nombres`, `apellidos`, `telefono`, `pass`, `idPush`, `idFace`, `estado`) VALUES
(1, 'prueba@gmail.com', 'Gilmar', 'Ocampo Nieves', '5841611', '1234', NULL, NULL, 'ACTIVO'),
(2, 'cliente2@gmail.com', 'Fabio Andres', 'Rojas Gulloso', NULL, '1234', NULL, NULL, 'ACTIVO'),
(3, 'perro@gmail.com', 'El musulman', 'Anti bombas', NULL, '1234', NULL, NULL, 'ACTIVO'),
(4, 'perrofabio@gmail.com', 'perro', 'fabio', '5841611', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', '01', '01', 'ACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departamento`
--

CREATE TABLE `departamento` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `departamento`
--

INSERT INTO `departamento` (`id`, `nombre`, `estado`) VALUES
(1, 'Cesar', 'ACTIVO');

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
  `pass` varchar(50) NOT NULL,
  `idPush` varchar(100) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL,
  `estadoOnline` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`id`, `idSucursal`, `identificacion`, `email`, `nombres`, `apellidos`, `telefono`, `pass`, `idPush`, `estado`, `estadoOnline`) VALUES
(1, 1, '1065650321', 'giocni@gmail.com', 'Gilmar', 'Ocampo Nieves', '3004061405', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'APA91bEiXMbd-69LQSFm3bMJMnC7ZE1nWmGAT9j3cli9lgbkbZfpzMLSEW95wwPtOjXcheuFxSfBN2lwRqgTn4E3Ky0g7wp996ts', 'ACTIVO', 'INACTIVO');

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

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id`, `nit`, `razonSocial`, `email`, `telefono`, `contacto`, `promedio`, `logo`, `estado`) VALUES
(1, '123', 'habla paja', 'prueba@gmail.com', '5841611', NULL, 0, NULL, 'ACTIVO');

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

--
-- Volcado de datos para la tabla `municipio`
--

INSERT INTO `municipio` (`id`, `idDepartamento`, `nombre`, `estado`) VALUES
(1, 1, 'Valledupar', 'ACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parametros`
--

CREATE TABLE `parametros` (
  `id` int(10) UNSIGNED NOT NULL,
  `diametro_busqueda` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `parametros`
--

INSERT INTO `parametros` (`id`, `diametro_busqueda`) VALUES
(1, 1);

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

--
-- Volcado de datos para la tabla `sector`
--

INSERT INTO `sector` (`id`, `nombre`, `descripcion`, `estado`) VALUES
(1, 'Lavaderos', 'Lavaderos de carros y motos', 'ACTIVO'),
(2, 'Peluquerias', 'Cortes de cabello y demas', 'ACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sectorempresa`
--

CREATE TABLE `sectorempresa` (
  `id` int(10) UNSIGNED NOT NULL,
  `idSector` int(10) UNSIGNED NOT NULL,
  `idEmpresa` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `sectorempresa`
--

INSERT INTO `sectorempresa` (`id`, `idSector`, `idEmpresa`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `id` int(10) UNSIGNED NOT NULL,
  `idEmpresa` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `descripcion` varchar(128) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`id`, `idEmpresa`, `nombre`, `descripcion`, `estado`) VALUES
(1, 1, 'Lavada estandar', 'Lavada de carro sencilla', 'ACTIVO'),
(2, 1, 'Lavadero con mujeres encueras', NULL, 'ACTIVO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `serviciosempleado`
--

CREATE TABLE `serviciosempleado` (
  `id` int(10) UNSIGNED NOT NULL,
  `idEmpleado` int(10) UNSIGNED NOT NULL,
  `idServicio` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `serviciosempleado`
--

INSERT INTO `serviciosempleado` (`id`, `idEmpleado`, `idServicio`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `serviciossucursal`
--

CREATE TABLE `serviciossucursal` (
  `id` int(10) UNSIGNED NOT NULL,
  `idServicio` int(10) UNSIGNED NOT NULL,
  `idSucursal` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `serviciossucursal`
--

INSERT INTO `serviciossucursal` (`id`, `idServicio`, `idSucursal`) VALUES
(1, 1, 1);

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

--
-- Volcado de datos para la tabla `sucursal`
--

INSERT INTO `sucursal` (`id`, `idEmpresa`, `idMunicipio`, `nombre`, `direccion`, `telefono`, `latitud`, `longitud`, `promedio`, `estado`) VALUES
(1, 1, 1, 'El centro', 'Cra 6 # 18a - 61', '5841611', '10.4762763', '-73.2590097', 0, 'ACTIVO');

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
  `fechaSolicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fechaInicio` datetime DEFAULT NULL,
  `fechaFinal` datetime DEFAULT NULL,
  `tiempo` smallint(6) DEFAULT '0',
  `turno` int(10) UNSIGNED NOT NULL,
  `tipoTurno` enum('NORMAL','VIP') NOT NULL,
  `estadoTurno` enum('SOLICITADO','CONFIRMADO','TERMINADO','CANCELADO','ATENDIENDO') NOT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `turno`
--

INSERT INTO `turno` (`id`, `idCliente`, `idServicio`, `idSucursal`, `idEmpleado`, `fechaSolicitud`, `fechaInicio`, `fechaFinal`, `tiempo`, `turno`, `tipoTurno`, `estadoTurno`, `estado`) VALUES
(1, 1, 1, 1, 1, '2016-02-12 15:27:26', '2016-02-13 01:45:19', '2016-02-13 01:45:34', 0, 1, 'NORMAL', 'SOLICITADO', 'ACTIVO'),
(3, 2, 1, 1, 1, '2016-02-13 10:27:00', '2016-02-13 01:45:37', '2016-02-13 12:14:22', 0, 2, 'NORMAL', 'CONFIRMADO', 'ACTIVO'),
(4, 3, 1, 1, 1, '2016-02-13 10:27:27', '2016-02-13 18:04:05', '2016-02-13 18:05:54', 0, 3, 'NORMAL', 'SOLICITADO', 'ACTIVO');

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
-- Indices de la tabla `parametros`
--
ALTER TABLE `parametros`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sector`
--
ALTER TABLE `sector`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sectorempresa`
--
ALTER TABLE `sectorempresa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idSector` (`idSector`),
  ADD KEY `idEmpresa` (`idEmpresa`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idSucursal_2` (`idEmpresa`);

--
-- Indices de la tabla `serviciosempleado`
--
ALTER TABLE `serviciosempleado`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idUsuario` (`idEmpleado`),
  ADD KEY `idServicio` (`idServicio`);

--
-- Indices de la tabla `serviciossucursal`
--
ALTER TABLE `serviciossucursal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idServicio` (`idServicio`),
  ADD KEY `idSucursal` (`idSucursal`);

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
  ADD KEY `idTipoTurno` (`tipoTurno`),
  ADD KEY `idEstadoTurno` (`estadoTurno`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `departamento`
--
ALTER TABLE `departamento`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `estadoturno`
--
ALTER TABLE `estadoturno`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `municipio`
--
ALTER TABLE `municipio`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `parametros`
--
ALTER TABLE `parametros`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `sector`
--
ALTER TABLE `sector`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `sectorempresa`
--
ALTER TABLE `sectorempresa`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT de la tabla `serviciosempleado`
--
ALTER TABLE `serviciosempleado`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `serviciossucursal`
--
ALTER TABLE `serviciossucursal`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `sucursal`
--
ALTER TABLE `sucursal`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT de la tabla `tipoturno`
--
ALTER TABLE `tipoturno`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT de la tabla `turno`
--
ALTER TABLE `turno`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
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
-- Filtros para la tabla `sectorempresa`
--
ALTER TABLE `sectorempresa`
  ADD CONSTRAINT `sectorempresa_ibfk_1` FOREIGN KEY (`idSector`) REFERENCES `sector` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `sectorempresa_ibfk_2` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD CONSTRAINT `servicio_ibfk_2` FOREIGN KEY (`idEmpresa`) REFERENCES `empresa` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `serviciosempleado`
--
ALTER TABLE `serviciosempleado`
  ADD CONSTRAINT `serviciosempleado_ibfk_1` FOREIGN KEY (`idEmpleado`) REFERENCES `empleado` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `serviciosempleado_ibfk_2` FOREIGN KEY (`idServicio`) REFERENCES `servicio` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `serviciossucursal`
--
ALTER TABLE `serviciossucursal`
  ADD CONSTRAINT `serviciossucursal_ibfk_1` FOREIGN KEY (`idServicio`) REFERENCES `servicio` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `serviciossucursal_ibfk_2` FOREIGN KEY (`idSucursal`) REFERENCES `sucursal` (`id`) ON UPDATE CASCADE;

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
  ADD CONSTRAINT `turno_ibfk_4` FOREIGN KEY (`idEmpleado`) REFERENCES `empleado` (`id`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
