function checkDependencyStatus(url, statusElement, dependencyName) {
  return $.get(url)
    .then(function (data) {
      console.log("Dependency check response for", dependencyName, ":", data);
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

function updateRecipeIngredientsDependencies() {
  const recipeId = $("#receta_select").val() || $("#receta_id").val();
  console.log("Checking ingredients for recipe:", recipeId);

  if (recipeId) {
    $.get("includes/get_recipe_ingredients_costs.php", {
      recipe_id: recipeId,
    }).done(function (data) {
      const hasIngredients = data.ingredients && data.ingredients.length > 0;
      $("#ingredientesStatus")
        .removeClass("checking available unavailable")
        .addClass(hasIngredients ? "available" : "unavailable")
        .html(
          hasIngredients
            ? '<i class="fas fa-check-circle"></i> Recipe ingredients available'
            : '<i class="fas fa-times-circle"></i> No Recipe ingredients available - Create recipe ingredients first'
        );

      // Only trigger cost calculations in costos_receta tab
      if (
        $("#costos_receta").hasClass("active") &&
        typeof calculateRecipeCosts === "function"
      ) {
        calculateRecipeCosts();
      }
    });
  } else {
    $("#ingredientesStatus")
      .removeClass("checking available unavailable")
      .addClass("checking")
      .html('<i class="fas fa-info-circle"></i> Select a recipe first');
  }
}

// Initialize when document is ready
$(document).ready(function () {
  console.log("Dependencies.js loaded");

  // Function to check dependencies based on current tab
  function checkCurrentTabDependencies() {
    const activeTabId = $(".tab-pane.active").attr("id");
    console.log("Checking dependencies for tab:", activeTabId);

    // Check recipes availability for relevant tabs
    if (
      ["costos_receta", "receta_ingredientes", "fact_sales"].includes(
        activeTabId
      )
    ) {
      checkDependencyStatus(
        "includes/get_recipes.php",
        "#recetaStatus",
        "Recipes"
      ).then(function (hasRecipes) {
        if (hasRecipes) {
          const selectedRecipe =
            $("#receta_select").val() || $("#receta_id").val();
          if (selectedRecipe) {
            updateRecipeIngredientsDependencies();
          }
        }
      });
    }
  }

  // Check dependencies when switching tabs
  $(".nav-link").on("shown.bs.tab", function (e) {
    checkCurrentTabDependencies();
  });

  // Handle recipe selection changes in any form
  $(document).on("change", 'select[name="receta_id"]', function () {
    const activeTabId = $(".tab-pane.active").attr("id");
    updateRecipeIngredientsDependencies();
  });

  // Check dependencies after form submissions
  $("form").on("submit", function (e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    $.ajax({
      url: "process.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          showToast(response.message, "success");
          // Reload dependencies after successful submission
          setTimeout(checkCurrentTabDependencies, 500);
          // Reset form if needed
          if (
            $("#costos_receta").hasClass("active") &&
            typeof resetCalculations === "function"
          ) {
            resetCalculations();
          }
          // Reload select options
          if (typeof loadSelectOptions === "function") {
            loadSelectOptions();
          }
        } else {
          showToast(response.message || "Error saving data", "danger");
        }
      },
      error: function () {
        showToast("Error saving data", "danger");
      },
    });
  });

  // Initial dependency check
  checkCurrentTabDependencies();
});
