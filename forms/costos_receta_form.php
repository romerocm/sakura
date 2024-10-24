<div class="tab-pane fade" id="costos_receta" role="tabpanel">
    <!-- Dependency Alert -->
    <div class="alert alert-info mt-3">
        <h5>Required Dependencies:</h5>
        <ol>
            <li>First create a <strong>Receta</strong> in the Receta tab</li>
            <li>Then add <strong>Ingredientes</strong> to that recipe in the Receta Ingredientes tab</li>
            <li>Finally, you can create the cost calculation here</li>
        </ol>
    </div>

    <!-- Form Status -->
    <div class="dependency-status mb-3">
        <div class="card">
            <div class="card-body">
                <h6>Dependencies Status:</h6>
                <div id="recetaStatus" class="status-item">
                    <i class="fas fa-circle-notch fa-spin"></i> Checking for available recipes...
                </div>
                <div id="ingredientesStatus" class="status-item">
                    <i class="fas fa-circle-notch fa-spin"></i> Checking for recipe ingredients...
                </div>
            </div>
        </div>
    </div>

    <form id="costosRecetaForm" class="mt-3">
        <input type="hidden" name="form_type" value="costos_receta">
        
        <!-- Manual ID Toggle -->
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckCostosReceta">
            <label class="form-check-label" for="manualIdCheckCostosReceta">
                Set ID manually
            </label>
        </div>

        <!-- Manual ID Input (hidden by default) -->
        <div class="mb-3 id-input" id="costosRecetaIdInput">
            <label for="costo_receta_id" class="form-label">ID</label>
            <input type="number" class="form-control" name="costo_receta_id" min="1">
        </div>

        <!-- Required Foreign Keys -->
        <div class="mb-3">
            <label for="receta_id" class="form-label">Receta</label>
            <select class="form-control" id="receta_select" name="receta_id" required>
                <option value="">Select Recipe</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="receta_ingredientes_id" class="form-label">Receta Ingredientes</label>
            <select class="form-control" id="receta_ingredientes_select" name="receta_ingredientes_id" required>
                <option value="">Select Recipe Ingredients</option>
            </select>
        </div>

        <!-- Cost Fields -->
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="costo_total_materia_prima" class="form-label">Costo Total Materia Prima</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="costo_total_materia_prima" 
                               name="costo_total_materia_prima" required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="margen_error_porcentaje" class="form-label">Margen Error Porcentaje</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" id="margen_error_porcentaje" 
                               name="margen_error_porcentaje" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="costo_total_preparacion" class="form-label">Costo Total Preparación</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="costo_total_preparacion" 
                               name="costo_total_preparacion" required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="costo_por_porcion" class="form-label">Costo por Porción</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="costo_por_porcion" 
                               name="costo_por_porcion" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="porcentaje_costo_mp" class="form-label">Porcentaje Costo MP</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" id="porcentaje_costo_mp" 
                               name="porcentaje_costo_mp" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="precio_potencial_venta" class="form-label">Precio Potencial Venta</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="precio_potencial_venta" 
                               name="precio_potencial_venta" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="impuesto_consumo_porcentaje" class="form-label">Impuesto Consumo Porcentaje</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" id="impuesto_consumo_porcentaje" 
                               name="impuesto_consumo_porcentaje" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="precio_venta" class="form-label">Precio Venta</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="precio_venta" 
                               name="precio_venta" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="precio_carta" class="form-label">Precio Carta</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="precio_carta" 
                               name="precio_carta" required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="precio_real_venta" class="form-label">Precio Real Venta</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="precio_real_venta" 
                               name="precio_real_venta" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="iva_por_porcion" class="form-label">IVA por Porción</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="iva_por_porcion" 
                               name="iva_por_porcion" required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="porcentaje_real_costo" class="form-label">Porcentaje Real Costo</label>
                    <div class="input-group">
                        <input type="number" step="0.01" class="form-control" id="porcentaje_real_costo" 
                               name="porcentaje_real_costo" required>
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<style>
    .status-item {
        margin: 5px 0;
        padding: 5px 0;
    }
    .status-item.available {
        color: green;
    }
    .status-item.unavailable {
        color: red;
    }
    .dependency-status {
        margin-top: 15px;
    }
</style>

<script>
function checkDependencies() {
    // Check for recipes
    $.get('includes/get_recipes.php', function(data) {
        const hasRecipes = $(data).filter('option[value!=""]').length > 0;
        $('#recetaStatus')
            .removeClass('available unavailable')
            .addClass(hasRecipes ? 'available' : 'unavailable')
            .html(hasRecipes ? 
                '<i class="fas fa-check-circle"></i> Recipes available' : 
                '<i class="fas fa-times-circle"></i> No recipes available - Create a recipe first');
    });

    // Check for recipe ingredients
    $.get('includes/get_recipe_costs.php', function(data) {
        const hasIngredients = $(data).filter('option[value!=""]').length > 0;
        $('#ingredientesStatus')
            .removeClass('available unavailable')
            .addClass(hasIngredients ? 'available' : 'unavailable')
            .html(hasIngredients ? 
                '<i class="fas fa-check-circle"></i> Recipe ingredients available' : 
                '<i class="fas fa-times-circle"></i> No recipe ingredients available - Add ingredients to recipes first');
    });
}

// Run dependency check when tab is shown
$('button[data-bs-target="#costos_receta"]').on('shown.bs.tab', function (e) {
    checkDependencies();
});
</script>
