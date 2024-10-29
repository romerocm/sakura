<?php
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_GET['recipe_id'])) {
    echo json_encode(['error' => 'Recipe ID is required']);
    exit;
}

try {
    $database = new Database();
    $db = $database->connect();
    
    error_log("Fetching recipe data for ID: " . $_GET['recipe_id']);
    
    // Get recipe details first
    $recipeQuery = "SELECT re.receta_id, 
                           re.nombre_receta, 
                           re.numero_preparacion,
                           re.numero_porciones,
                           re.fecha_elaboracion,
                           re.categoria_id
                    FROM receta_estandar re 
                    WHERE re.receta_id = :recipe_id";
                    
    $recipeStmt = $db->prepare($recipeQuery);
    $recipeStmt->bindParam(':recipe_id', $_GET['recipe_id']);
    $recipeStmt->execute();
    $recipe = $recipeStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$recipe) {
        error_log("Recipe not found for ID: " . $_GET['recipe_id']);
        echo json_encode(['error' => 'Recipe not found']);
        exit;
    }

    // Log recipe details for debugging
    error_log("Recipe found: " . print_r($recipe, true));
    
    // Get recipe ingredients with their costs
    $ingredientsQuery = "SELECT ri.receta_ingredientes_id,
                               ri.ingrediente_id, 
                               ri.cantidad,
                               ri.costo_total,
                               i.nombre as ingrediente_nombre,
                               i.costo_unitario,
                               i.descripcion as ingrediente_descripcion,
                               um.nombre as unidad_medida,
                               um.unidad_id
                        FROM receta_ingredientes ri
                        JOIN ingrediente i ON ri.ingrediente_id = i.ingrediente_id
                        JOIN unidad_medida um ON i.unidad_id = um.unidad_id
                        WHERE ri.receta_id = :recipe_id";
                        
    $ingredientsStmt = $db->prepare($ingredientsQuery);
    $ingredientsStmt->bindParam(':recipe_id', $_GET['recipe_id']);
    $ingredientsStmt->execute();
    $ingredients = $ingredientsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Log ingredients count for debugging
    error_log("Found " . count($ingredients) . " ingredients for recipe");
    
    // Calculate total raw material cost and normalize numeric values
    $totalCost = 0;
    foreach ($ingredients as &$ingredient) {
        // Ensure numeric values
        $ingredient['cantidad'] = floatval($ingredient['cantidad']);
        $ingredient['costo_unitario'] = floatval($ingredient['costo_unitario']);
        $ingredient['costo_total'] = floatval($ingredient['costo_total']);
        
        $totalCost += $ingredient['costo_total'];
    }
    
    // Get existing recipe costs if any
    $costsQuery = "SELECT costo_receta_id,
                          receta_id,
                          costo_total_materia_prima,
                          margen_error_porcentaje,
                          costo_total_preparacion,
                          costo_por_porcion,
                          porcentaje_costo_mp,
                          precio_potencial_venta,
                          impuesto_consumo_porcentaje,
                          precio_venta,
                          precio_carta,
                          precio_real_venta,
                          iva_por_porcion,
                          porcentaje_real_costo
                   FROM costos_receta 
                   WHERE receta_id = :recipe_id 
                   ORDER BY costo_receta_id DESC LIMIT 1";
                   
    $costsStmt = $db->prepare($costsQuery);
    $costsStmt->bindParam(':recipe_id', $_GET['recipe_id']);
    $costsStmt->execute();
    $existingCosts = $costsStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingCosts) {
        // Convert numeric values
        foreach ($existingCosts as $key => $value) {
            if ($key !== 'costo_receta_id' && $key !== 'receta_id') {
                $existingCosts[$key] = floatval($value);
            }
        }
        error_log("Found existing costs: " . print_r($existingCosts, true));
    }

    // Log total cost for debugging
    error_log("Total cost calculated: $totalCost");
    
    // Make sure recipe has numeric values
    if (isset($recipe['numero_porciones'])) {
        $recipe['numero_porciones'] = floatval($recipe['numero_porciones']);
    } else {
        $recipe['numero_porciones'] = 1; // Default to 1 if not set
    }

    // Prepare response
    $response = [
        'success' => true,
        'recipe' => $recipe,
        'ingredients' => $ingredients,
        'existing_costs' => $existingCosts,
        'totals' => [
            'raw_material_cost' => $totalCost,
            'ingredients_count' => count($ingredients)
        ]
    ];
    
    error_log("Sending response: " . json_encode($response));
    echo json_encode($response);
    
} catch(PDOException $e) {
    error_log("Database Error in get_recipe_ingredients_costs.php: " . $e->getMessage());
    error_log("SQL State: " . $e->getCode());
    echo json_encode([
        'error' => 'Database error occurred',
        'details' => $e->getMessage()
    ]);
} catch(Exception $e) {
    error_log("General Error in get_recipe_ingredients_costs.php: " . $e->getMessage());
    echo json_encode([
        'error' => 'An error occurred while processing the request',
        'details' => $e->getMessage()
    ]);
}
?>