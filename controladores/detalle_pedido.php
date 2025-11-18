<?php
require_once '../conexion/db.php';

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

if (isset($_POST['obtener_detalles'])) {
    obtener_detalles($_POST['obtener_detalles']);
}

if (isset($_POST['eliminar'])) {
    eliminar($_POST['eliminar']);
}

function guardar($lista) {
    // Crear un arreglo del texto que se le pasa
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    
    try {
        $conexion = $base_datos->conectar();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $query = $conexion->prepare(
            "INSERT INTO detalle_pedido (cantidad, pedido_compra, id_productos)
             VALUES (:cantidad, :pedido_compra, :id_productos);"
        );
        
        $resultado = $query->execute([
            'cantidad' => $json_datos['cantidad'],
            'pedido_compra' => $json_datos['pedido_compra'],
            'id_productos' => $json_datos['id_productos'],
        ]);
        
        if ($resultado) {
            echo json_encode(['success' => 'Detalle guardado correctamente']);
        } else {
            echo json_encode(['error' => 'Error al insertar el detalle']);
        }
        
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error PDO: ' . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
}

function obtener_detalles($id_pedido) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT dp.id_detalle_pedido, dp.cantidad, dp.pedido_compra, dp.id_productos, p.nombre_producto, p.precio
         FROM detalle_pedido dp
         LEFT JOIN productos p ON dp.id_productos = p.id_productos
         WHERE dp.pedido_compra = :id_pedido;"
    );
    $query->execute(['id_pedido' => $id_pedido]);
    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

function eliminar($id_detalle) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "DELETE FROM detalle_pedido WHERE id_detalle_pedido = :id;"
    );
    $query->execute(['id' => $id_detalle]);
    echo json_encode(['success' => 'Detalle eliminado correctamente']);
}
?>
