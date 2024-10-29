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
      console.log("Recipes HTML received");
      // Directly insert the HTML
      $("#receta_select").html(data).prop("disabled", false);

      // Only update dependency status, don't calculate
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

  // Check for recipe ingredients only if a recipe is selected
  const recipeId = $("#receta_select").val();
  if (recipeId && recipeId !== "") {
    $.get("includes/get_recipe_ingredients_costs.php", {
      recipe_id: recipeId,
    }).done(function (data) {
      console.log("Checking recipe ingredients status..."); // Debug log
      const hasIngredients = data.ingredients && data.ingredients.length > 0;
      $("#ingredientesStatus")
        .removeClass("checking available unavailable")
        .addClass(hasIngredients ? "available" : "unavailable")
        .html(
          hasIngredients
            ? '<i class="fas fa-check-circle"></i> Recipe ingredients available'
            : '<i class="fas fa-times-circle"></i> No Recipe ingredients available - Create recipe ingredients first'
        );
    });
  } else {
    // Clear ingredient status if no recipe selected
    $("#ingredientesStatus")
      .removeClass("checking available unavailable")
      .addClass("checking")
      .html('<i class="fas fa-info-circle"></i> Select a recipe first');
  }
}

function calculateRecipeCosts() {
  const recipeId = $("#receta_select").val();

  if (!recipeId || recipeId === "") {
    resetCalculations();
    return;
  }

  console.log("Calculating costs for recipe:", recipeId);

  $.get("includes/get_recipe_ingredients_costs.php", { recipe_id: recipeId })
    .done(function (data) {
      if (data.error) {
        showToast(data.error, "danger");
        resetCalculations();
        return;
      }

      try {
        // Get basic values
        const costoTotalMateriaPrima = data.ingredients.reduce(
          (sum, item) => sum + parseFloat(item.costo_total || 0),
          0
        );

        // Get form input values
        const margenError =
          parseFloat($("#margen_error_porcentaje").val()) || 10;
        const numeroPorciones = parseFloat(data.recipe.numero_porciones) || 1;
        const porcentajeCostoMP =
          parseFloat($("#porcentaje_costo_mp").val()) || 35;
        const impuestoConsumo =
          parseFloat($("#impuesto_consumo_porcentaje").val()) || 13;

        // Update all calculations
        // ... rest of your calculation code ...
      } catch (error) {
        console.error("Error in calculations:", error);
        showToast("Error in calculations: " + error.message, "danger");
        resetCalculations();
      }
    })
    .fail(function (error) {
      console.error("Error fetching recipe data:", error);
      showToast("Error calculating recipe costs", "danger");
      resetCalculations();
    });
}

function resetCalculations() {
  const fieldsToReset = [
    "costo_total_materia_prima",
    "costo_total_preparacion",
    "costo_por_porcion",
    "precio_potencial_venta",
    "precio_venta",
    "precio_real_venta",
    "iva_por_porcion",
    "porcentaje_real_costo",
  ];

  fieldsToReset.forEach((fieldId) => {
    $(`#${fieldId}`).val("0.00");
  });
}

// Initialize when document is ready
$(document).ready(function () {
  console.log("Document ready"); // Debug log

  // Only load recipes initially, don't calculate
  loadRecipeOptions();

  // Load recipes when the costos_receta tab is shown
  $('button[data-bs-target="#costos_receta"]').on("shown.bs.tab", function (e) {
    console.log("Costos Receta tab shown"); // Debug log
    loadRecipeOptions();
    // Don't calculate here, wait for recipe selection
  });

  // Handle recipe selection change
  $("#receta_select").on("change", function () {
    console.log("Recipe selection changed"); // Debug log
    const recipeId = $(this).val();
    if (recipeId && recipeId !== "") {
      calculateRecipeCosts();
    } else {
      resetCalculations();
    }
    updateDependencyStatus();
  });

  // Handle input changes that should trigger recalculation
  $(
    "#margen_error_porcentaje, #porcentaje_costo_mp, #impuesto_consumo_porcentaje"
  ).on("input", function () {
    const recipeId = $("#receta_select").val();
    if (recipeId && recipeId !== "") {
      console.log("Input value changed"); // Debug log
      calculateRecipeCosts();
    }
  });

  // Form submission handler
  $("#costosRecetaForm").on("submit", function (e) {
    e.preventDefault();

    const recipeId = $("#receta_select").val();
    if (!recipeId || recipeId === "") {
      showToast("Please select a recipe first", "warning");
      return;
    }

    // Get form data
    const formData = new FormData(this);

    // Submit form data
    $.ajax({
      url: "process.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.success) {
          showToast(response.message, "success");
          resetCalculations();
          $("#costosRecetaForm")[0].reset();
          loadRecipeOptions();
        } else {
          showToast(response.message || "Error saving recipe costs", "danger");
        }
      },
      error: function () {
        showToast("Error saving recipe costs", "danger");
      },
    });
  });
});
