<div class="tab-pane fade show active" id="categoria" role="tabpanel">
    <form id="categoriaForm" class="mt-3">
        <input type="hidden" name="form_type" value="categoria">
        
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckCategoria">
            <label class="form-check-label" for="manualIdCheckCategoria">
                Set ID manually
            </label>
        </div>

        <div class="mb-3 id-input" id="categoriaIdInput">
            <label for="categoria_id" class="form-label">ID</label>
            <input type="number" class="form-control" name="categoria_id" min="1">
        </div>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
