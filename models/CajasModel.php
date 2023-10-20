<?php
class CajasModel extends Query
{
    public function __construct()
    {
        parent::__construct();
    }
    public function abrirCaja($monto, $fecha_apertura, $id_usuario)
    {
        $sql = "INSERT INTO cajas (monto_inicial, fecha_apertura, id_usuario) VALUES (?,?,?)";
        $array = array($monto, $fecha_apertura, $id_usuario);
        return $this->insertar($sql, $array);
    }
    public function getCaja($id_usuario)
    {
        $sql = "SELECT * FROM cajas WHERE estado = 1 AND id_usuario = $id_usuario";
        return $this->select($sql);
    }

    public function getCajas()
    {
        $sql = "SELECT c.*, u.nombre FROM cajas c INNER JOIN usuarios u ON c.id_usuario = u.id";
        return $this->selectAll($sql);
    }

    //####### movimientos
    public function getVentas($campo, $id_usuario)
    {
        $sql = "SELECT SUM($campo) AS total FROM ventas WHERE metodo = 'CONTADO' AND estado = 1 AND apertura = 1 AND id_usuario = $id_usuario";
        return $this->select($sql);
    }
    public function getApartados($id_usuario)
    {
        $sql = "SELECT SUM(d.monto) AS total FROM detalle_apartado d INNER JOIN apartados a ON d.id_apartado = a.id WHERE d.apertura = 1 AND a.id_usuario = $id_usuario";
        return $this->select($sql);
    }
    public function getAbonos($id_usuario)
    {
        $sql = "SELECT SUM(a.abono) AS total FROM abonos a INNER JOIN creditos c ON a.id_credito = c.id INNER JOIN ventas v ON c.id_venta = v.id WHERE a.apertura = 1 AND v.id_usuario = $id_usuario";
        return $this->select($sql);
    }
    public function getCompra($id_usuario, $fecha_compra)
    {
        $sql = "SELECT SUM(c.precio * c.cantidad) AS ingresos FROM compras as c WHERE c.id_usuario = $id_usuario AND c.fecha = '$fecha_compra'";
        return $this->select($sql);
    }
    
    public function getTotalVentas($id_usuario, $fecha)
    {
        $sql = "SELECT
        COUNT(*) AS venta_total
        FROM
        ventas as v
        INNER JOIN
        detalle_venta as d
        ON 
            v.id = d.id_venta WHERE v.id_usuario=$id_usuario AND v.fecha='$fecha'";
        return $this->select($sql);
    }

    //cerrar caja
    public function cerrarCaja($fecha_cierre, $montoFinal, $totalVentas, $egresos, $id_usuario)
    {
        $sql = "UPDATE cajas SET fecha_cierre=?, monto_final=?, total_ventas=?, egresos=?, estado=? WHERE estado = ? AND id_usuario = ?";
        $array = array($fecha_cierre, $montoFinal, $totalVentas, $egresos, 0, 1, $id_usuario);
        return $this->save($sql, $array);
    }
    public function actualizarApertura($table, $id_usuario)
    {
        $sql = "UPDATE $table SET apertura = ? WHERE id_usuario = ?";
        $array = array(0, $id_usuario);
        return $this->save($sql, $array);
    }

    public function getHistorialCajas($idCaja)
    {
        $sql = "SELECT * FROM cajas WHERE id = $idCaja";
        return $this->select($sql);
    }
}