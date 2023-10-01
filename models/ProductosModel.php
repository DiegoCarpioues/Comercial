<?php
class ProductosModel extends Query{
    public function __construct() {
        parent::__construct();
    }
    public function getProductos($estado)
    {
        $sql = "SELECT p.*, c.categoria FROM productos p  INNER JOIN categorias c ON p.id_categoria = c.id WHERE p.estado = $estado";
        return $this->selectAll($sql);
    }

    public function getDatos($table)
    {
        $sql = "SELECT * FROM $table WHERE estado = 1";
        return $this->selectAll($sql);
    }

    public function registrar($codigo, $producto, $marca, $modelo, $ganancia, $descripcion, $id_categoria, $foto)
    {
        $sql = "INSERT INTO productos (codigo, producto, marca, modelo, ganancia, descripcion, id_categoria, foto) VALUES (?,?,?,?,?,?,?,?)";
        $array = array($codigo, $producto, $marca, $modelo, $ganancia, $descripcion,
        $id_categoria, $foto);
        return $this->insertar($sql, $array);
    }

    public function getValidar($campo, $valor, $accion, $id)
    {
        if ($accion == 'registrar' && $id == 0) {
            $sql = "SELECT id FROM productos WHERE $campo = '$valor'";
        }else{
            $sql = "SELECT id FROM productos WHERE $campo = '$valor' AND id != $id";
        }
        return $this->select($sql);
    }

    public function eliminar($estado, $idProducto)
    {
        $sql = "UPDATE productos SET estado = ? WHERE id = ?";
        $array = array($estado, $idProducto);
        return $this->save($sql, $array);
    }

    public function editar($idProducto)
    {
        $sql = "SELECT * FROM productos WHERE id = $idProducto";
        return $this->select($sql);
    }

    public function actualizar($codigo, $producto, $marca, $modelo, $ganancia, $descripcion,$id_categoria, $foto, $id)
    {
        $sql = "UPDATE productos SET codigo=?, producto=?, marca=?, modelo=?, ganancia=?,descripcion=?, id_categoria=?, foto=? WHERE id=?";
        $array = array($codigo, $producto, $marca, $modelo, $ganancia, $descripcion,
        $id_categoria, $foto, $id);
        return $this->save($sql, $array);
    }

    public function buscarPorCodigo($valor)
    {
        $sql = "SELECT * FROM productos WHERE codigo LIKE '%".$valor."%' AND estado = 1 LIMIT 10";
        return $this->selectAll($sql);
    }

    public function buscarPorNombre($valor)
    {
        $sql = "SELECT * FROM productos WHERE producto LIKE '%".$valor."%' AND estado = 1 LIMIT 10";
        return $this->selectAll($sql);
    }

    public function getEmpresa()
    {
        $sql = "SELECT * FROM configuracion";
        return $this->select($sql);
    }

   /*  public function buscarProdDispVentas($valor)//buscar producto disponible para la venta
    {
        //$sql = "SELECT * FROM productos WHERE codigo LIKE '%".$valor."%' AND estado = 1 LIMIT 10";
        $sql = "SELECT
        p.id,
        p.producto, 
        p.codigo, 
        p.ganancia, 
        c.cantidad,
        (c.precio + ( c.precio * p.ganancia )) AS total
    FROM
        detalle_venta
        INNER JOIN
        compras c
        ON 
            detalle_venta.id_compra = c.id
        INNER JOIN
        productos p
        ON 
            c.id_productos = p.id
        
        WHERE p.producto LIKE '%".$valor."%'
        ";
        return $this->selectAll($sql);
    } */

}
