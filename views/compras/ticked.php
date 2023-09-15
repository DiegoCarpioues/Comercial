<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL . 'assets/css/ticked.css'; ?>">
</head>

<body>
    <img src="<?php echo BASE_URL . 'assets/images/logo.png'; ?>" alt="">
    <div class="datos-empresa">
    <p> </p>
    <p>COMERCIAL</p>
    <p>IMPORTACIONES VARIAS</p>
    <p>Teléfono: 7503-2252</p>
    <p>Dirección: Calle Dr. Matias Delgado</p>
    <p>Frente a plaza 5 de Noviembre</p>
    <p>Cojutepeque Cuscatlan</p>
    <p>CP: 1401 El Salvador</p>
    </div>
    <h5 class="title">Datos del Proveedor</h5>
    <div class="datos-info">
        <p><strong>Ruc: </strong> <?php echo $data['compra']['ruc']; ?></p>
        <p><strong>Nombre: </strong> <?php echo $data['compra']['nombre']; ?></p>
        <p><strong>Teléfono: </strong> <?php echo $data['compra']['telefono']; ?></p>
    </div>
    <h5 class="title">Detalle de los Productos</h5>
    <table>
        <thead>
            <tr>
                <th>Cant</th>
                <th>Descripción</th>
                <th>Precio</th>
                <th>SubTotal</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $productos = json_decode($data['compra']['productos'], true);
            foreach ($productos as $producto) { ?>
                <tr>
                    <td><?php echo $producto['cantidad']; ?></td>
                    <td><?php echo $producto['nombre']; ?></td>
                    <td><?php echo number_format($producto['precio'], 2); ?></td>
                    <td><?php echo number_format($producto['cantidad'] * $producto['precio'], 2); ?></td>
                </tr>
            <?php } ?>
            <tr>
                <td class="text-right" colspan="3">Total</td>
                <td class="text-right"><?php echo number_format($data['compra']['total'], 2); ?></td>
            </tr>
        </tbody>
    </table>
    <div class="mensaje">
    <p><strong>DUDAS</strong> ESCRIBENOS AL CORREO <a href="www.gmail.com">snsdi2023@gmail.com</a></p>
        <?php if ($data['compra']['estado'] == 0) { ?>
            <h1>Compra Anulado</h1>
        <?php } ?>
    </div>

</body>

</html>