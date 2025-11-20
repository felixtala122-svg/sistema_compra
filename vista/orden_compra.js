function mostrarListaOrdenesCompra() {
    let contenido = dameContenido("paginas/movimientos/orden_compra/listar.php");
    $("#contenido-principal").html(contenido);
    cargarTablaOrdenesCompra();
}

function mostrarAgregarOrdenCompra() {
    let contenido = dameContenido("paginas/movimientos/orden_compra/agregar.php");
    $("#contenido-principal").html(contenido);
}

function agregarDetalleOrden() {
    let id_producto = $("#orden_compra_producto").val();
    let cantidad = $("#orden_compra_cantidad").val();
    
    if (id_producto === "0" || id_producto.trim().length === 0) {
        mensaje_dialogo_info_ERROR("Debes seleccionar un producto", "ATENCIÓN");
        return;
    }
    
    if (!cantidad || cantidad <= 0) {
        mensaje_dialogo_info_ERROR("La cantidad debe ser mayor a 0", "ATENCIÓN");
        return;
    }
    
    let nombre_producto = $("#orden_compra_producto option:selected").text();
    let fila = `<tr>`;
    fila += `<td><input type="hidden" class="producto_id" value="${id_producto}">${nombre_producto}</td>`;
    fila += `<td><input type="hidden" class="producto_cantidad" value="${cantidad}">${cantidad}</td>`;
    fila += `<td class='text-end'>`;
    fila += `<button class='btn btn-danger btn-sm eliminar-detalle-orden-btn' type="button"><i data-feather="trash-2"></i></button>`;
    fila += `</td>`;
    fila += `</tr>`;
    
    $("#detalles_orden_tb").append(fila);
    feather.replace();
    
    $("#orden_compra_producto").val("0");
    $("#orden_compra_cantidad").val("1");
}

function guardarOrdenCompra() {
    if ($("#orden_compra_usuario").val() === "0") {
        mensaje_dialogo_info_ERROR("Debes seleccionar un usuario", "ATENCIÓN");
        return;
    }
    
    let detalles = [];
    $("#detalles_orden_tb tr").each(function() {
        let id_producto = $(this).find(".producto_id").val();
        let cantidad = $(this).find(".producto_cantidad").val();
        if (id_producto && cantidad) {
            detalles.push({
                id_productos: id_producto,
                cantidad: cantidad
            });
        }
    });
    
    if (detalles.length === 0) {
        mensaje_dialogo_info_ERROR("Debes agregar al menos un detalle", "ATENCIÓN");
        return;
    }
    
    let cabecera = {
        fecha_orden: $("#orden_compra_fecha").val(),
        id_usuario: $("#orden_compra_usuario").val(),
        estado: 'ACTIVO'
    };
    
    let respuesta_cabecera = ejecutarAjax("controladores/orden_compra.php", "guardar=" + JSON.stringify(cabecera));
    
    try {
        let json_cabecera = JSON.parse(respuesta_cabecera);
        
        if (json_cabecera.error) {
            mensaje_dialogo_info_ERROR(json_cabecera.error, "Error al guardar orden");
            return;
        }
        
        if (!json_cabecera.success || !json_cabecera.id_orden) {
            mensaje_dialogo_info_ERROR("No se generó ID para la orden", "Error");
            return;
        }
        
        let id_orden = json_cabecera.id_orden;
        console.log("CABECERA -> ID Orden: " + id_orden);
        
        $("#detalles_orden_tb tr").each(function() {
            let id_producto = $(this).find(".producto_id").val();
            let cantidad = $(this).find(".producto_cantidad").val();
            
            if (id_producto && cantidad) {
                let detalle = {
                    orden_compra: id_orden,
                    id_productos: id_producto,
                    cantidad: cantidad
                };
                
                let respuesta_detalle = ejecutarAjax("controladores/detalle_orden.php", "guardar=" + JSON.stringify(detalle));
                console.log("DETALLE -> " + respuesta_detalle);
                
                try {
                    let json_detalle = JSON.parse(respuesta_detalle);
                    if (json_detalle.error) {
                        console.error("Error en detalle:", json_detalle.error);
                    }
                } catch (e) {
                    console.error("Error al parsear detalle:", respuesta_detalle);
                }
            }
        });
        
        mensaje_confirmacion("Orden guardada correctamente", "Éxito");
        mostrarListaOrdenesCompra();
        
    } catch (e) {
        console.error("Error al parsear cabecera:", respuesta_cabecera);
        mensaje_dialogo_info_ERROR("Error al procesar la respuesta del servidor", "Error");
    }
}

$(document).on("click", ".eliminar-detalle-orden-btn", function () {
    $(this).closest("tr").remove();
});

function cargarTablaOrdenesCompra() {
    let datos = ejecutarAjax("controladores/orden_compra.php", "listar=1");
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='5' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.orden_compra}</td>`;
            fila += `<td>${item.fecha_orden}</td>`;
            fila += `<td>${item.nombre_usuario ? item.nombre_usuario : ''}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-info btn-sm ver-detalles-orden' data-id='${item.orden_compra}'><i data-feather="eye"></i></button> `;
            fila += `<button class='btn btn-warning btn-sm imprimir-orden' data-id='${item.orden_compra}'><i data-feather="printer"></i></button> `;
            if (item.estado === "ACTIVO") {
                fila += `<button class='btn btn-danger btn-sm anular-orden' data-id='${item.orden_compra}'><i data-feather="x-circle"></i></button>`;
            }
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#ordenes_compra_tb").html(fila);
    feather.replace();
}

$(document).on("click", ".ver-detalles-orden", function () {
    let id = $(this).data("id");
    let datos = ejecutarAjax("controladores/orden_compra.php", "obtener_detalles=" + id);
    
    if (datos === "0") {
        mensaje_dialogo_info_ERROR("No hay detalles para esta orden", "Información");
        return;
    }
    
    let json_datos = JSON.parse(datos);
    let detalles_html = "<table class='table table-sm'><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th></tr></thead><tbody>";
    
    json_datos.forEach(function(item) {
        detalles_html += `<tr><td>${item.nombre_producto}</td><td>${item.cantidad}</td><td>${item.precio}</td></tr>`;
    });
    
    detalles_html += "</tbody></table>";
    
    Swal.fire({
        title: 'Detalles de la Orden #' + id,
        html: detalles_html,
        icon: 'info',
        confirmButtonText: 'Cerrar'
    });
});

$(document).on("click", ".anular-orden", function () {
    let id = $(this).data("id");
    Swal.fire({
        title: 'Anular Orden?',
        text: "¿Desea anular esta orden de compra?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Anular'
    }).then((result) => {
        if (result.isConfirmed) {
            ejecutarAjax("controladores/orden_compra.php", "anular=" + id);
            mensaje_confirmacion("Orden anulada correctamente", "Éxito");
            cargarTablaOrdenesCompra();
        }
    });
});

$(document).on("click", ".imprimir-orden", function () {
    let id = $(this).data("id");
    if (!id) {
        mensaje_dialogo_info_ERROR("Debes seleccionar una orden para imprimir", "Atención");
        return;
    }
    window.open("paginas/movimientos/orden_compra/print.php?id=" + id, "_blank");
});

function cancelarOrdenCompra() {
    mostrarListaOrdenesCompra();
}

$(document).on("keyup", "#b_orden_compra", function () {
    let texto = $(this).val();
    if (texto.trim().length === 0) {
        cargarTablaOrdenesCompra();
        return;
    }
    let datos = ejecutarAjax("controladores/orden_compra.php", "buscar=" + texto);
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='5' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.orden_compra}</td>`;
            fila += `<td>${item.fecha_orden}</td>`;
            fila += `<td>${item.nombre_usuario ? item.nombre_usuario : ''}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-info btn-sm ver-detalles-orden' data-id='${item.orden_compra}'><i data-feather="eye"></i></button> `;
            fila += `<button class='btn btn-warning btn-sm imprimir-orden' data-id='${item.orden_compra}'><i data-feather="printer"></i></button> `;
            if (item.estado === "ACTIVO") {
                fila += `<button class='btn btn-danger btn-sm anular-orden' data-id='${item.orden_compra}'><i data-feather="x-circle"></i></button>`;
            }
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#ordenes_compra_tb").html(fila);
    feather.replace();
});