function checkDependencyStatus(url, statusElement) {
  return $.get(url)
    .then(function (data) {
      const hasOptions = $(data).filter('option[value!=""]').length > 0;
      $(statusElement)
        .removeClass("checking available unavailable")
        .addClass(hasOptions ? "available" : "unavailable")
        .html(
          hasOptions
            ? '<i class="fas fa-check-circle"></i> Available'
            : '<i class="fas fa-times-circle"></i> Not available'
        );
      return hasOptions;
    })
    .catch(function (error) {
      $(statusElement)
        .removeClass("checking available unavailable")
        .addClass("unavailable")
        .html('<i class="fas fa-times-circle"></i> Error checking status');
      console.error("Error checking dependency:", error);
      return false;
    });
}

// Recipe Dependencies
function checkRecipeDependencies() {
  $(".status-item")
    .addClass("checking")
    .html('<i class="fas fa-circle-notch fa-spin"></i> Checking...');

  return Promise.all([
    checkDependencyStatus(
      "includes/get_categories.php",
      "#recipeCategoriaStatus"
    ),
  ]);
}

// Recipe Ingredients Dependencies
function checkRecipeIngredientsDependencies() {
  $(".status-item")
    .addClass("checking")
    .html('<i class="fas fa-circle-notch fa-spin"></i> Checking...');

  return Promise.all([
    checkDependencyStatus(
      "includes/get_recipes.php",
      "#recipeIngredientRecetaStatus"
    ),
    checkDependencyStatus(
      "includes/get_ingredients.php",
      "#recipeIngredientIngredienteStatus"
    ),
  ]);
}

// Recipe Costs Dependencies
function checkRecipeCostsDependencies() {
  $(".status-item")
    .addClass("checking")
    .html('<i class="fas fa-circle-notch fa-spin"></i> Checking...');

  return Promise.all([
    checkDependencyStatus("includes/get_recipes.php", "#recetaStatus"),
    checkDependencyStatus(
      "includes/get_recipe_costs.php",
      "#ingredientesStatus"
    ),
  ]);
}

// Sales Dependencies
function checkSalesDependencies() {
  $(".status-item")
    .addClass("checking")
    .html('<i class="fas fa-circle-notch fa-spin"></i> Checking...');

  return Promise.all([
    checkDependencyStatus("includes/get_recipes.php", "#salesRecetaStatus"),
    checkDependencyStatus("includes/get_recipe_costs.php", "#salesCostoStatus"),
    checkDependencyStatus(
      "includes/get_order_types.php",
      "#salesOrderTypeStatus"
    ),
  ]);
}

// Initialize dependencies check on tab show
$(document).ready(function () {
  // Recipe tab
  $("#recipes-section").on("shown.bs.tab", function () {
    checkRecipeDependencies();
  });

  // Recipe Ingredients tab
  $("#receta-ingredientes").on("shown.bs.tab", function () {
    checkRecipeIngredientsDependencies();
  });

  // Recipe Costs tab
  $("#costos_receta").on("shown.bs.tab", function () {
    checkRecipeCostsDependencies();
  });

  // Sales tab
  $("#fact_sales").on("shown.bs.tab", function () {
    checkSalesDependencies();
  });

  // Add CSS for status indicators
  $("<style>")
    .text(
      `
            .status-item {
                margin: 5px 0;
                padding: 5px 0;
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
        `
    )
    .appendTo("head");
});
