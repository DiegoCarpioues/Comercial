<?php
class ComprasModel extends Query{
    public function __construct() {
        parent::__construct();
    }
    public function getProducto($idProducto)
    {
        $sql = "SELECT * FROM productos WHERE id = $idProducto";
        return $this->select($sql);
    }
    public function registrarCompra($idproductos, $cantidad,$precio, $fecha, $hora, $serie,$apertura, $idproveedor, $idusuario,$estado)
    {
        $sql = "INSERT INTO compras (id_productos, cantidad,precio, fecha, hora, serie, apertura, id_proveedor, id_usuario, estado) VALUES (?,?,?,?,?,?,?,?,?,?)";
        $array = array($idproductos, $cantidad,$precio, $fecha, $hora, $serie, $apertura, $idproveedor, $idusuario,$estado);
        return $this->insertar($sql, $array);
    }
   // public function getEmpresa()
    //{
    //    $sql = "SELECT * FROM configuracion";
       // return $this->select($sql);
    //}
    public function getCompra($idCompra)
    {
        $sql = "SELECT c.*, p.ruc, p.nombre, p.telefono, p.direccion FROM compras c INNER JOIN proveedor p ON c.id_proveedor = p.id WHERE c.id = $idCompra";
        return $this->select($sql);
    }
    //actualizar stock}
    public function actualizarStock($cantidad, $idProducto)
    {
        $sql = "UPDATE productos SET cantidad = ? WHERE id = ?";
        $array = array($cantidad, $idProducto);
        return $this->save($sql, $array);
    }

    public function getCompras()
    {
        $sql = "SELECT c.id, DATE_FORMAT(c.fecha, '%d/%m/%Y') as fecha, DATE_FORMAT(c.hora, '%h:%i %p') as hora, pro.producto, c.cantidad, c.precio, (c.cantidad * c.precio) as subtotal, p.nombre as proveedor, c.serie, c.estado
        FROM proveedor as p
        INNER JOIN compras as c ON p.id = c.id_proveedor
        INNER JOIN productos as pro ON c.id_productos = pro.id
        ORDER BY c.fecha DESC, c.hora DESC;";
        return $this->selectAll($sql);
    }

    public function anular($idCompra)
    {
        $sql = "UPDATE compras SET estado = ? WHERE id = ?";
        $array = array(0, $idCompra);
        return $this->save($sql, $array);
    }
    //movimiento
    public function registrarMovimiento($movimiento, $accion, $cantidad, $stockActual, $idProducto, $id_usuario)
    {
        $sql = "INSERT INTO inventario (movimiento, accion, cantidad, stock_actual, id_producto, id_usuario) VALUES (?,?,?,?,?,?)";
        $array = array($movimiento, $accion, $cantidad, $stockActual, $idProducto, $id_usuario);
        return $this->insertar($sql, $array);
    }
}
?>
