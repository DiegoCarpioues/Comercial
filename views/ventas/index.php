<?php include_once 'views/templates/header.php'; ?>
<?php include_once 'views/templates/header.php'; ?>

<?php include_once 'views/templates/header.php';
$id_usuario = $_SESSION['id_usuario'];
$model = 'CajasModel';
$ruta = 'models/' . $model . '.php';
if (file_exists($ruta)) {
    require_once $ruta;
    $this->model = new $model();
}
$verificar = $this->model->getCajaActivas($id_usuario);
if($verificar!=null){
?>

<div class="card">
    <div class="card-body">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-ventas-tab" data-bs-toggle="tab" data-bs-target="#nav-ventas" type="button" role="tab" aria-controls="nav-ventas" aria-selected="true">Ventas</button>
                <button class="nav-link" id="nav-historial-tab" data-bs-toggle="tab" data-bs-target="#nav-historial" type="button" role="tab" aria-controls="nav-historial" aria-selected="false">Historial</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active p-3" id="nav-ventas" role="tabpanel" aria-labelledby="nav-ventas-tab" tabindex="0">
                <h5 class="card-title text-center"><i class="fas fa-cash-register"></i> Nueva Venta</h5>
                <hr>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                            <label class="btn btn-primary">
                                <input type="radio" id="barcode" checked name="buscarProducto"><i class="fas fa-barcode"></i> Barcode
                            </label>
                            <label class="btn btn-info">
                                <input type="radio" id="nombre" name="buscarProducto"><i class="fas fa-list"></i> Nombre
                            </label>
                        </div>
                    </div>

                    <div class="col-md-3 mb-2">
                        <div class="input-group">
                            <span class="input-group-text">N° Factura</span>
                            <input class="form-control" type="text" id="serie" value="<?php echo $data['serie'][0]; ?>" disabled>
                        </div>
                    </div>
                </div>

                <!-- input para buscar codigo -->
                <div class="input-group mb-2" id="containerCodigo">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input class="form-control" type="text" id="buscarProductoCodigoVenta" placeholder="Ingrese Barcode - Enter" autocomplete="off">
                </div>

                <!-- input para buscar nombre -->
                <div class="input-group d-none mb-2" id="containerNombre">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input class="form-control" type="text" id="buscarProductoNombreVenta" placeholder="Buscar Producto" autocomplete="off">
                </div>

                <span class="text-danger fw-bold mb-2" id="errorBusqueda"></span>

                <!-- table productos -->

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle" id="tblNuevaVenta" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>SubTotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <hr>

                <div class="row justify-content-between">
                    <div class="col-md-4">
                        <label>Buscar Cliente</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input class="form-control" type="text" id="buscarCliente" placeholder="Buscar Cliente">
                            
                            <input type="hidden" id="idCliente" >
                        </div>

                        <span class="text-danger fw-bold mb-2" id="errorCliente"></span>

                        <label>Telefono</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input class="form-control" type="text" id="telefonoCliente" placeholder="Telefono" disabled>
                        </div>
                        <label>Dirección</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-home"></i></span>
                            <input class="form-control" type="text" id="direccionCliente" placeholder="Dirección">
                        </div>
                        <label>Vendedor</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input class="form-control" type="text" id="idUsuario" value="<?php echo $_SESSION['nombre_usuario']; ?>" placeholder="Vendedor" disabled>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label> Venta Total </label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                            <input class="form-control" type="number" id="sumaTotalVenta" placeholder="0.00" disabled>
                        </div>
                        <label>Descuento</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-percent"></i></span>
                            <input class="form-control" type="number" sleep="1" min="0" max="100" value="0" id="descuento" >
                        </div>
                        <label>Total a Pagar</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                            <input class="form-control" type="text" id="totalPagar" value="0.00" placeholder="Total Pagar" disabled>
                            <input class="form-control" type="hidden" id="totalPagarHidden" >
                        </div>
                        <div class="esContado">
                            <div class="form-group mb-2">
                                <label for="idtransaccion">Transacción</label>
                                <select id="idtransaccion" class="form-control">
                                    <option value="VENTA">VENTA</option>
                                    <option value="APARTADO">APARTADO</option>
                                </select>
                            </div>
                        </div>
                        <div class="esCredito">
                            <label>Prima</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input class="form-control" type="number" id="prima" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">   
                        <div class="form-group mb-2">
                            <label for="metodo">Metodo</label>
                            <select id="metodo" class="form-control">
                                <option value="CONTADO">CONTADO</option>
                                <option value="CREDITO">CREDITO</option>
                            </select>
                        </div>
                        <div class="esContado">
                            <div class="transaccion">
                                <label>Apartado $</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                    <input class="form-control" type="number" id="apartado" placeholder="0.00">
                                </div>
                            </div>
                            <label>Pago</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input class="form-control" type="number" min="0.00" value="0.00" id="pago" placeholder="0.00" >
                            </div>
                            <span class="text-danger fw-bold mb-2" id="errorPago"></span>
                            <label>Cambio</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input class="form-control text-danger" type="text" id="cambio" placeholder="0.00" readonly>
                            </div>
                        </div>

                        <div class="esCredito">
                            <label>Meses plazo</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input class="form-control" type="number" id="mesesPlazo" placeholder="0.00">
                            </div>
                            <label>Interes Mensual</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input class="form-control" type="number" id="interesMensual" value="0" placeholder="0">
                            </div>
                            <label>Cuota mensual</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                <input class="form-control" type="text" id="cuotaMensual" placeholder="Cuota mensual" disabled>
                                <input class="form-control" type="hidden" id="cuotaMensualHidden" >
                            </div>
                        </div>
                        <div class="d-grid">
                            <button class="btn btn-primary" type="button" id="btnGuardarVenta">Completar</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade p-3" id="nav-historial" role="tabpanel" aria-labelledby="nav-historial-tab" tabindex="0">
                <div class="d-flex justify-content-center mb-3">
                <div class="form-group">
                        <label for="desde">Desde</label>
                        <input type="date" id="desde" onchange="filtroFechas()" class="form-control" >
                    </div>
                    <div class="form-group">
                        <label for="hasta">Hasta</label>
                        <input type="date" id="hasta" onchange="filtroFechas()" class="form-control" >
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle nowrap" id="tblHistorial" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Serie</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Metodo</th>
                                <th>Descuento</th>
                                <th>Estado</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'views/templates/footer.php';
}else{
?>
    <div class="card">
    <div class="card-body">
        <div class="alert alert-danger border-0 bg-danger alert-dismissible fade show py-2">
        <div class="d-flex align-items-center">
                <div class="font-35 text-white">
                    <i class="fas fa-lock"></i>
                </div>
                <div class="ms-3">
                    <h6 class="mb-0 text-white text-center">CAJA CERRADA</h6>
                </div>
            </div>
        </div>
    </div>
    </div>
<?php 
include_once 'views/templates/footer.php';
}?>