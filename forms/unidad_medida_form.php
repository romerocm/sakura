<div class="tab-pane fade" id="unidad_medida" role="tabpanel">
    <!-- Info Alert -->
    <div class="alert alert-info mt-3">
        <h5><i class="fas fa-info-circle"></i> Information:</h5>
        <p>Create units of measurement here (e.g., kilograms, liters, units). These will be used when defining ingredients.</p>
        <hr>
        <p class="mb-0"><i class="fas fa-lightbulb"></i> Tip: Common units include: kg, g, l, ml, units, pieces, etc.</p>
    </div>

    <form id="unidadMedidaForm" class="mt-3">
        <input type="hidden" name="form_type" value="unidad_medida">
        
        <!-- Manual ID Toggle -->
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckUnidadMedida">
            <label class="form-check-label" for="manualIdCheckUnidadMedida">
                Set ID manually
            </label>
        </div>

        <!-- Manual ID Input -->
        <div class="mb-3 id-input" id="unidadMedidaIdInput">
            <label for="unidad_id" class="form-label">ID</label>
            <input type="number" class="form-control" name="unidad_id" min="1">
        </div>

        <!-- Unit Name -->
        <div class="mb-3">
            <label for="nombre" class="form-label">
                Nombre de Unidad
                <i class="fas fa-question-circle text-muted" data-bs-toggle="tooltip" 
                   title="Enter the name of the unit (e.g., Kilogram, Liter, Piece)"></i>
            </label>
            <input type="text" class="form-control" id="nombre" name="nombre" required
                   placeholder="Enter unit name (e.g., Kilogram, Liter)">
            <div class="form-text">Enter a clear and recognizable unit name that will be used throughout the system.</div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Unit
        </button>
    </form>


<style>
.quick-add {
    margin: 0 2px;
}
.tooltip {
    position: absolute;
    z-index: 1070;
}
</style>

<script>
// Initialize tooltips
$(function () {
    $('[data-bs-toggle="tooltip"]').tooltip();
});
