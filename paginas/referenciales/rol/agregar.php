<div class="container-fluid card" style="padding: 30px;">
    <div class="row g-3">
        <div class="col-md-12">
            <h3 id="rol_form_titulo">Nuevo Rol</h3>
        </div>
        <div class="col-md-12">
            <hr>
        </div>
        <input type="hidden" id="id_rol" value="0">
        <div class="col-md-6">
            <label for="rol_descripcion" class="form-label">Descripción *</label>
            <input type="text" class="form-control" id="rol_descripcion" placeholder="Descripción del rol">
        </div>
        <div class="col-md-6">
            <label for="rol_estado" class="form-label">Estado *</label>
            <select id="rol_estado" class="form-select">
                <option value="ACTIVO">ACTIVO</option>
                <option value="INACTIVO">INACTIVO</option>
            </select>
        </div>
        <div class="col-md-12 text-end">
            <button class="btn btn-secondary" onclick="cancelarRol(); return false;">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarRol(); return false;">Guardar</button>
        </div>
    </div>
</div>
