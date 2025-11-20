<div class="container-fluid card" style="padding: 30px;">
	<div class="row g-3">
		<div class="col-md-12">
			<h3 id="comparador_form_titulo">Nuevo Comparador de Presupuesto</h3>
		</div>
		<div class="col-md-12">
			<hr>
		</div>
		<input type="hidden" id="id_comparador" value="0">
		<div class="col-md-6">
			<label for="comparador_fecha" class="form-label">Fecha *</label>
			<input type="date" class="form-control" id="comparador_fecha">
		</div>
		<div class="col-md-6">
			<label for="comparador_usuario" class="form-label">Usuario *</label>
			<select id="comparador_usuario" class="form-select">
				<option value="0">Selecciona un Usuario</option>
			</select>
		</div>
		<div class="col-md-12">
			<hr>
			<h5>Detalles del Comparador</h5>
		</div>
		<div class="col-md-6">
			<label for="comparador_producto" class="form-label">Producto</label>
			<select id="comparador_producto" class="form-select">
				<option value="0">Selecciona un Producto</option>
			</select>
		</div>
		<div class="col-md-3">
			<label for="comparador_cantidad" class="form-label">Cantidad</label>
			<input type="number" class="form-control" id="comparador_cantidad" placeholder="Cantidad" value="1" min="1">
		</div>
		<div class="col-md-3 align-self-end">
			<button class="btn btn-success w-100" onclick="agregarDetalleComparador(); return false;">Agregar Producto</button>
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
					<tbody id="detalles_comparador_tb"></tbody>
				</table>
			</div>
		</div>
		<div class="col-md-12 text-end">
			<button class="btn btn-secondary" onclick="cancelarComparador(); return false;">Cancelar</button>
			<button class="btn btn-primary" onclick="guardarComparador(); return false;">Guardar</button>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	let hoy = new Date();
	let fecha = hoy.toISOString().split('T')[0];
	$("#comparador_fecha").val(fecha);
	cargarListaUsuariosActivos('#comparador_usuario');
	cargarListaProductosActivos('#comparador_producto');
});
</script>
