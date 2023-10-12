let tblCreditos, tblAbonos;
var idCredito;
var idVenta;
var num_cuota;
var fechaActual;
const cliente = document.querySelector('#Cliente');
const telefonoCliente = document.querySelector('#telefonoCliente');
const direccionCliente = document.querySelector('#direccionCliente');
const abonado = document.querySelector('#abonado');
const restante = document.querySelector('#restante');
const fecha = document.querySelector('#fecha');
const abono_total = document.querySelector('#abono_total');
const cuota = document.querySelector('#cuota');
const mora= document.querySelector('#mora');
const monto_abonar=document.querySelector('#monto_abonar');
const cambio_abonar=document.querySelector('#cambio_abonar');
var caeMora=false;
const errorPago = document.querySelector("#errorPago");
const btnAccion = document.querySelector('#btnAccion');

//const nuevoAbono = document.querySelector('#nuevoAbono');
const modalAbono = new bootstrap.Modal('#modalAbono');
const errorCliente = document.querySelector('#errorCliente');

//para filtro por rango de fechas
const desde = document.querySelector('#desde');
const hasta = document.querySelector('#hasta');


//Mostra tabla de creditos
document.addEventListener('DOMContentLoaded', function(){
    //cargar datos con el plugin datatables
    tblCreditos = $('#tblCreditos').DataTable({
        ajax: {
            url: base_url + 'creditos/listar',
            dataSrc: ''
        },
        columns: [
            {class: "fecha", data: 'fecha' },
            { data: 'nombre' },            
            { data: 'venta' },
            { data: 'total' },
            { data: 'cuotas_totales' },
            { data: 'cuotas_pagadas' },
            { data: 'total_abonado' },
            { data: 'total_restante' },
            { data: 'estado' },
            { data: 'acciones' },
        ],
        language: {
            url: base_url + 'assets/js/espanol.json'
        },
        dom,
        buttons,
        responsive: true,
        order: [[5, 'desc']],
    });

/*     //autocomplete clientes
    $("#buscarCliente").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: base_url + 'creditos/buscar',
                dataType: "json",
                data: {
                    term: request.term
                },
                success: function (data) {
                    response(data);
                    if (data.length > 0) {
                        errorCliente.textContent = '';
                    } else {
                        errorCliente.textContent = 'NO HAY CLIENTE CON ESE NOMBRE';
                    }
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            telefonoCliente.value = ui.item.telefono;
            direccionCliente.innerHTML = ui.item.direccion;
            idCredito.value = ui.item.id;

            abonado.value = ui.item.abonado;
            restante.value = ui.item.restante;
            monto_total.value = ui.item.monto;
            fecha.value = ui.item.fecha;

            monto_abonar.focus();
        }
    }); */

    //levantar modal para agregar abono
/*     nuevoAbono.addEventListener('click', function(){
        idCredito.value = '';
        telefonoCliente.value = '';
        cliente.value = '';
        direccionCliente.innerHTML = '';
        abonado.value = '';
        restante.value = '';
        monto_total.value = '';
        fecha.value = '';
        monto_abonar.value = '';
        modalAbono.show();
    }) */

 /*    document.getElementById("nuevoAbono").addEventListener("click", function() {
        console.log("Entra a modal")
        var modal = document.getElementById("modalAbono");
      
        // Mostrar la modal
        modal.style.display = "block";
      
        // Si deseas permitir que la modal se cierre al hacer clic en el fondo oscuro:
        modal.addEventListener("click", function(e) {
          if (e.target === modal) {
            modal.style.display = "none";
          }
        });
      }); */



      //Registro de abonos
    btnAccion.addEventListener('click', function(){
        if (monto_abonar.value == '') {
            alertaPersonalizada('warning', 'INGRESE EL MONTO');
        }else{
            const url = base_url + 'creditos/registrarAbono';
            //hacer una instancia del objeto XMLHttpRequest 
            const http = new XMLHttpRequest();
            //Abrir una Conexion - POST - GET
            http.open('POST', url, true);
            //Enviar Datos
            http.send(JSON.stringify({
                
                numero: num_cuota+1,
                abono : monto_abonar.value,
                fecha: fechaActual,
                mora: caeMora,
                apertura: true,
                idCredito : idCredito,
                
            }));
  
            http.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    console.log("Respuesta",this.responseText)
                    const res = JSON.parse(this.responseText);
                    alertaPersonalizada(res.type, res.msg);
                    if (res.type == 'success') {
                        modalAbono.hide();
                        monto_abonar.value='';
                        //Verficar si el prestamo ya esta cancelado
                        $.ajax({
                            url: base_url + 'creditos/finalizar',
                            dataType: "json",
                            data: {
                                id_credito: idCredito,
                                id_venta: idVenta
                            },
                            success: function (data) {
                                console.log("Respuesta de credito: ", data)
                                if (data.length > 0) {
                                    alertaPersonalizada('success', 'CREDITO COMPLETADO');
                                } 
                            }
                        });
                        
                        tblCreditos.ajax.reload();
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
                                  base_url + "creditos/ticked/" + idCredito;
                                window.open(ruta, "_blank");
                              } else if (result.isDenied) {
                                const ruta = base_url + 'creditos/reporte/' + idCredito;
                                window.open(ruta, '_blank');
                              }
                              setTimeout(() => {
                                enviarComprobante(idCredito);
                              }, 1500);
                            });
                          }, 2000);
                    }
                }
            }
        }
    })



/*     //cargar datos con el plugin datatables
    tblAbonos = $('#tblAbonos').DataTable({
        ajax: {
            url: base_url + 'creditos/listarAbonos',
            dataSrc: ''
        },
        columns: [
            { data: 'fecha' },
            { data: 'abono' },
            { data: 'credito' }
        ],
        language: {
            url: base_url + 'assets/js/espanol.json'
        },
        dom,
        buttons,
        responsive: true,
        order: [[0, 'asc']],
    }); */

    //filtro rango de fechas
/*     desde.addEventListener('change', function () {
        tblCreditos.draw();
    })
    hasta.addEventListener('change', function () {
        tblCreditos.draw();
    })
 */
    //Valida el monto de pago y calcula el cambio
    monto_abonar.addEventListener('change', function () {
        let totalPago = parseFloat(abono_total.value);
        let pago = parseFloat(monto_abonar.value);
        let cambio = 0.00;
        
        if(pago >= totalPago){
            cambio = pago - totalPago;
            cambio_abonar.value = cambio.toFixed(2); // Mostrar dos decimales
            errorPago.textContent = "";
        } else {
            errorPago.textContent = "EL ABONO DEBE SER MAYOR AL TOTAL A ABONAR";
            cambio_abonar.value = "ERROR";
        }
        if(pago == 0){
            errorPago.textContent = "";
            cambio_abonar.value = "0.00";
        }
      })


      function enviarComprobante(idCredito) {
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
            const url = base_url + "creditos/enviarCorreo/" + idCredito;
            //hacer una instancia del objetosXMLHttpRequest
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


          //filtro rango de fechas


/*     $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var FilterStart = desde.value;
            var FilterEnd = hasta.value;
            var DataTableStart = data[0].trim();
            var DataTableEnd = data[0].trim();
            if (FilterStart == '' || FilterEnd == '') {
                return true;
            }
            if (DataTableStart >= FilterStart && DataTableEnd <= FilterEnd) {
                return true;
            } else {
                return false;
            }

        }); */
})

  function mostrarModal(data){
    this.idCredito =data.id_credito;
    this.idVenta=data.id_venta;
    this.num_cuota=data.cuotas_pagadas;
    telefonoCliente.value = data.telefono;
    cliente.value = data.nombre;
    direccionCliente.innerHTML = data.direccion;
    abonado.value = data.total_abonado;
    restante.value = data.total_restante;
    fechaActual = new Date();
    fechaActual.toLocaleString({ timeZone: "America/El_Salvador" });
    fechaActual.setDate(fechaActual.getDate() - 1);
    fecha.value=fechaActual.toISOString().slice(0, 10);;
    cuota.value = data.cuota;
    fechaActual=new Date('2023-11-21');
    //Calcular mora enviandole la fecha actual, de venta y el numero de cuota que se va cancelar
    this.calcularMora(fechaActual,data.fecha,(data.cuotas_pagadas +1),data.cuota);
    
    abono_total.value = (parseFloat(cuota.value) + parseFloat(mora.value)).toFixed(2);
    modalAbono.show();
  }

  function calcularMora(fechaActual, fechaVenta, numCuota, cuota) {
    // Suma 2 meses a la fecha de venta
    var fechaInicio = new Date(fechaVenta);

    fechaInicio.setMonth(fechaInicio.getMonth() + numCuota); // suma los meses segun la cuota a cancelar

    fechaFin=new Date(fechaInicio)
    fechaFin.setDate(fechaFin.getMonth() + numCuota);
    fechaFin.setDate(fechaFin.getDate() + 10); // Sumar 10 días mas para pago

    if (fechaActual > fechaFin) { // si la fecha actual se pasa de la fecha limite de pago entra a mora
        mora.value = (parseFloat(cuota) * 0.05).toFixed(2)
        this.caeMora=true;
    }else{ // Si es menor no cae en mora
        mora.value = (parseFloat(0)).toFixed(2);
    }

}

//Filtro de fechas
function filtroFechas(){
    
    const fechaDesde = new Date(($('#desde').val()));
    const fechaHasta = new Date(($('#hasta').val()));

    $('#tblCreditos tbody tr').each(function() {
      const fechaTexto = $(this).find('.fecha').text(); // Suponiendo que la fecha esté en una columna con clase 'fecha'
      const fecha = parseDate(fechaTexto);
      console.log("Fecha tabla: ", fecha)
      if (fecha >= fechaDesde && fecha <= fechaHasta) {
        $(this).show(); // Mostrar la fila
      } else {
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
  
  