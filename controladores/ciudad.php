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
        "SELECT id_ciudad, nombre, departamento, pais, direccion, estado
           FROM ciudad
       ORDER BY id_ciudad DESC;"
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
        "INSERT INTO ciudad (nombre, departamento, pais, direccion, estado)
         VALUES (:nombre, :departamento, :pais, :direccion, :estado);"
    );
    $params = [
        'nombre' => $json_datos['nombre'],
        'departamento' => !empty($json_datos['departamento']) ? $json_datos['departamento'] : null,
        'pais' => !empty($json_datos['pais']) ? $json_datos['pais'] : null,
        'direccion' => !empty($json_datos['direccion']) ? $json_datos['direccion'] : null,
        'estado' => $json_datos['estado'],
    ];
    $query->execute($params);
}

function actualizar($lista) {
    $json_datos = json_decode($lista, true);
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "UPDATE ciudad
            SET nombre = :nombre,
                departamento = :departamento,
                pais = :pais,
                direccion = :direccion,
                estado = :estado
          WHERE id_ciudad = :id_ciudad;"
    );
    $params = [
        'id_ciudad' => $json_datos['id_ciudad'],
        'nombre' => $json_datos['nombre'],
        'departamento' => !empty($json_datos['departamento']) ? $json_datos['departamento'] : null,
        'pais' => !empty($json_datos['pais']) ? $json_datos['pais'] : null,
        'direccion' => !empty($json_datos['direccion']) ? $json_datos['direccion'] : null,
        'estado' => $json_datos['estado'],
    ];
    $query->execute($params);
}

function obtener_por_id($id) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT id_ciudad, nombre, departamento, pais, direccion, estado
           FROM ciudad
          WHERE id_ciudad = :id
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
        "UPDATE ciudad SET estado = 'INACTIVO' WHERE id_ciudad = :id;"
    );
    $query->execute(['id' => $id]);
}

function buscar($texto) {
    $base_datos = new DB();
    $query = $base_datos->conectar()->prepare(
        "SELECT id_ciudad, nombre, departamento, pais, direccion, estado
           FROM ciudad
          WHERE CONCAT(nombre, ' ', COALESCE(departamento, ''), ' ', COALESCE(pais, ''), ' ', COALESCE(direccion, ''), ' ', estado, ' ', id_ciudad) LIKE :texto
       ORDER BY id_ciudad DESC
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
        "SELECT id_ciudad, nombre
           FROM ciudad
          WHERE estado = 'ACTIVO'
       ORDER BY nombre;"
    );
    $query->execute();
    if ($query->rowCount()) {
        print_r(json_encode($query->fetchAll(PDO::FETCH_OBJ)));
    } else {
        echo '0';
    }
}
?>
