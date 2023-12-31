<?php
require 'vendor/autoload.php';
use Dompdf\Dompdf;
class Cajas extends Controller
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
        if (!verificar('cajas')){
            header('Location: ' . BASE_URL . 'admin/permisos');
            exit;
        }
        $this->id_usuario = $_SESSION['id_usuario'];
    }
    public function index()
    {
        $data['script'] = 'cajas.js';
        $data['title'] = 'Movimientos de Caja';
        $data['caja'] = $this->model->getCaja($this->id_usuario);
        $this->views->getView('cajas', 'index', $data);
    }
    public function abrirCaja()
    {
        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);
        if (empty($datos['monto'])) {
            $res = array('msg' => 'EL MONTO ES REQUERIDO', 'type' => 'warning');
        } else {
            $verificar = $this->model->getCaja($this->id_usuario);
            if (empty($verificar)) {
                $fecha_apertura = date('Y-m-d');
                $monto = strClean($datos['monto']);
                $data = $this->model->abrirCaja($monto, $fecha_apertura, $this->id_usuario);
                if ($data > 0) {
                    $res = array('msg' => 'CAJA ABIERTA', 'type' => 'success');
                } else {
                    $res = array('msg' => 'ERROR AL ABRIR LA CAJA', 'type' => 'error');
                }
            } else {
                $res = array('msg' => 'LA CAJA YA ESTA ABIERTA', 'type' => 'warning');
            }
        }
        echo json_encode($res);
        die();
    }

    public function listar()
    {
        $data = $this->model->getCajas();
        for ($i=0; $i < count($data); $i++) { 
            $data[$i]['accion'] = '<a href="'.BASE_URL.'cajas/historialRepote/'.$data[$i]['id'].'" target="_blank" class="btn btn-danger"><i class="fas fa-file-pdf"></i></a>';
        }
        echo json_encode($data);
        die();
    }

    
    public function getDatos()
    {
        $consultaVenta = $this->model->getVentas('total', $this->id_usuario);
        $ventas = ($consultaVenta['total'] != null) ? $consultaVenta['total'] : 0;

        $consultaDescuento = $this->model->getVentas('descuento', $this->id_usuario);
        $descuento = ($consultaDescuento['total'] != null) ? $consultaDescuento['total'] : 0;

        $consultaApartados = $this->model->getApartados($this->id_usuario);
        $apartados = ($consultaApartados['total'] != null) ? $consultaApartados['total'] : 0;

        $consultaCreditos = $this->model->getAbonos($this->id_usuario);
        $creditos = ($consultaCreditos['total'] != null) ? $consultaCreditos['total'] : 0;

        $consultaCompras = 100;
        $compras = ($consultaCompras['total'] != null) ? $consultaCompras['total'] : 0;



        $montoInicial = $this->model->getCaja($this->id_usuario);

        $data['egresos'] = number_format($compras, 2, '.', '');
        $data['ingresos'] = number_format(($ventas + $apartados + $creditos) - $descuento, 2, '.', '');
        $data['montoInicial'] = (!empty($montoInicial['monto_inicial'])) ? number_format($montoInicial['monto_inicial'], 2, '.', '') : 0;
        $data['saldo'] = number_format(($data['ingresos'] + $data['montoInicial']) - $data['egresos'], 2, '.', '');

        $data['egresosDecimal'] = number_format($data['egresos'], 2);
        $data['ingresosDecimal'] = number_format($data['ingresos'], 2);
        $data['inicialDecimal'] = number_format($data['montoInicial'], 2);
        $data['saldoDecimal'] = number_format($data['saldo'], 2);

        return $data;
    }
    public function reporte()
    {
        ob_start();

        $data['title'] = 'Reporte Actual';
        $data['actual'] = true;
        $data['movimientos'] = $this->getDatos();
        $this->views->getView('cajas', 'reporte', $data);
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

    public function cerrarCaja()
    {
        //$data = $this->getDatos();
        //$ventas = $this->model->getTotalVentas($this->id_usuario);
        date_default_timezone_set('America/El_Salvador');
        $fecha_cierre = date('Y-m-d');
        $montoVentasFinal = $this->model->getTotalMontoVentas($this->id_usuario, $fecha_cierre);
        $montoCreditos = $this->model->getTotalMontoCreditos($this->id_usuario, $fecha_cierre);
        $montoAbonos = $this->model->getTotalMontoAbonos($this->id_usuario, $fecha_cierre);
        $totalVentas = $this->model->getTotalVentas($this->id_usuario, $fecha_cierre);
        
        $egresos =$this->model->getCompra($this->id_usuario, $fecha_cierre);

        $montoFinal=$montoVentasFinal['ventas_contado'] + $montoCreditos['total_creditos'] + $montoAbonos['total_abonos'];
        $result = $this->model->cerrarCaja($fecha_cierre, $montoFinal, $totalVentas['venta_total'], $egresos['ingresos'], $this->id_usuario);
        if ($result == 1) {
/*             $this->model->actualizarApertura('compras', $this->id_usuario);
            $this->model->actualizarApertura('ventas', $this->id_usuario);
            $this->model->actualizarApertura('abonos', $this->id_usuario);
            $this->model->actualizarApertura('detalle_apartado', $this->id_usuario); */
            $res = array('msg' => 'CAJA CERRADO', 'type' => 'success');
        }else{
            $res = array('msg' => 'ERROR AL CERRAR LA CAJA', 'type' => 'error');
        }
        echo json_encode($res);
        die();
    }

    public function historialRepote($idCaja)
    {
        ob_start();
        $data['title'] = 'Reporte: ' . $idCaja;
        $data['idCaja'] = $idCaja;
        $data['actual'] = false;
        $datos = $this->model->getHistorialCajas($idCaja);
        $data['movimientos']['inicialDecimal'] = $datos['monto_inicial'];
        $data['movimientos']['ingresosDecimal'] = $datos['monto_final'];
        $data['movimientos']['egresosDecimal'] = $datos['egresos'];
        $data['movimientos']['saldoDecimal'] = number_format($datos['monto_final'] + $datos['monto_inicial'], 2);
        $this->views->getView('cajas', 'reporte', $data);
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

}