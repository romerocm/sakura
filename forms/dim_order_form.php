<div class="tab-pane fade" id="dim_order" role="tabpanel">
    <!-- Dependency Alert -->
    <div class="alert alert-info mt-3">
        <h5><i class="fas fa-info-circle"></i> Information:</h5>
        <p>Order types define the different ways orders can be placed (e.g., Dine-in, Takeout, Delivery).</p>
    </div>

    <form id="dimOrderForm" class="mt-3">
        <input type="hidden" name="form_type" value="dim_order_type">
        
        <!-- Manual ID Toggle -->
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckDimOrder">
            <label class="form-check-label" for="manualIdCheckDimOrder">
                Set ID manually
            </label>
        </div>

        <!-- Manual ID Input (hidden by default) -->
        <div class="mb-3 id-input" id="dimOrderIdInput">
            <label for="order_type_id" class="form-label">ID</label>
            <input type="number" class="form-control" name="order_type_id" min="1">
        </div>

        <!-- Order Type -->
        <div class="mb-3">
            <label for="order_type" class="form-label">Tipo de Orden</label>
            <input type="text" class="form-control" id="order_type" name="order_type" required
                   placeholder="Enter order type (e.g., Dine-in, Takeout)">
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label">Descripción</label>
            <textarea class="form-control" id="description" name="description" rows="3"
                      placeholder="Enter description of the order type"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Save Order Type
        </button>
    </form>
</div>
