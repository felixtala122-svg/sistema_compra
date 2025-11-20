<?php
header('Content-Type: application/json; charset=utf-8');
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
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    
    try {
        $conexion = $base_datos->conectar();
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $query = $conexion->prepare(
            "INSERT INTO detalle_comparador (cantidad, comparador_presupuesto, id_productos)
             VALUES (:cantidad, :comparador_presupuesto, :id_productos);"
        );
        
        $resultado = $query->execute([
            'cantidad' => $json_datos['cantidad'],
            'comparador_presupuesto' => $json_datos['comparador_presupuesto'],
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

function obtener_detalles($id_comparador) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT dc.id_detalle_comparador, dc.cantidad, dc.comparador_presupuesto, dc.id_productos, 
                p.nombre_producto, p.precio
         FROM detalle_comparador dc
         LEFT JOIN productos p ON dc.id_productos = p.id_productos
         WHERE dc.comparador_presupuesto = :id_comparador;"
    );
    $query->execute(['id_comparador' => $id_comparador]);
    if ($query->rowCount()) {
        echo json_encode($query->fetchAll(PDO::FETCH_OBJ));
    } else {
        echo '0';
    }
}

function eliminar($id_detalle) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "DELETE FROM detalle_comparador WHERE id_detalle_comparador = :id;"
    );
    $query->execute(['id' => $id_detalle]);
    echo json_encode(['success' => 'Detalle eliminado correctamente']);
}
?>
