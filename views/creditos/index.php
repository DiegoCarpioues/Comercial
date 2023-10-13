<?php include_once 'views/templates/header.php'; ?>

<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div></div>
            <div class="dropdown ms-auto">
                <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                </a>
                <ul class="dropdown-menu">
                    <!-- <li><a class="dropdown-item" href="#" id="nuevoAbono"><i class="fas fa-dollar-sign"></i> Abonos</a> -->
                    </li>
                </ul>
            </div>
        </div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-creditos-tab" data-bs-toggle="tab" data-bs-target="#nav-creditos" type="button" role="tab" aria-controls="nav-creditos" aria-selected="true">Creditos</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active mt-2" id="nav-creditos" role="tabpanel" aria-labelledby="nav-creditos-tab" tabindex="0">
                <h5 class="card-title text-center"><i class="fas fa-credit-card"></i> Listado de Creditos</h5>
                <hr>
                <div class="d-flex justify-content-center mb-3">
                    <div class="form-group">
                        <label for="desde">Desde</label>
                        <input id="desde" onchange="filtroFechas()" class="form-control" type="date">
                    </div>
                    <div class="form-group">
                        <label for="hasta">Hasta</label>
                        <input id="hasta"onchange="filtroFechas()" class="form-control" type="date">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle nowrap" id="tblCreditos" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Cliente</th>
                                <th>N° Venta</th>
                                <th>Total</th>
                                <th>Cuo. Totales</th>
                                <th>Cuo. Pagadas</th>
                                <th>Abonado</th>
                                <th>Restante</th>
                                <th>Estado</th>
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

<div id="modalAbono" class="modal fade" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Abono</h5>
                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                <div class="col-md-12">
                        <label>Cliente</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input class="form-control" type="text" id="Cliente" placeholder="Telefono" disabled>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label>Telefono</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input class="form-control" type="text" id="telefonoCliente" placeholder="Telefono" disabled>
                        </div>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label>Dirección</label>
                        <ul class="list-group">
                            <li class="list-group-item" id="direccionCliente"><i class="fas fa-home"></i></li>
                        </ul>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>Abonado</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                            <input class="form-control" type="text" id="abonado" readonly>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label>Restante</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                            <input class="form-control" type="text" id="restante" readonly>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="fecha">Fecha Venta</label>
                            <input type="date" id="fecha" class="form-control" placeholder="Fecha Venta" readonly>
                        </div>
                    </div>

                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="cuota">Cuota</label>
                            <input id="cuota" class="form-control" type="text" placeholder="Monto Total" readonly>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="mora">Mora</label>
                            <input id="mora" class="form-control" type="text" placeholder="Monto Total" readonly>
                        </div>
                    </div>

                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="abono_total">Total Abono</label>
                            <input id="abono_total" class="form-control" type="text" placeholder="Monto Total" readonly>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="monto_abonar">Abonar</label>
                            <input id="monto_abonar" class="form-control" type="number" step="0.01" min="0.01" placeholder="Monto Abonar">
                            
                        </div>
                    </div>

                    <div class="col-md-4 mb-2">
                        <div class="form-group">
                            <label for="monto_abonar">Cambio</label>
                            <input id="cambio_abonar" class="form-control" type="number"  placeholder="Monto Abonar" disabled>
                        </div>
                    </div>
                    <span class="text-danger fw-bold mb-2" id="errorPago"></span>
                    </div>
                <div class="d-grid">
                    <button class="btn btn-primary" type="button" id="btnAccion">Abonar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'views/templates/footer.php'; ?>