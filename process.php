<?php
require_once 'config.php';

header('Content-Type: application/json');

function sendResponse($success, $message, $data = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->connect();

    // Check if the content is JSON
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    
    if (strpos($contentType, "application/json") !== false) {
        // Handle JSON data (for daily sales summary)
        $jsonData = json_decode(file_get_contents("php://input"), true);
        if ($jsonData === null) {
            sendResponse(false, "Invalid JSON data");
        }
        $form_type = $jsonData['form_type'];
    } else {
        // Handle regular form data
        $form_type = $_POST['form_type'];
    }

    try {
        $db->beginTransaction();

        switch($form_type) {
            case 'daily_sales_summary':
                // Insert into daily_sales_summary
                $query = "INSERT INTO daily_sales_summary (
                            sale_date, 
                            total_sales, 
                            net_sales, 
                            tips, 
                            customer_count
                        ) VALUES (
                            :sale_date, 
                            :total_sales, 
                            :net_sales, 
                            :tips, 
                            :customer_count
                        )";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':sale_date', $jsonData['sale_date']);
                $stmt->bindParam(':total_sales', $jsonData['total_sales']);
                $stmt->bindParam(':net_sales', $jsonData['net_sales']);
                $stmt->bindParam(':tips', $jsonData['tips']);
                $stmt->bindParam(':customer_count', $jsonData['customer_count']);
                $stmt->execute();
                
                $summary_id = $db->lastInsertId();

                // Insert category summaries
                if (!empty($jsonData['categories'])) {
                    $categoryQuery = "INSERT INTO category_sales_summary (
                                    summary_id, 
                                    categoria_id, 
                                    percentage, 
                                    quantity, 
                                    total
                                ) VALUES (
                                    :summary_id, 
                                    :categoria_id, 
                                    :percentage, 
                                    :quantity, 
                                    :total
                                )";
                    $categoryStmt = $db->prepare($categoryQuery);

                    foreach ($jsonData['categories'] as $category) {
                        $categoryStmt->bindParam(':summary_id', $summary_id);
                        $categoryStmt->bindParam(':categoria_id', $category['category_id']);
                        $categoryStmt->bindParam(':percentage', $category['percentage']);
                        $categoryStmt->bindParam(':quantity', $category['quantity']);
                        $categoryStmt->bindParam(':total', $category['total']);
                        $categoryStmt->execute();
                    }
                }

                // Insert product summaries
                if (!empty($jsonData['products'])) {
                    $productQuery = "INSERT INTO product_sales_summary (
                                    summary_id, 
                                    receta_id, 
                                    costo_receta_id,
                                    percentage, 
                                    quantity, 
                                    total
                                ) VALUES (
                                    :summary_id, 
                                    :receta_id, 
                                    (SELECT costo_receta_id 
                                    FROM costos_receta 
                                    WHERE receta_id = :receta_id_for_cost
                                    ORDER BY costo_receta_id DESC 
                                    LIMIT 1),
                                    :percentage, 
                                    :quantity, 
                                    :total
                                )";
                    $productStmt = $db->prepare($productQuery);

                    foreach ($jsonData['products'] as $product) {
                        // Log the product data for debugging
                        error_log("Processing product: " . json_encode($product));

                        // Check if costo_receta_id is null
                        $costoRecetaIdQuery = "SELECT costo_receta_id 
                                               FROM costos_receta 
                                               WHERE receta_id = :receta_id_for_cost
                                               ORDER BY costo_receta_id DESC 
                                               LIMIT 1";
                        $costoRecetaStmt = $db->prepare($costoRecetaIdQuery);
                        $costoRecetaStmt->bindParam(':receta_id_for_cost', $product['recipe_id']);
                        $costoRecetaStmt->execute();
                        $costoRecetaId = $costoRecetaStmt->fetchColumn();

                        if ($costoRecetaId === false) {
                            error_log("No costo_receta_id found for recipe_id: " . $product['recipe_id']);
                            sendResponse(false, 'No costo_receta_id found for recipe_id: ' . $product['recipe_id']);
                        }
                        $productStmt->bindParam(':summary_id', $summary_id);
                        $productStmt->bindParam(':receta_id', $product['recipe_id']);
                        $productStmt->bindParam(':receta_id_for_cost', $product['recipe_id']); // Bind recipe_id again for subquery
                        $productStmt->bindParam(':percentage', $product['percentage']);
                        $productStmt->bindParam(':quantity', $product['quantity']);
                        $productStmt->bindParam(':total', $product['total']);
                        $productStmt->execute();
                    }
                }

                // Insert into fact_sales
                if (!empty($jsonData['products'])) {
                    $factSalesQuery = "INSERT INTO fact_sales (
                                        sale_date,
                                        receta_id,
                                        costo_receta_id,
                                        order_type_id,
                                        quantity,
                                        total_amount,
                                        discount_amount,
                                        tip_amount,
                                        summary_id
                                    ) VALUES (
                                        :sale_date,
                                        :receta_id,
                                        (SELECT costo_receta_id 
                                         FROM costos_receta 
                                         WHERE receta_id = :receta_id_for_cost
                                         ORDER BY costo_receta_id DESC 
                                         LIMIT 1),
                                        :order_type_id,
                                        :quantity,
                                        :total_amount,
                                        :discount_amount,
                                        :tip_amount,
                                        :summary_id
                                    )";
                    $factSalesStmt = $db->prepare($factSalesQuery);

                    foreach ($jsonData['products'] as $product) {
                        $factSalesStmt->bindParam(':sale_date', $jsonData['sale_date']);
                        $factSalesStmt->bindParam(':receta_id', $product['recipe_id']);
                        $factSalesStmt->bindParam(':receta_id_for_cost', $product['recipe_id']); // Bind recipe_id again for subquery
                        $factSalesStmt->bindParam(':order_type_id', $product['order_type_id']);
                        $factSalesStmt->bindParam(':quantity', $product['quantity']);
                        $factSalesStmt->bindParam(':total_amount', $product['total']);
                        $factSalesStmt->bindParam(':discount_amount', $product['discount_amount']);
                        $factSalesStmt->bindParam(':tip_amount', $jsonData['tips']);
                        $factSalesStmt->bindParam(':summary_id', $summary_id);
                        $factSalesStmt->execute();
                    }
                }

                $db->commit();
                sendResponse(true, 'Daily sales summary and fact sales added successfully!', ['summary_id' => $summary_id]);
                break;

                case 'categoria':
                    if (!empty($_POST['categoria_id'])) {
                        $query = "INSERT INTO categoria (categoria_id, nombre) VALUES (:categoria_id, :nombre)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':categoria_id', $_POST['categoria_id']);
                    } else {
                        $query = "INSERT INTO categoria (nombre) VALUES (:nombre)";
                        $stmt = $db->prepare($query);
                    }
                    $stmt->bindParam(':nombre', $_POST['nombre']);
                    $stmt->execute();
                    $db->commit();
                    sendResponse(true, 'Category added successfully!');
                    break;
    
                case 'costos_receta':
                    if (!empty($_POST['costo_receta_id'])) {
                        $query = "INSERT INTO costos_receta (
                                    costo_receta_id, 
                                    receta_id, 
                                    receta_ingredientes_id, 
                                    costo_total_materia_prima, 
                                    margen_error_porcentaje,
                                    margen_error_costo_total,
                                    costo_total_preparacion, 
                                    costo_por_porcion, 
                                    porcentaje_costo_mp, 
                                    precio_potencial_venta,
                                    impuesto_consumo_porcentaje,
                                    impuesto_consumo_costo_total,
                                    precio_venta, 
                                    precio_carta, 
                                    precio_real_venta, 
                                    iva_por_porcion,
                                    iva_porcion_costo_total,
                                    porcentaje_real_costo
                                ) VALUES (
                                    :costo_receta_id,
                                    :receta_id, 
                                    :receta_ingredientes_id, 
                                    :costo_total_materia_prima, 
                                    :margen_error_porcentaje,
                                    :margen_error_costo_total,
                                    :costo_total_preparacion, 
                                    :costo_por_porcion, 
                                    :porcentaje_costo_mp, 
                                    :precio_potencial_venta,
                                    :impuesto_consumo_porcentaje,
                                    :impuesto_consumo_costo_total,
                                    :precio_venta,
                                    :precio_carta, 
                                    :precio_real_venta, 
                                    :iva_por_porcion,
                                    :iva_porcion_costo_total,
                                    :porcentaje_real_costo
                                )";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':costo_receta_id', $_POST['costo_receta_id']);
                    } else {
                        $query = "INSERT INTO costos_receta (
                                    receta_id, 
                                    receta_ingredientes_id, 
                                    costo_total_materia_prima, 
                                    margen_error_porcentaje,
                                    margen_error_costo_total,
                                    costo_total_preparacion, 
                                    costo_por_porcion, 
                                    porcentaje_costo_mp, 
                                    precio_potencial_venta,
                                    impuesto_consumo_porcentaje,
                                    impuesto_consumo_costo_total,
                                    precio_venta, 
                                    precio_carta, 
                                    precio_real_venta, 
                                    iva_por_porcion,
                                    iva_porcion_costo_total,
                                    porcentaje_real_costo
                                ) VALUES (
                                    :receta_id, 
                                    :receta_ingredientes_id, 
                                    :costo_total_materia_prima, 
                                    :margen_error_porcentaje,
                                    :margen_error_costo_total,
                                    :costo_total_preparacion, 
                                    :costo_por_porcion, 
                                    :porcentaje_costo_mp, 
                                    :precio_potencial_venta,
                                    :impuesto_consumo_porcentaje,
                                    :impuesto_consumo_costo_total,
                                    :precio_venta,
                                    :precio_carta, 
                                    :precio_real_venta, 
                                    :iva_por_porcion,
                                    :iva_porcion_costo_total,
                                    :porcentaje_real_costo
                                )";
                        $stmt = $db->prepare($query);
                    }
                    
                    // Bind all parameters
                    $stmt->bindParam(':receta_id', $_POST['receta_id']);
                    $stmt->bindParam(':receta_ingredientes_id', $_POST['receta_ingredientes_id']);
                    $stmt->bindParam(':costo_total_materia_prima', $_POST['costo_total_materia_prima']);
                    $stmt->bindParam(':margen_error_porcentaje', $_POST['margen_error_porcentaje']);
                    $stmt->bindParam(':margen_error_costo_total', $_POST['margen_error_costo_total']);
                    $stmt->bindParam(':costo_total_preparacion', $_POST['costo_total_preparacion']);
                    $stmt->bindParam(':costo_por_porcion', $_POST['costo_por_porcion']);
                    $stmt->bindParam(':porcentaje_costo_mp', $_POST['porcentaje_costo_mp']);
                    $stmt->bindParam(':precio_potencial_venta', $_POST['precio_potencial_venta']);
                    $stmt->bindParam(':impuesto_consumo_porcentaje', $_POST['impuesto_consumo_porcentaje']);
                    $stmt->bindParam(':impuesto_consumo_costo_total', $_POST['impuesto_consumo_costo_total']);
                    $stmt->bindParam(':precio_venta', $_POST['precio_venta']);
                    $stmt->bindParam(':precio_carta', $_POST['precio_carta']);
                    $stmt->bindParam(':precio_real_venta', $_POST['precio_real_venta']);
                    $stmt->bindParam(':iva_por_porcion', $_POST['iva_por_porcion']);
                    $stmt->bindParam(':iva_porcion_costo_total', $_POST['iva_porcion_costo_total']);
                    $stmt->bindParam(':porcentaje_real_costo', $_POST['porcentaje_real_costo']);
                    
                    $stmt->execute();
                    $db->commit();
                    sendResponse(true, 'Recipe costs added successfully!');
                    break;
    
                case 'dim_order_type':
                    if (!empty($_POST['order_type_id'])) {
                        $query = "INSERT INTO dim_order_type (order_type_id, order_type, description) VALUES (:order_type_id, :order_type, :description)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':order_type_id', $_POST['order_type_id']);
                    } else {
                        $query = "INSERT INTO dim_order_type (order_type, description) VALUES (:order_type, :description)";
                        $stmt = $db->prepare($query);
                    }
                    $stmt->bindParam(':order_type', $_POST['order_type']);
                    $stmt->bindParam(':description', $_POST['description']);
                    $stmt->execute();
                    $db->commit();
                    sendResponse(true, 'Order type added successfully!');
                    break;
    
                case 'fact_sales':
                    if (!empty($_POST['sale_id'])) {
                        $query = "INSERT INTO fact_sales (
                            sale_id, sale_date, receta_id, costo_receta_id, order_type_id, quantity, 
                            total_amount, discount_amount, tip_amount, summary_id
                        ) VALUES (
                            :sale_id, :sale_date, :receta_id, :costo_receta_id, :order_type_id, :quantity,
                            :total_amount, :discount_amount, :tip_amount, :summary_id
                        )";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':sale_id', $_POST['sale_id']);
                    } else {
                        $query = "INSERT INTO fact_sales (
                            sale_date, receta_id, costo_receta_id, order_type_id, quantity, 
                            total_amount, discount_amount, tip_amount, summary_id
                        ) VALUES (
                            :sale_date, :receta_id, :costo_receta_id, :order_type_id, :quantity,
                            :total_amount, :discount_amount, :tip_amount, :summary_id
                        )";
                        $stmt = $db->prepare($query);
                    }
                    $stmt->bindParam(':sale_date', $_POST['sale_date']);
                    $stmt->bindParam(':receta_id', $_POST['receta_id']);
                    $stmt->bindParam(':costo_receta_id', $_POST['costo_receta_id']);
                    $stmt->bindParam(':order_type_id', $_POST['order_type_id']);
                    $stmt->bindParam(':quantity', $_POST['quantity']);
                    $stmt->bindParam(':total_amount', $_POST['total_amount']);
                    $stmt->bindParam(':discount_amount', $_POST['discount_amount']);
                    $stmt->bindParam(':tip_amount', $_POST['tip_amount']);
                    $stmt->bindParam(':summary_id', $_POST['summary_id']);
                    $stmt->execute();
                    $db->commit();
                    sendResponse(true, 'Sale added successfully!');
                    break;
    
                case 'ingrediente':
                    if (!empty($_POST['ingrediente_id'])) {
                        $query = "INSERT INTO ingrediente (ingrediente_id, nombre, descripcion, costo_unitario, unidad_id, is_active) 
                                 VALUES (:ingrediente_id, :nombre, :descripcion, :costo_unitario, :unidad_id, :is_active)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':ingrediente_id', $_POST['ingrediente_id']);
                    } else {
                        $query = "INSERT INTO ingrediente (nombre, descripcion, costo_unitario, unidad_id, is_active) 
                                 VALUES (:nombre, :descripcion, :costo_unitario, :unidad_id, :is_active)";
                        $stmt = $db->prepare($query);
                    }
                    $stmt->bindParam(':nombre', $_POST['nombre']);
                    $stmt->bindParam(':descripcion', $_POST['descripcion']);
                    $stmt->bindParam(':costo_unitario', $_POST['costo_unitario']);
                    $stmt->bindParam(':unidad_id', $_POST['unidad_id']);
                    $stmt->bindParam(':is_active', $_POST['is_active']);
                    $stmt->execute();
                    $db->commit();
                    sendResponse(true, 'Ingredient added successfully!');
                    break;
    
                case 'receta':
                    if (!empty($_POST['receta_id'])) {
                        $query = "INSERT INTO receta_estandar (receta_id, nombre_receta, categoria_id, numero_preparacion, fecha_elaboracion, numero_porciones) 
                                 VALUES (:receta_id, :nombre_receta, :categoria_id, :numero_preparacion, :fecha_elaboracion, :numero_porciones)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':receta_id', $_POST['receta_id']);
                    } else {
                        $query = "INSERT INTO receta_estandar (nombre_receta, categoria_id, numero_preparacion, fecha_elaboracion, numero_porciones) 
                                 VALUES (:nombre_receta, :categoria_id, :numero_preparacion, :fecha_elaboracion, :numero_porciones)";
                        $stmt = $db->prepare($query);
                    }
                    $stmt->bindParam(':nombre_receta', $_POST['nombre_receta']);
                    $stmt->bindParam(':categoria_id', $_POST['categoria_id']);
                    $stmt->bindParam(':numero_preparacion', $_POST['numero_preparacion']);
                    $stmt->bindParam(':fecha_elaboracion', $_POST['fecha_elaboracion']);
                    $stmt->bindParam(':numero_porciones', $_POST['numero_porciones']);
                    $stmt->execute();
                    $db->commit();
                    sendResponse(true, 'Recipe added successfully!');
                    break;
    
                case 'receta_ingredientes':
                    if (!empty($_POST['receta_ingredientes_id'])) {
                        $query = "INSERT INTO receta_ingredientes (receta_ingredientes_id, receta_id, ingrediente_id, cantidad, costo_total) 
                                 VALUES (:receta_ingredientes_id, :receta_id, :ingrediente_id, :cantidad, :costo_total)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':receta_ingredientes_id', $_POST['receta_ingredientes_id']);
                    } else {
                        $query = "INSERT INTO receta_ingredientes (receta_id, ingrediente_id, cantidad, costo_total) 
                                 VALUES (:receta_id, :ingrediente_id, :cantidad, :costo_total)";
                        $stmt = $db->prepare($query);
                    }
                    $stmt->bindParam(':receta_id', $_POST['receta_id']);
                    $stmt->bindParam(':ingrediente_id', $_POST['ingrediente_id']);
                    $stmt->bindParam(':cantidad', $_POST['cantidad']);
                    $stmt->bindParam(':costo_total', $_POST['costo_total']);
                    $stmt->execute();
                    $db->commit();
                    sendResponse(true, 'Recipe ingredients added successfully!');
                    break;
    
                case 'unidad_medida':
                    if (!empty($_POST['unidad_id'])) {
                        $query = "INSERT INTO unidad_medida (unidad_id, nombre) VALUES (:unidad_id, :nombre)";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':unidad_id', $_POST['unidad_id']);
                    } else {
                        $query = "INSERT INTO unidad_medida (nombre) VALUES (:nombre)";
                        $stmt = $db->prepare($query);
                    }
                    $stmt->bindParam(':nombre', $_POST['nombre']);
                    $stmt->execute();
                    $db->commit();
                    sendResponse(true, 'Unit of measurement added successfully!');
                    break;
    
                default:
                    sendResponse(True, 'Processing Form...');
            }
        } catch(PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            sendResponse(false, 'Database error: ' . $e->getMessage());
        } catch(Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            sendResponse(false, 'Error: ' . $e->getMessage());
        }
    }
    ?>
