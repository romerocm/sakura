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
        echo json_encode(['error' => 'Recipe not found']);
        exit;
    }
    
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
    
    // Get existing recipe costs if any
    $costsQuery = "SELECT * FROM costos_receta 
                   WHERE receta_id = :recipe_id 
                   ORDER BY costo_receta_id DESC LIMIT 1";
                   
    $costsStmt = $db->prepare($costsQuery);
    $costsStmt->bindParam(':recipe_id', $_GET['recipe_id']);
    $costsStmt->execute();
    $existingCosts = $costsStmt->fetch(PDO::FETCH_ASSOC);
    
    // Calculate total raw material cost
    $totalCost = 0;
    foreach ($ingredients as $ingredient) {
        $totalCost += floatval($ingredient['costo_total']);
    }
    
    // Log for debugging
    error_log("Recipe ID: " . $_GET['recipe_id']);
    error_log("Found " . count($ingredients) . " ingredients");
    error_log("Total cost calculated: " . $totalCost);
    
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
    
    echo json_encode($response);
    
} catch(PDOException $e) {
    error_log("Database Error in get_recipe_ingredients_costs.php: " . $e->getMessage());
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