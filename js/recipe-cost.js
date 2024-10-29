// Recipe costs calculation module
function calculateRecipeCosts() {
  // Get the selected recipe ID
  const recipeId = $("#receta_select").val();

  if (!recipeId) {
    resetCalculations();
    return;
  }

  // Fetch recipe details and ingredients costs
  $.get(
    "includes/get_recipe_ingredients_costs.php",
    { recipe_id: recipeId },
    function (data) {
      if (data.error) {
        showToast(data.error, "danger");
        resetCalculations();
        return;
      }

      updateCalculations(data);
    }
  ).fail(function (jqXHR, textStatus, errorThrown) {
    showToast("Error fetching recipe data: " + textStatus, "danger");
    resetCalculations();
  });
}

function updateCalculations(data) {
  try {
    // Get basic values from data
    const costoTotalMateriaPrima = data.ingredients.reduce(
      (sum, item) => sum + parseFloat(item.costo_total || 0),
      0
    );

    // Get form input values
    const margenError = parseFloat($("#margen_error_porcentaje").val()) || 0;
    const numeroPorciones = parseFloat(data.recipe.numero_porciones) || 1;
    const porcentajeCostoMP = parseFloat($("#porcentaje_costo_mp").val()) || 35;
    const impuestoConsumo =
      parseFloat($("#impuesto_consumo_porcentaje").val()) || 13;

    // 1. Costo Total Materia Prima
    $("#costo_total_materia_prima").val(costoTotalMateriaPrima.toFixed(2));

    // 2. Margen de Error Value (10% by default)
    const margenErrorValue = costoTotalMateriaPrima * (margenError / 100);

    // 3. Costo Total Preparación
    const costoTotalPreparacion = costoTotalMateriaPrima + margenErrorValue;
    $("#costo_total_preparacion").val(costoTotalPreparacion.toFixed(2));

    // 4. Costo por Porción
    const costoPorPorcion = costoTotalPreparacion / numeroPorciones;
    $("#costo_por_porcion").val(costoPorPorcion.toFixed(2));

    // 5. Precio Potencial de Venta
    const precioPotencialVenta = costoPorPorcion / (porcentajeCostoMP / 100);
    $("#precio_potencial_venta").val(precioPotencialVenta.toFixed(2));

    // 6. Impuesto al Consumo Value
    const impuestoConsumoValue = precioPotencialVenta * (impuestoConsumo / 100);

    // 7. Precio Venta Sugerido
    const precioVentaSugerido = precioPotencialVenta + impuestoConsumoValue;
    $("#precio_venta").val(precioVentaSugerido.toFixed(2));

    // 8. Precio Real de Venta (Sin IVA)
    const precioRealVentaSinIVA =
      precioVentaSugerido / (1 + impuestoConsumo / 100);
    $("#precio_real_venta").val(precioRealVentaSinIVA.toFixed(2));

    // 9. IVA Cobrado por Porción
    const ivaCobradoPorPorcion = precioVentaSugerido - precioRealVentaSinIVA;
    $("#iva_por_porcion").val(ivaCobradoPorPorcion.toFixed(2));

    // 10. Porcentaje Real Costo Materia Prima
    const porcentajeRealCostoMP =
      (costoPorPorcion / precioRealVentaSinIVA) * 100;
    $("#porcentaje_real_costo").val(porcentajeRealCostoMP.toFixed(2));
  } catch (error) {
    console.error("Error in calculations:", error);
    showToast("Error in calculations: " + error.message, "danger");
    resetCalculations();
  }
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

// Event Listeners
$(document).ready(function () {
  // Recalculate when these inputs change
  $(
    "#receta_select, #margen_error_porcentaje, #porcentaje_costo_mp, #impuesto_consumo_porcentaje"
  ).on("change input", calculateRecipeCosts);

  // Initial calculation if recipe is already selected
  if ($("#receta_select").val()) {
    calculateRecipeCosts();
  }

  // Form submission handler
  $("#costosRecetaForm").on("submit", function (e) {
    e.preventDefault();

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
