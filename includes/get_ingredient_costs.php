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
                           COALESCE(re.numero_porciones, 1) as numero_porciones,
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

    // Get recipe ingredients with their costs
    $ingredientsQuery = "SELECT ri.receta_ingredientes_id,
                               ri.ingrediente_id, 
                               ri.cantidad,
                               COALESCE(ri.costo_total, 0) as costo_total,
                               i.nombre as ingrediente_nombre,
                               COALESCE(i.costo_unitario, 0) as costo_unitario,
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

    error_log("Found " . count($ingredients) . " ingredients for recipe");
    
    // Calculate total raw material cost
    $totalCost = 0;
    foreach ($ingredients as &$ingredient) {
        $ingredient['cantidad'] = floatval($ingredient['cantidad']);
        $ingredient['costo_unitario'] = floatval($ingredient['costo_unitario']);
        $ingredient['costo_total'] = floatval($ingredient['costo_total']);
        $totalCost += $ingredient['costo_total'];
    }
    
    // Get existing recipe costs
    $costsQuery = "SELECT costo_receta_id,
                          receta_id,
                          COALESCE(costo_total_materia_prima, 0) as costo_total_materia_prima,
                          COALESCE(margen_error_porcentaje, 10) as margen_error_porcentaje,
                          COALESCE(costo_total_preparacion, 0) as costo_total_preparacion,
                          COALESCE(costo_por_porcion, 0) as costo_por_porcion,
                          COALESCE(porcentaje_costo_mp, 35) as porcentaje_costo_mp,
                          COALESCE(precio_potencial_venta, 0) as precio_potencial_venta,
                          COALESCE(impuesto_consumo_porcentaje, 13) as impuesto_consumo_porcentaje,
                          COALESCE(precio_venta, 0) as precio_venta,
                          COALESCE(precio_carta, 0) as precio_carta,
                          COALESCE(precio_real_venta, 0) as precio_real_venta,
                          COALESCE(iva_por_porcion, 0) as iva_por_porcion,
                          COALESCE(porcentaje_real_costo, 0) as porcentaje_real_costo
                   FROM costos_receta 
                   WHERE receta_id = :recipe_id 
                   ORDER BY costo_receta_id DESC LIMIT 1";
                   
    $costsStmt = $db->prepare($costsQuery);
    $costsStmt->bindParam(':recipe_id', $_GET['recipe_id']);
    $costsStmt->execute();
    $existingCosts = $costsStmt->fetch(PDO::FETCH_ASSOC);
    
    // Ensure all numeric values
    $recipe['numero_porciones'] = floatval($recipe['numero_porciones']);
    
    if ($existingCosts) {
        foreach ($existingCosts as $key => $value) {
            if ($key !== 'costo_receta_id' && $key !== 'receta_id') {
                $existingCosts[$key] = floatval($value);
            }
        }
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
    
    error_log("Sending response for recipe " . $recipe['nombre_receta']);
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