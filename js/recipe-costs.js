// Only calculation-related functions
function calculateRecipeCosts() {
  const recipeId = $("#receta_select").val();
  console.log("Calculating costs for recipe:", recipeId);

  if (!recipeId || recipeId === "") {
    resetCalculations();
    return;
  }

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

        console.log("Initial values:", {
          costoTotalMateriaPrima,
          margenError,
          numeroPorciones,
          porcentajeCostoMP,
          impuestoConsumo,
        });

        // Update all form fields with calculations
        $("#costo_total_materia_prima").val(costoTotalMateriaPrima.toFixed(2));

        const margenErrorValue = costoTotalMateriaPrima * (margenError / 100);
        const costoTotalPreparacion = costoTotalMateriaPrima + margenErrorValue;
        $("#costo_total_preparacion").val(costoTotalPreparacion.toFixed(2));

        const costoPorPorcion = costoTotalPreparacion / numeroPorciones;
        $("#costo_por_porcion").val(costoPorPorcion.toFixed(2));

        const precioPotencialVenta =
          costoPorPorcion / (porcentajeCostoMP / 100);
        $("#precio_potencial_venta").val(precioPotencialVenta.toFixed(2));

        const impuestoConsumoValue =
          precioPotencialVenta * (impuestoConsumo / 100);
        const precioVentaSugerido = precioPotencialVenta + impuestoConsumoValue;
        $("#precio_venta").val(precioVentaSugerido.toFixed(2));

        const precioRealVentaSinIVA =
          precioVentaSugerido / (1 + impuestoConsumo / 100);
        $("#precio_real_venta").val(precioRealVentaSinIVA.toFixed(2));

        const ivaCobradoPorPorcion =
          precioVentaSugerido - precioRealVentaSinIVA;
        $("#iva_por_porcion").val(ivaCobradoPorPorcion.toFixed(2));

        const porcentajeRealCostoMP =
          (costoPorPorcion / precioRealVentaSinIVA) * 100;
        $("#porcentaje_real_costo").val(porcentajeRealCostoMP.toFixed(2));

        console.log("Calculations completed successfully");
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

// Only bind calculation-specific events
$(document).ready(function () {
  // Handle input changes that should trigger recalculation
  $(
    "#margen_error_porcentaje, #porcentaje_costo_mp, #impuesto_consumo_porcentaje"
  ).on("input", function () {
    if ($("#costos_receta").hasClass("active")) {
      const recipeId = $("#receta_select").val();
      if (recipeId && recipeId !== "") {
        calculateRecipeCosts();
      }
    }
  });
});
