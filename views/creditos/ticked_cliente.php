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
    <?php date_default_timezone_set('America/El_Salvador');?>
        <p><strong>Fecha:</strong> <?php echo date('d/m/Y'); ?></p>
        <p><strong>DUI:</strong> <?php echo $data['credito']['num_identidad']; ?></p>
        <p><strong>Nombre:</strong> <?php echo $data['credito']['nombre']; ?></p>
        <p><strong>Teléfono: </strong> <?php echo $data['credito']['telefono']; ?></p>
    </div>
    <h5 class="title">Detalle de los Productos</h5>
    <table>
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Cantidad</th>   
            </tr>
        </thead>
        <tbody>
            <?php
            $subTotal = 0;
            
            foreach ($data['productos'] as $detalle) {
                ?>
                    <tr>
                        <td><?php echo $detalle['descripcion']; ?></td>
                        <td><?php echo $detalle['cantidad']; ?></td>
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
                <th>Cuota</th>
                <th>Mora</th>
                <th>Abono</th>
                <th>Cambio</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $abonado = 0;
            $abono=$data['abonos'];
            $i = count($abono)-1;

                ?>
                <tr>
                    <td class="text-center"><?php echo number_format($data['credito']['cuota'],2); ?></td>
                    <td class="text-center"><?php echo number_format($abono[$i]['mora_calculada'],2); ?></td>
                    <td class="text-center"><?php echo number_format($abono[$i]['abono'], 2); ?></td>
                    <td class="text-center"><?php echo number_format($abono[$i]['abono'] -($data['credito']['cuota']+$abono[$i]['mora_calculada']), 2); ?></td>
                </tr>
            <tr class="total">
                <td></td>
                <td></td>
                <td class="text-right">Abonado</td>
                <td class="text-right"><?php echo number_format($data['credito']['total_abonado'], 2); ?></td>
            </tr>
            <tr class="total">
                <td></td>
                <td></td>
                <td class="text-right">Restante</td>
                <td class="text-right"><?php echo number_format($data['credito']['total_restante'],2); ?></td>
            </tr>
        </tbody>
    </table>
    <div class="mensaje">
        <p><strong>DUDAS</strong> ESCRIBENOS AL CORREO <a href="www.gmail.com">snsdi2023@gmail.com</a></p>
        <?php if ($data['credito']['estado'] == 'Inactivo') { ?>
            <h1>CREDITO COMPLETADO</h1>
        <?php }else{ ?>
            <h1>CREDITO PENDIENTE</h1>
        <?php } ?>
    </div>

</body>

</html>