<div class="card">
  <div class="card-header pb-0">
    <h5 class="card-title">Agregar Presupuesto</h5>
  </div>
  <div class="card-body">
    <form id="form_presupuesto">
      <div class="row">
        <div class="col-md-3">
          <label class="form-label">Fecha <span class="text-danger">*</span></label>
          <input type="date" id="presupuesto_fecha" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Usuario <span class="text-danger">*</span></label>
          <select id="presupuesto_usuario" class="form-select" required>
            <option value="0">-- Seleccionar --</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Proveedor <span class="text-danger">*</span></label>
          <select id="presupuesto_proveedor" class="form-select" required>
            <option value="0">-- Seleccionar --</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Orden de Compra</label>
          <input type="number" id="presupuesto_orden_compra" class="form-control" min="0">
        </div>
      </div>

      <hr>

      <div class="row">
        <div class="col-md-3">
          <label class="form-label">Producto <span class="text-danger">*</span></label>
          <select id="presupuesto_producto" class="form-select" required>
            <option value="0">-- Seleccionar --</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Cantidad <span class="text-danger">*</span></label>
          <input type="number" id="presupuesto_cantidad" class="form-control" value="1" min="1" required>
        </div>
        <div class="col-md-2">
          <label class="form-label">Precio Unit. <span class="text-danger">*</span></label>
          <input type="number" id="presupuesto_precio" class="form-control" step="0.01" value="0.00" required>
        </div>
        <div class="col-md-2">
          <label class="form-label">&nbsp;</label>
          <button type="button" class="btn btn-success btn-block w-100" onclick="agregarDetallePresupuesto()">
            <i data-feather="plus"></i>
          </button>
        </div>
      </div>

      <hr>

      <h6>Detalles</h6>
      <div class="table-responsive">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Precio Unit.</th>
              <th>Subtotal</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody id="detalles_presupuesto_tb">
          </tbody>
        </table>
      </div>

      <div class="row mt-3">
        <div class="col-md-8"></div>
        <div class="col-md-4">
          <div class="row">
            <div class="col-6">
              <label class="form-label">Total:</label>
            </div>
            <div class="col-6">
              <h6 id="presupuesto_total">0.00</h6>
            </div>
          </div>
        </div>
      </div>

      <hr>

      <div class="row">
        <div class="col-md-12">
          <button type="button" class="btn btn-primary" onclick="guardarPresupuesto()">
            <i data-feather="save"></i> Guardar
          </button>
          <button type="button" class="btn btn-secondary" onclick="cancelarPresupuesto()">
            <i data-feather="x"></i> Cancelar
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Cargar usuarios
  let usuarios = ejecutarAjax("controladores/usuario.php", "listar=1");
  if (usuarios !== "0") {
    let json_usuarios = JSON.parse(usuarios);
    json_usuarios.map(function(item) {
      $("#presupuesto_usuario").append(`<option value="${item.id_usuarios}">${item.nombre}</option>`);
    });
  }

  // Cargar proveedores
  let proveedores = ejecutarAjax("controladores/proveedor.php", "listar=1");
  if (proveedores !== "0") {
    let json_proveedores = JSON.parse(proveedores);
    json_proveedores.map(function(item) {
      $("#presupuesto_proveedor").append(`<option value="${item.id_proveedor}">${item.nombre}</option>`);
    });
  }

  // Cargar productos
  let productos = ejecutarAjax("controladores/producto.php", "listar=1");
  if (productos !== "0") {
    let json_productos = JSON.parse(productos);
    json_productos.map(function(item) {
      $("#presupuesto_producto").append(`<option value="${item.id_productos}" data-precio="${item.precio}">${item.nombre}</option>`);
    });
  }

  // Actualizar precio cuando se selecciona producto
  $("#presupuesto_producto").on("change", function() {
    let precio = $(this).find("option:selected").data("precio");
    $("#presupuesto_precio").val(precio || 0);
  });

  // Establecer fecha de hoy
  let hoy = new Date().toISOString().split('T')[0];
  $("#presupuesto_fecha").val(hoy);
});
</script>