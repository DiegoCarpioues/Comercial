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
                    <span class="factura">Factura</span>
                    <p>N°: <strong><?php echo $data['venta']['serie']; ?></strong></p>
                    <p>Fecha: <?php echo date('d/m/Y', strtotime($data['venta']['fecha'])); ?></p>
                    <p>Hora: <?php echo $data['venta']['hora']; ?></p>
                </div>
            </td>
        </tr>
    </table>


    <h5 class="title">Datos del Cliente</h5>
    <table id="container-info">
        <tr>
            <td>
            <strong>DUI: </strong>
                <p><?php echo $data['venta']['num_identidad'] ?></p>
            </td>
            <td>
                <strong>Nombre: </strong>
                <p><?php echo $data['venta']['nombre'] ?></p>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Teléfono: </strong>
                <p><?php echo $data['venta']['telefono'] ?></p>
            </td>
            <td>
                <strong>Dirección: </strong>
                <p><?php echo $data['venta']['direccion'] ?></p>
            </td>
        </tr>
    </table>
    <h5 class="title">Detalle de los Productos</h5>
    <table id="container-producto">
        <thead>
            <tr>
                <th>Cantidad</th>
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

            $igv = $subTotal * 0.13;
            $total = $subTotal + $igv;
            $totalCD = $total - $data['venta']['descuento'];
            $totalSD = $total;
            ?>
            <tr class="total">
                <td class="text-right" colspan="3">SubTotal</td>
                <td class="text-right"><?php echo number_format($subTotal, 2); ?></td>
            </tr>
            <tr class="total">
                <td class="text-right" colspan="3">IVA 13%</td>
                <td class="text-right"><?php echo number_format($igv, 2); ?></td>
            </tr>
            <tr class="total">
                <td class="text-right" colspan="3">Total con Descuento</td>
                <td class="text-right"><?php echo number_format($totalCD, 2); ?></td>
            </tr>
            <tr class="total">
                <td class="text-right" colspan="3">Total sin Descuento</td>
                <td class="text-right"><?php echo number_format($totalSD, 2); ?></td>
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