<div class="tab-pane fade" id="ingrediente" role="tabpanel">
    <form id="ingredienteForm" class="mt-3">
        <input type="hidden" name="form_type" value="ingrediente">
        
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckIngrediente">
            <label class="form-check-label" for="manualIdCheckIngrediente">
                Set ID manually
            </label>
        </div>

        <div class="mb-3 id-input" id="ingredienteIdInput">
            <label for="ingrediente_id" class="form-label">ID</label>
            <input type="number" class="form-control" name="ingrediente_id" min="1">
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="costo_unitario" class="form-label">Costo Unitario</label>
            <input type="number" step="0.01" class="form-control" id="costo_unitario" name="costo_unitario" required>
        </div>

        <div class="mb-3">
            <label for="cantidad_disponible" class="form-label">Cantidad Disponible</label>
            <input type="number" step="0.01" class="form-control" id="cantidad_disponible" name="cantidad_disponible" required>
        </div>

        <div class="mb-3">
            <label for="unidad_id" class="form-label">Unidad de Medida</label>
            <select class="form-control" id="unidad_id" name="unidad_id" required>
                <!-- Will be populated dynamically -->
            </select>
        </div>

        <div class="mb-3">
            <label for="is_active" class="form-label">Estado</label>
            <select class="form-control" id="is_active" name="is_active" required>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
