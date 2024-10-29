function checkDependencyStatus(url, statusElement, dependencyName) {
  return $.get(url)
    .then(function (data) {
      console.log("Dependency check response:", data); // Debug log
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
    .fail(function (error) {
      console.error("Error checking dependency:", error);
      $(statusElement)
        .removeClass("checking available unavailable")
        .addClass("unavailable")
        .html(
          `<i class="fas fa-times-circle"></i> Error checking ${dependencyName.toLowerCase()}`
        );
      return false;
    });
}

// Recipe Dependencies
function checkRecipeDependencies() {
  $(".status-item")
    .addClass("checking")
    .html('<i class="fas fa-circle-notch fa-spin"></i> Checking...');

  return checkDependencyStatus(
    "includes/get_recipes.php",
    "#recetaStatus",
    "Recipes"
  );
}

// Recipe Ingredients Dependencies
function checkRecipeIngredientsDependencies() {
  $(".status-item")
    .addClass("checking")
    .html('<i class="fas fa-circle-notch fa-spin"></i> Checking...');

  const recipeId = $("#receta_select").val();
  if (recipeId) {
    return $.get("includes/get_recipe_ingredients_costs.php", {
      recipe_id: recipeId,
    })
      .then(function (data) {
        const hasIngredients = data.ingredients && data.ingredients.length > 0;
        $("#ingredientesStatus")
          .removeClass("checking available unavailable")
          .addClass(hasIngredients ? "available" : "unavailable")
          .html(
            hasIngredients
              ? '<i class="fas fa-check-circle"></i> Recipe ingredients available'
              : '<i class="fas fa-times-circle"></i> No Recipe ingredients available - Create recipe ingredients first'
          );
        return hasIngredients;
      })
      .fail(function (error) {
        console.error("Error checking recipe ingredients:", error);
        $("#ingredientesStatus")
          .removeClass("checking available unavailable")
          .addClass("unavailable")
          .html(
            '<i class="fas fa-times-circle"></i> Error checking recipe ingredients'
          );
        return false;
      });
  } else {
    $("#ingredientesStatus")
      .removeClass("checking available unavailable")
      .addClass("unavailable")
      .html('<i class="fas fa-times-circle"></i> Select a recipe first');
    return Promise.resolve(false);
  }
}

// Initialize all event listeners for tab changes
$(document).ready(function () {
  console.log("Dependencies.js loaded"); // Debug log

  // Handle tab changes
  $(".nav-link").on("shown.bs.tab", function (e) {
    const targetId = $(e.target).attr("data-bs-target");
    console.log("Tab shown:", targetId); // Debug log

    // Only check recipe dependencies in relevant tabs
    if (targetId === "#costos_receta" || targetId === "#receta_ingredientes") {
      checkRecipeDependencies().then(function (hasRecipes) {
        if (hasRecipes && targetId === "#costos_receta") {
          checkRecipeIngredientsDependencies();
        }
      });
    }
  });

  // Handle recipe selection changes
  $("#receta_select").on("change", function () {
    const targetTab = $(".tab-pane.active").attr("id");
    console.log("Recipe selection changed in tab:", targetTab); // Debug log

    if (targetTab === "costos_receta") {
      checkRecipeIngredientsDependencies();
    }
  });

  // Handle form submissions
  $("form").on("submit", function (e) {
    const form = this;
    const formType = $(form).find('input[name="form_type"]').val();

    // After successful form submission
    setTimeout(function () {
      const activeTab = $(".tab-pane.active").attr("id");
      console.log("Form submitted in tab:", activeTab); // Debug log

      if (
        activeTab === "costos_receta" ||
        activeTab === "receta_ingredientes"
      ) {
        checkRecipeDependencies().then(function (hasRecipes) {
          if (hasRecipes && activeTab === "costos_receta") {
            checkRecipeIngredientsDependencies();
          }
        });
      }
    }, 500);
  });

  // Initial dependency check if needed
  const activeTab = $(".tab-pane.active").attr("id");
  if (activeTab === "costos_receta" || activeTab === "receta_ingredientes") {
    checkRecipeDependencies().then(function (hasRecipes) {
      if (hasRecipes && activeTab === "costos_receta") {
        checkRecipeIngredientsDependencies();
      }
    });
  }
});
