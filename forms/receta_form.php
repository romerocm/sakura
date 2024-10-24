<div class="tab-pane fade" id="receta" role="tabpanel">
    <!-- Dependency Alert -->
    <div class="alert alert-info mt-3">
        <h5><i class="fas fa-info-circle"></i> Dependencies Required:</h5>
        <ul>
            <li>Categories must be defined first</li>
        </ul>
    </div>

    <!-- Form Status -->
    <div class="dependency-status mb-3">
        <div class="card">
            <div class="card-body">
                <h6>Dependencies Status:</h6>
                <div id="recipeCategoriaStatus" class="status-item">
                    <i class="fas fa-circle-notch fa-spin"></i> Checking for categories...
                </div>
            </div>
        </div>
    </div>

    <form id="recetaForm" class="mt-3">
        <input type="hidden" name="form_type" value="receta">
        
        <!-- Manual ID Toggle -->
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckReceta">
            <label class="form-check-label" for="manualIdCheckReceta">
                Set ID manually
            </label>
        </div>

        <!-- Manual ID Input -->
        <div class="mb-3 id-input" id="recetaIdInput">
            <label for="receta_id" class="form-label">ID</label>
            <input type="number" class="form-control" name="receta_id" min="1">
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Recipe Number -->
                <div class="mb-3">
                    <label for="numero_receta" class="form-label">Número de Receta</label>
                    <input type="number" class="form-control" id="numero_receta" 
                           name="numero_receta" required>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Recipe Name -->
                <div class="mb-3">
                    <label for="nombre_receta" class="form-label">Nombre de Receta</label>
                    <input type="text" class="form-control" id="nombre_receta" 
                           name="nombre_receta" required placeholder="Enter recipe name">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Category -->
                <div class="mb-3">
                    <label for="categoria_id" class="form-label">Categoría</label>
                    <select class="form-control" id="categoria_select" name="categoria_id" required>
                        <option value="">Select Category</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Preparation Number -->
                <div class="mb-3">
                    <label for="numero_preparacion" class="form-label">Número de Preparación</label>
                    <input type="text" class="form-control" id="numero_preparacion" 
                           name="numero_preparacion" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Elaboration Date -->
                <div class="mb-3">
                    <label for="fecha_elaboracion" class="form-label">Fecha de Elaboración</label>
                    <input type="date" class="form-control" id="fecha_elaboracion" 
                           name="fecha_elaboracion">
                </div>
            </div>
            <div class="col-md-6">
                <!-- Number of Portions -->
                <div class="mb-3">
                    <label for="numero_porciones" class="form-label">Número de Porciones</label>
                    <input type="number" step="0.01" class="form-control" id="numero_porciones" 
                           name="numero_porciones" required>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Recipe
        </button>
    </form>
</div>

<script>
function checkRecipeDependencies() {
    $.get('includes/get_categories.php', function(data) {
        const hasCategories = $(data).filter('option[value!=""]').length > 0;
        $('#recipeCategoriaStatus')
            .removeClass('available unavailable')
            .addClass(hasCategories ? 'available' : 'unavailable')
            .html(hasCategories ? 
                '<i class="fas fa-check-circle"></i> Categories available' : 
                '<i class="fas fa-times-circle"></i> No categories available - Create categories first');
    });
}

$('button[data-bs-target="#receta"]').on('shown.bs.tab', function (e) {
    checkRecipeDependencies();
});
</script>
