<?php
header('Content-Type: application/json');

require_once("../conexion/db.php");

$db = new DB();
$conexion = $db->conectar();

if (isset($_GET['guardar']) || isset($_POST['guardar'])) {
    try {
        $datos = json_decode($_POST['guardar'] ?? $_GET['guardar'], true);
        
        $sql = "INSERT INTO detalle_presupuesto (id_presupuesto, id_productos, cantidad, precio_unitario, subtotal) 
                VALUES (:id_presupuesto, :id_productos, :cantidad, :precio_unitario, :subtotal)";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':id_presupuesto' => $datos['id_presupuesto'],
            ':id_productos' => $datos['id_productos'],
            ':cantidad' => $datos['cantidad'],
            ':precio_unitario' => $datos['precio_unitario'] ?? 0,
            ':subtotal' => ($datos['cantidad'] * ($datos['precio_unitario'] ?? 0))
        ]);
        
        $id_detalle = $conexion->lastInsertId();
        
        echo json_encode([
            'success' => true,
            'id_detalle_presupuesto' => $id_detalle
        ]);
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

if (isset($_GET['eliminar']) || isset($_POST['eliminar'])) {
    try {
        $id = $_POST['eliminar'] ?? $_GET['eliminar'];
        
        $sql = "DELETE FROM detalle_presupuesto WHERE id_detalle_presupuesto = :id";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([':id' => $id]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Detalle eliminado correctamente'
        ]);
    } catch (PDOException $e) {
        echo json_encode(array('error' => $e->getMessage()));
    }
}

?>