<?php
require_once 'config.php';

header('Content-Type: application/json');

// Add error logging
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

function sendResponse($success, $message, $data = null) {
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    error_log("Sending response: " . json_encode($response));
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->connect();

    // Check if the content is JSON
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    
    if (strpos($contentType, "application/json") !== false) {
        $jsonData = json_decode(file_get_contents("php://input"), true);
        error_log("Received JSON data: " . json_encode($jsonData));
        
        if ($jsonData === null) {
            error_log("JSON decode error: " . json_last_error_msg());
            sendResponse(false, "Invalid JSON data");
        }
        $form_type = $jsonData['form_type'];
    } else {
        $form_type = $_POST['form_type'];
    }

    try {
        $db->beginTransaction();
        error_log("Starting transaction for form type: " . $form_type);

        switch($form_type) {
            case 'daily_sales_summary':
                // Validate and convert numeric values
                $saleDate = date('Y-m-d', strtotime($jsonData['sale_date']));
                $totalSales = (float)$jsonData['total_sales'];
                $netSales = (float)$jsonData['net_sales'];
                $tips = (float)$jsonData['tips'];
                $customerCount = (int)$jsonData['customer_count'];

                error_log("Processing daily sales summary with values: " . json_encode([
                    'sale_date' => $saleDate,
                    'total_sales' => $totalSales,
                    'net_sales' => $netSales,
                    'tips' => $tips,
                    'customer_count' => $customerCount
                ]));

                // Insert daily summary
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
                
                $stmt->bindValue(':sale_date', $saleDate);
                $stmt->bindValue(':total_sales', $totalSales);
                $stmt->bindValue(':net_sales', $netSales);
                $stmt->bindValue(':tips', $tips);
                $stmt->bindValue(':customer_count', $customerCount);
                
                $stmt->execute();
                $summary_id = $db->lastInsertId();
                error_log("Daily summary inserted with ID: " . $summary_id);

                // Insert category summaries
                if (!empty($jsonData['categories'])) {
                    error_log("Processing categories: " . count($jsonData['categories']));
                    
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
                        $categoryStmt->bindValue(':summary_id', $summary_id);
                        $categoryStmt->bindValue(':categoria_id', (int)$category['category_id']);
                        $categoryStmt->bindValue(':percentage', (float)$category['percentage']);
                        $categoryStmt->bindValue(':quantity', (int)$category['quantity']);
                        $categoryStmt->bindValue(':total', (float)$category['total']);
                        
                        error_log("Inserting category: " . json_encode($category));
                        $categoryStmt->execute();
                    }
                }

                // Insert product summaries
                if (!empty($jsonData['products'])) {
                    error_log("Processing products: " . count($jsonData['products']));
                    
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
                                    :costo_receta_id,
                                    :percentage, 
                                    :quantity, 
                                    :total
                                )";
                    $productStmt = $db->prepare($productQuery);

                    // Also prepare fact_sales insert
                    $salesQuery = "INSERT INTO fact_sales (
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
                                    :costo_receta_id,
                                    :order_type_id,
                                    :quantity,
                                    :total_amount,
                                    :discount_amount,
                                    :tip_amount,
                                    :summary_id
                                )";
                    $salesStmt = $db->prepare($salesQuery);

                    foreach ($jsonData['products'] as $product) {
                        // Insert into product_sales_summary
                        $productStmt->bindValue(':summary_id', $summary_id);
                        $productStmt->bindValue(':receta_id', (int)$product['recipe_id']);
                        $productStmt->bindValue(':costo_receta_id', (int)$product['costo_receta_id']);
                        $productStmt->bindValue(':percentage', (float)$product['percentage']);
                        $productStmt->bindValue(':quantity', (int)$product['quantity']);
                        $productStmt->bindValue(':total', (float)$product['total']);
                        
                        error_log("Inserting product summary: " . json_encode($product));
                        $productStmt->execute();

                        // Insert into fact_sales
                        $salesStmt->bindValue(':sale_date', $saleDate);
                        $salesStmt->bindValue(':receta_id', (int)$product['recipe_id']);
                        $salesStmt->bindValue(':costo_receta_id', (int)$product['costo_receta_id']);
                        $salesStmt->bindValue(':order_type_id', 1); // Default order type
                        $salesStmt->bindValue(':quantity', (int)$product['quantity']);
                        $salesStmt->bindValue(':total_amount', (float)$product['total']);
                        $salesStmt->bindValue(':discount_amount', 0);
                        $salesStmt->bindValue(':tip_amount', 0);
                        $salesStmt->bindValue(':summary_id', $summary_id);
                        
                        error_log("Inserting fact sales: " . json_encode($product));
                        $salesStmt->execute();
                    }
                }

                $db->commit();
                error_log("Transaction committed successfully");
                sendResponse(true, 'Daily sales summary added successfully!', ['summary_id' => $summary_id]);
                break;

                case 'daily_sales_summary':
                    // Validate and convert numeric values
                    $saleDate = date('Y-m-d', strtotime($jsonData['sale_date']));
                    $totalSales = (float)$jsonData['total_sales'];
                    $netSales = (float)$jsonData['net_sales'];
                    $tips = (float)$jsonData['tips'];
                    $customerCount = (int)$jsonData['customer_count'];
    
                    error_log("Processing daily sales summary with values: " . json_encode([
                        'sale_date' => $saleDate,
                        'total_sales' => $totalSales,
                        'net_sales' => $netSales,
                        'tips' => $tips,
                        'customer_count' => $customerCount
                    ]));
    
                    // Insert daily summary
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
                    
                    $stmt->bindValue(':sale_date', $saleDate);
                    $stmt->bindValue(':total_sales', $totalSales);
                    $stmt->bindValue(':net_sales', $netSales);
                    $stmt->bindValue(':tips', $tips);
                    $stmt->bindValue(':customer_count', $customerCount);
                    
                    $stmt->execute();
                    $summary_id = $db->lastInsertId();
                    error_log("Daily summary inserted with ID: " . $summary_id);
    
                    // Insert category summaries
                    if (!empty($jsonData['categories'])) {
                        error_log("Processing categories: " . count($jsonData['categories']));
                        
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
                            $categoryStmt->bindValue(':summary_id', $summary_id);
                            $categoryStmt->bindValue(':categoria_id', (int)$category['category_id']);
                            $categoryStmt->bindValue(':percentage', (float)$category['percentage']);
                            $categoryStmt->bindValue(':quantity', (int)$category['quantity']);
                            $categoryStmt->bindValue(':total', (float)$category['total']);
                            
                            error_log("Inserting category: " . json_encode($category));
                            $categoryStmt->execute();
                        }
                    }
    
                    // Insert product summaries
                    if (!empty($jsonData['products'])) {
                        error_log("Processing products: " . count($jsonData['products']));
                        
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
                                        :costo_receta_id,
                                        :percentage, 
                                        :quantity, 
                                        :total
                                    )";
                        $productStmt = $db->prepare($productQuery);
    
                        // Also prepare fact_sales insert
                        $salesQuery = "INSERT INTO fact_sales (
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
                                        :costo_receta_id,
                                        :order_type_id,
                                        :quantity,
                                        :total_amount,
                                        :discount_amount,
                                        :tip_amount,
                                        :summary_id
                                    )";
                        $salesStmt = $db->prepare($salesQuery);
    
                        foreach ($jsonData['products'] as $product) {
                            // Insert into product_sales_summary
                            $productStmt->bindValue(':summary_id', $summary_id);
                            $productStmt->bindValue(':receta_id', (int)$product['recipe_id']);
                            $productStmt->bindValue(':costo_receta_id', (int)$product['costo_receta_id']);
                            $productStmt->bindValue(':percentage', (float)$product['percentage']);
                            $productStmt->bindValue(':quantity', (int)$product['quantity']);
                            $productStmt->bindValue(':total', (float)$product['total']);
                            
                            error_log("Inserting product summary: " . json_encode($product));
                            $productStmt->execute();
    
                            // Insert into fact_sales
                            $salesStmt->bindValue(':sale_date', $saleDate);
                            $salesStmt->bindValue(':receta_id', (int)$product['recipe_id']);
                            $salesStmt->bindValue(':costo_receta_id', (int)$product['costo_receta_id']);
                            $salesStmt->bindValue(':order_type_id', 1); // Default order type
                            $salesStmt->bindValue(':quantity', (int)$product['quantity']);
                            $salesStmt->bindValue(':total_amount', (float)$product['total']);
                            $salesStmt->bindValue(':discount_amount', 0);
                            $salesStmt->bindValue(':tip_amount', 0);
                            $salesStmt->bindValue(':summary_id', $summary_id);
                            
                            error_log("Inserting fact sales: " . json_encode($product));
                            $salesStmt->execute();
                        }
                    }
    
                    $db->commit();
                    error_log("Transaction committed successfully");
                    sendResponse(true, 'Daily sales summary added successfully!', ['summary_id' => $summary_id]);
                    break;

                } catch(PDOException $e) {
                    if ($db->inTransaction()) {
                        $db->rollBack();
                        error_log("Transaction rolled back due to error: " . $e->getMessage());
                    }
                    sendResponse(false, 'Database error: ' . $e->getMessage());
                } catch(Exception $e) {
                    if ($db->inTransaction()) {
                        $db->rollBack();
                        error_log("Transaction rolled back due to general error: " . $e->getMessage());
                    }
                    sendResponse(false, 'Error: ' . $e->getMessage());
                }
            }
            ?>