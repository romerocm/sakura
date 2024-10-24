<div class="tab-pane fade" id="fact_sales" role="tabpanel">
    <form id="factSalesForm" class="mt-3">
        <input type="hidden" name="form_type" value="fact_sales">
        
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckFactSales">
            <label class="form-check-label" for="manualIdCheckFactSales">
                Set ID manually
            </label>
        </div>

        <div class="mb-3 id-input" id="factSalesIdInput">
            <label for="sale_id" class="form-label">Sale ID</label>
            <input type="number" class="form-control" name="sale_id" min="1">
        </div>

        <div class="mb-3">
            <label for="sale_date" class="form-label">Sale Date</label>
            <input type="datetime-local" class="form-control" id="sale_date" name="sale_date" required>
        </div>

        <div class="mb-3">
            <label for="receta_id" class="form-label">Receta ID</label>
            <select class="form-control" id="receta_id_sales" name="receta_id" required>
                <!-- Will be populated dynamically -->
            </select>
        </div>

        <div class="mb-3">
            <label for="costo_receta_id" class="form-label">Costo Receta ID</label>
            <select class="form-control" id="costo_receta_id" name="costo_receta_id" required>
                <!-- Will be populated dynamically -->
            </select>
        </div>

        <div class="mb-3">
            <label for="order_type_id" class="form-label">Order Type</label>
            <select class="form-control" id="order_type_id" name="order_type_id" required>
                <!-- Will be populated dynamically -->
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>

        <div class="mb-3">
            <label for="total_amount" class="form-label">Total Amount</label>
            <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" required>
        </div>

        <div class="mb-3">
            <label for="discount_amount" class="form-label">Discount Amount</label>
            <input type="number" step="0.01" class="form-control" id="discount_amount" name="discount_amount" required>
        </div>

        <div class="mb-3">
            <label for="tip_amount" class="form-label">Tip Amount</label>
            <input type="number" step="0.01" class="form-control" id="tip_amount" name="tip_amount" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
