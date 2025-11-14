<div class="container-fluid card" style="padding: 30px;">
    <div class="row g-3">
        <div class="col-md-12">
            <h3 id="producto_form_titulo">Nuevo Producto</h3>
        </div>
        <div class="col-md-12">
            <hr>
        </div>
        <input type="hidden" id="id_productos" value="0">
        <div class="col-md-6">
            <label for="producto_nombre_producto" class="form-label">Nombre Producto *</label>
            <input type="text" class="form-control" id="producto_nombre_producto" placeholder="Nombre del producto">
        </div>
        <div class="col-md-6">
            <label for="producto_costo" class="form-label">Costo *</label>
            <input type="number" class="form-control" id="producto_costo" placeholder="Costo" step="0.01" value="0">
        </div>
        <div class="col-md-6">
            <label for="producto_precio" class="form-label">Precio *</label>
            <input type="number" class="form-control" id="producto_precio" placeholder="Precio" step="0.01" value="0">
        </div>
        <div class="col-md-6">
            <label for="producto_id_categoria" class="form-label">Categoría</label>
            <input type="number" class="form-control" id="producto_id_categoria" placeholder="ID Categoría" value="0">
        </div>
        <div class="col-md-6">
            <label for="producto_id_tipo_producto" class="form-label">Tipo de Producto</label>
            <input type="number" class="form-control" id="producto_id_tipo_producto" placeholder="ID Tipo de Producto" value="0">
        </div>
        <div class="col-md-6">
            <label for="producto_estado" class="form-label">Estado *</label>
            <select id="producto_estado" class="form-select">
                <option value="ACTIVO">ACTIVO</option>
                <option value="INACTIVO">INACTIVO</option>
            </select>
        </div>
        <div class="col-md-12 text-end">
            <button class="btn btn-secondary" onclick="cancelarProducto(); return false;">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarProducto(); return false;">Guardar</button>
        </div>
    </div>
</div>
