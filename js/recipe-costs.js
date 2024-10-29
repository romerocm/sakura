function loadRecipeOptions() {
  console.log("Loading recipes..."); // Debug log

  // Show loading state
  $("#receta_select")
    .prop("disabled", true)
    .html('<option value="">Loading recipes...</option>');

  // Use jQuery get with explicit content type
  $.ajax({
    url: "includes/get_recipes.php",
    method: "GET",
    dataType: "html",
    success: function (data) {
      console.log("Recipes HTML received:", data);
      // Directly insert the HTML
      $("#receta_select").html(data).prop("disabled", false);

      // After loading recipes, only update dependency status
      updateDependencyStatus();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error("Error loading recipes:", textStatus, errorThrown);
      $("#receta_select")
        .html('<option value="">Error loading recipes</option>')
        .prop("disabled", false);
      showToast("Error loading recipes", "danger");
    },
  });
}

function updateDependencyStatus() {
  // Check for recipes
  $.get("includes/get_recipes.php", function (data) {
    console.log("Checking recipes status..."); // Debug log
    const hasRecipes = $(data).filter('option[value!=""]').length > 0;
    $("#recetaStatus")
      .removeClass("checking available unavailable")
      .addClass(hasRecipes ? "available" : "unavailable")
      .html(
        hasRecipes
          ? '<i class="fas fa-check-circle"></i> Recipes available'
          : '<i class="fas fa-times-circle"></i> No recipes available - Create a recipe first'
      );
  });

  // Only check for ingredients if we're on the costos_receta tab
  if ($("#costos_receta").hasClass("active")) {
    const recipeId = $("#receta_select").val();
    if (recipeId) {
      $.get("includes/get_recipe_ingredients_costs.php", {
        recipe_id: recipeId,
      })
        .done(function (data) {
          console.log("Checking recipe ingredients status..."); // Debug log
          const hasIngredients =
            data.ingredients && data.ingredients.length > 0;
          $("#ingredientesStatus")
            .removeClass("checking available unavailable")
            .addClass(hasIngredients ? "available" : "unavailable")
            .html(
              hasIngredients
                ? '<i class="fas fa-check-circle"></i> Recipe ingredients available'
                : '<i class="fas fa-times-circle"></i> No Recipe ingredients available - Create recipe ingredients first'
            );

          // Only calculate costs if we're on the costos_receta tab
          if ($("#costos_receta").hasClass("active")) {
            calculateRecipeCosts();
          }
        })
        .fail(function (error) {
          console.error("Error checking ingredients:", error);
          $("#ingredientesStatus")
            .removeClass("checking available unavailable")
            .addClass("unavailable")
            .html(
              '<i class="fas fa-times-circle"></i> Error checking recipe ingredients'
            );
        });
    }
  }
}

// Initialize when document is ready
$(document).ready(function () {
  console.log("Document ready"); // Debug log

  // Load recipes when any tab is shown
  $(".nav-link").on("shown.bs.tab", function (e) {
    console.log("Tab shown:", e.target); // Debug log
    loadRecipeOptions();
  });

  // Handle recipe selection change only in costos_receta tab
  $("#receta_select").on("change", function () {
    console.log("Recipe selection changed"); // Debug log
    const recipeId = $(this).val();
    if ($("#costos_receta").hasClass("active")) {
      if (recipeId) {
        calculateRecipeCosts();
      } else {
        resetCalculations();
      }
    }
  });

  // Handle input changes that should trigger recalculation
  $(
    "#margen_error_porcentaje, #porcentaje_costo_mp, #impuesto_consumo_porcentaje"
  ).on("input", function () {
    if ($("#costos_receta").hasClass("active")) {
      console.log("Input value changed"); // Debug log
      calculateRecipeCosts();
    }
  });

  // Form submission handler with refresh of dependencies
  $(".nav-item form").on("submit", function (e) {
    e.preventDefault();
    console.log("Form submitted"); // Debug log

    const formData = new FormData(this);

    $.ajax({
      url: "process.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          showToast(response.message, "success");
          // Reload dependencies after successful save
          loadRecipeOptions();
          // Reset form if needed
          if ($("#costos_receta").hasClass("active")) {
            resetCalculations();
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

  // Initial load of recipes
  loadRecipeOptions();
});
