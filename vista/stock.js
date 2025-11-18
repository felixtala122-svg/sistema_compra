function mostrarListaStock() {
    let contenido = dameContenido("paginas/referenciales/stock/listar.php");
    $("#contenido-principal").html(contenido);
    cargarTablaStock();
}

function mostrarAgregarStock() {
    let contenido = dameContenido("paginas/referenciales/stock/agregar.php");
    $("#contenido-principal").html(contenido);
}

function guardarStock() {
    if ($("#stock_id_productos").val() === "0" || $("#stock_id_productos").val().trim().length === 0) {
        mensaje_dialogo_info_ERROR("Debes ingresar el producto", "ATENCIÓN");
        return;
    }
    let cabecera = {
        cantidad_actual: $("#stock_cantidad_actual").val(),
        cantidad_minima: $("#stock_cantidad_minima").val(),
        id_productos: $("#stock_id_productos").val(),
        estado: $("#stock_estado").val(),
    };
    if ($("#id_stock").val() === "0") {
        ejecutarAjax("controladores/stock.php", "guardar=" + JSON.stringify(cabecera));
        mensaje_confirmacion("Guardado correctamente", "Éxito");
    } else {
        cabecera = { ...cabecera, id_stock: $("#id_stock").val() };
        ejecutarAjax("controladores/stock.php", "actualizar=" + JSON.stringify(cabecera));
        mensaje_confirmacion("Actualizado correctamente", "Éxito");
    }
    mostrarListaStock();
}

function cargarTablaStock() {
    let datos = ejecutarAjax("controladores/stock.php", "listar=1");
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='6' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.id_stock}</td>`;
            fila += `<td>${item.id_productos}</td>`;
            fila += `<td>${item.cantidad_actual}</td>`;
            fila += `<td>${item.cantidad_minima}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-warning editar-stock'><i data-feather="edit"></i></button> `;
            fila += `<button class='btn btn-danger eliminar-stock'><i data-feather="trash"></i></button>`;
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#stocks_tb").html(fila);
    feather.replace();
}

$(document).on("click", ".eliminar-stock", function () {
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
            ejecutarAjax("controladores/stock.php", "eliminar=" + id);
            mensaje_confirmacion("Eliminado correctamente", "Éxito");
            cargarTablaStock();
        }
    });
});

$(document).on("click", ".editar-stock", function () {
    let id = $(this).closest("tr").find("td:eq(0)").text();
    let response = ejecutarAjax("controladores/stock.php", "id=" + id);
    if (response === "0") {
        mensaje_dialogo_info_ERROR("No se pudo obtener el registro", "Error");
        return;
    }
    let json_registro = JSON.parse(response);
    let contenido = dameContenido("paginas/referenciales/stock/agregar.php");
    $("#contenido-principal").html(contenido);
    $("#stock_form_titulo").text("Editar Stock");
    $("#id_stock").val(json_registro.id_stock);
    $("#stock_id_productos").val(json_registro.id_productos);
    $("#stock_cantidad_actual").val(json_registro.cantidad_actual);
    $("#stock_cantidad_minima").val(json_registro.cantidad_minima);
    $("#stock_estado").val(json_registro.estado);
});

function cancelarStock() {
    mostrarListaStock();
}

$(document).on("keyup", "#b_stock", function () {
    let texto = $(this).val();
    if (texto.trim().length === 0) {
        cargarTablaStock();
        return;
    }
    let datos = ejecutarAjax("controladores/stock.php", "buscar=" + texto);
    let fila = "";
    if (datos === "0") {
        fila = `<tr><td colspan='6' class='text-center'>No hay registros</td></tr>`;
    } else {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            fila += `<tr>`;
            fila += `<td>${item.id_stock}</td>`;
            fila += `<td>${item.id_productos}</td>`;
            fila += `<td>${item.cantidad_actual}</td>`;
            fila += `<td>${item.cantidad_minima}</td>`;
            fila += `<td><span class="badge bg-${item.estado === "ACTIVO" ? "success" : "danger"}">${item.estado}</span></td>`;
            fila += `<td class='text-end'>`;
            fila += `<button class='btn btn-warning editar-stock'><i data-feather="edit"></i></button> `;
            fila += `<button class='btn btn-danger eliminar-stock'><i data-feather="trash"></i></button>`;
            fila += `</td>`;
            fila += `</tr>`;
        });
    }
    $("#stocks_tb").html(fila);
    feather.replace();
});

function cargarListaStockActivos(componente) {
    let datos = ejecutarAjax("controladores/stock.php", "leer_activos=1");
    let option = "<option value='0'>Selecciona un Stock</option>";
    if (datos !== "0") {
        let json_datos = JSON.parse(datos);
        json_datos.map(function (item) {
            option += `<option value='${item.id_stock}'>Stock ${item.id_stock} - Producto ${item.id_productos}</option>`;
        });
    }
    $(componente).html(option);
}
