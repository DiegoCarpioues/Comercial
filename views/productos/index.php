<?php include_once 'views/templates/header.php'; ?>

<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div></div>
            <div class="dropdown ms-auto">
                <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="<?php echo BASE_URL . 'productos/reportePdf'; ?>" target="_blank"><i class="fas fa-file-pdf text-danger"></i> Reporte PDF</a>
                    </li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL . 'productos/reporteExcel'; ?>"><i class="fas fa-file-excel text-success"></i> Reporte Excel</a>
                    </li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL . 'productos/generarBarcode'; ?>" target="_blank"><i class="fas fa-barcode"></i> Barcode</a>
                    </li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL . 'productos/inactivos'; ?>"><i class="fas fa-trash text-warning"></i> Inactivos</a>
                    </li>
                </ul>
            </div>
        </div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-productos-tab" data-bs-toggle="tab" data-bs-target="#nav-productos" type="button" role="tab" aria-controls="nav-productos" aria-selected="true">Productos</button>
                <button class="nav-link" id="nav-nuevo-tab" data-bs-toggle="tab" data-bs-target="#nav-nuevo" type="button" role="tab" aria-controls="nav-nuevo" aria-selected="false">Nuevo</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active mt-2" id="nav-productos" role="tabpanel" aria-labelledby="nav-productos-tab" tabindex="0">
                <h5 class="card-title text-center"><i class="fas fa-list"></i> Listado de Productos</h5>
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover nowrap" id="tblProductos" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Codigo</th>
                                <th>Producto</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Ganancia %</th>
                                <th>Categoría</th>
                                <th>Descripción</th>
                                <th>Foto</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade p-3" id="nav-nuevo" role="tabpanel" aria-labelledby="nav-nuevo-tab" tabindex="0">
                <form id="formulario" autocomplete="off">
                    <input type="hidden" id="id" name="id">
                    <input type="hidden" id="foto_actual" name="foto_actual">
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3">
                            <label for="codigo">Código <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                <input class="form-control" type="text" name="codigo" id="codigo" onkeypress="return soloNumeros(event)" placeholder="Barcode">
                            </div>
                            <span id="errorCodigo" class="text-danger"></span>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="nombre">Producto <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-box-archive"></i></span>
                                <input class="form-control" type="text" name="producto" id="producto" placeholder="Producto">
                            </div>
                            <span id="errorProducto" class="text-danger"></span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="precio_compra">Marca <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <input class="form-control" type="text" name="marca" id="marca" placeholder="Marca">
                            </div>
                            <span id="errorMarca" class="text-danger"></span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="precio_venta">Modelo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-paperclip"></i></span>
                                <input class="form-control" type="text" name="modelo" id="modelo" placeholder="Modelo">
                            </div>
                            <span id="errorModelo" class="text-danger"></span>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="precio_venta">Ganancia <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                <input class="form-control" type="number" step="0.01" min="0.01" name="ganancia" id="ganancia" placeholder="Ganancia">
                            </div>
                            <span id="errorGanancia" class="text-danger"></span>
                        </div>
                        <div class="col-md-5 mb-3">
                            <div class="form-group">
                                <label for="id_categoria">Categoría <span class="text-danger">*</span></label>
                                <select id="id_categoria" class="form-control" name="id_categoria">
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($data['categorias'] as $categoria) { ?>
                                        <option value="<?php echo $categoria['id']; ?>"><?php echo $categoria['categoria']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <span id="errorCategoria" class="text-danger"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label for="foto">Foto (Opcional)</label>
                                <input id="foto" class="form-control" type="file" name="foto">
                            </div>
                            <div id="containerPreview">
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-group">
                                <label for="direccion">Descripción </label>
                                <textarea id="descripcion" class="form-control" name="descripcion" rows="3" placeholder="Descripción"></textarea>
                            </div>
                            <span id="errorDescripcion" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-danger" type="button" id="btnNuevo">Nuevo</button>
                        <button class="btn btn-primary" type="submit" id="btnAccion">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include_once 'views/templates/footer.php'; ?>
