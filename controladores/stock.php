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
        "SELECT id_stock, cantidad_actual, cantidad_minima, id_productos, estado
           FROM stock
       ORDER BY id_stock DESC;"
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
        "INSERT INTO stock (cantidad_actual, cantidad_minima, id_productos, estado)
         VALUES (:cantidad_actual, :cantidad_minima, :id_productos, :estado);"
    );
    $params = [
        'cantidad_actual' => !empty($json_datos['cantidad_actual']) ? $json_datos['cantidad_actual'] : 0,
        'cantidad_minima' => !empty($json_datos['cantidad_minima']) ? $json_datos['cantidad_minima'] : 0,
        'id_productos' => !empty($json_datos['id_productos']) ? $json_datos['id_productos'] : 0,
        'estado' => $json_datos['estado'],
    ];
    $query->execute($params);
}

function actualizar($lista) {
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "UPDATE stock
            SET cantidad_actual = :cantidad_actual,
                cantidad_minima = :cantidad_minima,
                id_productos = :id_productos,
                estado = :estado
          WHERE id_stock = :id_stock;"
    );
    $params = [
        'id_stock' => $json_datos['id_stock'],
        'cantidad_actual' => !empty($json_datos['cantidad_actual']) ? $json_datos['cantidad_actual'] : 0,
        'cantidad_minima' => !empty($json_datos['cantidad_minima']) ? $json_datos['cantidad_minima'] : 0,
        'id_productos' => !empty($json_datos['id_productos']) ? $json_datos['id_productos'] : 0,
        'estado' => $json_datos['estado'],
    ];
    $query->execute($params);
}

function obtener_por_id($id) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT id_stock, cantidad_actual, cantidad_minima, id_productos, estado
           FROM stock
          WHERE id_stock = :id
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
        "UPDATE stock SET estado = 'INACTIVO' WHERE id_stock = :id;"
    );
    $query->execute(['id' => $id]);
}

function buscar($texto) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT id_stock, cantidad_actual, cantidad_minima, id_productos, estado
           FROM stock
          WHERE CONCAT(id_productos, ' ', estado, ' ', id_stock) LIKE :texto
       ORDER BY id_stock DESC
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
        "SELECT id_stock, id_productos
           FROM stock
          WHERE estado = 'ACTIVO'
       ORDER BY id_stock;"
    );
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}
?>
