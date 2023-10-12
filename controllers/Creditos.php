<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;

class Creditos extends Controller
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
        if (!verificar('credito ventas')){
            header('Location: ' . BASE_URL . 'admin/permisos');
            exit;
        }
        $this->id_usuario = $_SESSION['id_usuario'];
    }
    public function index()
    {
        $data['script'] = 'creditos.js';
        $data['title'] = 'Administrar Creditos';
        $this->views->getView('creditos', 'index', $data);
    }
    public function listar()
    {
        $data = $this->model->getCreditos();
        for ($i = 0; $i < count($data); $i++) {
            $data_json = json_encode($data[$i], JSON_UNESCAPED_UNICODE);
            $data[$i]['total']="$ ".$data[$i]['total'];
            $data[$i]['fecha'] = date('d/m/Y', strtotime($data[$i]['fecha']));
            $data[$i]['total_abonado']="$ ".$data[$i]['total_abonado'];
            $data[$i]['total_restante']="$ ".$data[$i]['total_restante'];
            if($data[$i]['estado'] == "Activo"){
                $data[$i]['acciones'] = '<a class="dropdown-item" href="#" id="nuevoAbono" onclick="mostrarModal(' . htmlspecialchars($data_json, ENT_QUOTES, 'UTF-8') . ')"><i class="fas fa-dollar-sign"></i> Abonos</a>';
            }else{
                $data[$i]['acciones'] ='';
            }
            if ($data[$i]['estado'] == "Activo") {
                $data[$i]['estado'] = '<span class="badge bg-warning">PENDIENTE</span>';
            } else if($data[$i]['estado'] == 'Inactivo'){
                $data[$i]['estado'] = '<span class="badge bg-success">COMPLETADO</span>';
            }
            
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function finalizar()
    {
        $array = array();
        $id_credito = strClean($_GET['id_credito']);
        $id_venta = strClean($_GET['id_venta']);
        $data = $this->model->VerificarCreditoFinalizado($id_credito);
    
        if (!empty($data)) {
            $valor_credito= $this->model->finalizaCredito(0,$id_credito);
            $valor_venta= $this->model->finalizaVenta(1,$id_venta);
            if($valor_credito>0){;
                if($valor_venta){
                    array_push($array, true);
                }
            }
        }
        echo json_encode($array, JSON_UNESCAPED_UNICODE);
        die();
    }

    public function registrarAbono()
    {
        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);
        if (!empty($datos)) {
            $numero = $datos['numero'];
            $abono = $datos['abono'];
            $fecha = $datos['fecha'];
            $apertura = $datos['apertura'];
            $mora = $datos['mora'];
            $idCredito =$datos['idCredito'];
            
            $data = $this->model->registrarAbono($numero,$abono,$fecha,$mora,$apertura, $idCredito);
            if ($data > 0) {
                $res = array('msg' => 'ABONO REGISTRADO', 'type' => 'success');
            }else{
            $res = array('msg' => 'ERROR AL REGISTRAR', 'type' => 'error');
            }
        }else{
            $res = array('msg' => 'TODO LOS CAMPOS SON REQUERIDO', 'type' => 'warning');
        }
        echo json_encode($res);
        die();
    }

    public function reporte($idCredito)
    {
        ob_start();
        $data['title'] = 'Reporte';
       // $data['empresa'] = $this->model->getEmpresa();
        $data['credito'] = $this->model->getCredito($idCredito);
        $data['abonos'] = $this->model->getAbonos($idCredito);
        $data['productos'] = $this->model->getProductos($data['credito']['id_venta']);
        if (empty($data['credito'])) {
            echo 'Pagina no Encontrada';
            exit;
        }
        $this->views->getView('creditos', 'reporte', $data);
        $html = ob_get_clean();
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isJavascriptEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'vertical');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('reporte.pdf', array('Attachment' => false));
    }


    //

    public function ticked($idCredito)
    {
        ob_start();
        $data['title'] = 'Reporte';
       // $data['empresa'] = $this->model->getEmpresa();
        $data['credito'] = $this->model->getCredito($idCredito);
        $data['abonos'] = $this->model->getAbonos($idCredito);
        $data['productos'] = $this->model->getProductos($data['credito']['id_venta']);
        if (empty($data['credito'])) {
            echo 'Pagina no Encontrada';
            exit;
        }
        $this->views->getView('creditos', 'ticked', $data);
        $html = ob_get_clean();
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('isJavascriptEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf->setOptions($options);
        $dompdf->loadHtml($html);

        //$dompdf->setPaper('A4', 'vertical');
        $dompdf->setPaper(array(0, 0, 130, 841), 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('reporte.pdf', array('Attachment' => false));
    }


        // ENVIAR TICKET AL CORREO DEL CLIENTE
        public function enviarCorreo($idCredito)
        {
/*             $arrayDetProd = array();
    
            $dat = $this->model->getDetalleVenta($idVenta);
            foreach ($dat as $row) {
                $result['cantidad'] = $row['cantidad'];
                $result['descripcion'] = $row['descripcion'];
                $result['precio'] = $row['precio'];
                $result['total'] = $row['total'];
                array_push($arrayDetProd, $result);
            }
    
            $data['detalle_venta'] = $arrayDetProd;
            $data['venta'] = $this->model->getVenta($idVenta);
    
            ob_start();
            $data['title'] = 'Reporte';
            $this->views->getView('ventas', 'ticket_cliente', $data);
            $html = ob_get_clean(); */
            ob_start();
            $data['title'] = 'Reporte';
           // $data['empresa'] = $this->model->getEmpresa();
            $data['credito'] = $this->model->getCredito($idCredito);
            $data['abonos'] = $this->model->getAbonos($idCredito);
            $data['productos'] = $this->model->getProductos($data['credito']['id_venta']);
            if (empty($data['credito'])) {
                echo 'Pagina no Encontrada';
                exit;
            }
            $this->views->getView('creditos', 'ticked_cliente', $data);
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
                    $mail->addAddress('20carpioronaldo@gmail.com');
    
                    //Content
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';                                  //Set email format to HTML
                    $mail->Subject = 'Comprobante - ' . TITLE;
                    $mail->Body    = $html;
    
                    $mail->send();
    
                    $res = array('msg' => 'CORREO ENVIADO CON LOS DATOS DE ABONO', 'type' => 'success');
    
                    
                } catch (Exception $e) {
                    $res = array('msg' => 'ERROR AL ENVIAR EL CORREO: ' . $mail->ErrorInfo, 'type' => 'error');
                }
            }else{
                $res = array('msg' => 'VENTA NO ENCONTRADA', 'type' => 'warning');
            }
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
            die();
        }
    

    public function listarAbonos()
    {
        $data = $this->model->getHistorialAbonos();
        for ($i=0; $i < count($data); $i++) { 
            $data[$i]['credito'] = 'N°: ' . $data[$i]['id_credito'];
        }
        echo json_encode($data);
        die();
    }
}
