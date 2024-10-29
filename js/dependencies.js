function checkDependencyStatus() {
  console.log("Checking dependencies...");

  // Check current tab
  const currentTab = $(".tab-pane.active").attr("id");
  console.log("Current tab:", currentTab);

  // Only check recipes if in relevant tabs
  if (currentTab === "costos_receta" || currentTab === "receta_ingredientes") {
    // Check for recipes
    $.get("includes/get_recipes.php", function (data) {
      const hasRecipes = $(data).filter('option[value!=""]').length > 0;
      $("#recetaStatus")
        .removeClass("checking available unavailable")
        .addClass(hasRecipes ? "available" : "unavailable")
        .html(
          hasRecipes
            ? '<i class="fas fa-check-circle"></i> Recipes available'
            : '<i class="fas fa-times-circle"></i> No recipes available - Create a recipe first'
        );

      // Only check ingredients if we have recipes and are in the right tab
      if (hasRecipes && currentTab === "costos_receta") {
        const selectedRecipe = $("#receta_select").val();
        if (selectedRecipe) {
          checkRecipeIngredients(selectedRecipe);
        }
      }
    });
  }
}

function checkRecipeIngredients(recipeId) {
  if (!recipeId) return;

  $.get("includes/get_ingredient_costs.php", { recipe_id: recipeId })
    .done(function (data) {
      const hasIngredients = data.ingredients && data.ingredients.length > 0;
      $("#ingredientesStatus")
        .removeClass("checking available unavailable")
        .addClass(hasIngredients ? "available" : "unavailable")
        .html(
          hasIngredients
            ? '<i class="fas fa-check-circle"></i> Recipe ingredients available'
            : '<i class="fas fa-times-circle"></i> No Recipe ingredients available - Create recipe ingredients first'
        );
    })
    .fail(function (error) {
      console.error("Error checking ingredients:", error);
      $("#ingredientesStatus")
        .removeClass("checking available unavailable")
        .addClass("unavailable")
        .html('<i class="fas fa-times-circle"></i> Error checking ingredients');
    });
}

// Initialize when document is ready
$(document).ready(function () {
  console.log("Dependencies.js loaded");

  // Check dependencies when switching tabs
  $(".nav-link").on("shown.bs.tab", function (e) {
    console.log("Tab changed");
    checkDependencyStatus();
  });

  // Check dependencies when recipe is selected
  $(document).on("change", 'select[name="receta_id"]', function () {
    console.log("Recipe changed");
    const recipeId = $(this).val();
    if (recipeId) {
      checkRecipeIngredients(recipeId);
    }
  });

  // Initial check
  checkDependencyStatus();
});
