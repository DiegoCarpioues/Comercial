<?php
class VentasModel extends Query{
    public function __construct() {
        parent::__construct();
    }

    /* para ventas */
    public function buscarProdDispVentas($valor)//buscar producto disponible para la venta
    {
        //$sql = "SELECT * FROM productos WHERE codigo LIKE '%".$valor."%' AND estado = 1 LIMIT 10";
        $sql = "
        SELECT
        p.id AS id,
        c.id AS idCompra,
        p.producto, 
        p.codigo, 
        SUM(cc.comprados - IFNULL(v.vendidos, 0)) AS disponibles,
        p.ganancia, 
        c.cantidad,
        ROUND(SUM(ROUND((c.precio + (c.precio * p.ganancia / 100)), 2))/COUNT(c.id),2) AS precio
    FROM
        compras c
    INNER JOIN
        productos p
    ON 
        c.id_productos = p.id
    LEFT JOIN
        (SELECT
            id,
            SUM(cantidad) AS comprados
        FROM
            compras
        WHERE
            estado = 1
        GROUP BY
            id) AS cc
    ON 
        cc.id = c.id
    LEFT JOIN
        (SELECT
            id_compra,
            SUM(detv.cantidad) AS vendidos
        FROM
            detalle_venta detv
        INNER JOIN
            compras c
        ON 
            detv.id_compra = c.id
        WHERE
            c.estado = 1
        GROUP BY
            id_compra) AS v
    ON 
        c.id = v.id_compra
    WHERE
        p.producto LIKE '%".$valor."%' OR p.codigo LIKE '%".$valor."%';
        ";
        return $this->selectAll($sql);
    }

    public function getVentas()
    {
        //$sql = "SELECT v.*, c.nombre FROM ventas v INNER JOIN clientes c ON v.id_cliente = c.id";

        $sql = "SELECT v.id, DATE_FORMAT(v.fecha, '%d/%m/%Y') AS fecha, DATE_FORMAT(v.hora, '%h:%i %p') AS hora, v.metodo, v.descuento, v.serie,
            v.estado, cli.nombre, dtv.cantidad,p.codigo,p.ganancia,
            ROUND(SUM((((p.ganancia/100) * c.precio) + c.precio) * dtv.cantidad),2) AS total
        FROM detalle_venta dtv
        INNER JOIN ventas AS v ON dtv.id_venta = v.id
        INNER JOIN clientes AS cli ON v.id_cliente = cli.id
        INNER JOIN compras AS c ON dtv.id_compra = c.id
        INNER JOIN productos AS p ON c.id_productos = p.id
        GROUP BY v.id";
        return $this->selectAll($sql);
    }

    public function getSerie()
    {
        $sql = "SELECT MAX(serie) AS total FROM ventas";
        return $this->select($sql);
    }

    //creo este no lo ocupo
    public function getProducto($idProducto)
    {
        $sql = "SELECT * FROM productos WHERE id = $idProducto";
        return $this->select($sql);
    }
    
    public function registrarVenta($fecha, $hora, $metodo, $descuento, $serie, $pago, $estado, $idCliente, $idUsuario)
    {
        $sql = "INSERT INTO ventas( fecha, hora, metodo, descuento, serie, pago, estado, id_cliente, id_usuario) VALUES (?,?,?,?,?,?,?,?,?);";
        $array = array($fecha, $hora, $metodo, $descuento, $serie, $pago, $estado, $idCliente, $idUsuario);
        return $this->insertar($sql, $array);
    }

    //movimiento detalle_venta
    public function registraDetalleVenta($idCompra, $idVenta, $cantidad)
    {
        $sql = "INSERT INTO detalle_venta( id_compra, id_venta, cantidad) VALUES (?, ?, ?);";
        $array = array($idCompra, $idVenta, $cantidad);
        return $this->insertar($sql, $array);
    }

    public function registrarCredito($idVenta, $prima, $mesesPlazo, $ganancia, $estado, $total, $cuota)
    {
        $sql = "INSERT INTO creditos(id_venta, prima, meses_plazo, ganancia, estado, total, cuota) VALUES (?, ?, ?, ?, ?, ?, ?);";
        $array = array($idVenta, $prima, $mesesPlazo, $ganancia, $estado, $total, $cuota);
        return $this->insertar($sql, $array);
    }

    public function actualizarEstadoCompra($idCompra, $estado)
    {
        $sql = "UPDATE compras SET estado = ? WHERE id = ?";
        $array = array($estado, $idCompra);
        return $this->insertar($sql, $array);
    }

    public function ultimaVenta(){
        $sql = "SELECT id AS id FROM ventas ORDER BY id DESC LIMIT 1";
        return $this->select($sql);
    }

    public function obtenerListaCompras($valor)//buscar producto disponible para la venta
    {
        $sql = " 
        SELECT
            c.id AS idCompra,
            p.producto, 
            p.codigo, 
            (cc.comprados - IFNULL(v.vendidos, 0)) AS disponibles, 
            c.cantidad
        FROM
            compras c
        INNER JOIN
            productos p
        ON 
            c.id_productos = p.id
        LEFT JOIN
            (SELECT
                id,
                SUM(cantidad) AS comprados
            FROM
                compras
            WHERE
                estado = 1
            GROUP BY
                id) AS cc
        ON 
            cc.id = c.id
        LEFT JOIN
            (SELECT
                id_compra,
                SUM(detv.cantidad) AS vendidos
            FROM
                detalle_venta detv
            INNER JOIN
                compras c
            ON 
                detv.id_compra = c.id
            WHERE
                c.estado = 1
            GROUP BY
                id_compra) AS v
        ON 
            c.id = v.id_compra
        WHERE
            p.codigo = '".$valor."'
        ORDER BY c.id ASC;
        ";
        return $this->selectAll($sql);
    }

    public function getVenta($idVenta)
    {
        //$sql = "SELECT v.*, c.num_identidad, c.nombre, c.telefono, c.correo, c.direccion FROM ventas v INNER JOIN clientes c ON v.id_cliente = c.id WHERE v.id = $idVenta";
        $sql = " SELECT
            ventas.id,
            productos.id AS id_producto, 
            productos.producto,
            productos.codigo,
            productos.descripcion,
            ROUND(((productos.ganancia/100) * compras.precio) + compras.precio, 2)AS precio,
            detalle_venta.cantidad,
            ROUND(((((productos.ganancia/100) * compras.precio) + compras.precio) * detalle_venta.cantidad),2) AS total,
            ventas.fecha,
            ventas.hora,
            ventas.metodo,
            ventas.descuento,
            ventas.serie,
            ventas.pago,
            ventas.estado,
            clientes.id AS id_cliente, 
            usuarios.id AS id_usuario, 
            clientes.num_identidad, 
            clientes.nombre, 
            clientes.telefono, 
            clientes.correo, 
            clientes.direccion,
            COUNT(ventas.id) AS cantProd
        FROM
            detalle_venta
            INNER JOIN
            compras
            ON 
                detalle_venta.id_compra = compras.id
            INNER JOIN
            productos
            ON 
                compras.id_productos = productos.id
            INNER JOIN
            ventas
            ON 
                detalle_venta.id_venta = ventas.id
            INNER JOIN
            clientes
            ON 
                ventas.id_cliente = clientes.id
            INNER JOIN
            usuarios
            ON 
                compras.id_usuario = usuarios.id AND
                ventas.id_usuario = usuarios.id
            WHERE ventas.id = '".$idVenta."'
            GROUP BY detalle_venta.id ;
            ";
        return $this->select($sql);
    }
    public function getDetalleVenta($idVenta)
    {
        //$sql = "SELECT v.*, c.num_identidad, c.nombre, c.telefono, c.correo, c.direccion FROM ventas v INNER JOIN clientes c ON v.id_cliente = c.id WHERE v.id = $idVenta";
        $sql = " SELECT
        ventas.id AS idventa,
        productos.producto,
        productos.codigo,
        productos.descripcion, 
        ROUND(((productos.ganancia/100) * compras.precio) + compras.precio, 2)AS precio, 
        detalle_venta.cantidad,
        ROUND(((((productos.ganancia/100) * compras.precio) + compras.precio) * detalle_venta.cantidad),2) AS total
    FROM
        detalle_venta
        INNER JOIN
        compras
        ON 
            detalle_venta.id_compra = compras.id
        INNER JOIN
        productos
        ON 
            compras.id_productos = productos.id
        INNER JOIN
        ventas
        ON 
            detalle_venta.id_venta = ventas.id
        INNER JOIN
        clientes
        ON 
            ventas.id_cliente = clientes.id
        INNER JOIN
        usuarios
        ON 
            compras.id_usuario = usuarios.id AND
            ventas.id_usuario = usuarios.id
        WHERE ventas.id = '".$idVenta."'
        GROUP BY detalle_venta.id ; 
            ";
        return $this->selectAll($sql);
    }

    public function obtenerCredito($idVenta){
        $sql = " 
        SELECT
            creditos.*
        FROM
            creditos
            INNER JOIN
            ventas
            ON 
                creditos.id_venta = ventas.id
        WHERE 
		    ventas.id = '".$idVenta."'
        ";
        return $this->selectAll($sql);
    }




    public function actualizarStock($cantidad, $ventas, $idProducto)
    {
        $sql = "UPDATE productos SET cantidad = ?, ventas=? WHERE id = ?";
        $array = array($cantidad, $ventas, $idProducto);
        return $this->save($sql, $array);
    }
    public function anular($idVenta)
    {
        $sql = "UPDATE ventas SET estado = ? WHERE id = ?";
        $array = array(0, $idVenta);
        return $this->save($sql, $array);
    }
    public function anularCredito($idVenta)
    {
        $sql = "UPDATE creditos SET estado = ? WHERE id_venta = ?";
        $array = array(2, $idVenta);
        return $this->save($sql, $array);
    }
    public function getCaja($id_usuario)
    {
        $sql = "SELECT * FROM cajas WHERE estado = 1 AND id_usuario = $id_usuario";
        return $this->select($sql);
    }
}


?>