<div class="container-fluid card" style="padding: 30px;">
    <div class="row g-3">
        <div class="col-md-12">
            <h3 id="orden_compra_form_titulo">Nueva Orden de Compra</h3>
        </div>
        <div class="col-md-12">
            <hr>
        </div>
        <input type="hidden" id="id_orden_compra" value="0">
        <div class="col-md-6">
            <label for="orden_compra_fecha" class="form-label">Fecha *</label>
            <input type="date" class="form-control" id="orden_compra_fecha">
        </div>
        <div class="col-md-6">
            <label for="orden_compra_usuario" class="form-label">Usuario *</label>
            <select id="orden_compra_usuario" class="form-select">
                <option value="0">Selecciona un Usuario</option>
            </select>
        </div>
        <div class="col-md-12">
            <hr>
            <h5>Detalles de la Orden</h5>
        </div>
        <div class="col-md-6">
            <label for="orden_compra_producto" class="form-label">Producto</label>
            <select id="orden_compra_producto" class="form-select">
                <option value="0">Selecciona un Producto</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="orden_compra_cantidad" class="form-label">Cantidad</label>
            <input type="number" class="form-control" id="orden_compra_cantidad" placeholder="Cantidad" value="1" min="1">
        </div>
        <div class="col-md-3 align-self-end">
            <button class="btn btn-success w-100" onclick="agregarDetalleOrden(); return false;">Agregar Producto</button>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th class="text-end">Operaciones</th>
                        </tr>
                    </thead>
                    <tbody id="detalles_orden_tb"></tbody>
                </table>
            </div>
        </div>
        <div class="col-md-12 text-end">
            <button class="btn btn-secondary" onclick="cancelarOrdenCompra(); return false;">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarOrdenCompra(); return false;">Guardar</button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Establecer fecha actual
    let hoy = new Date();
    let fecha = hoy.toISOString().split('T')[0];
    $("#orden_compra_fecha").val(fecha);
    
    // Cargar usuarios y productos
    cargarListaUsuariosActivos('#orden_compra_usuario');
    cargarListaProductosActivos('#orden_compra_producto');
});
</script>