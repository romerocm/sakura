<!-- Previous dependencies alert and status sections remain the same -->

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
        <label for="input_costo_receta_id" class="form-label">ID</label>
        <input type="number" class="form-control" id="input_costo_receta_id" name="costo_receta_id" min="1">
    </div>

    <!-- Recipe Selection -->
    <div class="mb-3">
        <label for="receta_select" class="form-label">Receta</label>
        <select class="form-control" id="receta_select" name="receta_id" required>
            <option value="">Select Recipe</option>
            <?php
            try {
                $database = new Database();
                $db = $database->connect();
                
                $query = "SELECT receta_id, nombre_receta FROM receta_estandar ORDER BY nombre_receta";
                $stmt = $db->prepare($query);
                $stmt->execute();
                
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='" . htmlspecialchars($row['receta_id']) . "'>" 
                         . htmlspecialchars($row['nombre_receta']) . "</option>";
                }
            } catch(PDOException $e) {
                error_log("Error loading recipes in costos_receta_form: " . $e->getMessage());
            }
            ?>
        </select>
    </div>

    <!-- Recipe Ingredients Selection -->
    <div class="mb-3">
        <label for="receta_ingredientes_select" class="form-label">Receta Ingredientes</label>
        <select class="form-control" id="receta_ingredientes_select" name="receta_ingredientes_id" required>
            <option value="">Select Recipe Ingredients</option>
        </select>
    </div>

    <!-- Cost Fields -->
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="input_costo_total_materia_prima" class="form-label">Costo Total Materia Prima</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control" id="input_costo_total_materia_prima" 
                           name="costo_total_materia_prima" readonly>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="input_margen_error_porcentaje" class="form-label">Margen Error (%)</label>
                <div class="input-group">
                    <input type="number" step="0.01" class="form-control" id="input_margen_error_porcentaje" 
                           name="margen_error_porcentaje" value="10">
                    <span class="input-group-text">%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Continue this pattern for all other inputs -->
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="input_costo_total_preparacion" class="form-label">Costo Total Preparación</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control" id="input_costo_total_preparacion" 
                           name="costo_total_preparacion" readonly>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="input_costo_por_porcion" class="form-label">Costo por Porción</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control" id="input_costo_por_porcion" 
                           name="costo_por_porcion" readonly>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="input_porcentaje_costo_mp" class="form-label">% Costo MP</label>
                <div class="input-group">
                    <input type="number" step="0.01" class="form-control" id="input_porcentaje_costo_mp" 
                           name="porcentaje_costo_mp" value="35">
                    <span class="input-group-text">%</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="input_precio_potencial_venta" class="form-label">Precio Potencial Venta</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control" id="input_precio_potencial_venta" 
                           name="precio_potencial_venta" readonly>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="input_impuesto_consumo_porcentaje" class="form-label">Impuesto Consumo (%)</label>
                <div class="input-group">
                    <input type="number" step="0.01" class="form-control" id="input_impuesto_consumo_porcentaje" 
                           name="impuesto_consumo_porcentaje" value="13">
                    <span class="input-group-text">%</span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="input_precio_venta" class="form-label">Precio Venta</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control" id="input_precio_venta" 
                           name="precio_venta" readonly>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="input_precio_carta" class="form-label">Precio Carta</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control" id="input_precio_carta" 
                           name="precio_carta">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="input_precio_real_venta" class="form-label">Precio Real Venta</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control" id="input_precio_real_venta" 
                           name="precio_real_venta" readonly>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="input_iva_por_porcion" class="form-label">IVA por Porción</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control" id="input_iva_por_porcion" 
                           name="iva_por_porcion" readonly>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="input_porcentaje_real_costo" class="form-label">% Real de Costo</label>
                <div class="input-group">
                    <input type="number" step="0.01" class="form-control" id="input_porcentaje_real_costo" 
                           name="porcentaje_real_costo" readonly>
                    <span class="input-group-text">%</span>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Save Recipe Costs
    </button>
</form>