<?php
require_once '../config.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT cr.costo_receta_id, rs.nombre_receta 
              FROM costos_receta cr 
              JOIN receta_standar rs ON cr.receta_id = rs.receta_id 
              ORDER BY rs.nombre_receta";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    echo "<option value=''>Select Recipe Cost</option>";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='" . htmlspecialchars($row['costo_receta_id']) . "'>" . htmlspecialchars($row['nombre_receta']) . "</option>";
    }
} catch(PDOException $e) {
    echo "<option value=''>Error loading recipe costs</option>";
}
?>
