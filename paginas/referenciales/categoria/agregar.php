<div class="container-fluid card" style="padding: 30px;">
    <div class="row g-3">
        <div class="col-md-12">
            <h3 id="categoria_form_titulo">Nueva Categoría</h3>
        </div>
        <div class="col-md-12">
            <hr>
        </div>
        <input type="hidden" id="id_categoria" value="0">
        <div class="col-md-6">
            <label for="categoria_nombre_categoria" class="form-label">Nombre *</label>
            <input type="text" class="form-control" id="categoria_nombre_categoria" placeholder="Nombre de la categoría">
        </div>
        <div class="col-md-6">
            <label for="categoria_estado" class="form-label">Estado *</label>
            <select id="categoria_estado" class="form-select">
                <option value="ACTIVO">ACTIVO</option>
                <option value="INACTIVO">INACTIVO</option>
            </select>
        </div>
        <div class="col-md-12">
            <label for="categoria_descripcion" class="form-label">Descripción</label>
            <textarea id="categoria_descripcion" class="form-control" rows="3" placeholder="Descripción de la categoría"></textarea>
        </div>
        <div class="col-md-12 text-end">
            <button class="btn btn-secondary" onclick="cancelarCategoria(); return false;">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarCategoria(); return false;">Guardar</button>
        </div>
    </div>
</div>
