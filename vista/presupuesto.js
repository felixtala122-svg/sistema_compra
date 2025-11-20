function mostrarListaPresupuestos() {
    let contenido = dameContenido("paginas/movimientos/presupuesto/listar.php");
    $("#contenido-principal").html(contenido);
    cargarTablaPresupuestos();
}

function mostrarAgregarPresupuesto() {
    let contenido = dameContenido("paginas/movimientos/presupuesto/agregar.php");
    $("#contenido-principal").html(contenido);
}

function agregarDetallePresupuesto() {
    let id_producto = $("#presupuesto_producto").val();
    let cantidad = $("#presupuesto_cantidad").val();
    let precio_unitario = $("#presupuesto_precio").val();
    
    if (id_producto === "0" || id_producto.trim().length === 0) {
        mensaje_dialogo_info_ERROR("Debes seleccionar un producto", "ATENCIÓN");
        return;
    }
    
    if (!cantidad || cantidad <= 0) {
        mensaje_dialogo_info_ERROR("La cantidad debe ser mayor a 0", "ATENCIÓN");
        return;
    }
    
    if (!precio_unitario || precio_unitario < 0) {
        mensaje_dialogo_info_ERROR("El precio debe ser mayor o igual a 0", "ATENCIÓN");
        return;
    }

    let nombre_producto = $("#presupuesto_producto option:selected").text();
    let subtotal = parseFloat(cantidad) * parseFloat(precio_unitario);
    
    let fila = `<tr>`;
    fila += `<td><input type="hidden" class="producto_id" value="${id_producto}">${nombre_producto}</td>`;
    fila += `<td><input type="hidden" class="producto_cantidad" value="${cantidad}">${cantidad}</td>`;
    fila += `<td><input type="hidden" class="producto_precio" value="${precio_unitario}">${parseFloat(precio_unitario).toFixed(2)}</td>`;
    fila += `<td><input type="hidden" class="producto_subtotal" value="${subtotal}">${subtotal.toFixed(2)}</td>`;
    fila += `<td class='text-end'>`;
    fila += `<button class='btn btn-danger btn-sm eliminar-detalle-presupuesto-btn' type="button"><i data-feather="trash-2"></i></button>`;
    fila += `</td>`;
    fila += `</tr>`;
    
    $("#detalles_presupuesto_tb").append(fila);
    feather.replace();
    
    // Recalcular total
    calcularTotalPresupuesto();
    
    $("#presupuesto_producto").val("0");
    $("#presupuesto_cantidad").val("1");
    $("#presupuesto_precio").val("0.00");
}

function calcularTotalPresupuesto() {
    let total = 0;
    $("#detalles_presupuesto_tb tr").each(function() {
        let subtotal = $(this).find(".producto_subtotal").val();
        if (subtotal) {
            total += parseFloat(subtotal);
        }
    });
    $("#presupuesto_total").text(total.toFixed(2));
}

function guardarPresupuesto() {
    if ($("#presupuesto_usuario").val() === "0") {
        mensaje_dialogo_info_ERROR("Debes seleccionar un usuario", "ATENCIÓN");
        return;
    }
    
    if ($("#presupuesto_proveedor").val() === "0") {
        mensaje_dialogo_info_ERROR("Debes seleccionar un proveedor", "ATENCIÓN");
        return;
    }
    
    let detalles = [];
    $("#detalles_presupuesto_tb tr").each(function() {
        let id_producto = $(this).find(".producto_id").val();
        let cantidad = $(this).find(".producto_cantidad").val();
        let precio = $(this).find(".producto_precio").val();
        if (id_producto && cantidad && precio) {
            detalles.push({
                id_productos: id_producto,
                cantidad: cantidad,
                precio_unitario: precio
            });
        }
    });
    
    if (detalles.length === 0) {
        mensaje_dialogo_info_ERROR("Debes agregar al menos un detalle", "ATENCIÓN");
        return;
    }
    
    let cabecera = {
        fecha: $("#presupuesto_fecha").val(),
        id_usuario: $("#presupuesto_usuario").val(),
        id_proveedor: $("#presupuesto_proveedor").val(),
        estado: 'ACTIVO',
        id_orden_compra: $("#presupuesto_orden_compra").val() || null
    };
    
    let respuesta_cabecera = ejecutarAjax("controladores/presupuesto.php", "guardar=" + JSON.stringify(cabecera));
    
    try {
        let json_cabecera = JSON.parse(respuesta_cabecera);
        
        if (json_cabecera.error) {
            mensaje_dialogo_info_ERROR(json_cabecera.error, "Error al guardar presupuesto");
            return;
        }
        
        if (!json_cabecera.success || !json_cabecera.id_presupuesto) {
            mensaje_dialogo_info_ERROR("No se generó ID para el presupuesto", "Error");
            return;
        }
        
        let id_presupuesto = json_cabecera.id_presupuesto;
        console.log("CABECERA -> ID Presupuesto: " + id_presupuesto);
        
        $("#detalles_presupuesto_tb tr").each(function() {
            let id_producto = $(this).find(".producto_id").val();
            let cantidad = $(this).find(".producto_cantidad").val();
            let precio = $(this).find(".producto_precio").val();
            
            if (id_producto && cantidad && precio) {
                let detalle = {
                    id_presupuesto: id_presupuesto,
                    id_productos: id_producto,
                    cantidad: cantidad,
                    precio_unitario: precio
                };
                
                let respuesta_detalle = ejecutarAjax("controladores/detalle_presupuesto.php", "guardar=" + JSON.stringify(detalle));
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
        
        mensaje_confirmacion("Presupuesto guardado correctamente", "Éxito");
        mostrarListaPresupuestos();
        
    } catch (e) {
        console.error("Error al parsear cabecera:", respuesta_cabecera);
        mensaje_dialogo_info_ERROR("Error al procesar la respuesta del servidor", "Error");
    }
}

$(document).on("click", ".eliminar-detalle-presupuesto-btn", function () {
    $(this).closest("tr").remove();
    calcularTotalPresupuesto();
});

function cargarTablaPresupuestos() {
    let datos = ejecutarAjax("controladores/presupuesto.php", "listar=1");
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='7' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.id_presupuesto}</td>`;
            fila += `<td>${item.fecha}</td>`;
            fila += `<td>${item.nombre_usuario ? item.nombre_usuario : ''}</td>`;
            fila += `<td>${item.nombre_proveedor ? item.nombre_proveedor : ''}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td>${item.id_orden_compra ? item.id_orden_compra : '-'}</td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-info btn-sm ver-detalles-presupuesto' data-id='${item.id_presupuesto}'><i data-feather="eye"></i></button> `;
            fila += `<button class='btn btn-warning btn-sm imprimir-presupuesto' data-id='${item.id_presupuesto}'><i data-feather="printer"></i></button> `;
            if (item.estado === "ACTIVO") {
                fila += `<button class='btn btn-danger btn-sm anular-presupuesto' data-id='${item.id_presupuesto}'><i data-feather="x-circle"></i></button>`;
            }
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#presupuestos_tb").html(fila);
    feather.replace();
}

$(document).on("click", ".ver-detalles-presupuesto", function () {
    let id = $(this).data("id");
    let datos = ejecutarAjax("controladores/presupuesto.php", "obtener_detalles=" + id);
    
    if (datos === "0") {
        mensaje_dialogo_info_ERROR("No hay detalles para este presupuesto", "Información");
        return;
    }
    
    let json_datos = JSON.parse(datos);
    let detalles_html = "<table class='table table-sm'><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio Unit.</th><th>Subtotal</th></tr></thead><tbody>";
    
    let total = 0;
    json_datos.forEach(function(item) {
        let subtotal = parseFloat(item.cantidad) * parseFloat(item.precio_unitario);
        total += subtotal;
        detalles_html += `<tr><td>${item.nombre_producto}</td><td>${item.cantidad}</td><td>${parseFloat(item.precio_unitario).toFixed(2)}</td><td>${subtotal.toFixed(2)}</td></tr>`;
    });
    
    detalles_html += `<tr class='table-active'><td colspan='3'><strong>TOTAL</strong></td><td><strong>${total.toFixed(2)}</strong></td></tr>`;
    detalles_html += "</tbody></table>";
    
    Swal.fire({
        title: 'Detalles del Presupuesto #' + id,
        html: detalles_html,
        icon: 'info',
        confirmButtonText: 'Cerrar'
    });
});

$(document).on("click", ".anular-presupuesto", function () {
    let id = $(this).data("id");
    Swal.fire({
        title: 'Anular Presupuesto?',
        text: "¿Desea anular este presupuesto?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Anular'
    }).then((result) => {
        if (result.isConfirmed) {
            ejecutarAjax("controladores/presupuesto.php", "anular=" + id);
            mensaje_confirmacion("Presupuesto anulado correctamente", "Éxito");
            cargarTablaPresupuestos();
        }
    });
});

$(document).on("click", ".imprimir-presupuesto", function () {
    let id = $(this).data("id");
    if (!id) {
        mensaje_dialogo_info_ERROR("Debes seleccionar un presupuesto para imprimir", "Atención");
        return;
    }
    window.open("paginas/movimientos/presupuesto/print.php?id=" + id, "_blank");
});

function cancelarPresupuesto() {
    mostrarListaPresupuestos();
}

$(document).on("keyup", "#b_presupuesto", function () {
    let texto = $(this).val();
    if (texto.trim().length === 0) {
        cargarTablaPresupuestos();
        return;
    }
    let datos = ejecutarAjax("controladores/presupuesto.php", "buscar=" + texto);
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='7' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.id_presupuesto}</td>`;
            fila += `<td>${item.fecha}</td>`;
            fila += `<td>${item.nombre_usuario ? item.nombre_usuario : ''}</td>`;
            fila += `<td>${item.nombre_proveedor ? item.nombre_proveedor : ''}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td>${item.id_orden_compra ? item.id_orden_compra : '-'}</td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-info btn-sm ver-detalles-presupuesto' data-id='${item.id_presupuesto}'><i data-feather="eye"></i></button> `;
            fila += `<button class='btn btn-warning btn-sm imprimir-presupuesto' data-id='${item.id_presupuesto}'><i data-feather="printer"></i></button> `;
            if (item.estado === "ACTIVO") {
                fila += `<button class='btn btn-danger btn-sm anular-presupuesto' data-id='${item.id_presupuesto}'><i data-feather="x-circle"></i></button>`;
            }
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#presupuestos_tb").html(fila);
    feather.replace();
});