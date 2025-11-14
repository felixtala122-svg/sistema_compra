<div class="container-fluid card" style="padding: 30px;">
    <div class="row g-3">
        <div class="col-md-12">
            <h3 id="ciudad_form_titulo">Nueva Ciudad</h3>
        </div>
        <div class="col-md-12">
            <hr>
        </div>
        <input type="hidden" id="id_ciudad" value="0">
        <div class="col-md-6">
            <label for="ciudad_nombre" class="form-label">Nombre *</label>
            <input type="text" class="form-control" id="ciudad_nombre" placeholder="Nombre de la ciudad">
        </div>
        <div class="col-md-6">
            <label for="ciudad_departamento" class="form-label">Departamento</label>
            <input type="text" class="form-control" id="ciudad_departamento" placeholder="Departamento">
        </div>
        <div class="col-md-6">
            <label for="ciudad_pais" class="form-label">País</label>
            <input type="text" class="form-control" id="ciudad_pais" placeholder="País">
        </div>
        <div class="col-md-6">
            <label for="ciudad_estado" class="form-label">Estado *</label>
            <select id="ciudad_estado" class="form-select">
                <option value="ACTIVO">ACTIVO</option>
                <option value="INACTIVO">INACTIVO</option>
            </select>
        </div>
        <div class="col-md-12">
            <label for="ciudad_direccion" class="form-label">Dirección</label>
            <textarea id="ciudad_direccion" class="form-control" rows="3" placeholder="Dirección"></textarea>
        </div>
        <div class="col-md-12 text-end">
            <button class="btn btn-secondary" onclick="cancelarCiudad(); return false;">Cancelar</button>
            <button class="btn btn-primary" onclick="guardarCiudad(); return false;">Guardar</button>
        </div>
    </div>
</div>
