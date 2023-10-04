
const inputBuscarCodigo = document.querySelector('#buscarProductoCodigo');
const inputBuscarNombre = document.querySelector('#buscarProductoNombre');
const tblNuevaCompra = document.querySelector('#tblNuevaCompra tbody');

var listaDeProductos = [];

var inputCantidadElements = document.querySelectorAll('.inputCantidad');
var inputPrecioElements = document.querySelectorAll('.inputPrecio');
const btnAccion = document.querySelector('#btnAccion');

const serie = document.querySelector('#serie');
//provedores
const telefonoProveedor = document.querySelector('#telefonoProveedor');
const direccionProveedor = document.querySelector('#proveedorDireccion');
const idProveedor = document.querySelector('#idProveedor');
const errorProveedor = document.querySelector('#errorProveedor');

let tblHistorial;



function llenartablaCompras(listaDeProductos) {
    var total=0;
    let html = '';
    if (listaDeProductos.length > 0) {   
      listaDeProductos.forEach((data, index) => {
        total+=data.subtotal;
        html += `<tr>
        <td>${data.nombre}</td>
        <td width="150">
          <input type="number" min="1" class="form-control inputCantidad" data-id="${index}" value="${data.cantidad}" placeholder="Cantidad" onchange="actualizarSubtotal(${index})">
        </td>
        <td width="150">
          <input type="number" min="0" class="form-control inputPrecio" data-id="${index}" value="${data.precio}" placeholder="Precio" onchange="actualizarSubtotal(${index})">
        </td>
        <td>$ ${data.subtotal}</td>
        <td><button class="btn btn-danger btnEliminar" data-id="${index}" type="button"><i class="fas fa-trash"></i></button></td>
      </tr>`;
      
      });
    }
    // Actualizar la tabla HTML
    $("#total").text("Total: $"+total);
    tblNuevaCompra.innerHTML = html;
  }
function actualizarSubtotal(index) {
    var inputCantidad = document.querySelector(`.inputCantidad[data-id="${index}"]`);
    var inputPrecio = document.querySelector(`.inputPrecio[data-id="${index}"]`);
    var cantidad = parseInt(inputCantidad.value, 10);
    var precio = parseFloat(inputPrecio.value);
    listaDeProductos[index].cantidad = cantidad
    listaDeProductos[index].precio = precio
    listaDeProductos[index].subtotal = parseFloat((cantidad * precio).toFixed(2)); // Redondear a 2 decimales
    llenartablaCompras(listaDeProductos);
  }
  $(document).on('click', '.btnEliminar', function() {
    var index = $(this).data('id');   
    listaDeProductos.splice(index, 1);
    llenartablaCompras(listaDeProductos);
  });

document.addEventListener('DOMContentLoaded', function () {
   
    tblHistorial=$('#tblHistorial').DataTable({
      ajax: {
          url: base_url + 'compras/listar',
          dataSrc: ''
      },
      columns: [
          {class: "fecha", data: 'fecha' },
          { data: 'hora' },
          { data: 'producto' },
          { data: 'cantidad' },
          { data: 'precio' },
          { data: 'subtotal' },
          { data: 'proveedor' },
          { data: 'serie' },
          { data: 'acciones' },
      ],
      language: {
          url: base_url + 'assets/js/espanol.json'
      },
      dom,
      buttons,
      responsive: true,
      order: [[1, 'desc']],
      order: [[0, 'desc']],
  });

 



    //autocomplete proveedores
    $("#buscarProveedor").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: base_url + 'proveedor/buscar',
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function (data) {
                    response(data);
                    if (data.length > 0) {
                        errorProveedor.textContent = '';
                    } else {
                        errorProveedor.textContent = 'NO HAY PROVEEDOR CON ESE NOMBRE';
                    }
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            telefonoProveedor.value = ui.item.telefono;
            direccionProveedor.innerHTML = ui.item.direccion;
            idProveedor.value = ui.item.id;
            serie.focus();
        }
    });

    //autocomplete productos por nombre
    $("#buscarProductoNombre").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: base_url + 'productos/buscarPorNombre',
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function (data) {
                    response(data);
                    if (data.length > 0) {
                        errorBusqueda.textContent = '';

                    } else {
                        errorBusqueda.textContent = 'NO HAY PRODUCTO CON ESE NOMBRE';
                    }
                }
            });
        },
      minLength: 2,
        select: function (event, ui) {
            // Agregar productos a listaDeProductos
            listaDeProductos.push({id:ui.item.id,nombre: ui.item.label, cantidad: 0, precio: 0, subtotal: 0 });
            llenartablaCompras(listaDeProductos);
            inputBuscarNombre.innerHTML=ui.item.id;
            inputBuscarNombre.focus(); 
        } 
    });

  //autocomplete productos por codigo
    $("#buscarProductoCodigo").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: base_url + 'productos/buscarPorCodigo',
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function (data) {
                    response(data);
                    if (data.length > 0) {
                        errorBusqueda.textContent = '';
                    } else {
                        errorBusqueda.textContent = 'NO HAY PRODUCTO CON ESE CÓDIGO';
                    }
                }
            });
        },
      minLength: 2,
        select: function (event, ui) {
            listaDeProductos.push({id:ui.item.id,nombre: ui.item.producto, cantidad: 0, precio: 0, subtotal: 0 });
            llenartablaCompras(listaDeProductos);
            inputBuscarCodigo.innerHTML=ui.item.id;
            inputBuscarCodigo.focus();
         
        } 
      });



    //cargar datos
    mostrarProducto();

    //completar compra
    btnAccion.addEventListener('click', function () {
        let filas = document.querySelectorAll('#tblNuevaCompra tr').length;
        if (filas < 0) {
            alertaPersonalizada('warning', 'COMPRA VACIA');
            return;
        } else if (idProveedor.value == ''
            && telefonoProveedor.value == '') {
            alertaPersonalizada('warning', 'EL PROVEEDOR ES REQUERIDO');
            return;
        } else if (serie.value == '') {
            alertaPersonalizada('warning', 'LA SERIE ES REQUERIDO');
            return;
        } else {
            const url = base_url + 'compras/registrarCompra';
            //hacer una instancia del objeto XMLHttpRequest
            const http = new XMLHttpRequest();
            //Abrir una Conexion - POST - GET
            http.open('POST', url, true);
            //Enviar Datos
            http.send(JSON.stringify({
                productos: listaDeProductos,
                idProveedor: idProveedor.value,
                serie: serie.value,
            }));
            //verificar estados
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    const res = JSON.parse(this.responseText);
                    alertaPersonalizada(res.type, res.msg);
                    if (res.type == 'success') {
                        localStorage.removeItem(nombreKey);
                        setTimeout(() => {
                            window.location.reload();

                        }, 2500);
                       
                    }
                }
            }
        }

    })

    //mostrar historial
    //cargar datos con el plugin datatables


})

 function anularCompra(index){
    const url = base_url + 'compras/anular/' + index;
    eliminarRegistros(url, tblHistorial);
 }


  

//cargar productos
function mostrarProducto() {
    if (localStorage.getItem(nombreKey) != null) {
        const url = base_url + 'productos/mostrarDatos';
        //hacer una instancia del objeto XMLHttpRequest
        const http = new XMLHttpRequest();
        //Abrir una Conexion - POST - GET
        http.open('POST', url, true);
        //Enviar Datos
        http.send(JSON.stringify(listaCarrito));
        //verificar estados
        http.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                const res = JSON.parse(this.responseText);
                let html = '';
                if (res.productos.length > 0) {
                    res.productos.forEach(producto => {
                        html += `<tr>
                            <td>${producto.nombre}</td>
                            <td>${producto.precio_compra}</td>
                            <td width="100">
                            <input type="number" min="1" class="form-control inputCantidad" data-id="${producto.id}" value="${producto.cantidad}" placeholder="Cantidad">
                            </td>
                            <td>${producto.subTotalCompra}</td>
                            <td><button class="btn btn-danger btnEliminar" data-id="${producto.id}" type="button"><i class="fas fa-trash"></i></button></td>
                        </tr>`;
                    });
                    tblNuevaCompra.innerHTML = html;
                    totalPagar.value = res.totalCompra;
                    btnEliminarProducto();
                    agregarCantidad();
                } else {
                    tblNuevaCompra.innerHTML = '';
                }
            }
        }
    } else {
        tblNuevaCompra.innerHTML = `<tr>
            <td colspan="4" class="text-center">COMPRA VACIA</td>
        </tr>`;
    }
}

    //filtro rango de fechas
function filtroFechas(){
    
    const fechaDesde = new Date(($('#desde').val()));
    const fechaHasta = new Date(($('#hasta').val()));

    console.log("Fecha desde: ",fechaDesde)
    $('#tblHistorial tbody tr').each(function() {
      const fechaTexto = $(this).find('.fecha').text(); // Suponiendo que la fecha esté en una columna con clase 'fecha'
      const fecha = parseDate(fechaTexto);
      console.log("Fecha tabla: ", fecha)
      if (fecha >= fechaDesde && fecha <= fechaHasta) {
        console.log("valido")
        $(this).show(); // Mostrar la fila
      } else {
        console.log("No valido")
        $(this).hide(); // Ocultar la fila
      }
    });
  
}

function parseDate(dateString) {
    const parts = dateString.split('/');
    if (parts.length === 3) {
      const day = parseInt(parts[0], 10);
      const month = parseInt(parts[1], 10) - 1; // Restamos 1 al mes porque en JavaScript los meses se cuentan desde 0 a 11
      const year = parseInt(parts[2], 10);
      return new Date(year + '-' + (month < 9 ? '0' : '') + (month + 1) + '-' + (day < 10 ? '0' : '') + day);
    }
    return null; // Devuelve null si la cadena no tiene el formato esperado
  }
  
function verReporte(idCompra) {
    Swal.fire({
        title: 'Desea Generar Reporte?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: 'Ticked',
        denyButtonText: `Factura`,
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            const ruta = base_url + 'compras/reporte/ticked/' + idCompra;
            window.open(ruta, '_blank');
        } else if (result.isDenied) {
            const ruta = base_url + 'compras/reporte/factura/' + idCompra;
            window.open(ruta, '_blank');
        }
    })
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



  
  