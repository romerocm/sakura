<div class="tab-pane fade" id="dim_order" role="tabpanel">
    <form id="dimOrderForm" class="mt-3">
        <input type="hidden" name="form_type" value="dim_order">
        
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckDimOrder">
            <label class="form-check-label" for="manualIdCheckDimOrder">
                Set ID manually
            </label>
        </div>

        <div class="mb-3 id-input" id="dimOrderIdInput">
            <label for="order_type_id" class="form-label">Order Type ID</label>
            <input type="number" class="form-control" name="order_type_id" min="1">
        </div>

        <div class="mb-3">
            <label for="order_type" class="form-label">Order Type</label>
            <input type="text" class="form-control" id="order_type" name="order_type" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
