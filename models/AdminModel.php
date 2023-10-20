<?php
class AdminModel extends Query{
    public function __construct() {
        parent::__construct();
    }

    public function getTotales($table)
    {
        $sql = "SELECT COUNT(*) AS total FROM $table WHERE estado = 1";
        return $this->select($sql);
    }

    public function calcularVentasCompras($table, $desde, $hasta, $id_usuario)
    {
        $sql = "SELECT SUM(IF(MONTH(fecha) = 1, total, 0)) AS ene,
        SUM(IF(MONTH(fecha) = 2, total, 0)) AS feb,
        SUM(IF(MONTH(fecha) = 3, total, 0)) AS mar,
        SUM(IF(MONTH(fecha) = 4, total, 0)) AS abr,
        SUM(IF(MONTH(fecha) = 5, total, 0)) AS may,
        SUM(IF(MONTH(fecha) = 6, total, 0)) AS jun,
        SUM(IF(MONTH(fecha) = 7, total, 0)) AS jul,
        SUM(IF(MONTH(fecha) = 8, total, 0)) AS ago,
        SUM(IF(MONTH(fecha) = 9, total, 0)) AS sep,
        SUM(IF(MONTH(fecha) = 10, total, 0)) AS oct,
        SUM(IF(MONTH(fecha) = 11, total, 0)) AS nov,
        SUM(IF(MONTH(fecha) = 12, total, 0)) AS dic
        FROM $table WHERE fecha BETWEEN '$desde' AND '$hasta' AND id_usuario = $id_usuario";
        return $this->select($sql);
    }

    public function totalVentasCompras($table, $desde, $hasta, $id_usuario)
    {
        $sql = "SELECT COUNT(*) AS total FROM $table WHERE fecha BETWEEN '$desde' AND '$hasta' AND estado = 1 AND id_usuario = $id_usuario";
        return $this->select($sql);
    }

    public function topProductos($cantidad)
    {
        $sql = "SELECT p.*, c.categoria FROM productos p INNER JOIN categorias c ON p.id_categoria = c.id ";
        return $this->selectAll($sql);
    }

    public function nuevosProductos($cantidad)
    {
        $sql = "SELECT
        p.*, 
        co.fecha,
        co.precio as precio_compra, 
        (p.ganancia*(co.precio/100)) + co.precio as precio_venta,
        c.categoria
    FROM
        productos as p
        INNER JOIN
        categorias as c
        ON 
            p.id_categoria = c.id
        INNER JOIN
        compras as co
        ON 
            p.id = co.id_productos LIMIT $cantidad";
        return $this->selectAll($sql);
    }

    public function calcularGatos($desde, $hasta, $id_usuario)
    {
        $sql = "SELECT SUM(IF(MONTH(fecha) = 1, monto, 0)) AS ene,
        SUM(IF(MONTH(fecha) = 2, monto, 0)) AS feb,
        SUM(IF(MONTH(fecha) = 3, monto, 0)) AS mar,
        SUM(IF(MONTH(fecha) = 4, monto, 0)) AS abr,
        SUM(IF(MONTH(fecha) = 5, monto, 0)) AS may,
        SUM(IF(MONTH(fecha) = 6, monto, 0)) AS jun,
        SUM(IF(MONTH(fecha) = 7, monto, 0)) AS jul,
        SUM(IF(MONTH(fecha) = 8, monto, 0)) AS ago,
        SUM(IF(MONTH(fecha) = 9, monto, 0)) AS sep,
        SUM(IF(MONTH(fecha) = 10, monto, 0)) AS oct,
        SUM(IF(MONTH(fecha) = 11, monto, 0)) AS nov,
        SUM(IF(MONTH(fecha) = 12, monto, 0)) AS dic
        FROM gastos WHERE fecha BETWEEN '$desde' AND '$hasta' AND id_usuario = $id_usuario";
        return $this->select($sql);
    }

    public function minimosProductos()
    {
        $sql = "SELECT descripcion, cantidad FROM productos WHERE cantidad < 15 LIMIT 4";
        return $this->selectAll($sql);
    }

    //reporte pdf
    public function minimosProductosPDF()
    {
        $sql = "SELECT p.*, c.categoria FROM productos p INNER JOIN categorias c ON p.id_categoria = c.id WHERE p.cantidad < 15";
        return $this->selectAll($sql);
    }

    public function listarLogs()
    {
        $sql = "SELECT * FROM acceso";
        return $this->selectAll($sql);
    }

    public function limpiraDatos()
    {
        $sql = "TRUNCATE acceso";
        return $this->select($sql);
    }
}
?>
