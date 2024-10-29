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

      // After loading recipes, update dependency status
      updateDependencyStatus();

      // Check if we should calculate costs (if recipe is selected)
      const selectedRecipe = $("#receta_select").val();
      if (selectedRecipe) {
        calculateRecipeCosts();
      }
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

  // Check for recipe ingredients
  const recipeId = $("#receta_select").val();
  if (recipeId) {
    $.get(
      "includes/get_recipe_ingredients_costs.php",
      { recipe_id: recipeId },
      function (data) {
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
      }
    );
  }
}

function calculateRecipeCosts() {
  const recipeId = $("#receta_select").val();

  if (!recipeId) {
    resetCalculations();
    return;
  }

  console.log("Calculating costs for recipe:", recipeId); // Debug log

  $.get(
    "includes/get_recipe_ingredients_costs.php",
    { recipe_id: recipeId },
    function (data) {
      if (data.error) {
        showToast(data.error, "danger");
        resetCalculations();
        return;
      }

      console.log("Recipe data received:", data); // Debug log

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

        console.log("Initial values:", {
          costoTotalMateriaPrima,
          margenError,
          numeroPorciones,
          porcentajeCostoMP,
          impuestoConsumo,
        });

        // 1. COSTO TOTAL DE LA MATERIA PRIMA
        $("#costo_total_materia_prima").val(costoTotalMateriaPrima.toFixed(2));

        // 2. MARGEN DE ERROR
        const margenErrorValue = costoTotalMateriaPrima * (margenError / 100);

        // 3. COSTO TOTAL DE LA PREPARACION
        const costoTotalPreparacion = costoTotalMateriaPrima + margenErrorValue;
        $("#costo_total_preparacion").val(costoTotalPreparacion.toFixed(2));

        // 4. COSTO POR PORCION
        const costoPorPorcion = costoTotalPreparacion / numeroPorciones;
        $("#costo_por_porcion").val(costoPorPorcion.toFixed(2));

        // 5. PRECIO POTENCIAL DE VENTA
        const precioPotencialVenta =
          costoPorPorcion / (porcentajeCostoMP / 100);
        $("#precio_potencial_venta").val(precioPotencialVenta.toFixed(2));

        // 6. IMPUESTO AL CONSUMO
        const impuestoConsumoValue =
          precioPotencialVenta * (impuestoConsumo / 100);

        // 7. PRECIO VENTA SUGERIDO
        const precioVentaSugerido = precioPotencialVenta + impuestoConsumoValue;
        $("#precio_venta").val(precioVentaSugerido.toFixed(2));

        // 8. PRECIO REAL DE VENTA (SIN IVA)
        const precioRealVentaSinIVA =
          precioVentaSugerido / (1 + impuestoConsumo / 100);
        $("#precio_real_venta").val(precioRealVentaSinIVA.toFixed(2));

        // 9. IVA COBRADO POR PORCION
        const ivaCobradoPorPorcion =
          precioVentaSugerido - precioRealVentaSinIVA;
        $("#iva_por_porcion").val(ivaCobradoPorPorcion.toFixed(2));

        // 10. % REAL DE COSTO MATERIA PRIMA
        const porcentajeRealCostoMP =
          (costoPorPorcion / precioRealVentaSinIVA) * 100;
        $("#porcentaje_real_costo").val(porcentajeRealCostoMP.toFixed(2));

        console.log("Calculations completed successfully");
      } catch (error) {
        console.error("Error in calculations:", error);
        showToast("Error in calculations: " + error.message, "danger");
        resetCalculations();
      }
    }
  ).fail(function (error) {
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
  console.log("Document ready, loading recipes..."); // Debug log
  loadRecipeOptions();

  // Load recipes when the costos_receta tab is shown
  $('button[data-bs-target="#costos_receta"]').on("shown.bs.tab", function (e) {
    console.log("Costos Receta tab shown"); // Debug log
    loadRecipeOptions();
  });

  // Handle recipe selection change
  $("#receta_select").on("change", function () {
    console.log("Recipe selection changed"); // Debug log
    const recipeId = $(this).val();
    if (recipeId) {
      calculateRecipeCosts();
    } else {
      resetCalculations();
    }
  });

  // Handle input changes that should trigger recalculation
  $(
    "#margen_error_porcentaje, #porcentaje_costo_mp, #impuesto_consumo_porcentaje"
  ).on("input", function () {
    console.log("Input value changed"); // Debug log
    calculateRecipeCosts();
  });

  // Form submission handler
  $("#costosRecetaForm").on("submit", function (e) {
    e.preventDefault();
    console.log("Form submitted"); // Debug log

    // Ensure all calculations are up to date
    calculateRecipeCosts();

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
