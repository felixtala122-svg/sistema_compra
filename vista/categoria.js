function mostrarListaCategorias() {
    let contenido = dameContenido("paginas/referenciales/categoria/listar.php");
    $("#contenido-principal").html(contenido);
    cargarTablaCategorias();
}

function mostrarAgregarCategoria() {
    let contenido = dameContenido("paginas/referenciales/categoria/agregar.php");
    $("#contenido-principal").html(contenido);
}

function guardarCategoria() {
    if ($("#categoria_nombre_categoria").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Debes ingresar el nombre de la categoría", "ATENCIÓN");
        return;
    }
    let cabecera = {
        nombre_categoria: $("#categoria_nombre_categoria").val().trim(),
        descripcion: $("#categoria_descripcion").val().trim(),
        estado: $("#categoria_estado").val(),
    };
    if ($("#id_categoria").val() === "0") {
        ejecutarAjax("controladores/categoria.php", "guardar=" + JSON.stringify(cabecera));
        mensaje_confirmacion("Guardado correctamente", "Éxito");
    } else {
        cabecera = { ...cabecera, id_categoria: $("#id_categoria").val() };
        ejecutarAjax("controladores/categoria.php", "actualizar=" + JSON.stringify(cabecera));
        mensaje_confirmacion("Actualizado correctamente", "Éxito");
    }
    mostrarListaCategorias();
}

function cargarTablaCategorias() {
    let datos = ejecutarAjax("controladores/categoria.php", "listar=1");
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='5' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.id_categoria}</td>`;
            fila += `<td>${item.nombre_categoria}</td>`;
            fila += `<td>${item.descripcion ? item.descripcion : ''}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-warning editar-categoria'><i data-feather="edit"></i></button> `;
            fila += `<button class='btn btn-danger eliminar-categoria'><i data-feather="trash"></i></button>`;
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#categorias_tb").html(fila);
    feather.replace();
}

$(document).on("click", ".eliminar-categoria", function () {
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
            ejecutarAjax("controladores/categoria.php", "eliminar=" + id);
            mensaje_confirmacion("Eliminado correctamente", "Éxito");
            cargarTablaCategorias();
        }
    });
});

$(document).on("click", ".editar-categoria", function () {
    let id = $(this).closest("tr").find("td:eq(0)").text();
    let response = ejecutarAjax("controladores/categoria.php", "id=" + id);
    if (response === "0") {
        mensaje_dialogo_info_ERROR("No se pudo obtener el registro", "Error");
        return;
    }
    let json_registro = JSON.parse(response);
    let contenido = dameContenido("paginas/referenciales/categoria/agregar.php");
    $("#contenido-principal").html(contenido);
    $("#categoria_form_titulo").text("Editar Categoría");
    $("#id_categoria").val(json_registro.id_categoria);
    $("#categoria_nombre_categoria").val(json_registro.nombre_categoria);
    $("#categoria_descripcion").val(json_registro.descripcion);
    $("#categoria_estado").val(json_registro.estado);
});

function cancelarCategoria() {
    mostrarListaCategorias();
}

$(document).on("keyup", "#b_categoria", function () {
    let texto = $(this).val();
    if (texto.trim().length === 0) {
        cargarTablaCategorias();
        return;
    }
    let datos = ejecutarAjax("controladores/categoria.php", "buscar=" + texto);
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='5' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.id_categoria}</td>`;
            fila += `<td>${item.nombre_categoria}</td>`;
            fila += `<td>${item.descripcion ? item.descripcion : ''}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-warning editar-categoria'><i data-feather="edit"></i></button> `;
            fila += `<button class='btn btn-danger eliminar-categoria'><i data-feather="trash"></i></button>`;
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#categorias_tb").html(fila);
    feather.replace();
});

function cargarListaCategoriasActivos(componente) {
    let datos = ejecutarAjax("controladores/categoria.php", "leer_activos=1");
    let option = "<option value='0'>Selecciona una Categoría</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.id_categoria}'>${item.nombre_categoria}</option>`;
        });
    }
    $(componente).html(option);
}
