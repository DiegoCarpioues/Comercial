let tblProductos;
const formulario = document.querySelector('#formulario');
const btnAccion = document.querySelector('#btnAccion');
const btnNuevo = document.querySelector('#btnNuevo');

const id = document.querySelector('#id');
const codigo = document.querySelector('#codigo');
const nombre = document.querySelector('#nombre');
const precio_compra = document.querySelector('#precio_compra');
const precio_venta = document.querySelector('#precio_venta');
const id_categoria = document.querySelector('#id_categoria');
const foto = document.querySelector('#foto');
const foto_actual = document.querySelector('#foto_actual');
const containerPreview = document.querySelector('#containerPreview');

const errorCodigo = document.querySelector('#errorCodigo');
const errorNombre = document.querySelector('#errorNombre');
const errorCompra = document.querySelector('#errorCompra');
const errorVenta = document.querySelector('#errorVenta');
const errorCategoria = document.querySelector('#errorCategoria');
const filtro = document.querySelector('#filtro');

document.addEventListener('DOMContentLoaded', function () {
    //cargar datos con el plugin datatables
   tblProductos=$('#tblProductos').DataTable({
        ajax: {
            url: base_url + 'productos/listar/'+true,
            dataSrc: ''
        },
        columns: [
            { data: 'codigo' },
            { data: 'producto' },
            { data: 'marca' },
            { data: 'modelo' },
            { data: 'ganancia' },
            { data: 'categoria' },
            { data: 'descripcion' },
            { data: 'imagen' },
            { data: 'acciones' }
        ],
        language: {
            url: base_url + 'assets/js/espanol.json'
        },
        dom,
        buttons,
        responsive: true,
        order: [[0, 'asc']],
    });

  

    //Filtro de estados de quipo

    filtro.addEventListener('change', function (e) {
        
        console.log("Estado: ", filtro.value)

        if(filtro.value=="Activo"){
            tabla(true);
        }else if(filtro.value=="Inactivo"){
            tabla(false);
        }
    })


    function tabla($estado){
        tblProductos.destroy();
        tblProductos=$('#tblProductos').DataTable({
            ajax: {
                url: base_url + 'productos/listar/'+$estado,
                dataSrc: ''
            },
            columns: [
                { data: 'codigo' },
                { data: 'producto' },
                { data: 'marca' },
                { data: 'modelo' },
                { data: 'ganancia' },
                { data: 'categoria' },
                { data: 'descripcion' },
                { data: 'imagen' },
                { data: 'acciones' }
            ],
            language: {
                url: base_url + 'assets/js/espanol.json'
            },
            dom,
            buttons,
            responsive: true,
            order: [[0, 'asc']],
        });
    
    }

    //vista Previa
    foto.addEventListener('change', function (e) {
        foto_actual.value = '';
        if (e.target.files[0].type == 'image/png' ||
            e.target.files[0].type == 'image/jpg' ||
            e.target.files[0].type == 'image/jpeg') {
            const url = e.target.files[0];
            const tmpUrl = URL.createObjectURL(url);
            containerPreview.innerHTML = `<img class="img-thumbnail" src="${tmpUrl}" width="200">
            <button class="btn btn-danger" type="button" onclick="deleteImg()"><i class="fas fa-trash"></i></button>`;
        } else {
            foto.value = '';
            alertaPersonalizada('warning', 'SOLO SE PERMITEN IMG DE TIPO PNG-JPG-JPEG');
        }
    })
    //limpiar campos
    btnNuevo.addEventListener('click', function () {
        id.value = '';
        btnAccion.textContent = 'Registrar';
        formulario.reset();
        deleteImg();
        limpiarCampos();
    })
    //registrar Productos
    formulario.addEventListener('submit', function (e) {
        e.preventDefault();
        limpiarCampos();

        if (codigo.value == '') {
            errorCodigo.textContent = 'EL CODIGO ES REQUERIDO';

        } else if (producto.value == '') {
            errorPrucducto.textContent = 'EL NOMBRE DE PRODCUCTO ES REQUERIDO';
        } else if (marca.value == '') {
            errorMarca.textContent = 'LA MARCA ES REQUERIDA';
        } else if (modelo.value == '') {
            errorModelo.textContent = 'EL MODELO ES REQUERIDO';
        } else if (id_categoria.value == '') {
            errorCategoria.textContent = 'SELECCIONA LA CATEGORIA';
        } else if(ganancia.value == ''){
            errorGanancia.textContent = 'LA GANANCIA ES REQUERIDA';
        }else {
            const url = base_url + 'productos/registrar';
            insertarRegistros(url, this, tblProductos, btnAccion, false);
            limpiarCampos()
        }
    })
})

function deleteImg() {
    foto.value = '';
    containerPreview.innerHTML = '';
    foto_actual.value = '';
}

function eliminarProducto(idProducto) {
    const url = base_url + 'productos/eliminar/' + idProducto;
    eliminarRegistros(url, tblProductos);

    
}

function editarProducto(idProducto) {
    limpiarCampos();
    const url = base_url + 'productos/editar/' + idProducto;
    //hacer una instancia del objeto XMLHttpRequest
    const http = new XMLHttpRequest();
    //Abrir una Conexion - POST - GET
    http.open('GET', url, true);
    //Enviar Datos
    http.send();
    //verificar estados
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            id.value = res.id;
            codigo.value = res.codigo;
            producto.value = res.producto;
            marca.value = res.marca;
            modelo.value = res.modelo;
            ganancia.value = res.ganancia;
            id_categoria.value = res.id_categoria;
            foto_actual.value = res.foto;
            descripcion.value = res.descripcion;
            containerPreview.innerHTML = `<img class="img-thumbnail" src="${base_url + res.foto}" width="200">
            <button class="btn btn-danger" type="button" onclick="deleteImg()"><i class="fas fa-trash"></i></button>`;
            btnAccion.textContent = 'Actualizar';
            firstTab.show()
        }
    }
}

function limpiarCampos() {
    errorCodigo.textContent = '';
    errorProducto.textContent = '';
    errorMarca.textContent = '';
    errorModelo.textContent = '';
    errorGanancia.textContent = '';
    errorCategoria.textContent = '';
    containerPreview.innerHTML = '';
    errorDescripcion.textContent = '';
}

// ---- Validacion de Campos de tipo numericos ---- //
function soloNumeros(e) {
    var key = e.keyCode || e.which,
      tecla = String.fromCharCode(key).toLowerCase(),
      numeros = "0123456789",
      especiales = [8, 37, 39, 46],
      tecla_especial = false;
  
    for (var i in especiales) {
      if (key == especiales[i]) {
        tecla_especial = true;
        break;
      }
    }
  
    if (numeros.indexOf(tecla) == -1 && !tecla_especial) {
      return false;
    }
  };