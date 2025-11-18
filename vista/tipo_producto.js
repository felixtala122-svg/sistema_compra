function mostrarListaTiposProductos() {
    let contenido = dameContenido("paginas/referenciales/tipo_producto/listar.php");
    $("#contenido-principal").html(contenido);
    cargarTablaTiposProductos();
}

function mostrarAgregarTipoProducto() {
    let contenido = dameContenido("paginas/referenciales/tipo_producto/agregar.php");
    $("#contenido-principal").html(contenido);
}

function guardarTipoProducto() {
    if ($("#tipo_producto_nombre_tipo").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Debes ingresar el nombre del tipo de producto", "ATENCIÓN");
        return;
    }
    let cabecera = {
        nombre_tipo: $("#tipo_producto_nombre_tipo").val().trim(),
        descripcion: $("#tipo_producto_descripcion").val().trim(),
        estado: $("#tipo_producto_estado").val(),
    };
    if ($("#id_tipo_producto").val() === "0") {
        ejecutarAjax("controladores/tipo_producto.php", "guardar=" + JSON.stringify(cabecera));
        mensaje_confirmacion("Guardado correctamente", "Éxito");
    } else {
        cabecera = { ...cabecera, id_tipo_producto: $("#id_tipo_producto").val() };
        ejecutarAjax("controladores/tipo_producto.php", "actualizar=" + JSON.stringify(cabecera));
        mensaje_confirmacion("Actualizado correctamente", "Éxito");
    }
    mostrarListaTiposProductos();
}

function cargarTablaTiposProductos() {
    let datos = ejecutarAjax("controladores/tipo_producto.php", "listar=1");
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='5' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.id_tipo_producto}</td>`;
            fila += `<td>${item.nombre_tipo}</td>`;
            fila += `<td>${item.descripcion ? item.descripcion : ''}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-warning editar-tipo-producto'><i data-feather="edit"></i></button> `;
            fila += `<button class='btn btn-danger eliminar-tipo-producto'><i data-feather="trash"></i></button>`;
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#tipos_productos_tb").html(fila);
    feather.replace();
}

$(document).on("click", ".eliminar-tipo-producto", function () {
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
            ejecutarAjax("controladores/tipo_producto.php", "eliminar=" + id);
            mensaje_confirmacion("Eliminado correctamente", "Éxito");
            cargarTablaTiposProductos();
        }
    });
});

$(document).on("click", ".editar-tipo-producto", function () {
    let id = $(this).closest("tr").find("td:eq(0)").text();
    let response = ejecutarAjax("controladores/tipo_producto.php", "id=" + id);
    if (response === "0") {
        mensaje_dialogo_info_ERROR("No se pudo obtener el registro", "Error");
        return;
    }
    let json_registro = JSON.parse(response);
    let contenido = dameContenido("paginas/referenciales/tipo_producto/agregar.php");
    $("#contenido-principal").html(contenido);
    $("#tipo_producto_form_titulo").text("Editar Tipo de Producto");
    $("#id_tipo_producto").val(json_registro.id_tipo_producto);
    $("#tipo_producto_nombre_tipo").val(json_registro.nombre_tipo);
    $("#tipo_producto_descripcion").val(json_registro.descripcion);
    $("#tipo_producto_estado").val(json_registro.estado);
});

function cancelarTipoProducto() {
    mostrarListaTiposProductos();
}

$(document).on("keyup", "#b_tipo_producto", function () {
    let texto = $(this).val();
    if (texto.trim().length === 0) {
        cargarTablaTiposProductos();
        return;
    }
    let datos = ejecutarAjax("controladores/tipo_producto.php", "buscar=" + texto);
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='5' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.id_tipo_producto}</td>`;
            fila += `<td>${item.nombre_tipo}</td>`;
            fila += `<td>${item.descripcion ? item.descripcion : ''}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-warning editar-tipo-producto'><i data-feather="edit"></i></button> `;
            fila += `<button class='btn btn-danger eliminar-tipo-producto'><i data-feather="trash"></i></button>`;
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#tipos_productos_tb").html(fila);
    feather.replace();
});

function cargarListaTiposProductosActivos(componente) {
    let datos = ejecutarAjax("controladores/tipo_producto.php", "leer_activos=1");
    let option = "<option value='0'>Selecciona un Tipo de Producto</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.id_tipo_producto}'>${item.nombre_tipo}</option>`;
        });
    }
    $(componente).html(option);
}
