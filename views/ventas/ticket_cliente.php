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
    <h5 class="title">Datos del Cliente</h5>
    <div class="datos-info">
        <p><strong>DUI:</strong> <?php echo $data['venta']['num_identidad']; ?></p>
        <p><strong>Nombre:</strong> <?php echo $data['venta']['nombre']; ?></p>
        <p><strong>Teléfono: </strong> <?php echo $data['venta']['telefono']; ?></p>
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
            $subTotal = 0;
            
            foreach ($data['detalle_venta'] as $detalle) {
                ?>
                    <tr>
                        <td><?php echo $detalle['cantidad']; ?></td>
                        <td><?php echo $detalle['descripcion']; ?></td>
                        <td><?php echo number_format($detalle['precio'], 2); ?></td>
                        <td><?php echo number_format($detalle['total'], 2); ?></td>
                    </tr>
                <?php
                $subTotal += $detalle['total'];
            }
            
            ?>
            <tr>
                <td class="text-right" colspan="3">Descuento %</td>
                <td class="text-right"><?php echo number_format($data['venta']['descuento'], 2); ?></td>
            </tr>
            <tr>
                <td class="text-right" colspan="3">Total con descuento $</td>
                <td class="text-right"><?php echo number_format($data['venta']['total'], 2); ?></td>
            </tr>
            <tr>
                <td class="text-right" colspan="3">Total sin descuento $</td>
                <td class="text-right"><?php echo number_format($data['venta']['total'] + ($data['venta']['total'] * ($data['venta']['descuento']/100)), 2); ?></td>
            </tr>

            <tr>
                <td class="text-right" colspan="3">Pago con $</td>
                <td class="text-right"><?php echo number_format($data['venta']['pago'], 2); ?></td>
            </tr>
            <tr>
                <td class="text-right" colspan="3">Cambio $</td>
                <td class="text-right"><?php echo number_format($data['venta']['pago'] - $data['venta']['total'], 2); ?></td>
            </tr>

        </tbody>
    </table>
    <div class="mensaje">
        <h4><strong>METODO: <?php echo $data['venta']['metodo'] ?>
        <p><strong>DUDAS</strong> ESCRIBENOS AL CORREO <a href="www.gmail.com">snsdi2023@gmail.com</a></p>
        <?php if ($data['venta']['estado'] == 0) { ?>
            <h1>Venta Pendiente</h1>
        <?php }else if ($data['venta']['estado'] == 1) { ?>
            <h1>Venta Completada</h1>
        <?php }else{ ?>
            <h1>Producto Apartado</h1>
        <?php } ?>
    </div>

</body>

</html>