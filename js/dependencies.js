function checkDependencyStatus(url, statusElement, dependencyName) {
  return $.get(url)
    .then(function (data) {
      const $data = $(data);
      const hasOptions = $data.filter('option[value!=""]').length > 0;

      $(statusElement)
        .removeClass("checking available unavailable")
        .addClass(hasOptions ? "available" : "unavailable")
        .html(
          hasOptions
            ? `<i class="fas fa-check-circle"></i> ${dependencyName} available`
            : `<i class="fas fa-times-circle"></i> No ${dependencyName} available - Create ${dependencyName.toLowerCase()} first`
        );
      return hasOptions;
    })
    .catch(function (error) {
      $(statusElement)
        .removeClass("checking available unavailable")
        .addClass("unavailable")
        .html(
          `<i class="fas fa-times-circle"></i> Error checking ${dependencyName.toLowerCase()}`
        );
      console.error("Error checking dependency:", error);
      return false;
    });
}

// Ingredient Dependencies
function checkIngredientDependencies() {
  $(".status-item")
    .addClass("checking")
    .html(
      '<i class="fas fa-circle-notch fa-spin"></i> Checking for units of measurement...'
    );

  return checkDependencyStatus(
    "includes/get_units.php",
    "#unidadMedidaStatus",
    "Units"
  );
}

// Recipe Dependencies
function checkRecipeDependencies() {
  $(".status-item")
    .addClass("checking")
    .html('<i class="fas fa-circle-notch fa-spin"></i> Checking...');

  return checkDependencyStatus(
    "includes/get_categories.php",
    "#recipeCategoriaStatus",
    "Categories"
  );
}

// Recipe Ingredients Dependencies
function checkRecipeIngredientsDependencies() {
  $(".status-item")
    .addClass("checking")
    .html('<i class="fas fa-circle-notch fa-spin"></i> Checking...');

  Promise.all([
    checkDependencyStatus(
      "includes/get_recipes.php",
      "#recipeIngredientRecetaStatus",
      "Recipes"
    ),
    checkDependencyStatus(
      "includes/get_ingredients.php",
      "#recipeIngredientIngredienteStatus",
      "Ingredients"
    ),
  ]);
}

// Recipe Costs Dependencies
function checkRecipeCostsDependencies() {
  $(".status-item")
    .addClass("checking")
    .html('<i class="fas fa-circle-notch fa-spin"></i> Checking...');

  Promise.all([
    checkDependencyStatus(
      "includes/get_recipes.php",
      "#recetaStatus",
      "Recipes"
    ),
    checkDependencyStatus(
      "includes/get_recipe_costs.php",
      "#ingredientesStatus",
      "Recipe ingredients"
    ),
  ]);
}

// Sales Dependencies
function checkSalesDependencies() {
  $(".status-item")
    .addClass("checking")
    .html('<i class="fas fa-circle-notch fa-spin"></i> Checking...');

  Promise.all([
    checkDependencyStatus(
      "includes/get_recipes.php",
      "#salesRecetaStatus",
      "Recipes"
    ),
    checkDependencyStatus(
      "includes/get_recipe_costs.php",
      "#salesCostoStatus",
      "Recipe costs"
    ),
    checkDependencyStatus(
      "includes/get_order_types.php",
      "#salesOrderTypeStatus",
      "Order types"
    ),
  ]);
}

// Initialize all event listeners for tab changes
document.addEventListener("DOMContentLoaded", function () {
  // Add CSS for status indicators if not already in main CSS
  if (!document.querySelector("#dependency-status-styles")) {
    $('<style id="dependency-status-styles">')
      .text(
        `
                .status-item {
                    margin: 5px 0;
                    padding: 5px 0;
                    transition: all 0.3s ease;
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
                .status-item i {
                    margin-right: 5px;
                    width: 16px;
                    text-align: center;
                }
            `
      )
      .appendTo("head");
  }

  // Ingredient tab
  $('button[data-bs-target="#ingrediente"]').on("shown.bs.tab", function (e) {
    checkIngredientDependencies();
  });

  // Recipe tab
  $('button[data-bs-target="#receta"]').on("shown.bs.tab", function (e) {
    checkRecipeDependencies();
  });

  // Recipe Ingredients tab
  $('button[data-bs-target="#receta_ingredientes"]').on(
    "shown.bs.tab",
    function (e) {
      checkRecipeIngredientsDependencies();
    }
  );

  // Recipe Costs tab
  $('button[data-bs-target="#costos_receta"]').on("shown.bs.tab", function (e) {
    checkRecipeCostsDependencies();
  });

  // Sales tab
  $('button[data-bs-target="#fact_sales"]').on("shown.bs.tab", function (e) {
    checkSalesDependencies();
  });

  // Run initial check for the active tab
  const activeTab = $(".tab-pane.active").attr("id");
  switch (activeTab) {
    case "ingrediente":
      checkIngredientDependencies();
      break;
    case "receta":
      checkRecipeDependencies();
      break;
    case "receta_ingredientes":
      checkRecipeIngredientsDependencies();
      break;
    case "costos_receta":
      checkRecipeCostsDependencies();
      break;
    case "fact_sales":
      checkSalesDependencies();
      break;
  }
});
