-- SQL para crear las tablas necesarias para el módulo Comparador de Presupuesto
-- Ejecutar en la base de datos usada por la aplicación (ej. 'compra')

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Tabla: comparador_presupuesto (cabecera)
CREATE TABLE IF NOT EXISTS `comparador_presupuesto` (
  `comparador_presupuesto` INT NOT NULL AUTO_INCREMENT,
  `fecha_comparacion` DATE NOT NULL,
  `estado` VARCHAR(20) NOT NULL DEFAULT 'ACTIVO',
  `id_usuario` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`comparador_presupuesto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: detalle_comparador (líneas)
CREATE TABLE IF NOT EXISTS `detalle_comparador` (
  `id_detalle_comparador` INT NOT NULL AUTO_INCREMENT,
  `comparador_presupuesto` INT NOT NULL,
  `id_productos` INT NULL,
  `cantidad` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_detalle_comparador`),
  INDEX `idx_dc_comparador` (`comparador_presupuesto`),
  INDEX `idx_dc_producto` (`id_productos`),
  CONSTRAINT `fk_dc_comparador` FOREIGN KEY (`comparador_presupuesto`) REFERENCES `comparador_presupuesto` (`comparador_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE
  -- Nota: la siguiente llave foránea es opcional y requiere que exista la tabla `productos` con columna `id_productos`
  -- , CONSTRAINT `fk_dc_producto` FOREIGN KEY (`id_productos`) REFERENCES `productos` (`id_productos`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Fin
