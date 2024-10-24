<div class="tab-pane fade" id="receta" role="tabpanel">
    <form id="recetaForm" class="mt-3">
        <input type="hidden" name="form_type" value="receta">
        
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckReceta">
            <label class="form-check-label" for="manualIdCheckReceta">
                Set ID manually
            </label>
        </div>

        <div class="mb-3 id-input" id="recetaIdInput">
            <label for="receta_id" class="form-label">ID</label>
            <input type="number" class="form-control" name="receta_id" min="1">
        </div>

        <div class="mb-3">
            <label for="nombre_receta" class="form-label">Nombre de Receta</label>
            <input type="text" class="form-control" id="nombre_receta" name="nombre_receta" required>
        </div>

        <div class="mb-3">
            <label for="categoria_id" class="form-label">Categoría</label>
            <select class="form-control" id="categoria_select" name="categoria_id" required>
                <!-- Will be populated dynamically -->
            </select>
        </div>

        <div class="mb-3">
            <label for="numero_preparacion" class="form-label">Número de Preparación</label>
            <input type="text" class="form-control" id="numero_preparacion" name="numero_preparacion" required>
        </div>

        <div class="mb-3">
            <label for="fecha_elaboracion" class="form-label">Fecha de Elaboración</label>
            <input type="date" class="form-control" id="fecha_elaboracion" name="fecha_elaboracion">
        </div>

        <div class="mb-3">
            <label for="numero_porciones" class="form-label">Número de Porciones</label>
            <input type="number" step="0.01" class="form-control" id="numero_porciones" name="numero_porciones" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
