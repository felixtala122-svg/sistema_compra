-- Crear tabla presupuesto (compatible con orden_compra)
CREATE TABLE IF NOT EXISTS `presupuesto` (
  `id_presupuesto` int NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `id_usuario` int NOT NULL,
  `id_proveedor` int NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'ACTIVO',
  `id_orden_compra` int,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_presupuesto`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `id_orden_compra` (`id_orden_compra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla detalle_presupuesto
CREATE TABLE IF NOT EXISTS `detalle_presupuesto` (
  `id_detalle_presupuesto` int NOT NULL AUTO_INCREMENT,
  `id_presupuesto` int NOT NULL,
  `id_productos` int NOT NULL,
  `cantidad` int NOT NULL DEFAULT '0',
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_detalle_presupuesto`),
  KEY `id_presupuesto` (`id_presupuesto`),
  KEY `id_productos` (`id_productos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;