<div class="tab-pane fade" id="receta_ingredientes" role="tabpanel">
    <form id="recetaIngredientesForm" class="mt-3">
        <input type="hidden" name="form_type" value="receta_ingredientes">
        
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckRecetaIngredientes">
            <label class="form-check-label" for="manualIdCheckRecetaIngredientes">
                Set ID manually
            </label>
        </div>

        <div class="mb-3 id-input" id="recetaIngredientesIdInput">
            <label for="receta_ingredientes_id" class="form-label">ID</label>
            <input type="number" class="form-control" name="receta_ingredientes_id" min="1">
        </div>

        <div class="mb-3">
            <label for="receta_id" class="form-label">Receta</label>
            <select class="form-control" id="receta_select" name="receta_id" required>
                <!-- Will be populated dynamically -->
            </select>
        </div>

        <div class="mb-3">
            <label for="ingrediente_id" class="form-label">Ingrediente</label>
            <select class="form-control" id="ingrediente_select" name="ingrediente_id" required>
                <!-- Will be populated dynamically -->
            </select>
        </div>

        <div class="mb-3">
            <label for="cantidad" class="form-label">Cantidad</label>
            <input type="number" step="0.01" class="form-control" id="cantidad" name="cantidad" required>
        </div>

        <div class="mb-3">
            <label for="costo_total" class="form-label">Costo Total</label>
            <input type="number" step="0.01" class="form-control" id="costo_total" name="costo_total" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
