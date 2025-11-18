<?php
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

function guardar($lista) {
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $conexion = $base_datos->conectar();
    
    try {
        // Habilitar excepciones en PDO
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Insertar pedido_compra
        $query = $conexion->prepare(
            "INSERT INTO pedido_compra (fecha_compra, estado, id_usuario)
             VALUES (:fecha_compra, :estado, :id_usuario);"
        );
        $params = [
            'fecha_compra' => !empty($json_datos['fecha_compra']) ? $json_datos['fecha_compra'] : date('Y-m-d'),
            'estado' => 'ACTIVO',
            'id_usuario' => !empty($json_datos['id_usuario']) ? $json_datos['id_usuario'] : 1,
        ];
        
        if (!$query->execute($params)) {
            echo json_encode(['error' => 'No se pudo insertar el pedido']);
            return;
        }
        
        // Obtener el ID del pedido insertado
        $id_pedido = $conexion->lastInsertId();
        
        if (!$id_pedido) {
            echo json_encode(['error' => 'No se generó ID para el pedido']);
            return;
        }
        
        echo json_encode(['success' => 'Pedido guardado correctamente', 'id_pedido' => $id_pedido]);
        
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error PDO: ' . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
}

function listar() {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT pc.pedido_compra, pc.fecha_compra, pc.estado, pc.id_usuario, u.nombre_usuario
           FROM pedido_compra pc
           LEFT JOIN usuarios u ON pc.id_usuario = u.id_usuario
       ORDER BY pc.pedido_compra DESC;"
    );
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

function anular($id) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "UPDATE pedido_compra SET estado = 'ANULADO' WHERE pedido_compra = :id;"
    );
    $query->execute(['id' => $id]);
}

function obtener_por_id($id) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT pc.pedido_compra, pc.fecha_compra, pc.estado, pc.id_usuario, u.nombre_usuario
           FROM pedido_compra pc
           LEFT JOIN usuarios u ON pc.id_usuario = u.id_usuario
          WHERE pc.pedido_compra = :id
          LIMIT 1;"
    );
    $query->execute(['id' => $id]);
    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

function buscar($texto) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT pc.pedido_compra, pc.fecha_compra, pc.estado, pc.id_usuario, u.nombre_usuario
           FROM pedido_compra pc
           LEFT JOIN usuarios u ON pc.id_usuario = u.id_usuario
          WHERE CONCAT(pc.pedido_compra, ' ', pc.fecha_compra, ' ', pc.estado, ' ', u.nombre_usuario) LIKE :texto
       ORDER BY pc.pedido_compra DESC
          LIMIT 50;"
    );
    $query->execute(['texto' => "%$texto%"]);
    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

function obtener_detalles($id_pedido) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT dp.id_detalle_pedido, dp.cantidad, dp.id_productos, pr.nombre_producto, pr.precio
           FROM detalle_pedido dp
           LEFT JOIN productos pr ON dp.id_productos = pr.id_productos
          WHERE dp.pedido_compra = :id_pedido;"
    );
    $query->execute(['id_pedido' => $id_pedido]);
    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}
?>
