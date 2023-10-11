<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL . 'assets/css/factura.css'; ?>">
</head>

<body>
    <table id="datos-empresa">
        <tr>
            <td class="logo">
                <img src="<?php echo BASE_URL . 'assets/images/logo.png'; ?>" alt="">
            </td>
            <td class="info-empresa">
                 <p>COMERCIAL</p>
                <p>IMPORTACIONES VARIAS</p>
                <p>Teléfono: 7503-2252</p>
                <p>Dirección: Calle Dr. Matias Delgado Frente a plaza 5 de Noviembre Cojutepeque Cuscatlan</p>
                <p>CP: 1401 El Salvador</p>
            </td>
            <td class="info-compra">
                <div class="container-factura">
                    <span class="factura">Credito</span>
                    <?php date_default_timezone_set('America/El_Salvador');?>
                    <p>N°: <strong><?php echo $data['credito']['id_credito']; ?></strong></p>
                    <p>Fecha: <?php echo date('d/m/Y'); ?></p>
                    <p>Hora: <?php echo date('H:i:s'); ?></p>
                </div>
            </td>
        </tr>
    </table>


    <h5 class="title">Datos del Cliente</h5>
    <table id="container-info">
        <tr>
            <td>
                <strong>DUI: </strong>
                <p><?php echo $data['credito']['num_identidad'] ?></p>
            </td>
            <td>
                <strong>Nombre: </strong>
                <p><?php echo $data['credito']['nombre'] ?></p>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Teléfono: </strong>
                <p><?php echo $data['credito']['telefono'] ?></p>
            </td>
            <td>
                <strong>Dirección: </strong>
                <p><?php echo $data['credito']['direccion'] ?></p>
            </td>
        </tr>
    </table>

    <h5 class="title">Detalle de los Productos</h5>
    <table id="container-producto">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $subTotal = 0;

            foreach ($data['productos'] as $producto) {
                ?>
                    <tr>
                        <td class="text-center"><?php echo $producto['descripcion']; ?></td>
                        <td class="text-center"><?php echo $producto['cantidad']; ?></td>
                    </tr>
                <?php

            }
            ?>
           
        </tbody>

    </table>

    <h5 class="title">Detalle de los Abonos</h5>
    <table id="container-producto">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Num Cuota</th>
                <th>Cuota</th>
                <th>Mora</th>
                <th>Abono</th>
                <th>Cambio</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $abonado = 0;
            foreach ($data['abonos'] as $abono) {
                $abonado += $abono['abono'];
                ?>
                <tr>
                <td class="text-center"><?php echo date('d/m/Y', strtotime($abono['fecha'])); ?></td>
                    <td class="text-center"><?php echo number_format($abono['numero']); ?></td>
                    <td class="text-center"><?php echo number_format($data['credito']['cuota'],2); ?></td>
                    <td class="text-center"><?php echo number_format($abono['mora_calculada'],2); ?></td>
                    <td class="text-center"><?php echo number_format($abono['abono'], 2); ?></td>
                    <td class="text-center"><?php echo number_format($abono['abono'] -$data['credito']['cuota'], 2); ?></td>
                </tr>
            <?php } ?>
            <tr class="total">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">Abonado</td>
                <td class="text-right"><?php echo number_format($data['credito']['total_abonado'], 2); ?></td>
            </tr>
            <tr class="total">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">Restante</td>
                <td class="text-right"><?php echo number_format($data['credito']['total_restante'],2); ?></td>
            </tr>
        </tbody>
    </table>


    <div class="mensaje">
        <?php if ($data['credito']['estado'] == "Inactivo") { ?>
            <h1>CREDITO FINALIZADO</h1>
        <?php } else { ?>
            <h1>CREDITO PENDIENTE</h1>
        <?php } ?>
    </div>

</body>

</html>