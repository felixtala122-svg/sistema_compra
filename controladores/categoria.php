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
        "SELECT id_categoria, nombre_categoria, descripcion, estado
           FROM categoria
       ORDER BY id_categoria DESC;"
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
        "INSERT INTO categoria (nombre_categoria, descripcion, estado)
         VALUES (:nombre_categoria, :descripcion, :estado);"
    );
    $params = [
        'nombre_categoria' => $json_datos['nombre_categoria'],
        'descripcion' => !empty($json_datos['descripcion']) ? $json_datos['descripcion'] : null,
        'estado' => $json_datos['estado'],
    ];
    $query->execute($params);
}

function actualizar($lista) {
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "UPDATE categoria
            SET nombre_categoria = :nombre_categoria,
                descripcion = :descripcion,
                estado = :estado
          WHERE id_categoria = :id_categoria;"
    );
    $params = [
        'id_categoria' => $json_datos['id_categoria'],
        'nombre_categoria' => $json_datos['nombre_categoria'],
        'descripcion' => !empty($json_datos['descripcion']) ? $json_datos['descripcion'] : null,
        'estado' => $json_datos['estado'],
    ];
    $query->execute($params);
}

function obtener_por_id($id) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT id_categoria, nombre_categoria, descripcion, estado
           FROM categoria
          WHERE id_categoria = :id
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
        "UPDATE categoria SET estado = 'INACTIVO' WHERE id_categoria = :id;"
    );
    $query->execute(['id' => $id]);
}

function buscar($texto) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT id_categoria, nombre_categoria, descripcion, estado
           FROM categoria
          WHERE CONCAT(nombre_categoria, ' ', COALESCE(descripcion, ''), ' ', estado, ' ', id_categoria) LIKE :texto
       ORDER BY id_categoria DESC
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
        "SELECT id_categoria, nombre_categoria
           FROM categoria
          WHERE estado = 'ACTIVO'
       ORDER BY nombre_categoria;"
    );
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}
?>
