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
                    <span class="factura">Movimientos</span>
                    <?php if ($data['actual']) { ?>
                        <p>Actual</p>
                        <p><?php echo $_SESSION['nombre_usuario']; ?></p>
                    <?php } else { ?>
                        <p>N°: <strong><?php echo $data['idCaja']; ?></strong></p>
                    <?php } ?>
                </div>
            </td>
        </tr>
    </table>
    <table id="container-producto">
        <thead>
            <tr>
                <th>Monto Inicial</th>
                <th>Ingresos</th>
                <th>Egresos</th>
                <th>Saldo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center"><?php echo '$' . $data['movimientos']['inicialDecimal']; ?></td>
                <td class="text-center"><?php echo '$' . $data['movimientos']['ingresosDecimal']; ?></td>
                <td class="text-center"><?php echo '$' . $data['movimientos']['egresosDecimal']; ?></td>
                <td class="text-center"><?php echo '$' . $data['movimientos']['saldoDecimal']; ?></td>
            </tr>
        </tbody>
    </table>
    <div class="mensaje">
    <p><strong>DUDAS</strong> ESCRIBENOS AL CORREO <a href="www.gmail.com">snsdi2023@gmail.com</a></p>
    </div>

</body>

</html>