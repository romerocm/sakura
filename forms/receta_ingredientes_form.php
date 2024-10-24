<div class="tab-pane fade" id="receta_ingredientes" role="tabpanel">
    <!-- Dependency Alert -->
    <div class="alert alert-info mt-3">
        <h5><i class="fas fa-info-circle"></i> Dependencies Required:</h5>
        <ul>
            <li>Recipe must be created first</li>
            <li>Ingredients must be available</li>
        </ul>
    </div>

    <!-- Form Status -->
    <div class="dependency-status mb-3">
        <div class="card">
            <div class="card-body">
                <h6>Dependencies Status:</h6>
                <div id="recipeIngredientRecetaStatus" class="status-item">
                    <i class="fas fa-circle-notch fa-spin"></i> Checking for recipes...
                </div>
                <div id="recipeIngredientIngredienteStatus" class="status-item">
                    <i class="fas fa-circle-notch fa-spin"></i> Checking for ingredients...
                </div>
            </div>
        </div>
    </div>

    <form id="recetaIngredientesForm" class="mt-3">
        <input type="hidden" name="form_type" value="receta_ingredientes">
        
        <!-- Manual ID Toggle -->
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckRecetaIngredientes">
            <label class="form-check-label" for="manualIdCheckRecetaIngredientes">
                Set ID manually
            </label>
        </div>

        <!-- Manual ID Input -->
        <div class="mb-3 id-input" id="recetaIngredientesIdInput">
            <label for="receta_ingredientes_id" class="form-label">ID</label>
            <input type="number" class="form-control" name="receta_ingredientes_id" min="1">
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Recipe -->
                <div class="mb-3">
                    <label for="receta_id" class="form-label">Receta</label>
                    <select class="form-control" id="receta_select" name="receta_id" required>
                        <option value="">Select Recipe</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Ingredient -->
                <div class="mb-3">
                    <label for="ingrediente_id" class="form-label">Ingrediente</label>
                    <select class="form-control" id="ingrediente_select" name="ingrediente_id" required>
                        <option value="">Select Ingredient</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Quantity -->
                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad</label>
                    <input type="number" step="0.01" class="form-control" id="cantidad" 
                           name="cantidad" required>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Total Cost -->
                <div class="mb-3">
                    <label for="costo_total" class="form-label">Costo Total</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="costo_total" 
                               name="costo_total" required>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Recipe Ingredients
        </button>
    </form>
</div>

<script>
function checkRecipeIngredientsDependencies() {
    $.get('includes/get_recipes.php', function(data) {
        const hasRecipes = $(data).filter('option[value!=""]').length > 0;
        $('#recipeIngredientRecetaStatus')
            .removeClass('available unavailable')
            .addClass(hasRecipes ? 'available' : 'unavailable')
            .html(hasRecipes ? 
                '<i class="fas fa-check-circle"></i> Recipes available' : 
                '<i class="fas fa-times-circle"></i> No recipes available - Create recipes first');
    });

    $.get('includes/get_ingredients.php', function(data) {
        const hasIngredients = $(data).filter('option[value!=""]').length > 0;
        $('#recipeIngredientIngredienteStatus')
            .removeClass('available unavailable')
            .addClass(hasIngredients ? 'available' : 'unavailable')
            .html(hasIngredients ? 
                '<i class="fas fa-check-circle"></i> Ingredients available' : 
                '<i class="fas fa-times-circle"></i> No ingredients available - Add ingredients first');
    });
}

// Auto-calculate total cost when ingredient and quantity change
function updateTotalCost() {
    const ingredienteId = $('#ingrediente_select').val();
    const cantidad = $('#cantidad').val();
    
    if (ingredienteId && cantidad) {
        // Get ingredient cost from the database
        $.get('includes/get_ingredient_cost.php', { id: ingredienteId }, function(data) {
            if (data.costo_unitario) {
                const totalCost = parseFloat(data.costo_unitario) * parseFloat(cantidad);
                $('#costo_total').val(totalCost.toFixed(2));
            }
        });
    }
}

// Run dependency check when tab is shown
$('button[data-bs-target="#receta_ingredientes"]').on('shown.bs.tab', function (e) {
    checkRecipeIngredientsDependencies();
});

// Add event listeners for auto-calculation
$('#ingrediente_select, #cantidad').on('change input', updateTotalCost);
</script>
