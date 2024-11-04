<div class="tab-pane fade" id="fact_sales" role="tabpanel">
    <div class="alert alert-info mt-3">
        <h5><i class="fas fa-info-circle"></i> Daily Sales Summary Entry</h5>
        <p>Enter daily sales summaries, linking products to existing recipes.</p>
    </div>

    <form id="dailySalesForm" class="mt-3">
        <!-- Daily Summary Section -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-calendar"></i> Daily Summary
                </h5>
                <div>
                    <button type="button" class="btn btn-light btn-sm" id="prevDay">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button type="button" class="btn btn-light btn-sm" id="nextDay">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="sale_date" name="sale_date" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Venta Total</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="total_sales" name="total_sales" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Venta Neta</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="net_sales" name="net_sales" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Propinas</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="tips" name="tips" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Clientes</label>
                            <input type="number" class="form-control" id="customer_count" name="customer_count" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Ordenes</label>
                            <input type="number" class="form-control" id="orders_count" name="orders_count" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Orden Promedio</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="average_order" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tags"></i> Categorías Vendidas
                    <button type="button" class="btn btn-light btn-sm float-end" id="addCategory">
                        <i class="fas fa-plus"></i> Add Category
                    </button>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="categoriesTable">
                        <thead>
                            <tr>
                                <th>Categoría</th>
                                <th>Porcentaje</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="category-row">
                                <td>
                                    <select class="form-select category-select" required>
                                        <option value="">Seleccionar...</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" class="form-control category-percentage" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm category-quantity" required>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control category-total" required>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-info">
                                <td><strong>Total</strong></td>
                                <td><span id="categoryPercentageTotal">0</span>%</td>
                                <td><span id="categoryQuantityTotal">0</span></td>
                                <td>$<span id="categoryTotalAmount">0.00</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-utensils"></i> Productos Vendidos
                    <button type="button" class="btn btn-light btn-sm float-end" id="addProduct">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="productsTable">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Porcentaje</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="product-row">
                                <td>
                                    <select class="form-select recipe-select" required>
                                        <option value="">Seleccionar...</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <input type="number" step="0.1" class="form-control product-percentage" required>
                                        <span class="input-group-text">%</span>
                                    </div>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm product-quantity" required>
                                </td>
                                <td>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control product-total" required>
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-danger btn-sm remove-row">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="table-info">
                                <td><strong>Total</strong></td>
                                <td><span id="productPercentageTotal">0</span>%</td>
                                <td><span id="productQuantityTotal">0</span></td>
                                <td>$<span id="productTotalAmount">0.00</span></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-save"></i> Save Current Day
                </button>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-secondary w-100" id="copyPrevious">
                    <i class="fas fa-copy"></i> Copy Previous Day Data
                </button>
            </div>
            <div class="col-md-4">
                <button type="button" class="btn btn-success w-100" id="verifyTotals">
                    <i class="fas fa-check-circle"></i> Verify Totals
                </button>
            </div>
        </div>
    </form>
    <button type="button" class="btn btn-info w-100" id="openAIChatbot">
        <i class="fas fa-robot"></i> Open AI Chatbot
    </button>
<script src="js/chatbot.js"></script>
</div>
