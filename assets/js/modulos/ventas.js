/* Input para buscar el producto disponible para la venta */
const inputBuscarCodigoVenta = document.querySelector(
  "#buscarProductoCodigoVenta"
);
const inputBuscarNombreVenta = document.querySelector(
  "#buscarProductoNombreVenta"
);

const tblNuevaVenta = document.querySelector("#tblNuevaVenta tbody");

const idCliente = document.querySelector("#idCliente");
const idUsuario = document.querySelector("#idUsuario");
const telefonoCliente = document.querySelector("#telefonoCliente");
const direccionCliente = document.querySelector("#direccionCliente");
const errorCliente = document.querySelector("#errorCliente");
const errorPago = document.querySelector("#errorPago");

const descuento = document.querySelector("#descuento");
const metodo = document.querySelector("#metodo");
const serie = document.querySelector("#serie");
const pago = document.querySelector("#pago");
const impresion_directa = document.querySelector("#impresion_directa");
var listaDeProductos = [];

const precioProducto = document.querySelector("#precioProducto");
const totalPagarHidden = document.querySelector("#totalPagarHidden");
const cambio = document.querySelector("#cambio");

const btnGuardarVenta = document.querySelector('#btnGuardarVenta');

document.addEventListener("DOMContentLoaded", function () {
  //cargar productos de localStorage
  //mostrarProducto();

    // Obtener referencia al elemento select
    var metodoPagoSelect = document.getElementById("metodo");

    // Obtener una lista de elementos con la clase deseada
    var elementosOcultarMostrar = document.querySelectorAll(".esCredito");

  // Ocultar los elementos al cargar la página (por defecto)
  ocultarElementos();

  // Función para ocultar los elementos
  function ocultarElementos() {
    elementosOcultarMostrar.forEach(function (elemento) {
      elemento.style.display = "none";
    });
  }

  // Agregar un evento de cambio al select
  metodoPagoSelect.addEventListener("change", function () {
    var selectedValue = metodoPagoSelect.value;

    // Obtener una lista de elementos con la clase deseada
    var elementosOcultarMostrar = document.querySelectorAll(".esCredito");
    var elementOcultContado = document.querySelectorAll(".esContado");

    // Ocultar o mostrar los elementos según el método de pago seleccionado
    elementosOcultarMostrar.forEach(function (elemento) {
      if (selectedValue === "CONTADO") {
        elemento.style.display = "none";
      } else if (selectedValue === "CREDITO") {
        elemento.style.display = "block";
      }
    });

    elementOcultContado.forEach(function (elemento) {
        if (selectedValue === "CONTADO") {
          elemento.style.display = "block";
        } else if (selectedValue === "CREDITO") {
          elemento.style.display = "none";
        }
      });
  });

  // Obtener referencias a los elementos HTML
  var sumaTotalVentaInput = document.getElementById("sumaTotalVenta");
  var interesMensualInput = document.getElementById("interesMensual");
  var mesesPlazoInput = document.getElementById("mesesPlazo");
  var primaInput = document.getElementById("prima");
  var cuotaMensualInput = document.getElementById("cuotaMensual");
  var totalPagarInput = document.getElementById("totalPagar");
  var descuentoInput = document.getElementById("descuento");
  var pagoInput = document.getElementById("pago");
  var cambioInput = document.getElementById("cambio");
  var totalAPagar = document.getElementById("totalPagar");
  var prima = document.getElementById("prima");
  var interesMensual = document.getElementById("interesMensual");
  var mesesPlazo = document.getElementById("mesesPlazo");
  var cuotaMensual = document.getElementById("cuotaMensual");
  

  // Agregar un evento de cambio a los campos relevantes
  sumaTotalVentaInput.addEventListener("input", calcularCuotaMensual);
  interesMensualInput.addEventListener("input", calcularCuotaMensual);
  mesesPlazoInput.addEventListener("input", calcularCuotaMensual);
  primaInput.addEventListener("input", calcularCuotaMensual);
  //totalPagarInput.addEventListener("input", calcularTotalPago);
  sumaTotalVentaInput.addEventListener("input", calcularTotalPago);
  descuentoInput.addEventListener("input", calcularTotalPago);
  totalPagarInput.addEventListener("input", calcularCambio);
  pagoInput.addEventListener("input", calcularCambio);

  // Función para calcular la cuota mensual
  function calcularCuotaMensual() {
    let totalVenta = parseFloat(sumaTotalVentaInput.value);
    let interesMensual = parseFloat(interesMensualInput.value) / 100; // Convertir a decimal
    let mesesPlazo = parseFloat(mesesPlazoInput.value);
    let prima = parseFloat(primaInput.value);

    // Calcular el monto del préstamo (precio del producto - prima)
    let montoPrestamo = totalVenta - prima;

    // Calcular la cuota mensual
    if (
      !isNaN(totalVenta) &&
      !isNaN(interesMensual) &&
      !isNaN(mesesPlazo) &&
      !isNaN(prima)
    ) {
      let tasaInteresMensual = Math.pow(1 + interesMensual, mesesPlazo);
      let cuotaMensual =
        (montoPrestamo * interesMensual * tasaInteresMensual) /
        (tasaInteresMensual - 1);
      cuotaMensualInput.value = cuotaMensual.toFixed(2); // Mostrar dos decimales
    } else {
      cuotaMensualInput.value = ""; // Limpiar el campo si falta algún valor
    }
  }

  // Función para calcular el total a pagar
  function calcularTotalPago() {
    var sumaTotalVenta = parseFloat(sumaTotalVentaInput.value) || 0;
    var descuentoPorcentaje = parseFloat(descuentoInput.value) || 0;
    var descuento = (descuentoPorcentaje / 100) * sumaTotalVenta;
    var totalPagar = sumaTotalVenta - descuento;
  
    if(descuentoPorcentaje == 0){
        totalPagarInput.value = sumaTotalVenta;
    }else{
        totalPagarInput.value = totalPagar.toFixed(2);
    }
    
  }

  function calcularCambio(){
    let totalPago = parseFloat(totalPagarInput.value);
    let pago = parseFloat(pagoInput.value);
    let cambio = 0.00;
    
    if(pago >= totalPago){
        cambio = pago - totalPago;
        cambioInput.value = cambio.toFixed(2); // Mostrar dos decimales
        errorPago.textContent = "";
    } else {
        errorPago.textContent = "EL PAGO DEBE SER MAYOR AL TOTAL A PAGAR";
        cambioInput.value = "ERROR";
    }
    if(pago == 0){
        errorPago.textContent = "";
        cambioInput.value = "0.00";
    }
  }


  //autocomplete clientes
  $("#buscarCliente").autocomplete({
    source: function (request, response) {
      $.ajax({
        url: base_url + "clientes/buscar",
        dataType: "json",
        data: {
          term: request.term,
        },
        success: function (data) {
          response(data);
          if (data.length > 0) {
            errorCliente.textContent = "";
          } else {
            errorCliente.textContent = "NO HAY CLIENTE CON ESE NOMBRE";
          }
        },
      });
    },
    minLength: 2,
    select: function (event, ui) {
      telefonoCliente.value = ui.item.telefono;
      direccionCliente.value = ui.item.direccion;
      idCliente.value = ui.item.id;
    },
  });

  //completar venta
  btnGuardarVenta.addEventListener("click", function () {
    let filas = document.querySelectorAll("#tblNuevaVenta tr").length;
    if (filas < 2) {
      alertaPersonalizada("warning", "CARRITO VACIO");
      return;
    } else if (metodo.value == "") {
      alertaPersonalizada("warning", "EL METODO ES REQUERIDO");
      return;
    }else if (idCliente.value == "") {
      alertaPersonalizada("warning", "EL CLIENTE ES REQUERIDO");
      return;
    } else {
      const url = base_url + "ventas/registrarVenta";
      //hacer una instancia del objeto XMLHttpRequest
      const http = new XMLHttpRequest();
      //Abrir una Conexion - POST - GET
      http.open("POST", url, true);
      //Enviar Datos
      http.send(
        JSON.stringify({
          productos: listaDeProductos,
          idCliente: idCliente.value,
          idUsuario: idUsuario.value,
          metodo: metodo.value,
          descuento: descuento.value,
          serie: serie.value,
          pago: pago.value,
          totalAPagar: totalAPagar.value,
          prima: prima.value,
          interesMensual: interesMensual.value,
          mesesPlazo: mesesPlazo.value,
          cuotaMensual: cuotaMensual.value
          //impresion: impresion_directa.checked
        })
      );
      
      //verificar estados
      http.onreadystatechange = function () {
        alert(this.readyState +" -- "+ this.status);
        if (this.readyState == 4 && this.status == 200) {
          console.log(this.responseText);
          const res = JSON.parse(this.responseText);
          
          alert(this.responseText);
          //console.log(this.responseText);
          //alertaPersonalizada(res.type, res.msg);
          if (res.type == "success") {
            //localStorage.removeItem(nombreKey);//borrar
            setTimeout(() => {
              Swal.fire({
                title: "Desea Generar Reporte?",
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: "Ticked",
                denyButtonText: `Factura`,
              }).then((result) => {

                if (result.isConfirmed) {
                  const ruta =
                    base_url + "ventas/reporte/ticked/" + res.idVenta;
                  window.open(ruta, "_blank");
                } else if (result.isDenied) {
                  const ruta =
                    base_url + "ventas/reporte/factura/" + res.idVenta;
                  window.open(ruta, "_blank");
                }
                setTimeout(() => {
                  enviarComprobante(res.idVenta);
                }, 1500);
              });
            }, 2000);
          }
        }
      };
    }
  });

  //cargar datos con el plugin datatables
  tblHistorial = $("#tblHistorial").DataTable({
    ajax: {
      url: base_url + "ventas/listar",
      dataSrc: "",
    },
    columns: [
      { data: "serie" },
      { data: "fecha" },
      { data: "hora" },
      { data: "metodo" },
      { data: "descuento" },
      { data: "estado" },
      { data: "nombre" },
      { data: "total" },
      { data: "acciones" },
    ],
    language: {
      url: base_url + "assets/js/espanol.json",
    },
    dom,
    buttons,
    responsive: true,
    order: [[4, "desc"]],
  });

});

function verReporte(idVenta) {
  Swal.fire({
    title: "Desea Generar Reporte?",
    showDenyButton: true,
    showCancelButton: true,
    confirmButtonText: "Ticked",
    denyButtonText: `Factura`,
  }).then((result) => {
    /* Read more about isConfirmed, isDenied below */
    if (result.isConfirmed) {
      const ruta = base_url + "ventas/reporte/ticked/" + idVenta;
      window.open(ruta, "_blank");
    } else if (result.isDenied) {
      const ruta = base_url + "ventas/reporte/factura/" + idVenta;
      window.open(ruta, "_blank");
    }
  });
}

function anularVenta(idVenta) {
  Swal.fire({
    title: "Esta seguro de anular la venta?",
    text: "El stock de los productos cambiarán!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Anular!",
  }).then((result) => {
    if (result.isConfirmed) {
      const url = base_url + "ventas/anular/" + idVenta;
      //hacer una instancia del objeto XMLHttpRequest
      const http = new XMLHttpRequest();
      //Abrir una Conexion - POST - GET
      http.open("GET", url, true);
      //Enviar Datos
      http.send();
      //verificar estados
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          alertaPersonalizada(res.type, res.msg);
          if (res.type == "success") {
            tblHistorial.ajax.reload();
          }
        }
      };
    }
  });
}

function enviarComprobante(idVenta) {
  Swal.fire({
    title: "Enviar ticket de venta al correo?",
    text: "Asegurece de que existe el correo!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, enviar!",
  }).then((result) => {
    if (result.isConfirmed) {
      const url = base_url + "ventas/enviarCorreo/" + idVenta;
      //hacer una instancia del objeto XMLHttpRequest
      const http = new XMLHttpRequest();
      //Abrir una Conexion - POST - GET
      http.open("GET", url, true);
      //Enviar Datos
      http.send();
      //verificar estados
      http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
          const res = JSON.parse(this.responseText);
          alertaPersonalizada(res.type, res.msg);
          setTimeout(() => {
            window.location.reload();
          }, 2000);
        }
      };
    } else {
      window.location.reload();
    }
  });
}

/* NUEVO */
$("#buscarProductoNombreVenta, #buscarProductoCodigoVenta").autocomplete({
  source: function (request, response) {
    $.ajax({
      url: base_url + "ventas/buscarProdDispVentas",
      dataType: "json",
      data: {
        term: request.term,
      },
      success: function (data) {
        response(data);
        if (data.length > 0) {
          errorBusqueda.textContent = "";
        } else {
          errorBusqueda.textContent = "NO HAY PRODUCTO CON ESE NOMBRE O CÓDIGO";
        }
      },
    });
  },
  
  minLength: 2,
  select: function (event, ui) {
    llenartablaVentas(ui);
    inputBuscarCodigoVenta.innerHTML = '';
    /* inputBuscarNombreVenta.innerHTML = ui.item.id;
    inputBuscarNombreVenta.focus(); */
  },
  
});

function llenartablaVentas(ui) {
    // Agregar productos a listaDeProductos
    listaDeProductos.push({
      id: ui.item.id,
      codigo: ui.item.codigo,
      nombre: ui.item.label,
      cantidad: 0, //aquí se llenan los datos de la cantidad
      precio: ui.item.precio,
      subtotal: 0,
      disponibles: ui.item.disponibles,
      idCompra: ui.item.idCompra,
    });
  
    // Obtener el cuerpo de la tabla
    var tablaBody = $("#tblNuevaVenta tbody");
  
    // Obtener el índice del nuevo producto
    var nuevoIndex = listaDeProductos.length - 1;
  
    // Generar la nueva fila HTML para el producto
    var nuevaFila = `<tr id="fila_${nuevoIndex}">
      <td>${ui.item.label}</td>
      <td width="150">
        <input type="number" min="1" class="form-control inputPrecio" data-id="${nuevoIndex}" id="precio_${nuevoIndex}" value="${ui.item.precio}">
      </td>
      <td width="150">
        <input type="number" min="0" class="form-control inputCantidad" data-id="${nuevoIndex}" id="cantidad_${nuevoIndex}" value="0">
      </td>
      <td class="subtotal" id="subtotal_${nuevoIndex}">0.00</td>
      <td><button class="btn btn-danger btnEliminar" data-id="${nuevoIndex}" type="button"><i class="fas fa-trash"></i></button></td>
    </tr>`;
  
    // Agregar la nueva fila al cuerpo de la tabla
    tablaBody.append(nuevaFila);
  // Limpia los campos de entrada de código y nombre
  $("#buscarProductoCodigoVenta").val('');
  $("#buscarProductoNombreVenta").val('');
    // Llama a la función para calcular la suma total inicialmente
    calcularSumaTotalVenta();
  
    // Escuchar cambios en los campos de precio y cantidad para esta nueva fila
    $(`#precio_${nuevoIndex}, #cantidad_${nuevoIndex}`).on("input", function () {
      // Obtener el índice del producto desde el atributo data-id
      let index = $(this).data("id");
  
      // Obtener los valores de precio y cantidad
      let precio = parseFloat($("#precio_" + index).val());
      let cantidad = parseInt($("#cantidad_" + index).val());
      // Obtener la cantidad disponible del producto
      let cantidadDisponible = listaDeProductos[index].disponibles;

      //Validar el limite de compra segun la cantidad disponible
      if( cantidad > cantidadDisponible ){
        alertaPersonalizada("warning", "Cantidad máxima superada. La cantidad disponible es " + cantidadDisponible);
        // Restablecer el valor de cantidad a la cantidad disponible
        $("#cantidad_" + index).val(cantidadDisponible);
        cantidad = parseInt(cantidadDisponible);
      }

      // Calcular el subtotal
      var subtotal = precio * cantidad;
  
      // Actualizar el elemento HTML del subtotal
      $("#subtotal_" + index).text(subtotal.toFixed(2));
  
      // Llama a la función para recalcular la suma total
      calcularSumaTotalVenta();

      //asignandole los datos al array
      listaDeProductos[index].cantidad = cantidad;
    });
  
    // Función para calcular y mostrar la suma total
    function calcularSumaTotalVenta() {
      var sumaTotalVenta = 0;
      // Itera a través de todas las filas de la tabla
      $(".subtotal").each(function () {
        // Obtiene el valor del subtotal de cada fila y lo suma al total
        sumaTotalVenta += parseFloat($(this).text());
      });
  
      // Muestra la suma total en el campo "Venta Total"
      $("#sumaTotalVenta").val(sumaTotalVenta.toFixed(2)); 

      if($("#descuento").val() <= 0){
        $("#totalPagar").val(sumaTotalVenta.toFixed(2)); 
      }else{
        let sumaTotalVenta = parseFloat($("#sumaTotalVenta").val()) || 0;
        let descuentoPorcentaje = parseInt($("#descuento").val()) || 0;
        var descuento = (descuentoPorcentaje / 100) * sumaTotalVenta;
        var totalPagar = sumaTotalVenta - descuento;

      
        if(descuentoPorcentaje == 0){
            $("#totalPagar").val(sumaTotalVenta.toFixed(2));
        }else{
            $("#totalPagar").val(totalPagar.toFixed(2));
        }
      }
       //calcular cambio
       const errorPago = document.querySelector("#errorPago");
       let totalAPagar = parseFloat($("#totalPagar").val());
       let pago = parseFloat($("#pago").val());
       let cambio = 0.00;
       
       if(pago >= totalAPagar){
           cambio = pago - totalAPagar;

           $("#cambio").val(cambio.toFixed(2));
           errorPago.textContent = "";
       } else {
        if(pago > 0){
            errorPago.textContent = "EL PAGO DEBE SER MAYOR AL TOTAL A PAGAR";
            $("#cambio").val("ERROR");
        }
       }
       

    }

    // Escuchar clics en los botones de eliminar
    $(".btnEliminar").on("click", function () {
        // Obtener el índice del producto que se va a eliminar desde el atributo data-id
        var index = $(this).data("id");
    
        // Eliminar la fila correspondiente de la tabla
        $("#fila_" + index).remove();
    
        // Eliminar el producto correspondiente de la listaDeProductos (opcional)
        listaDeProductos.splice(index, 1);
    
        // Llama a la función para recalcular la suma total
        calcularSumaTotalVenta();
    });
  
}
  