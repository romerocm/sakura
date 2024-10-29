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
    <style>
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
        .manual-id-toggle {
            margin-bottom: 15px;
        }
        .id-input {
            display: none;
        }
        .nav-pills .nav-link {
            margin-right: 5px;
        }
        .nav-pills .nav-link i {
            margin-right: 5px;
        }
        .section-title {
            margin: 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }
        .tab-pane {
            padding: 20px 0;
        }
        .nested-tab {
            margin-top: 15px;
        }
        .status-item {
            margin: 5px 0;
            padding: 5px 0;
            transition: color 0.3s ease;
        }
        .status-item.checking {
            color: #666;
        }
        .status-item.available {
            color: #28a745;
        }
        .status-item.unavailable {
            color: #dc3545;
        }
        .dependency-status .card {
            margin-bottom: 20px;
            border-left: 4px solid #0d6efd;
        }
        .dependency-status .card-body {
            padding: 15px;
        }
        .alert-info {
            border-left: 4px solid #0dcaf0;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="mb-4">Sakura - Restaurant Management System</h2>
        <div class="toast-container"></div>

        <!-- Main tab groups -->
        <ul class="nav nav-pills mb-3" id="mainTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ingredients-tab" data-bs-toggle="pill" 
                        data-bs-target="#ingredients-section" type="button" role="tab">
                    <i class="fas fa-carrot"></i> Ingredients Management
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="recipes-tab" data-bs-toggle="pill" 
                        data-bs-target="#recipes-section" type="button" role="tab">
                    <i class="fas fa-book"></i> Recipes Management
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sales-tab" data-bs-toggle="pill" 
                        data-bs-target="#sales-section" type="button" role="tab">
                    <i class="fas fa-cash-register"></i> Sales Management
                </button>
            </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content" id="mainTabContent">
            <!-- Ingredients Management Section -->
            <div class="tab-pane fade show active" id="ingredients-section">
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
                <div class="tab-content mt-3" id="ingredientsTabContent">
                    <?php
                    include 'forms/unidad_medida_form.php';
                    include 'forms/categoria_form.php';
                    include 'forms/ingrediente_form.php';
                    ?>
                </div>
            </div>

            <!-- Recipes Management Section -->
            <div class="tab-pane fade" id="recipes-section">
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
                <div class="tab-content mt-3" id="recipesTabContent">
                    <?php
                    include 'forms/receta_form.php';
                    include 'forms/receta_ingredientes_form.php';
                    include 'forms/costos_receta_form.php';
                    ?>
                </div>
            </div>

            <!-- Sales Management Section -->
            <div class="tab-pane fade" id="sales-section">
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
                <div class="tab-content mt-3" id="salesTabContent">
                    <?php
                    include 'forms/dim_order_form.php';
                    include 'forms/fact_sales_form.php';
                    ?>
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

    <script>
        // Form handling
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle manual ID inputs
            document.querySelectorAll('.manual-id-toggle input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const form = this.closest('form');
                    const idInput = form.querySelector('.id-input');
                    idInput.style.display = this.checked ? 'block' : 'none';
                    if (!this.checked) {
                        idInput.querySelector('input').value = '';
                    }
                });
            });

            // Handle form submissions
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    fetch('process.php', {
                        method: 'POST',
                        body: new FormData(this)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message, 'success');
                            this.reset();
                            loadSelectOptions();
                            // Recheck dependencies after successful form submission
                            checkAllDependencies();
                        } else {
                            showToast(data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        showToast('An error occurred while processing your request.', 'danger');
                        console.error('Error:', error);
                    });
                });
            });

            // Load select options
            function loadSelectOptions() {
                $.get('includes/get_categories.php', function(data) {
                    $('#categoria_select').html(data);
                });
                
                $.get('includes/get_units.php', function(data) {
                    $('#unidad_id').html(data);
                });

                $.get('includes/get_recipes.php', function(data) {
                    $('#receta_select').html(data);
                });

                $.get('includes/get_ingredients.php', function(data) {
                    $('#ingrediente_select').html(data);
                });

                $.get('includes/get_order_types.php', function(data) {
                    $('#order_type_id').html(data);
                });

                $.get('includes/get_recipe_costs.php', function(data) {
                    $('#costo_receta_id').html(data);
                });
            }

            // Function to check all dependencies
            function checkAllDependencies() {
                const activeTabId = $('.tab-pane.active').attr('id');
                switch(activeTabId) {
                    case 'recipes-section':
                        checkRecipeDependencies();
                        break;
                    case 'receta_ingredientes':
                        checkRecipeIngredientsDependencies();
                        break;
                    case 'costos_receta':
                        checkRecipeCostsDependencies();
                        break;
                    case 'fact_sales':
                        checkSalesDependencies();
                        break;
                }
            }

            // Initial load of select options
            loadSelectOptions();
        });
    </script>
</body>
</html>