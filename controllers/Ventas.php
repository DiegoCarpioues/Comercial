<?php
require 'vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

use Dompdf\Dompdf;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Ventas extends Controller
{
    private $id_usuario;
    public function __construct()
    {
        parent::__construct();
        session_start();
        if (empty($_SESSION['id_usuario'])) {
            header('Location: ' . BASE_URL);
            exit;
        }
        if (!verificar('ventas')){
            header('Location: ' . BASE_URL . 'admin/permisos');
            exit;
        }
        $this->id_usuario = $_SESSION['id_usuario'];
    }
    public function index()
    {
        $data['title'] = 'Ventas';
        $data['script'] = 'ventas.js';
        $data['busqueda'] = 'busqueda.js';
        $data['carrito'] = 'posVenta';
        $resultSerie = $this->model->getSerie();
        $serie = ($resultSerie['total'] == null) ? 1 : $resultSerie['total'] + 1;
        $data['serie'] = $this->generate_numbers($serie, 1, 8);
        $this->views->getView('ventas', 'index', $data);
    }

    //buscar Productos por nombre
    public function buscarProdDispVentas()
    {
        $array = array();
        $valor = $_GET['term'];
        $data = $this->model->buscarProdDispVentas($valor);
        foreach ($data as $row) {
            $result['id'] = $row['id'];
            $result['label'] = $row['producto'];
            $result['precio'] = $row['precio'];
            $result['disponibles'] = $row['disponibles'];
            $result['idCompra'] = $row['idCompra'];
            $result['codigo'] = $row['codigo'];
            array_push($array, $result);
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function registrarVenta()
    {
        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);
        //$array['productos'] = array();
        
        if (!empty($datos['productos'])) {
            $fecha = date('Y-m-d');
            $hora = date('H:i:s');
            $metodo = $datos['metodo']; 
            $totalAPagar = $datos['totalAPagar'];
            $prima = $datos['prima'];
            $interesMensual = $datos['interesMensual'];
            $mesesPlazo = $datos['mesesPlazo'];
            $cuotaMensual = $datos['cuotaMensual'];   
            $transaccion = $datos['transaccion'];   
            $apartado = $datos['apartado'];          
            $resultSerie = $this->model->getSerie();
            $numSerie = ($resultSerie['total'] == null) ? 1 : $resultSerie['total'] + 1;

            $serie = $this->generate_numbers($numSerie, 1, 8);
            $descuento = (!empty($datos['descuento'])) ? $datos['descuento'] : 0;
            $pago = (!empty($datos['pago'])) ? $datos['pago'] : 0;
            $idCliente = $datos['idCliente'];
            $idUsuario = $datos['idUsuario'];
            if (empty($idCliente)) {
                $res = array('msg' => 'EL CLIENTE ES REQUERIDO', 'type' => 'warning');
            } else if (empty($idUsuario)) {
                $res = array('msg' => 'EL USUARIO ES REQUERIDO', 'type' => 'warning');
            } else if (empty($metodo)) {
                $res = array('msg' => 'EL METODO ES REQUERIDO', 'type' => 'warning');
            }else if ((empty($pago) || $pago <= 0) && ($metodo != 'CREDITO')) {
                $res = array('msg' => 'EL PAGO ES REQUERIDO', 'type' => 'warning');
            } else {

                    if($metodo == 'CONTADO' && $transaccion == 'VENTA' ){//para el estado
                        $venta = $this->model->registrarVenta($fecha, $hora, $metodo, $descuento, $serie[0], $pago, 1, $idCliente, $this->id_usuario);
                    }else if( $metodo == 'CONTADO' && $transaccion == 'APARTADO' ){
                        $venta = $this->model->registrarVenta($fecha, $hora, $metodo, $descuento, $serie[0], $pago, 2, $idCliente, $this->id_usuario);
                    }else{
                        $venta = $this->model->registrarVenta($fecha, $hora, $metodo, $descuento, $serie[0], 0.00, 0, $idCliente, $this->id_usuario);
                    }
                    
                    if ($venta > 0) {
                        $idVenta = 0;
                        $resultVent = $this->model->ultimaVenta();
                        $s = ($resultVent['id'] == null) ? 1 : $resultVent['id'];
                        $idVenta = $s;

                        foreach ($datos['productos'] as $producto) {
                            $cantidad = $producto['cantidad'];
                            //$idCompra = $producto['idCompra'];
                            $codigoProducto = $producto['codigo'];//no existe id dice
                            
                            /* OBTENGO LA LISTA QUE TRAE EN CADA COMPRA CUANTOS HAY DISPONIBLES SI LA CANTIDAD VENDIDA SUPERA A 
                            LA CANTIDAD DISPONIBLE EN LA COMPRA ENTONCES LA COMPRA CAMBIA DE ESTADO A 0 Y SE PASA A LA SIGUIENTE COMPRA... */
                            $compras = $this->model->obtenerListaCompras($codigoProducto);
                            $cantAVender = $cantidad;
                            //print_r($compras);
                            foreach ($compras as $compra) {
                                $idCompra = $compra['idCompra'];
                                if($cantAVender <= $producto['disponibles'] && $cantAVender > 0){
                                    if($compra['disponibles'] != null && $compra['disponibles'] != "" && $compra['disponibles'] > 0){
                                        $proDispVen = $compra['disponibles'];//producto disponible para la venta de x compra
                                        
                                        if($proDispVen > $cantAVender){
                                            $this->model->actualizarEstadoCompra($idCompra, 1);
                                            $this->model->registraDetalleVenta($idCompra, $idVenta, $cantAVender);
                                            $cantAVender = 0;
                                            break; //salirse
                                        }else if($proDispVen == $cantAVender){
                                            $this->model->actualizarEstadoCompra($idCompra, 0);
                                            $this->model->registraDetalleVenta($idCompra, $idVenta, $cantAVender);
                                            $cantAVender = 0;
                                            break;
                                        }else if($proDispVen <= $cantAVender){
                                            $this->model->actualizarEstadoCompra($idCompra, 0);
                                            $this->model->registraDetalleVenta($idCompra, $idVenta, $proDispVen);
                                            $cantAVender = $cantAVender - $proDispVen;
                                        }
                                    }
                                }
                            }
                        }
                        if ($metodo == 'CONTADO' && $transaccion == 'APARTADO') {
                            $unicaCuota = $totalAPagar - $apartado;
                            $this->model->registrarCredito($idVenta, $apartado, 1, 0, 1, $totalAPagar, $unicaCuota);
                        }else if ($metodo == 'CREDITO') {
                            $totalAPagar = ($mesesPlazo * $cuotaMensual) + $prima;
                            $this->model->registrarCredito($idVenta, $prima, $mesesPlazo, $interesMensual, 1, $totalAPagar, $cuotaMensual);
                        }
                       // if ($datos['impresion']) {
                           // $this->impresionDirecta($venta);
                       // }
                        $res = array('msg' => 'VENTA GENERADA', 'type' => 'success', 'idVenta' => $venta);
                    } else {
                        $res = array('msg' => 'ERROR AL GENERAR VENTA', 'type' => 'error');
                    }
                //}
            }
        } else {
            $res = array('msg' => 'CARRITO VACIO aca es', 'type' => 'warning');
        }
        echo json_encode($res);
        die();
    }

    public function reporte($datos)
    {
        ob_start();
        $array = explode(',', $datos);
        $tipo = $array[0];
        $idVenta = $array[1];
        $arrayDetProd = array();

        $data['title'] = 'Reporte';
       // $data['empresa'] = $this->model->getEmpresa();
        $data['venta'] = $this->model->getVenta($idVenta);
        
        
        $dat = $this->model->getDetalleVenta($idVenta);
        $contador = 0;
        foreach ($dat as $row) {
            if($data['venta']['metodo'] == 'CONTADO'){   
                $data2 = $this->model->buscarProdDispVentas($row['codigo']);
                $precioProd = $data2[0]['precio'];
                $result['cantidad'] = $row['cantidad'];
                $result['descripcion'] = $row['descripcion'];
                $result['precio'] = $precioProd;
                $result['total'] = $row['cantidad'] * $precioProd;
            }else{//mandar la prima en el subtotal
                if($contador <= 0){
                    $data2 = $this->model->obtenerCredito($row['idventa']);
                    $result['cantidad'] = 1;
                    $result['descripcion'] = $row['descripcion'];
                    $result['precio'] = $data2[0]['prima'];
                    $result['total'] = $data2[0]['prima'];
                    $contador ++;
                }
            }
            array_push($arrayDetProd, $result);
        }

        $data['detalle_venta'] = $arrayDetProd;
        
        if (empty($data['venta'])) {
            echo 'Pagina no Encontrada';
            exit;
        }
        $this->views->getView('ventas', $tipo, $data);
        $html = ob_get_clean();
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isJavascriptEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);

        if ($tipo == 'ticked') {
            $dompdf->setPaper(array(0, 0, 130, 841), 'portrait');
        } else {
            $dompdf->setPaper('A4', 'vertical');
        }

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('reporte.pdf', array('Attachment' => false));
    }

    public function listar()
    {
        $data = $this->model->getVentas();
        for ($i = 0; $i < count($data); $i++) {
            $data2 = $this->model->buscarProdDispVentas($data[$i]['codigo']);
            $precioProd = $data2[0]['precio'];//249.33
            $cantVend = $data[$i]['cantidad'];
            $desc = $data[$i]['descuento'];
            $subtotal = $precioProd*$cantVend;
            $descuento = ($subtotal*($desc/100));//13.71
            $totalSinIVA = ($subtotal - $descuento);//260.55
            $iva = $totalSinIVA*0.13;//33.87
            $data[$i]['total'] = (round($totalSinIVA+$iva,2));//294.42
            if ($data[$i]['estado'] == 1) {
                $data[$i]['estado'] = '<div>
                <span class="badge bg-success">Completado</span>
                </div>';
            } else if($data[$i]['estado'] == 0){
                $data[$i]['estado'] = '<div>
                <span class="badge bg-info">Pendiente</span>
                </div>';
            } else {
                $data[$i]['estado'] = '<div>
                <span class="badge bg-warning">Apartado</span>
                </div>';
            }

            $data[$i]['acciones'] = '<div>
            <a class="btn btn-danger" href="#" onclick="verReporte(' . $data[$i]['id'] . ')"><i class="fas fa-file-pdf"></i></a>
            </div>';
        }
        echo json_encode($data);
        die();
    }

    public function anular($idVenta)
    {
        if (isset($_GET) && is_numeric($idVenta)) {
            $data = $this->model->anular($idVenta);
            if ($data == 1) {
                $resultVenta = $this->model->getVenta($idVenta);
                $ventaProducto = json_decode($resultVenta['productos'], true);
                foreach ($ventaProducto as $producto) {
                    $result = $this->model->getProducto($producto['id']);
                    $nuevaCantidad = $result['cantidad'] + $producto['cantidad'];
                    $totalVentas = $result['ventas'] - $producto['cantidad'];
                    $this->model->actualizarStock($nuevaCantidad, $totalVentas, $producto['id']);

                    //movimientos
                    $movimiento = 'Devolución Venta N°: ' . $idVenta;
                    $this->model->registrarMovimiento($movimiento, 'entrada', $producto['cantidad'], $nuevaCantidad, $producto['id'], $this->id_usuario);
                }
                if ($resultVenta['metodo'] == 'CREDITO') {
                    $this->model->anularCredito($idVenta);
                }
                $res = array('msg' => 'VENTA ANULADO', 'type' => 'success');
            } else {
                $res = array('msg' => 'ERROR AL ANULAR', 'type' => 'error');
            }
        } else {
            $res = array('msg' => 'ERROR DESCONOCIDO', 'type' => 'error');
        }
        echo json_encode($res);
        die();
    }
//BORRAR
    public function impresionDirecta($idVenta)
    {
       // $empresa = $this->model->getEmpresa();
        $venta = $this->model->getVenta($idVenta);
        $nombre_impresora = "POS-58-Series";
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        # Vamos a alinear al centro lo próximo que imprimamos
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        /*
            Intentaremos cargar e imprimir
            el logo
        */
        try {
            $logo = EscposImage::load("assets/images/logo.png", false);
            $printer->bitImage($logo);
        } catch (Exception $e) {/*No hacemos nada si hay error*/
        }

        /*
            Ahora vamos a imprimir un encabezado
        */

       // $printer->text($empresa['nombre'] . "\n");
       // $printer->text('RUC: ' . $empresa['ruc'] . "\n");
       // $printer->text('Telefono: ' . $empresa['telefono'] . "\n");
       // $printer->text('Dirección: ' . $empresa['direccion'] . "\n");
        #La fecha también
       // $printer->text(date("Y-m-d H:i:s") . "\n\n");

        #Datos del cliente
        $printer->text('Datos del Cliente' . "\n");
        $printer->text('--------------------' . "\n");
        /*Alinear a la izquierda para la cantidad y el nombre*/
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        //$printer->text($venta['identidad'] . ': ' . $venta['num_identidad'] . "\n");
        $printer->text($venta['num_identidad'] . "\n");
        $printer->text('Nombre: ' . $venta['nombre'] . "\n");
        $printer->text('Telefono: ' . $venta['telefono'] . "\n");
        $printer->text('Dirección: ' . $venta['direccion'] . "\n\n");

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text('Detalles del Producto' . "\n");
        $printer->text('--------------------' . "\n");
        $productos = json_decode($venta['productos'], true);
        foreach ($productos as $producto) {
            /*Alinear a la izquierda para la cantidad y el nombre*/
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text($producto['cantidad'] . "x" . $producto['nombre'] . "\n");

            /*Y a la derecha para el importe*/
            $printer->setJustification(Printer::JUSTIFY_RIGHT);
            $printer->text(MONEDA . number_format($producto['precio'], 2) . "\n");
        }

        /*
            Terminamos de imprimir
            los productos, ahora va el total
        */
        $printer->text("--------\n");
        $printer->text("Descuento: " . MONEDA . number_format($venta['descuento'], 2) . "\n");
        $printer->text("--------\n");
        $printer->text("TOTAL: " . MONEDA . number_format($venta['total'] - $venta['descuento'], 2) . "\n\n");


        /*
            Podemos poner también un pie de página
        */
        //$printer->text($empresa['mensaje']);



        /*Alimentamos el papel 3 veces*/
        $printer->feed(3);

        /*
            Cortamos el papel. Si nuestra impresora
            no tiene soporte para ello, no generará
            ningún error
        */
        $printer->cut();

        /*
            Por medio de la impresora mandamos un pulso.
            Esto es útil cuando la tenemos conectada
            por ejemplo a un cajón
        */
        $printer->pulse();

        /*
            Para imprimir realmente, tenemos que "cerrar"
            la conexión con la impresora. Recuerda incluir esto al final de todos los archivos
        */
        $printer->close();
    }
////// BORRAR FIN
    public function verificarStock($idProducto)
    {
        $data = $this->model->getProducto($idProducto);
        echo json_encode($data);
        die();
    }

    function generate_numbers($start, $count, $digits)
    {
        $result = array();
        for ($n = $start; $n < $start + $count; $n++) {
            $result[] = str_pad($n, $digits, "0", STR_PAD_LEFT);
        }
        return $result;
    }

    // ENVIAR TICKET AL CORREO DEL CLIENTE
    public function enviarCorreo($idVenta)
    {
        $arrayDetProd = array();

        $dat = $this->model->getDetalleVenta($idVenta);
        $data['venta'] = $this->model->getVenta($idVenta);
        $contador = 0;
        foreach ($dat as $row) {
            if($data['venta']['metodo'] == 'CONTADO'){
                $data2 = $this->model->buscarProdDispVentas($row['codigo']);
                $precioProd = $data2[0]['precio'];
                $result['cantidad'] = $row['cantidad'];
                $result['descripcion'] = $row['descripcion'];
                $result['precio'] = $precioProd;
                $result['total'] = $row['cantidad'] * $precioProd;
            }else{//mandar la prima en el subtotal
                if($contador <= 0){
                    $data2 = $this->model->obtenerCredito($row['idventa']);
                    $result['cantidad'] = 1;
                    $result['descripcion'] = $row['descripcion'];
                    $result['precio'] = $data2[0]['prima'];
                    $result['total'] = $data2[0]['prima'];
                    $contador ++;
                }
            }
            array_push($arrayDetProd, $result);
        }

        $data['detalle_venta'] = $arrayDetProd;
        $data['venta'] = $this->model->getVenta($idVenta);

        ob_start();
        $data['title'] = 'Reporte';
        $this->views->getView('ventas', 'ticket_cliente', $data);
        $html = ob_get_clean();
        if (!empty($data)) {
            $mail = new PHPMailer(true);
            try {
                //Server settings
                //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                $mail->SMTPDebug = 0;                      //Enable verbose debug output
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = HOST_SMTP;                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = USER_SMTP;                     //SMTP username
                $mail->Password   = CLAVE_SMTP;                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port       = PUERTO_SMTP;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
               // $mail->setFrom($data['empresa']['correo'], $data['empresa']['nombre']);
                $mail->addAddress($data['venta']['correo']);

                //Content
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';                                  //Set email format to HTML
                $mail->Subject = 'Comprobante - ' . TITLE;
                $mail->Body    = $html;

                $mail->send();

                $res = array('msg' => 'CORREO ENVIADO CON LOS DATOS DE LA VENTA', 'type' => 'success');

                
            } catch (Exception $e) {
                $res = array('msg' => 'ERROR AL ENVIAR EL CORREO: ' . $mail->ErrorInfo, 'type' => 'error');
            }
        }else{
            $res = array('msg' => 'VENTA NO ENCONTRADA', 'type' => 'warning');
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        die();
    }

     

}
