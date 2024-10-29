<?php
require_once 'config.php';

header('Content-Type: application/json');

function sendResponse($success, $message) {
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->connect();

    $form_type = $_POST['form_type'];

    try {
        switch($form_type) {
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
                sendResponse(true, 'Category added successfully!');
                break;

            case 'costos_receta':
                if (!empty($_POST['costo_receta_id'])) {
                    $query = "INSERT INTO costos_receta (costo_receta_id, receta_id, receta_ingredientes_id, costo_total_materia_prima, 
                             margen_error_porcentaje, costo_total_preparacion, costo_por_porcion, porcentaje_costo_mp, precio_potencial_venta,
                             impuesto_consumo_porcentaje, precio_venta, precio_carta, precio_real_venta, iva_por_porcion, porcentaje_real_costo) 
                             VALUES (:costo_receta_id, :receta_id, :receta_ingredientes_id, :costo_total_materia_prima, :margen_error_porcentaje,
                             :costo_total_preparacion, :costo_por_porcion, :porcentaje_costo_mp, :precio_potencial_venta, :impuesto_consumo_porcentaje,
                             :precio_venta, :precio_carta, :precio_real_venta, :iva_por_porcion, :porcentaje_real_costo)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':costo_receta_id', $_POST['costo_receta_id']);
                } else {
                    $query = "INSERT INTO costos_receta (receta_id, receta_ingredientes_id, costo_total_materia_prima, margen_error_porcentaje,
                             costo_total_preparacion, costo_por_porcion, porcentaje_costo_mp, precio_potencial_venta, impuesto_consumo_porcentaje,
                             precio_venta, precio_carta, precio_real_venta, iva_por_porcion, porcentaje_real_costo) 
                             VALUES (:receta_id, :receta_ingredientes_id, :costo_total_materia_prima, :margen_error_porcentaje, :costo_total_preparacion,
                             :costo_por_porcion, :porcentaje_costo_mp, :precio_potencial_venta, :impuesto_consumo_porcentaje, :precio_venta,
                             :precio_carta, :precio_real_venta, :iva_por_porcion, :porcentaje_real_costo)";
                    $stmt = $db->prepare($query);
                }
                $stmt->bindParam(':receta_id', $_POST['receta_id']);
                $stmt->bindParam(':receta_ingredientes_id', $_POST['receta_ingredientes_id']);
                $stmt->bindParam(':costo_total_materia_prima', $_POST['costo_total_materia_prima']);
                $stmt->bindParam(':margen_error_porcentaje', $_POST['margen_error_porcentaje']);
                $stmt->bindParam(':costo_total_preparacion', $_POST['costo_total_preparacion']);
                $stmt->bindParam(':costo_por_porcion', $_POST['costo_por_porcion']);
                $stmt->bindParam(':porcentaje_costo_mp', $_POST['porcentaje_costo_mp']);
                $stmt->bindParam(':precio_potencial_venta', $_POST['precio_potencial_venta']);
                $stmt->bindParam(':impuesto_consumo_porcentaje', $_POST['impuesto_consumo_porcentaje']);
                $stmt->bindParam(':precio_venta', $_POST['precio_venta']);
                $stmt->bindParam(':precio_carta', $_POST['precio_carta']);
                $stmt->bindParam(':precio_real_venta', $_POST['precio_real_venta']);
                $stmt->bindParam(':iva_por_porcion', $_POST['iva_por_porcion']);
                $stmt->bindParam(':porcentaje_real_costo', $_POST['porcentaje_real_costo']);
                $stmt->execute();
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
                sendResponse(true, 'Order type added successfully!');
                break;

            case 'fact_sales':
                if (!empty($_POST['sale_id'])) {
                    $query = "INSERT INTO fact_sales (sale_id, sale_date, receta_id, costo_receta_id, order_type_id, quantity, total_amount, 
                             discount_amount, tip_amount) VALUES (:sale_id, :sale_date, :receta_id, :costo_receta_id, :order_type_id, :quantity,
                             :total_amount, :discount_amount, :tip_amount)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':sale_id', $_POST['sale_id']);
                } else {
                    $query = "INSERT INTO fact_sales (sale_date, receta_id, costo_receta_id, order_type_id, quantity, total_amount, 
                             discount_amount, tip_amount) VALUES (:sale_date, :receta_id, :costo_receta_id, :order_type_id, :quantity,
                             :total_amount, :discount_amount, :tip_amount)";
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
                $stmt->execute();
                sendResponse(true, 'Sale added successfully!');
                break;

            case 'ingrediente':
                if (!empty($_POST['ingrediente_id'])) {
                    $query = "INSERT INTO ingrediente (ingrediente_id, nombre, descripcion, costo_unitario, cantidad_disponible, unidad_id, is_active) 
                             VALUES (:ingrediente_id, :nombre, :descripcion, :costo_unitario, :cantidad_disponible, :unidad_id, :is_active)";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':ingrediente_id', $_POST['ingrediente_id']);
                } else {
                    $query = "INSERT INTO ingrediente (nombre, descripcion, costo_unitario, cantidad_disponible, unidad_id, is_active) 
                             VALUES (:nombre, :descripcion, :costo_unitario, :cantidad_disponible, :unidad_id, :is_active)";
                    $stmt = $db->prepare($query);
                }
                $stmt->bindParam(':nombre', $_POST['nombre']);
                $stmt->bindParam(':descripcion', $_POST['descripcion']);
                $stmt->bindParam(':costo_unitario', $_POST['costo_unitario']);
                $stmt->bindParam(':cantidad_disponible', $_POST['cantidad_disponible']);
                $stmt->bindParam(':unidad_id', $_POST['unidad_id']);
                $stmt->bindParam(':is_active', $_POST['is_active']);
                $stmt->execute();
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
                sendResponse(true, 'Unit of measurement added successfully!');
                break;

            default:
                sendResponse(false, 'Invalid form type.');
        }
    } catch(PDOException $e) {
        sendResponse(false, 'Database error: ' . $e->getMessage());
    } catch(Exception $e) {
        sendResponse(false, 'Error: ' . $e->getMessage());
    }
}
?>
