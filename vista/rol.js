function mostrarListaRoles() {
    let contenido = dameContenido("paginas/referenciales/rol/listar.php");
    $("#contenido-principal").html(contenido);
    cargarTablaRoles();
}

function mostrarAgregarRol() {
    let contenido = dameContenido("paginas/referenciales/rol/agregar.php");
    $("#contenido-principal").html(contenido);
}

function guardarRol() {
    if ($("#rol_descripcion").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Debes ingresar la descripción del rol", "ATENCIÓN");
        return;
    }
    let cabecera = {
        descripcion: $("#rol_descripcion").val().trim(),
        estado: $("#rol_estado").val(),
    };
    if ($("#id_rol").val() === "0") {
        ejecutarAjax("controladores/rol.php", "guardar=" + JSON.stringify(cabecera));
        mensaje_confirmacion("Guardado correctamente", "Éxito");
    } else {
        cabecera = { ...cabecera, id_rol: $("#id_rol").val() };
        ejecutarAjax("controladores/rol.php", "actualizar=" + JSON.stringify(cabecera));
        mensaje_confirmacion("Actualizado correctamente", "Éxito");
    }
    mostrarListaRoles();
}

function cargarTablaRoles() {
    let datos = ejecutarAjax("controladores/rol.php", "listar=1");
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='4' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.id_rol}</td>`;
            fila += `<td>${item.descripcion}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-warning editar-rol'><i data-feather="edit"></i></button> `;
            fila += `<button class='btn btn-danger eliminar-rol'><i data-feather="trash"></i></button>`;
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#roles_tb").html(fila);
    feather.replace();
}

$(document).on("click", ".eliminar-rol", function () {
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
            ejecutarAjax("controladores/rol.php", "eliminar=" + id);
            mensaje_confirmacion("Eliminado correctamente", "Éxito");
            cargarTablaRoles();
        }
    });
});

$(document).on("click", ".editar-rol", function () {
    let id = $(this).closest("tr").find("td:eq(0)").text();
    let response = ejecutarAjax("controladores/rol.php", "id=" + id);
    if (response === "0") {
        mensaje_dialogo_info_ERROR("No se pudo obtener el registro", "Error");
        return;
    }
    let json_registro = JSON.parse(response);
    let contenido = dameContenido("paginas/referenciales/rol/agregar.php");
    $("#contenido-principal").html(contenido);
    $("#rol_form_titulo").text("Editar Rol");
    $("#id_rol").val(json_registro.id_rol);
    $("#rol_descripcion").val(json_registro.descripcion);
    $("#rol_estado").val(json_registro.estado);
});

function cancelarRol() {
    mostrarListaRoles();
}

$(document).on("keyup", "#b_rol", function () {
    let texto = $(this).val();
    if (texto.trim().length === 0) {
        cargarTablaRoles();
        return;
    }
    let datos = ejecutarAjax("controladores/rol.php", "buscar=" + texto);
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='4' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.id_rol}</td>`;
            fila += `<td>${item.descripcion}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-warning editar-rol'><i data-feather="edit"></i></button> `;
            fila += `<button class='btn btn-danger eliminar-rol'><i data-feather="trash"></i></button>`;
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#roles_tb").html(fila);
    feather.replace();
});

function cargarListaRolesActivos(componente) {
    let datos = ejecutarAjax("controladores/rol.php", "leer_activos=1");
    let option = "<option value='0'>Selecciona un Rol</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.id_rol}'>${item.descripcion}</option>`;
        });
    }
    $(componente).html(option);
}
