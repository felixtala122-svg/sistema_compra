<?php
require_once '../conexion/db.php';

if (isset($_POST['listar'])) {
    listar();
}

if (isset($_POST['guardar'])) {
    guardar($_POST['guardar']);
}

if (isset($_POST['actualizar'])) {
    actualizar($_POST['actualizar']);
}

if (isset($_POST['id'])) {
    obtener_por_id($_POST['id']);
}

if (isset($_POST['eliminar'])) {
    eliminar($_POST['eliminar']);
}

if (isset($_POST['buscar'])) {
    buscar($_POST['buscar']);
}

if (isset($_POST['leer_activos'])) {
    leer_activos();
}

function listar() {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT id_tipo_producto, nombre_tipo, descripcion, estado
           FROM tipo_producto
       ORDER BY id_tipo_producto DESC;"
    );
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

function guardar($lista) {
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "INSERT INTO tipo_producto (nombre_tipo, descripcion, estado)
         VALUES (:nombre_tipo, :descripcion, :estado);"
    );
    $params = [
        'nombre_tipo' => $json_datos['nombre_tipo'],
        'descripcion' => !empty($json_datos['descripcion']) ? $json_datos['descripcion'] : null,
        'estado' => $json_datos['estado'],
    ];
    $query->execute($params);
}

function actualizar($lista) {
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "UPDATE tipo_producto
            SET nombre_tipo = :nombre_tipo,
                descripcion = :descripcion,
                estado = :estado
          WHERE id_tipo_producto = :id_tipo_producto;"
    );
    $params = [
        'id_tipo_producto' => $json_datos['id_tipo_producto'],
        'nombre_tipo' => $json_datos['nombre_tipo'],
        'descripcion' => !empty($json_datos['descripcion']) ? $json_datos['descripcion'] : null,
        'estado' => $json_datos['estado'],
    ];
    $query->execute($params);
}

function obtener_por_id($id) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT id_tipo_producto, nombre_tipo, descripcion, estado
           FROM tipo_producto
          WHERE id_tipo_producto = :id
          LIMIT 1;"
    );
    $query->execute(['id' => $id]);
    if ($query->rowCount()) {
        print_r(json_encode($query->fetch(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

function eliminar($id) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "UPDATE tipo_producto SET estado = 'INACTIVO' WHERE id_tipo_producto = :id;"
    );
    $query->execute(['id' => $id]);
}

function buscar($texto) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT id_tipo_producto, nombre_tipo, descripcion, estado
           FROM tipo_producto
          WHERE CONCAT(nombre_tipo, ' ', COALESCE(descripcion, ''), ' ', estado, ' ', id_tipo_producto) LIKE :texto
       ORDER BY id_tipo_producto DESC
          LIMIT 50;"
    );
    $query->execute(['texto' => "%$texto%"]);
    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}

function leer_activos() {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT id_tipo_producto, nombre_tipo
           FROM tipo_producto
          WHERE estado = 'ACTIVO'
       ORDER BY nombre_tipo;"
    );
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}
?>
