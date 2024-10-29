// Add this to your existing form-handlers.js
function loadSelectOptions() {
  // Load recipes into all recipe select elements
  const recipeSelectors = [
    "#receta_select", // costos_receta_form
    "#receta_id", // fact_sales_form
    'select[name="receta_id"]', // any other form using recipe_id
  ];

  $.get("includes/get_recipes.php", function (data) {
    recipeSelectors.forEach((selector) => {
      const element = $(selector);
      if (element.length) {
        element.html(data);
      }
    });
  }).fail(function (error) {
    console.error("Error loading recipes:", error);
    showToast("Error loading recipes", "danger");
  });

  // Load other select options
  $.get("includes/get_categories.php", function (data) {
    $("#categoria_select").html(data);
  });

  $.get("includes/get_units.php", function (data) {
    $("#unidad_id").html(data);
  });

  $.get("includes/get_ingredients.php", function (data) {
    $("#ingrediente_select").html(data);
  });

  $.get("includes/get_order_types.php", function (data) {
    $("#order_type_id").html(data);
  });

  $.get("includes/get_recipe_costs.php", function (data) {
    $("#costo_receta_id").html(data);
  });
}

// Document ready handler
$(document).ready(function () {
  // Initial load of select options
  loadSelectOptions();

  // Reload select options after form submissions
  $("form").on("submit", function (e) {
    e.preventDefault();

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
          loadSelectOptions(); // Reload all select options
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

  // Handle tab changes
  $(".nav-link").on("shown.bs.tab", function (e) {
    loadSelectOptions(); // Reload options when switching tabs
  });
});

// Export the function so it can be used by other scripts
window.loadSelectOptions = loadSelectOptions;
