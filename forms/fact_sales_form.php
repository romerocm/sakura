<div class="tab-pane fade" id="fact_sales" role="tabpanel">
    <!-- Dependency Alert -->
    <div class="alert alert-info mt-3">
        <h5><i class="fas fa-info-circle"></i> Dependencies Required:</h5>
        <ul>
            <li>Recipe must exist</li>
            <li>Recipe costs must be calculated</li>
            <li>Order type must be defined</li>
        </ul>
    </div>

    <!-- Form Status -->
    <div class="dependency-status mb-3">
        <div class="card">
            <div class="card-body">
                <h6>Dependencies Status:</h6>
                <div id="salesRecetaStatus" class="status-item">
                    <i class="fas fa-circle-notch fa-spin"></i> Checking for recipes...
                </div>
                <div id="salesCostoStatus" class="status-item">
                    <i class="fas fa-circle-notch fa-spin"></i> Checking for recipe costs...
                </div>
                <div id="salesOrderTypeStatus" class="status-item">
                    <i class="fas fa-circle-notch fa-spin"></i> Checking for order types...
                </div>
            </div>
        </div>
    </div>

    <form id="factSalesForm" class="mt-3">
        <input type="hidden" name="form_type" value="fact_sales">
        
        <!-- Manual ID Toggle -->
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckFactSales">
            <label class="form-check-label" for="manualIdCheckFactSales">
                Set ID manually
            </label>
        </div>

        <!-- Manual ID Input (hidden by default) -->
        <div class="mb-3 id-input" id="factSalesIdInput">
            <label for="sale_id" class="form-label">ID</label>
            <input type="number" class="form-control" name="sale_id" min="1">
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Sale Date -->
                <div class="mb-3">
                    <label for="sale_date" class="form-label">Fecha de Venta</label>
                    <input type="date" class="form-control" id="sale_date" name="sale_date" required>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Order Type -->
                <div class="mb-3">
                    <label for="order_type_id" class="form-label">Tipo de Orden</label>
                    <select class="form-control" id="order_type_id" name="order_type_id" required>
                        <option value="">Select Order Type</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Recipe -->
                <div class="mb-3">
                    <label for="receta_id" class="form-label">Receta</label>
                    <select class="form-control" id="receta_id" name="receta_id" required>
                        <option value="">Select Recipe</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Recipe Cost -->
                <div class="mb-3">
                    <label for="costo_receta_id" class="form-label">Costo de Receta</label>
                    <select class="form-control" id="costo_receta_id" name="costo_receta_id" required>
                        <option value="">Select Recipe Cost</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Quantity -->
                <div class="mb-3">
                    <label for="quantity" class="form-label">Cantidad</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Total Amount -->
                <div class="mb-3">
                    <label for="total_amount" class="form-label">Monto Total</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="total_amount" 
                               name="total_amount" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <!-- Discount -->
                <div class="mb-3">
                    <label for="discount_amount" class="form-label">Descuento</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="discount_amount" 
                               name="discount_amount" required>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Tip -->
                <div class="mb-3">
                    <label for="tip_amount" class="form-label">Propina</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="tip_amount" 
                               name="tip_amount" required>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Sale
        </button>
    </form>
</div>

<script>
function checkSalesDependencies() {
    // Check for recipes
    $.get('includes/get_recipes.php', function(data) {
        const hasRecipes = $(data).filter('option[value!=""]').length > 0;
        $('#salesRecetaStatus')
            .removeClass('available unavailable')
            .addClass(hasRecipes ? 'available' : 'unavailable')
            .html(hasRecipes ? 
                '<i class="fas fa-check-circle"></i> Recipes available' : 
                '<i class="fas fa-times-circle"></i> No recipes available - Create recipes first');
    });

    // Check for recipe costs
    $.get('includes/get_recipe_costs.php', function(data) {
        const hasCosts = $(data).filter('option[value!=""]').length > 0;
        $('#salesCostoStatus')
            .removeClass('available unavailable')
            .addClass(hasCosts ? 'available' : 'unavailable')
            .html(hasCosts ? 
                '<i class="fas fa-check-circle"></i> Recipe costs available' : 
                '<i class="fas fa-times-circle"></i> No recipe costs available - Calculate costs first');
    });

    // Check for order types
    $.get('includes/get_order_types.php', function(data) {
        const hasOrderTypes = $(data).filter('option[value!=""]').length > 0;
        $('#salesOrderTypeStatus')
            .removeClass('available unavailable')
            .addClass(hasOrderTypes ? 'available' : 'unavailable')
            .html(hasOrderTypes ? 
                '<i class="fas fa-check-circle"></i> Order types available' : 
                '<i class="fas fa-times-circle"></i> No order types available - Create order types first');
    });
}

// Run dependency check when tab is shown
$('button[data-bs-target="#fact_sales"]').on('shown.bs.tab', function (e) {
    checkSalesDependencies();
});
</script>
