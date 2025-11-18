<?php
require_once '../../../conexion/db.php';

$base_datos = new DB();
$query = $base_datos->conectar()->prepare("
    SELECT 
        pc.pedido_compra as nro,
        pc.fecha_compra,
        u.nombre_usuario,
        pc.estado 
    FROM pedido_compra pc
    LEFT JOIN usuarios u ON u.id_usuario = pc.id_usuario 
    WHERE pc.pedido_compra = :id
");

$detalle = $base_datos->conectar()->prepare("
    SELECT 
        p.nombre_producto,
        tp.nombre_tipo as tipo_producto,
        dp.cantidad,
        p.precio
    FROM detalle_pedido dp
    LEFT JOIN productos p ON p.id_productos = dp.id_productos
    LEFT JOIN tipo_producto tp ON tp.id_tipo_producto = p.id_tipo_producto
    WHERE dp.pedido_compra = :id
");

$query->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$detalle->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$query->execute();
$detalle->execute();
$arreglo = $query->fetch(PDO::FETCH_OBJ);
?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Pedido de Compra</title>
        <link href="../../../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
        <style>
            @media print {
                .no-print {
                    display: none;
                }
                .print-only {
                    display: block;
                }
                body {
                    font-size: 11pt;
                    margin: 0;
                }
                .container {
                    width: 100%;
                    margin: 0;
                    padding: 0;
                }
            }
            body {
                margin: 20px;
                font-family: Arial, sans-serif;
                background-color: #fff;
            }
            .header {
                text-align: center;
                margin-bottom: 20px;
                border-bottom: 2px solid #333;
                padding-bottom: 10px;
            }
            .header h2 {
                margin: 5px 0;
                font-weight: bold;
            }
            .header h4 {
                margin: 5px 0;
                color: #666;
            }
            .info-section {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr 1fr;
                gap: 15px;
                margin: 20px 0;
                font-size: 13px;
            }
            .info-item {
                border: 1px solid #ddd;
                padding: 8px;
                background-color: #f9f9f9;
            }
            .info-item label {
                font-weight: bold;
                display: block;
                margin-bottom: 3px;
            }
            .info-item span {
                display: block;
            }
            .table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                font-size: 12px;
            }
            .table th {
                background-color: #2c3e50;
                color: white;
                padding: 10px;
                text-align: left;
                border: 1px solid #333;
            }
            .table td {
                padding: 8px;
                border: 1px solid #ddd;
            }
            .table tbody tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .table tbody tr:hover {
                background-color: #f0f0f0;
            }
            .text-center {
                text-align: center;
            }
            .text-right {
                text-align: right;
            }
            .footer {
                margin-top: 30px;
                text-align: center;
                border-top: 1px solid #ddd;
                padding-top: 10px;
                font-size: 11px;
                color: #666;
            }
            .no-print {
                margin: 20px 0;
                text-align: center;
            }
            .btn {
                padding: 8px 16px;
                background-color: #2c3e50;
                color: white;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 12px;
            }
            .btn:hover {
                background-color: #1a2332;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>PEDIDO DE COMPRA</h2>
                <h4>Sistema de Gestión de Compras</h4>
            </div>

            <div class="info-section">
                <div class="info-item">
                    <label>Nro de Pedido:</label>
                    <span><?= htmlspecialchars($arreglo->nro ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <label>Fecha:</label>
                    <span><?= htmlspecialchars($arreglo->fecha_compra ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <label>Usuario:</label>
                    <span><?= htmlspecialchars($arreglo->nombre_usuario ?? 'N/A') ?></span>
                </div>
                <div class="info-item">
                    <label>Estado:</label>
                    <span>
                        <strong style="color: <?= ($arreglo->estado === 'ACTIVO') ? 'green' : 'red' ?>">
                            <?= htmlspecialchars($arreglo->estado ?? 'N/A') ?>
                        </strong>
                    </span>
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Tipo de Producto</th>
                        <th class="text-center">Cantidad</th>
                        <th class="text-right">Precio Unitario</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    if ($detalle->rowCount()) {
                        foreach ($detalle as $fila) {
                            $subtotal = (is_numeric($fila['cantidad']) && is_numeric($fila['precio'])) 
                                ? ($fila['cantidad'] * $fila['precio']) 
                                : 0;
                            $total += $subtotal;
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($fila['nombre_producto'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($fila['tipo_producto'] ?? 'N/A') ?></td>
                                <td class="text-center"><?= number_format($fila['cantidad'], 2, ',', '.') ?></td>
                                <td class="text-right">$<?= number_format($fila['precio'], 2, ',', '.') ?></td>
                                <td class="text-right">$<?= number_format($subtotal, 2, ',', '.') ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="5" class="text-center">No hay detalles para este pedido</td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>

            <div class="text-right" style="margin-top: 20px; margin-right: 10px;">
                <h4>TOTAL: $<?= number_format($total, 2, ',', '.') ?></h4>
            </div>

            <div class="footer">
                <p>Este documento fue generado automáticamente por el Sistema de Gestión de Compras</p>
                <p>Fecha de impresión: <?= date('d/m/Y H:i:s') ?></p>
            </div>
        </div>

        <div class="no-print">
            <button class="btn" onclick="window.print()">Imprimir</button>
            <button class="btn" onclick="window.close()">Cerrar</button>
        </div>

        <script>
            // Auto-print cuando se abre la página
            window.onload = function() {
                // Uncomment para auto-imprimir al cargar
                // window.print();
            };
        </script>
    </body>
</html>
