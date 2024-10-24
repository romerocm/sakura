<?php
require_once '../config.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT unidad_id, nombre FROM unidad_medida ORDER BY nombre";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    echo "<option value=''>Select Unit</option>";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='" . htmlspecialchars($row['unidad_id']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
    }
} catch(PDOException $e) {
    echo "<option value=''>Error loading units</option>";
}
?>
