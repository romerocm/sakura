<div class="tab-pane fade" id="costos_receta" role="tabpanel">
    <!-- Dependency Alert -->
    <div class="alert alert-info mt-3">
        <h5>Required Dependencies:</h5>
        <ol>
            <li>First create a <strong>Receta</strong> in the Receta tab</li>
            <li>Then add <strong>Ingredientes</strong> to that recipe in the Receta Ingredientes tab</li>
            <li>Finally, you can create the cost calculation here</li>
        </ol>
    </div>

    <!-- Form Status -->
    <div class="dependency-status mb-3">
        <div class="card">
            <div class="card-body">
                <h6>Dependencies Status:</h6>
                <div id="recetaStatus" class="status-item">
                    <i class="fas fa-circle-notch fa-spin"></i> Checking for available recipes...
                </div>
                <div id="ingredientesStatus" class="status-item">
                    <i class="fas fa-circle-notch fa-spin"></i> Checking for recipe ingredients...
                </div>
            </div>
        </div>
    </div>

    <form id="costosRecetaForm" class="mt-3">
        <input type="hidden" name="form_type" value="costos_receta">
        
        <!-- Manual ID Toggle -->
        <div class="form-check manual-id-toggle">
            <input class="form-check-input" type="checkbox" id="manualIdCheckCostosReceta">
            <label class="form-check-label" for="manualIdCheckCostosReceta">
                Set ID manually
            </label>
        </div>

        <!-- Manual ID Input -->
        <div class="mb-3 id-input" id="costosRecetaIdInput">
            <label for="costo_receta_id" class="form-label">ID</label>
            <input type="number" class="form-control" id="costo_receta_id" name="costo_receta_id" min="1">
        </div>

        <!-- Recipe Selection -->
        <div class="mb-3">
            <label for="receta_select" class="form-label">Receta</label>
            <select class="form-control" id="receta_select" name="receta_id" required>
                <option value="">Select Recipe</option>
            </select>
        </div>

        <!-- Materia Prima Section -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Costo de Materia Prima</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="costo_total_materia_prima" class="form-label">Costo Total Materia Prima</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="costo_total_materia_prima" 
                                       name="costo_total_materia_prima">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Margen de Error Section -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">Margen de Error</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="margen_error_porcentaje" class="form-label">Porcentaje Margen Error</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="margen_error_porcentaje" 
                                       name="margen_error_porcentaje" value="10">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="margen_error_costo_total" class="form-label">Costo Total Margen Error</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="margen_error_costo_total" 
                                       name="margen_error_costo_total" >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Costos de Preparación Section -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">Costos de Preparación</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="costo_total_preparacion" class="form-label">Costo Total Preparación</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="costo_total_preparacion" 
                                       name="costo_total_preparacion" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="costo_por_porcion" class="form-label">Costo por Porción</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="costo_por_porcion" 
                                       name="costo_por_porcion" >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Costos MP y Precio Potencial Section -->
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="card-title mb-0">Costos MP y Precio Potencial</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="porcentaje_costo_mp" class="form-label">% Costo MP</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="porcentaje_costo_mp" 
                                       name="porcentaje_costo_mp" value="35">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="precio_potencial_venta" class="form-label">Precio Potencial Venta</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="precio_potencial_venta" 
                                       name="precio_potencial_venta" >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Impuestos Section -->
        <div class="card mb-4">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">Impuestos y IVA</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="impuesto_consumo_porcentaje" class="form-label">Impuesto Consumo (%)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="impuesto_consumo_porcentaje" 
                                       name="impuesto_consumo_porcentaje" value="13">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="impuesto_consumo_costo_total" class="form-label">Costo Total Impuesto</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="impuesto_consumo_costo_total" 
                                       name="impuesto_consumo_costo_total" >
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="iva_por_porcion" class="form-label">IVA por Porción</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="iva_por_porcion" 
                                       name="iva_por_porcion" value="13">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="iva_porcion_costo_total" class="form-label">Costo Total IVA</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="iva_porcion_costo_total" 
                                       name="iva_porcion_costo_total" >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Precios Finales Section -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">Precios Finales</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="precio_venta" class="form-label">Precio Venta Sugerido</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="precio_venta" 
                                       name="precio_venta" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="precio_carta" class="form-label">Precio de Menú o Carta</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="precio_carta" 
                                       name="precio_carta">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="precio_real_venta" class="form-label">Precio Real de Venta (Sin IVA)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="precio_real_venta" 
                                       name="precio_real_venta" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="porcentaje_real_costo" class="form-label">% Real de Costo MP</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control" id="porcentaje_real_costo" 
                                       name="porcentaje_real_costo" >
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">
            <i class="fas fa-save"></i> Guardar Costos de Receta
        </button>
    </form>
</div>