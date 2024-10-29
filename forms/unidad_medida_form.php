<div class="tab-pane fade" id="unidad_medida" role="tabpanel">
    <form id="unidadMedidaForm" class="mt-3">
        <input type="hidden" name="form_type" value="unidad_medida">
        
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckUnidadMedida">
            <label class="form-check-label" for="manualIdCheckUnidadMedida">
                Set ID manually
            </label>
        </div>

        <div class="mb-3 id-input" id="unidadMedidaIdInput">
            <label for="unidad_id" class="form-label">ID</label>
            <input type="number" class="form-control" name="unidad_id" min="1">
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
