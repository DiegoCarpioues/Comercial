<?php
class CreditosModel extends Query{
    public function __construct() {
        parent::__construct();
    }
    public function getCreditos()
    {
        $sql = "SELECT
        v.fecha,
        cl.nombre,
        v.serie as venta,
        c.total,
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

    public function buscarPorNombre($valor)
    {
        $sql = "SELECT cr.*, cl.nombre, cl.telefono, cl.direccion FROM creditos cr INNER JOIN ventas v ON cr.id_venta = v.id INNER JOIN clientes cl ON v.id_cliente = cl.id WHERE cl.nombre LIKE '%".$valor."%' AND cr.estado = 1 LIMIT 10";
        return $this->selectAll($sql);
    }

    public function registrarAbono($monto, $idCredito, $id_usuario)
    {
        $sql = "INSERT INTO abonos (abono, id_credito, id_usuario) VALUES (?,?,?)";
        $array = array($monto, $idCredito, $id_usuario);
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