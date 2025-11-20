function mostrarListaComparador() {
    let contenido = dameContenido("paginas/movimientos/comparador_presupuesto/listar.php");
    $("#contenido-principal").html(contenido);
    cargarTablaComparador();
}

function mostrarAgregarComparador() {
    let contenido = dameContenido("paginas/movimientos/comparador_presupuesto/agregar.php");
    $("#contenido-principal").html(contenido);
}

function agregarDetalleComparador() {
    let id_producto = $("#comparador_producto").val();
    let cantidad = $("#comparador_cantidad").val();
    
    if (id_producto === "0" || id_producto.trim().length === 0) {
        mensaje_dialogo_info_ERROR("Debes seleccionar un producto", "ATENCIÓN");
        return;
    }
    
    if (!cantidad || cantidad <= 0) {
        mensaje_dialogo_info_ERROR("La cantidad debe ser mayor a 0", "ATENCIÓN");
        return;
    }
    
    let nombre_producto = $("#comparador_producto option:selected").text();
    let fila = `<tr>`;
    fila += `<td><input type="hidden" class="producto_id" value="${id_producto}">${nombre_producto}</td>`;
    fila += `<td><input type="hidden" class="producto_cantidad" value="${cantidad}">${cantidad}</td>`;
    fila += `<td class='text-end'>`;
    fila += `<button class='btn btn-danger btn-sm eliminar-detalle-comp-btn' type="button"><i data-feather="trash-2"></i></button>`;
    fila += `</td>`;
    fila += `</tr>`;
    
    $("#detalles_comparador_tb").append(fila);
    feather.replace();
    
    $("#comparador_producto").val("0");
    $("#comparador_cantidad").val("1");
}

function guardarComparador() {
    if ($("#comparador_usuario").val() === "0") {
        mensaje_dialogo_info_ERROR("Debes seleccionar un usuario", "ATENCIÓN");
        return;
    }
    
    let detalles = [];
    $("#detalles_comparador_tb tr").each(function() {
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
        fecha_comparacion: $("#comparador_fecha").val(),
        id_usuario: $("#comparador_usuario").val(),
        estado: 'ACTIVO'
    };
    
    let respuesta_cabecera = ejecutarAjax("controladores/comparador_presupuesto.php", "guardar=" + JSON.stringify(cabecera));
    
    try {
        let json_cabecera = JSON.parse(respuesta_cabecera);
        
        if (json_cabecera.error) {
            mensaje_dialogo_info_ERROR(json_cabecera.error, "Error al guardar comparador");
            return;
        }
        
        if (!json_cabecera.success || !json_cabecera.id_comparador) {
            mensaje_dialogo_info_ERROR("No se generó ID para el comparador", "Error");
            return;
        }
        
        let id_comparador = json_cabecera.id_comparador;
        console.log("CABECERA -> ID Comparador: " + id_comparador);
        
        $("#detalles_comparador_tb tr").each(function() {
            let id_producto = $(this).find(".producto_id").val();
            let cantidad = $(this).find(".producto_cantidad").val();
            
            if (id_producto && cantidad) {
                let detalle = {
                    comparador_presupuesto: id_comparador,
                    id_productos: id_producto,
                    cantidad: cantidad
                };
                
                let respuesta_detalle = ejecutarAjax("controladores/detalle_comparador.php", "guardar=" + JSON.stringify(detalle));
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
        
        mensaje_confirmacion("Comparador guardado correctamente", "Éxito");
        mostrarListaComparador();
        
    } catch (e) {
        console.error("Error al parsear cabecera:", respuesta_cabecera);
        mensaje_dialogo_info_ERROR("Error al procesar la respuesta del servidor", "Error");
    }
}

$(document).on("click", ".eliminar-detalle-comp-btn", function () {
    $(this).closest("tr").remove();
});

function cargarTablaComparador() {
    let datos = ejecutarAjax("controladores/comparador_presupuesto.php", "listar=1");
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='5' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.comparador_presupuesto}</td>`;
            fila += `<td>${item.fecha_comparacion}</td>`;
            fila += `<td>${item.nombre_usuario ? item.nombre_usuario : ''}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-info btn-sm ver-detalles-comparador' data-id='${item.comparador_presupuesto}'><i data-feather="eye"></i></button> `;
            fila += `<button class='btn btn-warning btn-sm imprimir-comparador' data-id='${item.comparador_presupuesto}'><i data-feather="printer"></i></button> `;
            if (item.estado === "ACTIVO") {
                fila += `<button class='btn btn-danger btn-sm anular-comparador' data-id='${item.comparador_presupuesto}'><i data-feather="x-circle"></i></button>`;
            }
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#comparador_tb").html(fila);
    feather.replace();
}

$(document).on("click", ".ver-detalles-comparador", function () {
    let id = $(this).data("id");
    let datos = ejecutarAjax("controladores/comparador_presupuesto.php", "obtener_detalles=" + id);
    
    if (datos === "0") {
        mensaje_dialogo_info_ERROR("No hay detalles para este comparador", "Información");
        return;
    }
    
    let json_datos = JSON.parse(datos);
    let detalles_html = "<table class='table table-sm'><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th></tr></thead><tbody>";
    
    json_datos.forEach(function(item) {
        let subtotal = (item.cantidad * item.precio).toFixed(2);
        detalles_html += `<tr><td>${item.nombre_producto}</td><td>${item.cantidad}</td><td>${item.precio}</td></tr>`;
    });
    
    detalles_html += "</tbody></table>";
    
    Swal.fire({
        title: 'Detalles del Comparador #' + id,
        html: detalles_html,
        icon: 'info',
        confirmButtonText: 'Cerrar'
    });
});

$(document).on("click", ".anular-comparador", function () {
    let id = $(this).data("id");
    Swal.fire({
        title: 'Anular Comparador?',
        text: "¿Desea anular este comparador de presupuesto?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Anular'
    }).then((result) => {
        if (result.isConfirmed) {
            ejecutarAjax("controladores/comparador_presupuesto.php", "anular=" + id);
            mensaje_confirmacion("Comparador anulado correctamente", "Éxito");
            cargarTablaComparador();
        }
    });
});

$(document).on("click", ".imprimir-comparador", function () {
    let id = $(this).data("id");
    if (!id) {
        mensaje_dialogo_info_ERROR("Debes seleccionar un comparador para imprimir", "Atención");
        return;
    }
    window.open("paginas/movimientos/comparador_presupuesto/print.php?id=" + id, "_blank");
});

function cancelarComparador() {
    mostrarListaComparador();
}

$(document).on("keyup", "#b_comparador_presupuesto", function () {
    let texto = $(this).val();
    if (texto.trim().length === 0) {
        cargarTablaComparador();
        return;
    }
    let datos = ejecutarAjax("controladores/comparador_presupuesto.php", "buscar=" + texto);
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='5' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.comparador_presupuesto}</td>`;
            fila += `<td>${item.fecha_comparacion}</td>`;
            fila += `<td>${item.nombre_usuario ? item.nombre_usuario : ''}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-info btn-sm ver-detalles-comparador' data-id='${item.comparador_presupuesto}'><i data-feather="eye"></i></button> `;
            fila += `<button class='btn btn-warning btn-sm imprimir-comparador' data-id='${item.comparador_presupuesto}'><i data-feather="printer"></i></button> `;
            if (item.estado === "ACTIVO") {
                fila += `<button class='btn btn-danger btn-sm anular-comparador' data-id='${item.comparador_presupuesto}'><i data-feather="x-circle"></i></button>`;
            }
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#comparador_tb").html(fila);
    feather.replace();
});
