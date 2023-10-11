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
        $sql = "SELECT
        c.id as id_credito,
		v.id as id_venta,
        v.fecha,
	    cl.num_identidad,
        cl.nombre,
        cl.telefono,
        cl.direccion,
        v.serie as venta,
        c.cuota,
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
    v.id = c.id_venta  WHERE c.id= $idCredito";
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
        $sql = "SELECT
        *,
        CASE
            WHEN mora = TRUE THEN abono * 0.05
            ELSE 0
        END AS mora_calculada
    FROM abonos WHERE id_credito= $idCredito ORDER BY numero ASC";
        return $this->selectAll($sql);
    }

    public function getHistorialAbonos()
    {
        $sql = "SELECT * FROM abonos";
        return $this->selectAll($sql);
    }

    public function getProductos($idVenta)
    {
        $sql = "SELECT
        p.*,
        de.cantidad
        
    FROM
        ventas as v
        INNER JOIN
        detalle_venta as de
        ON 
            v.id = de.id_venta
        INNER JOIN
        compras as c
        ON 
            de.id_compra = c.id
        INNER JOIN
        productos as p
        ON 
            c.id_productos = p.id  WHERE v.id= $idVenta";
        return $this->selectAll($sql);
    }
    

    //public function getEmpresa()
    //{
      //  $sql = "SELECT * FROM configuracion";
        //return $this->select($sql);
    //}
}

?>