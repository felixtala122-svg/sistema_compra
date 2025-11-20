-- Crear tabla orden_compra
CREATE TABLE IF NOT EXISTS `orden_compra` (
  `orden_compra` int NOT NULL AUTO_INCREMENT,
  `fecha_orden` date NOT NULL,
  `id_usuario` int NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'ACTIVO',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`orden_compra`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla detalle_orden
CREATE TABLE IF NOT EXISTS `detalle_orden` (
  `detalle_orden` int NOT NULL AUTO_INCREMENT,
  `orden_compra` int NOT NULL,
  `id_productos` int NOT NULL,
  `cantidad` int NOT NULL DEFAULT '0',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`detalle_orden`),
  KEY `orden_compra` (`orden_compra`),
  KEY `id_productos` (`id_productos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;