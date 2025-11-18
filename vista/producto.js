function mostrarListaProductos() {
    let contenido = dameContenido("paginas/referenciales/producto/listar.php");
    $("#contenido-principal").html(contenido);
    cargarTablaProductos();
}

function mostrarAgregarProducto() {
    let contenido = dameContenido("paginas/referenciales/producto/agregar.php");
    $("#contenido-principal").html(contenido);
}

function guardarProducto() {
    if ($("#producto_nombre_producto").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Debes ingresar el nombre del producto", "ATENCIÓN");
        return;
    }
    let cabecera = {
        nombre_producto: $("#producto_nombre_producto").val().trim(),
        costo: $("#producto_costo").val(),
        precio: $("#producto_precio").val(),
        estado: $("#producto_estado").val(),
        id_categoria: $("#producto_id_categoria").val(),
        id_tipo_producto: $("#producto_id_tipo_producto").val(),
    };
    if ($("#id_productos").val() === "0") {
        ejecutarAjax("controladores/producto.php", "guardar=" + JSON.stringify(cabecera));
        mensaje_confirmacion("Guardado correctamente", "Éxito");
    } else {
        cabecera = { ...cabecera, id_productos: $("#id_productos").val() };
        ejecutarAjax("controladores/producto.php", "actualizar=" + JSON.stringify(cabecera));
        mensaje_confirmacion("Actualizado correctamente", "Éxito");
    }
    mostrarListaProductos();
}

function cargarTablaProductos() {
    let datos = ejecutarAjax("controladores/producto.php", "listar=1");
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='8' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.id_productos}</td>`;
            fila += `<td>${item.nombre_producto}</td>`;
            fila += `<td>${item.costo}</td>`;
            fila += `<td>${item.precio}</td>`;
            fila += `<td>${item.id_categoria}</td>`;
            fila += `<td>${item.id_tipo_producto}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-warning editar-producto'><i data-feather="edit"></i></button> `;
            fila += `<button class='btn btn-danger eliminar-producto'><i data-feather="trash"></i></button>`;
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#productos_tb").html(fila);
    feather.replace();
}

$(document).on("click", ".eliminar-producto", function () {
    let id = $(this).closest("tr").find("td:eq(0)").text();
    Swal.fire({
        title: 'Estas seguro?',
        text: "Desea eliminar esta registro?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        cancelButtonText: 'No',
        confirmButtonText: 'Si'
    }).then((result) => {
        if (result.isConfirmed) {
            ejecutarAjax("controladores/producto.php", "eliminar=" + id);
            mensaje_confirmacion("Eliminado correctamente", "Éxito");
            cargarTablaProductos();
        }
    });
});

$(document).on("click", ".editar-producto", function () {
    let id = $(this).closest("tr").find("td:eq(0)").text();
    let response = ejecutarAjax("controladores/producto.php", "id=" + id);
    if (response === "0") {
        mensaje_dialogo_info_ERROR("No se pudo obtener el registro", "Error");
        return;
    }
    let json_registro = JSON.parse(response);
    let contenido = dameContenido("paginas/referenciales/producto/agregar.php");
    $("#contenido-principal").html(contenido);
    $("#producto_form_titulo").text("Editar Producto");
    $("#id_productos").val(json_registro.id_productos);
    $("#producto_nombre_producto").val(json_registro.nombre_producto);
    $("#producto_costo").val(json_registro.costo);
    $("#producto_precio").val(json_registro.precio);
    
    // Cargar listas desplegables
    cargarListaCategoriasActivos('#producto_id_categoria');
    cargarListaTiposProductosActivos('#producto_id_tipo_producto');
    
    // Establecer valores después de cargar
    setTimeout(function(){
        $("#producto_id_categoria").val(json_registro.id_categoria);
        $("#producto_id_tipo_producto").val(json_registro.id_tipo_producto);
    }, 300);
    
    $("#producto_estado").val(json_registro.estado);
});

function cancelarProducto() {
    mostrarListaProductos();
}

$(document).on("keyup", "#b_producto", function () {
    let texto = $(this).val();
    if (texto.trim().length === 0) {
        cargarTablaProductos();
        return;
    }
    let datos = ejecutarAjax("controladores/producto.php", "buscar=" + texto);
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='8' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.id_productos}</td>`;
            fila += `<td>${item.nombre_producto}</td>`;
            fila += `<td>${item.costo}</td>`;
            fila += `<td>${item.precio}</td>`;
            fila += `<td>${item.id_categoria}</td>`;
            fila += `<td>${item.id_tipo_producto}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-warning editar-producto'><i data-feather="edit"></i></button> `;
            fila += `<button class='btn btn-danger eliminar-producto'><i data-feather="trash"></i></button>`;
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#productos_tb").html(fila);
    feather.replace();
});

function cargarListaProductosActivos(componente) {
    let datos = ejecutarAjax("controladores/producto.php", "leer_activos=1");
    let option = "<option value='0'>Selecciona un Producto</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.id_productos}'>${item.nombre_producto}</option>`;
        });
    }
    $(componente).html(option);
}
