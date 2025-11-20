<?php
require_once '../../../conexion/db.php';

$id = isset($_GET['id']) ? $_GET['id'] : 0;
$db = new DB();
$detalle = $db->conectar()->prepare(
    "SELECT do.id_detalle_orden, do.cantidad, p.nombre_producto, p.precio
     FROM detalle_orden do
     LEFT JOIN productos p ON do.id_productos = p.id_productos
     WHERE do.orden_compra = :id"
);
$detalle->execute(['id' => $id]);

?>
<html>
<head>
    <meta charset="utf-8" />
    <title>Orden #<?= htmlspecialchars($id) ?></title>
    <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h3>Orden de Compra #<?= htmlspecialchars($id) ?></h3>
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Precio Unitario</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            while ($fila = $detalle->fetch(PDO::FETCH_ASSOC)) {
                $subtotal = (is_numeric($fila['cantidad']) && is_numeric($fila['precio'])) ? ($fila['cantidad'] * $fila['precio']) : 0;
                $total += $subtotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($fila['nombre_producto'] ?? 'N/A') ?></td>
                    <td class="text-center"><?= number_format($fila['cantidad'], 2, ',', '.') ?></td>
                    <td class="text-right">$<?= number_format($fila['precio'], 2, ',', '.') ?></td>
                    <td class="text-right">$<?= number_format($subtotal, 2, ',', '.') ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end"><strong>Total</strong></td>
                <td class="text-right">$<?= number_format($total, 2, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>
</div>
</body>
</html>