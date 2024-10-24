<?php
require_once '../config.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->connect();
    
    $query = "SELECT receta_id, nombre_receta FROM receta_standar ORDER BY nombre_receta";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    echo "<option value=''>Select Recipe</option>";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='" . htmlspecialchars($row['receta_id']) . "'>" . htmlspecialchars($row['nombre_receta']) . "</option>";
    }
} catch(PDOException $e) {
    echo "<option value=''>Error loading recipes</option>";
}
?>
