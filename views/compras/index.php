<?php include_once 'views/templates/header.php'; ?>

<div class="card">
    <div class="card-body">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-compras-tab" data-bs-toggle="tab" data-bs-target="#nav-compras" type="button" role="tab" aria-controls="nav-compras" aria-selected="true">Compras</button>
                <button class="nav-link" id="nav-historial-tab" data-bs-toggle="tab" data-bs-target="#nav-historial" type="button" role="tab" aria-controls="nav-historial" aria-selected="false">Historial</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active p-3" id="nav-compras" role="tabpanel" aria-labelledby="nav-compras-tab" tabindex="0">
                <h5 class="card-title text-center"><i class="fas fa-truck"></i> Nueva Compra</h5>
                <hr>
                <div class="btn-group btn-group-toggle mb-2" data-toggle="buttons">
                    <label class="btn btn-primary">
                        <input type="radio" id="barcode" checked name="buscarProducto"><i class="fas fa-barcode"></i> Código
                    </label>
                    <label class="btn btn-info">
                        <input type="radio" id="nombre" name="buscarProducto"><i class="fas fa-list"></i> Nombre
                    </label>
                </div>
            <div class="col-md-6">
                <!-- input para buscar codigo -->
                <div class="input-group mb-2" id="containerCodigo">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input class="form-control" type="text" id="buscarProductoCodigo" placeholder="Ingrese Código - Enter" autocomplete="off">
                </div>

                <!-- input para buscar nombre -->
                <div class="input-group d-none mb-2" id="containerNombre">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input class="form-control" type="text" id="buscarProductoNombre" placeholder="Buscar Producto" autocomplete="off">
                </div>

                <span class="text-danger fw-bold mb-2" id="errorBusqueda"></span>
            </div>
                <!-- table productos -->
                <br>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle" id="tblNuevaCompra" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <center><h5 id="total"></h5></center>
                    
                </div>

                <hr>

                <div class="row justify-content-between">
                    <div class="col-md-4">
                        <label>Buscar Proveedor</label>
                        <div class="input-group mb-2">
                            <input type="hidden" id="idProveedor">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input class="form-control" type="text" id="buscarProveedor" placeholder="Buscar Proveedor">
                        </div>
                        <span class="text-danger fw-bold mb-2" id="errorProveedor"></span>

                        <label>Telefono</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input class="form-control" type="text" id="telefonoProveedor" placeholder="Telefono" disabled>
                        </div>

                        <label>Dirección</label>
                        <ul class="list-group">
                            <li class="list-group-item" id="proveedorDireccion"><i class="fas fa-home"></i></li>
                        </ul>
                    </div>

                    <div class="col-md-4">
                        <label>Comprador</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input class="form-control" type="text" value="<?php echo $_SESSION['nombre_usuario']; ?>" placeholder="Comprador" disabled>
                        </div>

                        <label>Serie</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-spinner"></i></span>
                            <input class="form-control" type="text" id="serie" onkeypress="return soloNumeros(event)" placeholder="Serie Compra">
                        </div>

                        <div class="d-grid">
                            <button class="btn btn-primary" type="button" id="btnAccion">Completar</button>
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
                  <table class="table table-bordered table-striped table-hover nowrap" id="tblHistorial" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio $</th>
                                <th>Subtotal $</th>
                                <th>Proveedor</th>
                                <th>Serie</th>
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

<?php include_once 'views/templates/footer.php'; ?>
