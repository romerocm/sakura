<div class="tab-pane fade" id="ingrediente" role="tabpanel">
    <!-- Dependency Alert -->
    <div class="alert alert-info mt-3">
        <h5><i class="fas fa-info-circle"></i> Dependencies Required:</h5>
        <ul>
            <li>Unit of measurement must be defined first</li>
        </ul>
    </div>

    <!-- Form Status -->
    <div class="dependency-status mb-3">
        <div class="card">
            <div class="card-body">
                <h6>Dependencies Status:</h6>
                <div id="unidadMedidaStatus" class="status-item">
                    <i class="fas fa-circle-notch fa-spin"></i> Checking for units of measurement...
                </div>
            </div>
        </div>
    </div>

    <form id="ingredienteForm" class="mt-3">
        <input type="hidden" name="form_type" value="ingrediente">
        
        <!-- Manual ID Toggle -->
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckIngrediente">
            <label class="form-check-label" for="manualIdCheckIngrediente">
                Set ID manually
            </label>
        </div>

        <!-- Manual ID Input -->
        <div class="mb-3 id-input" id="ingredienteIdInput">
            <label for="ingrediente_id" class="form-label">ID</label>
            <input type="number" class="form-control" name="ingrediente_id" min="1">
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Name -->
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required
                           placeholder="Enter ingredient name">
                </div>
            </div>
            <div class="col-md-6">
                <!-- Unit -->
                <div class="mb-3">
                    <label for="unidad_id" class="form-label">Unidad de Medida</label>
                    <select class="form-control" id="unidad_id" name="unidad_id" required>
                        <option value="">Select Unit</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                      placeholder="Enter ingredient description"></textarea>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Unit Cost -->
                <div class="mb-3">
                    <label for="costo_unitario" class="form-label">Costo Unitario</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="costo_unitario" 
                               name="costo_unitario" required placeholder="0.00">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Status -->
                <div class="mb-3">
                    <label for="is_active" class="form-label">Estado</label>
                    <select class="form-control" id="is_active" name="is_active" required>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Ingredient
        </button>
    </form>
</div>

<script>
function checkIngredientDependencies() {
    $.get('includes/get_units.php', function(data) {
        const hasUnits = $(data).filter('option[value!=""]').length > 0;
        $('#unidadMedidaStatus')
            .removeClass('available unavailable')
            .addClass(hasUnits ? 'available' : 'unavailable')
            .html(hasUnits ? 
                '<i class="fas fa-check-circle"></i> Units of measurement available' : 
                '<i class="fas fa-times-circle"></i> No units available - Create units first');
    });
}

$('button[data-bs-target="#ingrediente"]').on('shown.bs.tab', function (e) {
    checkIngredientDependencies();
});
</script>