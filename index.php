<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="css/main.css" rel="stylesheet">
    <link href="css/forms.css" rel="stylesheet">
    <link href="css/sales.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Sakura - Restaurant Management System</h2>
        <div class="toast-container"></div>

        <!-- Main tab groups -->
        <ul class="nav nav-pills mb-4" id="mainTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ingredients-tab" data-bs-toggle="pill" 
                        data-bs-target="#ingredients-section" type="button" role="tab" 
                        aria-controls="ingredients-section" aria-selected="true">
                    <i class="fas fa-carrot"></i> Ingredients Management
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="recipes-tab" data-bs-toggle="pill" 
                        data-bs-target="#recipes-section" type="button" role="tab"
                        aria-controls="recipes-section" aria-selected="false">
                    <i class="fas fa-book"></i> Recipes Management
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sales-tab" data-bs-toggle="pill" 
                        data-bs-target="#sales-section" type="button" role="tab"
                        aria-controls="sales-section" aria-selected="false">
                    <i class="fas fa-cash-register"></i> Sales Management
                </button>
            </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content" id="mainTabContent">
            <!-- Ingredients Management Section -->
            <div class="tab-pane fade show active" id="ingredients-section" role="tabpanel" aria-labelledby="ingredients-tab">
                <h4 class="section-title"><i class="fas fa-carrot"></i> Ingredients Management</h4>
                <ul class="nav nav-tabs" id="ingredientsTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="unidad-medida-tab" data-bs-toggle="tab" 
                                data-bs-target="#unidad_medida" type="button" role="tab">
                            <i class="fas fa-ruler"></i> Unidad Medida
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="categoria-tab" data-bs-toggle="tab" 
                                data-bs-target="#categoria" type="button" role="tab">
                            <i class="fas fa-tags"></i> Categoría
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ingrediente-tab" data-bs-toggle="tab" 
                                data-bs-target="#ingrediente" type="button" role="tab">
                            <i class="fas fa-pepper-hot"></i> Ingrediente
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="ingredientsTabContent">
                    <?php include 'forms/unidad_medida_form.php'; ?>
                    <?php include 'forms/categoria_form.php'; ?>
                    <?php include 'forms/ingrediente_form.php'; ?>
                </div>
            </div>

            <!-- Recipes Management Section -->
            <div class="tab-pane fade" id="recipes-section" role="tabpanel" aria-labelledby="recipes-tab">
                <h4 class="section-title"><i class="fas fa-book"></i> Recipes Management</h4>
                <ul class="nav nav-tabs" id="recipesTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="receta-tab" data-bs-toggle="tab" 
                                data-bs-target="#receta" type="button" role="tab">
                            <i class="fas fa-scroll"></i> Receta
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="receta-ingredientes-tab" data-bs-toggle="tab" 
                                data-bs-target="#receta_ingredientes" type="button" role="tab">
                            <i class="fas fa-mortar-pestle"></i> Receta Ingredientes
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="costos-receta-tab" data-bs-toggle="tab" 
                                data-bs-target="#costos_receta" type="button" role="tab">
                            <i class="fas fa-calculator"></i> Costos Receta
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="recipesTabContent">
                    <?php include 'forms/receta_form.php'; ?>
                    <?php include 'forms/receta_ingredientes_form.php'; ?>
                    <?php include 'forms/costos_receta_form.php'; ?>
                </div>
            </div>

            <!-- Sales Management Section -->
            <div class="tab-pane fade" id="sales-section" role="tabpanel" aria-labelledby="sales-tab">
                <h4 class="section-title"><i class="fas fa-cash-register"></i> Sales Management</h4>
                <ul class="nav nav-tabs" id="salesTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="dim-order-tab" data-bs-toggle="tab" 
                                data-bs-target="#dim_order" type="button" role="tab">
                            <i class="fas fa-clipboard-list"></i> Order Types
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="fact-sales-tab" data-bs-toggle="tab" 
                                data-bs-target="#fact_sales" type="button" role="tab">
                            <i class="fas fa-receipt"></i> Sales Records
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="salesTabContent">
                    <?php include 'forms/dim_order_form.php'; ?>
                    <?php include 'forms/fact_sales_form.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery first, then Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom Scripts -->
    <script src="js/form-handlers.js"></script>
    <script src="js/toast.js"></script>
    <script src="js/dependencies.js"></script>
    <script src="js/recipe-costs.js"></script>
    <script src="js/sales-entry.js"></script>
    <script src="js/chatbot.js"></script>

</body>
</html>
