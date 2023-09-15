<?php
class UsuariosModel extends Query{
    public function __construct() {
        parent::__construct();
    }
    public function getRoles($estado)
    {
        $sql = "SELECT * FROM roles WHERE estado = $estado";
        return $this->selectAll($sql);
    }
    public function getUsuarios($estado)
    {
        $sql = "SELECT id, dui_usuario, CONCAT(nombre, ' ', apellido) AS nombres, correo, telefono, direccion, rol FROM usuarios WHERE estado = $estado";
        return $this->selectAll($sql);
    }
    public function registrar($dui_usuario,$nombres, $apellidos, $correo,
    $telefono, $direccion, $clave, $rol)
    {
        $sql = "INSERT INTO usuarios (dui_usuario,nombre, apellido, correo, telefono, direccion, clave, rol) VALUES (?,?,?,?,?,?,?,?)";
        $array = array($dui_usuario,$nombres, $apellidos, $correo, $telefono, $direccion, $clave, $rol);
        return $this->insertar($sql, $array);
    }
    public function getValidar($campo, $valor, $accion, $id)
    {
        if ($accion == 'registrar' && $id == 0) {
            $sql = "SELECT id, correo, telefono FROM usuarios WHERE $campo = '$valor'";
        }else{
            $sql = "SELECT id, correo, telefono FROM usuarios WHERE $campo = '$valor' AND id != $id";
        }
        return $this->select($sql);
    }
    public function eliminar($estado, $id)
    {
        $sql = "UPDATE usuarios SET estado = ? WHERE id = ?";
        $array = array($estado, $id);
        return $this->save($sql, $array);
    }
    public function editar($id)
    {
        $sql = "SELECT id, dui_usuario, nombre, apellido, correo, telefono, direccion, perfil, clave, fecha, rol FROM usuarios WHERE id = $id";
        return $this->select($sql);
    }
    public function actualizar($dui_usuario,$nombres, $apellidos, $correo,
    $telefono, $direccion, $rol, $id)
    {
        $sql = "UPDATE usuarios SET dui_usuario=?, nombre=?, apellido=?, correo=?, telefono=?, direccion=?, rol=? WHERE id=?";
        $array = array($dui_usuario,$nombres, $apellidos, $correo, $telefono, $direccion, $rol, $id);
        return $this->save($sql, $array);
    }

    public function modificarDatos($nombre, $apellidos, $correo,
    $telefono, $direccion, $clave, $perfil, $id)
    {
        $sql = "UPDATE usuarios SET nombre=?, apellido=?, correo=?, telefono=?, direccion=?, clave=?, perfil=? WHERE id=?";
        $array = array($nombre, $apellidos, $correo, $telefono, $direccion, $clave, $perfil, $id);
        return $this->save($sql, $array);
    }

}
?>