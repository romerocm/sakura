<div class="tab-pane fade" id="categoria" role="tabpanel">
    <!-- Dependency Alert -->
    <div class="alert alert-info mt-3">
        <h5><i class="fas fa-info-circle"></i> Information:</h5>
        <p>Categories are used to organize recipes. Create categories before adding recipes.</p>
    </div>

    <form id="categoriaForm" class="mt-3">
        <input type="hidden" name="form_type" value="categoria">
        
        <!-- Manual ID Toggle -->
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckCategoria">
            <label class="form-check-label" for="manualIdCheckCategoria">
                Set ID manually
            </label>
        </div>

        <!-- Manual ID Input (hidden by default) -->
        <div class="mb-3 id-input" id="categoriaIdInput">
            <label for="categoria_id" class="form-label">ID</label>
            <input type="number" class="form-control" name="categoria_id" min="1">
        </div>

        <!-- Category Name -->
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre de Categoría</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required
                   placeholder="Enter category name">
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Category
        </button>
    </form>
</div>
