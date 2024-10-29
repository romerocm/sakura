function calculateRecipeCosts() {
  const recipeId = $("#receta_select").val();
  if (!recipeId) {
    resetCalculations();
    return;
  }

  $.get("includes/get_recipe_ingredients_costs.php", { recipe_id: recipeId })
    .done(function (data) {
      if (!data.ingredients || data.ingredients.length === 0) {
        resetCalculations();
        return;
      }

      try {
        // Calculate total cost of ingredients
        const costoTotalMateriaPrima = data.ingredients.reduce(
          (sum, item) => sum + parseFloat(item.costo_total || 0),
          0
        );

        // Get other values from form
        const margenError =
          parseFloat($("#margen_error_porcentaje").val()) || 10;
        const numeroPorciones = parseFloat(data.recipe.numero_porciones) || 1;
        const porcentajeCostoMP =
          parseFloat($("#porcentaje_costo_mp").val()) || 35;
        const impuestoConsumo =
          parseFloat($("#impuesto_consumo_porcentaje").val()) || 13;

        // Set costo total materia prima
        $("#costo_total_materia_prima").val(costoTotalMateriaPrima.toFixed(2));

        // Calculate margen error value
        const margenErrorValue = costoTotalMateriaPrima * (margenError / 100);

        // Calculate costo total preparacion
        const costoTotalPreparacion = costoTotalMateriaPrima + margenErrorValue;
        $("#costo_total_preparacion").val(costoTotalPreparacion.toFixed(2));

        // Calculate costo por porcion
        const costoPorPorcion = costoTotalPreparacion / numeroPorciones;
        $("#costo_por_porcion").val(costoPorPorcion.toFixed(2));

        // Calculate precio potencial venta
        const precioPotencialVenta =
          costoPorPorcion / (porcentajeCostoMP / 100);
        $("#precio_potencial_venta").val(precioPotencialVenta.toFixed(2));

        // Calculate impuesto value
        const impuestoValue = precioPotencialVenta * (impuestoConsumo / 100);

        // Calculate precio venta sugerido
        const precioVentaSugerido = precioPotencialVenta + impuestoValue;
        $("#precio_venta").val(precioVentaSugerido.toFixed(2));

        // Calculate precio real venta
        const precioRealVentaSinIVA =
          precioVentaSugerido / (1 + impuestoConsumo / 100);
        $("#precio_real_venta").val(precioRealVentaSinIVA.toFixed(2));

        // Calculate IVA por porcion
        const ivaPorPorcion = precioVentaSugerido - precioRealVentaSinIVA;
        $("#iva_por_porcion").val(ivaPorPorcion.toFixed(2));

        // Calculate porcentaje real costo
        const porcentajeRealCosto =
          (costoPorPorcion / precioRealVentaSinIVA) * 100;
        $("#porcentaje_real_costo").val(porcentajeRealCosto.toFixed(2));
      } catch (error) {
        console.error("Error in calculations:", error);
        resetCalculations();
      }
    })
    .fail(function (error) {
      console.error("Error fetching recipe data:", error);
      resetCalculations();
    });
}

function resetCalculations() {
  const fields = [
    "costo_total_materia_prima",
    "costo_total_preparacion",
    "costo_por_porcion",
    "precio_potencial_venta",
    "precio_venta",
    "precio_real_venta",
    "iva_por_porcion",
    "porcentaje_real_costo",
  ];

  fields.forEach((field) => $(`#${field}`).val("0.00"));
}

// Initialize when document is ready
$(document).ready(function () {
  // Only calculate when recipe is selected
  $("#receta_select").on("change", function () {
    if ($(this).val()) {
      calculateRecipeCosts();
    } else {
      resetCalculations();
    }
  });

  // Recalculate when these inputs change
  $(
    "#margen_error_porcentaje, #porcentaje_costo_mp, #impuesto_consumo_porcentaje"
  ).on("input", function () {
    if ($("#receta_select").val()) {
      calculateRecipeCosts();
    }
  });
});
