<?php
class CreditosModel extends Query{
    public function __construct() {
        parent::__construct();
    }
    public function getCreditos()
    {
        $sql = "SELECT
        c.id as id_credito,
		v.id as id_venta,
        v.fecha,
        cl.nombre,
        cl.telefono,
        cl.direccion,
        v.serie as venta,
        c.total,
        c.cuota,
        c.meses_plazo as cuotas_totales,
        (SELECT COUNT(*) FROM abonos as ab where ab.id_credito=c.id) as cuotas_pagadas,
        ((SELECT COUNT(*) FROM abonos as ab where ab.id_credito=c.id)* c.cuota + c.prima) as total_abonado,
        (c.total - ((SELECT COUNT(*) FROM abonos as ab where ab.id_credito=c.id)* c.cuota + c.prima)) as total_restante,
    CASE
    WHEN c.estado = 1 THEN 'Activo'
    WHEN c.estado = 0 THEN 'Inactivo'
END as estado
FROM
ventas as v
INNER JOIN
clientes as cl
ON 
    v.id_cliente = cl.id
INNER JOIN
creditos as c
ON 
    v.id = c.id_venta ORDER BY v.fecha ASC";
        return $this->selectAll($sql);
    }
    public function getAbono($idCredito)
    {
        $sql = "SELECT SUM(abono) AS total FROM abonos WHERE id_credito = $idCredito";
        return $this->select($sql);
    }

    public function VerificarCreditoFinalizado($id)
    {
        $sql = "SELECT * FROM creditos as c INNER JOIN abonos as a ON c.id = a.id_credito WHERE a.numero=c.meses_plazo AND c.id='".$id."' ";
        return $this->selectAll($sql);
    }

    public function finalizaCredito($estado, $idCredito)
    {
        $sql = "UPDATE creditos SET estado = ? WHERE id = ?";
        $array = array($estado, $idCredito);
        return $this->save($sql, $array);
    }

    public function finalizaVenta($estado, $idVenta)
    {
        $sql = "UPDATE ventas SET estado = ? WHERE id = ?";
        $array = array($estado, $idVenta);
        return $this->save($sql, $array);
    }

    public function registrarAbono($numero, $abono,$fecha,$mora,$apertura, $id_credito)
    {
        $sql = "INSERT INTO abonos (numero, abono, fecha, mora, apertura,id_credito) VALUES (?,?,?,?,?,?)";
        $array = array($numero, $abono,$fecha, $mora,$apertura, $id_credito);
        return $this->insertar($sql, $array);
    }
    public function getCredito($idCredito)
    {
        $sql = "SELECT cr.*, v.productos, cl.num_identidad, cl.nombre, cl.telefono, cl.direccion FROM creditos cr INNER JOIN ventas v ON cr.id_venta = v.id INNER JOIN clientes cl ON v.id_cliente = cl.id WHERE cr.id = $idCredito";
        return $this->select($sql);
    }

    public function actualizarCredito($estado, $idCredito)
    {
        $sql = "UPDATE creditos SET estado = ? WHERE id = ?";
        $array = array($estado, $idCredito);
        return $this->save($sql, $array);
    }

    public function getAbonos($idCredito)
    {
        $sql = "SELECT * FROM abonos WHERE id_credito = $idCredito";
        return $this->selectAll($sql);
    }

    public function getHistorialAbonos()
    {
        $sql = "SELECT * FROM abonos";
        return $this->selectAll($sql);
    }

    //public function getEmpresa()
    //{
      //  $sql = "SELECT * FROM configuracion";
        //return $this->select($sql);
    //}
}

?>