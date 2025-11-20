<?php
require_once("../../conexion/db.php");

$db = new DB();
$conexion = $db->conectar();

$id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($id == 0) {
    echo "No se especificó un presupuesto";
    exit;
}

// Obtener datos del presupuesto
$sql = "SELECT 
        p.id_presupuesto, 
        p.fecha, 
        p.estado,
        u.nombre AS nombre_usuario,
        pr.nombre AS nombre_proveedor,
        p.id_orden_compra
    FROM presupuesto p
    JOIN usuarios u ON p.id_usuario = u.id_usuarios
    JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
    WHERE p.id_presupuesto = :id";

$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id]);
$presupuesto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$presupuesto) {
    echo "Presupuesto no encontrado";
    exit;
}

// Obtener detalles
$sql = "SELECT 
        dp.id_productos,
        p.nombre AS nombre_producto,
        dp.cantidad,
        dp.precio_unitario,
        dp.subtotal
    FROM detalle_presupuesto dp
    JOIN productos p ON dp.id_productos = p.id_productos
    WHERE dp.id_presupuesto = :id";

$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular total
$total = 0;
foreach ($detalles as $detalle) {
    $total += $detalle['subtotal'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Presupuesto #<?php echo $presupuesto['id_presupuesto']; ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .info-row { margin: 10px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PRESUPUESTO #<?php echo $presupuesto['id_presupuesto']; ?></h2>
    </div>

    <div class="info-row">
        <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($presupuesto['fecha'])); ?>
    </div>
    <div class="info-row">
        <strong>Usuario:</strong> <?php echo $presupuesto['nombre_usuario']; ?>
    </div>
    <div class="info-row">
        <strong>Proveedor:</strong> <?php echo $presupuesto['nombre_proveedor']; ?>
    </div>
    <div class="info-row">
        <strong>Estado:</strong> <?php echo $presupuesto['estado']; ?>
    </div>
    <?php if ($presupuesto['id_orden_compra']): ?>
    <div class="info-row">
        <strong>Orden de Compra:</strong> <?php echo $presupuesto['id_orden_compra']; ?>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalles as $detalle): ?>
            <tr>
                <td><?php echo $detalle['nombre_producto']; ?></td>
                <td><?php echo $detalle['cantidad']; ?></td>
                <td><?php echo number_format($detalle['precio_unitario'], 2); ?></td>
                <td><?php echo number_format($detalle['subtotal'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3">TOTAL</td>
                <td><?php echo number_format($total, 2); ?></td>
            </tr>
        </tbody>
    </table>

    <script>
        window.print();
    </script>
</body>
</html>