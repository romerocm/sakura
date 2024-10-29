function checkDependencyStatus(url, statusElement) {
  return $.get(url)
    .then(function (data) {
      // Convert string to jQuery object to check for options
      const $data = $(data);
      const hasOptions = $data.filter('option[value!=""]').length > 0;

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

// Ingredient Dependencies
function checkIngredientDependencies() {
  $(".status-item")
    .addClass("checking")
    .html(
      '<i class="fas fa-circle-notch fa-spin"></i> Checking for units of measurement...'
    );

  return checkDependencyStatus("includes/get_units.php", "#unidadMedidaStatus");
}

// Recipe Dependencies
function checkRecipeDependencies() {
  $(".status-item")
    .addClass("checking")
    .html('<i class="fas fa-circle-notch fa-spin"></i> Checking...');

  return checkDependencyStatus(
    "includes/get_categories.php",
    "#recipeCategoriaStatus"
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

  Promise.all([
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

  Promise.all([
    checkDependencyStatus("includes/get_recipes.php", "#salesRecetaStatus"),
    checkDependencyStatus("includes/get_recipe_costs.php", "#salesCostoStatus"),
    checkDependencyStatus(
      "includes/get_order_types.php",
      "#salesOrderTypeStatus"
    ),
  ]);
}

// Initialize all event listeners for tab changes
document.addEventListener("DOMContentLoaded", function () {
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

  // Run initial check if we're on the ingredients tab
  if ($("#ingrediente").hasClass("active")) {
    checkIngredientDependencies();
  }
});
