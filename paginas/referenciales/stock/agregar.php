<div class="container-fluid card" style="padding: 30px;">
    <div class="row g-3">
        <div class="col-md-12">
            <h3 id="stock_form_titulo">Nuevo Stock</h3>
        </div>
        <div class="col-md-12">
            <hr>
        </div>
        <input type="hidden" id="id_stock" value="0">
        <div class="col-md-6">
            <label for="stock_id_productos" class="form-label">Producto *</label>
            <input type="number" class="form-control" id="stock_id_productos" placeholder="ID del Producto" value="0">
        </div>
        <div class="col-md-6">
            <label for="stock_cantidad_actual" class="form-label">Cantidad Actual *</label>
            <input type="number" class="form-control" id="stock_cantidad_actual" placeholder="Cantidad actual" value="0">
        </div>
        <div class="col-md-6">
            <label for="stock_cantidad_minima" class="form-label">Cantidad Mínima *</label>
            <input type="number" class="form-control" id="stock_cantidad_minima" placeholder="Cantidad mínima" value="0">
        </div>
        <div class="col-md-6">
            <label for="stock_estado" class="form-label">Estado *</label>
            <select id="stock_estado" class="form-select">
                <option value="ACTIVO">ACTIVO</option>
                <option value="INACTIVO">INACTIVO</option>
            </select>
        </div>
        <div class="col-md-12 text-end">
            <button class="btn btn-secondary" onclick="cancelarStock(); return false;">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarStock(); return false;">Guardar</button>
        </div>
    </div>
</div>
