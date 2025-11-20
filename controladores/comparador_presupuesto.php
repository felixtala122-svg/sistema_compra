<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../conexion/db.php';

if (isset($_POST['listar'])) {
    listar();
}

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

if (isset($_POST['anular'])) {
    anular($_POST['anular']);
}

if (isset($_POST['id'])) {
    obtener_por_id($_POST['id']);
}

if (isset($_POST['buscar'])) {
    buscar($_POST['buscar']);
}

if (isset($_POST['obtener_detalles'])) {
    obtener_detalles($_POST['obtener_detalles']);
}

function listar() {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT cp.comparador_presupuesto, cp.fecha_comparacion, cp.estado, cp.id_usuario, u.nombre_usuario
           FROM comparador_presupuesto cp
           LEFT JOIN usuarios u ON cp.id_usuario = u.id_usuario
       ORDER BY cp.comparador_presupuesto DESC;"
    );
    $query->execute();
    if ($query->rowCount()) {
        echo json_encode($query->fetchAll(PDO::FETCH_OBJ));
    } else {
        echo '0';
    }
}

function guardar($lista) {
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $conexion = $base_datos->conectar();
    
    try {
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $query = $conexion->prepare(
            "INSERT INTO comparador_presupuesto (fecha_comparacion, estado, id_usuario)
             VALUES (:fecha_comparacion, :estado, :id_usuario);"
        );
        $params = [
            'fecha_comparacion' => !empty($json_datos['fecha_comparacion']) ? $json_datos['fecha_comparacion'] : date('Y-m-d'),
            'estado' => 'ACTIVO',
            'id_usuario' => !empty($json_datos['id_usuario']) ? $json_datos['id_usuario'] : 1,
        ];
        
        if (!$query->execute($params)) {
            echo json_encode(['error' => 'No se pudo insertar el comparador']);
            return;
        }
        
        $id_comparador = $conexion->lastInsertId();
        if (!$id_comparador) {
            echo json_encode(['error' => 'No se generó ID para el comparador']);
            return;
        }
        
        echo json_encode(['success' => 'Comparador guardado correctamente', 'id_comparador' => $id_comparador]);
        
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error PDO: ' . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
}

function anular($id) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "UPDATE comparador_presupuesto SET estado = 'ANULADO' WHERE comparador_presupuesto = :id;"
    );
    $query->execute(['id' => $id]);
}

function obtener_por_id($id) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT cp.comparador_presupuesto, cp.fecha_comparacion, cp.estado, cp.id_usuario, u.nombre_usuario
           FROM comparador_presupuesto cp
           LEFT JOIN usuarios u ON cp.id_usuario = u.id_usuario
          WHERE cp.comparador_presupuesto = :id
          LIMIT 1;"
    );
    $query->execute(['id' => $id]);
    if ($query->rowCount()) {
        echo json_encode($query->fetch(PDO::FETCH_OBJ));
    } else {
        echo '0';
    }
}

function buscar($texto) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT cp.comparador_presupuesto, cp.fecha_comparacion, cp.estado, cp.id_usuario, u.nombre_usuario
           FROM comparador_presupuesto cp
           LEFT JOIN usuarios u ON cp.id_usuario = u.id_usuario
          WHERE CONCAT(cp.comparador_presupuesto, ' ', cp.fecha_comparacion, ' ', cp.estado, ' ', u.nombre_usuario) LIKE :texto
       ORDER BY cp.comparador_presupuesto DESC
          LIMIT 50;"
    );
    $query->execute(['texto' => "%$texto%"]);
    if ($query->rowCount()) {
        echo json_encode($query->fetchAll(PDO::FETCH_OBJ));
    } else {
        echo '0';
    }
}

function obtener_detalles($id_comparador) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT dc.id_detalle_comparador, dc.cantidad, dc.comparador_presupuesto, dc.id_productos, p.nombre_producto, p.precio
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

?>
