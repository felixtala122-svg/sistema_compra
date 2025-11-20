<?php
header('Content-Type: application/json');

require_once("../conexion/db.php");

$db = new DB();
$conexion = $db->conectar();

if (isset($_GET['listar']) || isset($_POST['listar'])) {
    try {
        $sql = "SELECT 
                p.id_presupuesto, 
                p.fecha, 
                p.estado,
                u.nombre AS nombre_usuario,
                pr.nombre AS nombre_proveedor,
                p.id_orden_compra
            FROM presupuesto p
            JOIN usuarios u ON p.id_usuario = u.id_usuarios
            JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
            WHERE p.estado != 'ELIMINADO'
            ORDER BY p.id_presupuesto DESC";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($resultado) > 0) {
            echo json_encode($resultado);
        } else {
            echo "0";
        }
    } catch (PDOException $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

if (isset($_GET['guardar']) || isset($_POST['guardar'])) {
    try {
        $datos = json_decode($_POST['guardar'] ?? $_GET['guardar'], true);
        
        $sql = "INSERT INTO presupuesto (fecha, id_usuario, id_proveedor, estado, id_orden_compra) 
                VALUES (:fecha, :id_usuario, :id_proveedor, :estado, :id_orden_compra)";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':fecha' => $datos['fecha'],
            ':id_usuario' => $datos['id_usuario'],
            ':id_proveedor' => $datos['id_proveedor'],
            ':estado' => $datos['estado'] ?? 'ACTIVO',
            ':id_orden_compra' => $datos['id_orden_compra'] ?? null
        ]);
        
        $id_presupuesto = $conexion->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'id_presupuesto' => $id_presupuesto
        ]);
    } catch (PDOException $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

if (isset($_GET['anular']) || isset($_POST['anular'])) {
    try {
        $id = $_POST['anular'] ?? $_GET['anular'];
        
        $sql = "UPDATE presupuesto SET estado = 'ANULADO' WHERE id_presupuesto = :id";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Presupuesto anulado correctamente'
        ]);
    } catch (PDOException $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

if (isset($_GET['obtener_por_id']) || isset($_POST['obtener_por_id'])) {
    try {
        $id = $_POST['obtener_por_id'] ?? $_GET['obtener_por_id'];
        
        $sql = "SELECT * FROM presupuesto WHERE id_presupuesto = :id";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($resultado) {
            echo json_encode($resultado);
        } else {
            echo "0";
        }
    } catch (PDOException $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

if (isset($_GET['buscar']) || isset($_POST['buscar'])) {
    try {
        $texto = $_POST['buscar'] ?? $_GET['buscar'];
        
        $sql = "SELECT 
                p.id_presupuesto, 
                p.fecha, 
                p.estado,
                u.nombre AS nombre_usuario,
                pr.nombre AS nombre_proveedor,
                p.id_orden_compra
            FROM presupuesto p
            JOIN usuarios u ON p.id_usuario = u.id_usuarios
            JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
            WHERE p.estado != 'ELIMINADO'
            AND (p.id_presupuesto LIKE :texto 
                 OR u.nombre LIKE :texto 
                 OR pr.nombre LIKE :texto)
            ORDER BY p.id_presupuesto DESC";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':texto' => '%' . $texto . '%']);
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($resultado) > 0) {
            echo json_encode($resultado);
        } else {
            echo "0";
        }
    } catch (PDOException $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

if (isset($_GET['obtener_detalles']) || isset($_POST['obtener_detalles'])) {
    try {
        $id = $_POST['obtener_detalles'] ?? $_GET['obtener_detalles'];
        
        $sql = "SELECT 
                dp.id_detalle_presupuesto,
                dp.id_presupuesto,
                dp.id_productos,
                p.nombre AS nombre_producto,
                p.precio,
                dp.cantidad,
                dp.precio_unitario,
                dp.subtotal
            FROM detalle_presupuesto dp
            JOIN productos p ON dp.id_productos = p.id_productos
            WHERE dp.id_presupuesto = :id";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($resultado) > 0) {
            echo json_encode($resultado);
        } else {
            echo "0";
        }
    } catch (PDOException $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

?>