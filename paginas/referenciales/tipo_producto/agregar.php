<div class="container-fluid card" style="padding: 30px;">
    <div class="row g-3">
        <div class="col-md-12">
            <h3 id="tipo_producto_form_titulo">Nuevo Tipo de Producto</h3>
        </div>
        <div class="col-md-12">
            <hr>
        </div>
        <input type="hidden" id="id_tipo_producto" value="0">
        <div class="col-md-6">
            <label for="tipo_producto_nombre_tipo" class="form-label">Nombre *</label>
            <input type="text" class="form-control" id="tipo_producto_nombre_tipo" placeholder="Nombre del tipo de producto">
        </div>
        <div class="col-md-6">
            <label for="tipo_producto_estado" class="form-label">Estado *</label>
            <select id="tipo_producto_estado" class="form-select">
                <option value="ACTIVO">ACTIVO</option>
                <option value="INACTIVO">INACTIVO</option>
            </select>
        </div>
        <div class="col-md-12">
            <label for="tipo_producto_descripcion" class="form-label">Descripción</label>
            <textarea id="tipo_producto_descripcion" class="form-control" rows="3" placeholder="Descripción del tipo de producto"></textarea>
        </div>
        <div class="col-md-12 text-end">
            <button class="btn btn-secondary" onclick="cancelarTipoProducto(); return false;">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarTipoProducto(); return false;">Guardar</button>
        </div>
    </div>
</div>
