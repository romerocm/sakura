// Form handling logic
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
        element.select2({
          placeholder: "Search for a recipe",
          allowClear: true
        });
      }
    });
  }).fail(function (error) {
    console.error("Error loading recipes:", error);
    showToast("Error loading recipes", "danger");
  });

  // Load other select options
  $.get("includes/get_categories.php", function (data) {
    const categorySelect = $("#categoria_select");
    categorySelect.html(data);
    categorySelect.select2({
      placeholder: "Search for a category",
      allowClear: true
    });
  });

  $.get("includes/get_units.php", function (data) {
    const unitSelect = $("#unidad_id");
    unitSelect.html(data);
    unitSelect.select2({
      placeholder: "Search for a unit",
      allowClear: true
    });
  });

  // Load ingredients into the select element only once and initialize Select2
  const ingredientSelect = $("#ingrediente_select");
  if (!ingredientSelect.data("loaded")) {
    $.get("includes/get_ingredients.php", function (data) {
      ingredientSelect.html(data);
      ingredientSelect.data("loaded", true);
      ingredientSelect.select2({
        placeholder: "Search for an ingredient",
        allowClear: true
      });
    });
  }

  $.get("includes/get_order_types.php", function (data) {
    const orderTypeSelect = $("#order_type_id");
    orderTypeSelect.html(data);
    orderTypeSelect.select2({
      placeholder: "Search for an order type",
      allowClear: true
    });
  });

  $.get("includes/get_recipe_costs.php", function (data) {
    const recipeCostSelect = $("#costo_receta_id");
    recipeCostSelect.html(data);
    recipeCostSelect.select2({
      placeholder: "Search for a recipe cost",
      allowClear: true
    });
  });
}

// Function to check recipe dependencies
function checkRecipeDependencies() {
  $.get("includes/get_categories.php", function (data) {
    const hasCategories = $(data).filter('option[value!=""]').length > 0;
    $("#recipeCategoriaStatus")
      .removeClass("checking available unavailable")
      .addClass(hasCategories ? "available" : "unavailable")
      .html(
        hasCategories
          ? '<i class="fas fa-check-circle"></i> Categories available'
          : '<i class="fas fa-times-circle"></i> No categories available - Create categories first'
      );
  });
}

// Function to check recipe ingredients dependencies
function checkRecipeIngredientsDependencies() {
  $.get("includes/get_recipes.php", function (data) {
    const hasRecipes = $(data).filter('option[value!=""]').length > 0;
    $("#recipeIngredientRecetaStatus")
      .removeClass("checking available unavailable")
      .addClass(hasRecipes ? "available" : "unavailable")
      .html(
        hasRecipes
          ? '<i class="fas fa-check-circle"></i> Recipes available'
          : '<i class="fas fa-times-circle"></i> No recipes available - Create recipes first'
      );
  });

  $.get("includes/get_ingredients.php", function (data) {
    const hasIngredients = $(data).filter('option[value!=""]').length > 0;
    $("#recipeIngredientIngredienteStatus")
      .removeClass("checking available unavailable")
      .addClass(hasIngredients ? "available" : "unavailable")
      .html(
        hasIngredients
          ? '<i class="fas fa-check-circle"></i> Ingredients available'
          : '<i class="fas fa-times-circle"></i> No ingredients available - Add ingredients first'
      );
  });
}

// Function to check sales dependencies
function checkSalesDependencies() {
  // Check for recipes
  $.get("includes/get_recipes.php", function (data) {
    const hasRecipes = $(data).filter('option[value!=""]').length > 0;
    $("#salesRecetaStatus")
      .removeClass("checking available unavailable")
      .addClass(hasRecipes ? "available" : "unavailable")
      .html(
        hasRecipes
          ? '<i class="fas fa-check-circle"></i> Recipes available'
          : '<i class="fas fa-times-circle"></i> No recipes available - Create recipes first'
      );
  });

  // Check for recipe costs
  $.get("includes/get_recipe_costs.php", function (data) {
    const hasCosts = $(data).filter('option[value!=""]').length > 0;
    $("#salesCostoStatus")
      .removeClass("checking available unavailable")
      .addClass(hasCosts ? "available" : "unavailable")
      .html(
        hasCosts
          ? '<i class="fas fa-check-circle"></i> Recipe costs available'
          : '<i class="fas fa-times-circle"></i> No recipe costs available - Calculate costs first'
      );
  });

  // Check for order types
  $.get("includes/get_order_types.php", function (data) {
    const hasOrderTypes = $(data).filter('option[value!=""]').length > 0;
    $("#salesOrderTypeStatus")
      .removeClass("checking available unavailable")
      .addClass(hasOrderTypes ? "available" : "unavailable")
      .html(
        hasOrderTypes
          ? '<i class="fas fa-check-circle"></i> Order types available'
          : '<i class="fas fa-times-circle"></i> No order types available - Create order types first'
      );
  });
}

// Function to check ingredient dependencies
function checkIngredientDependencies() {
  $.get("includes/get_units.php", function (data) {
    const hasUnits = $(data).filter('option[value!=""]').length > 0;
    $("#unidadMedidaStatus")
      .removeClass("checking available unavailable")
      .addClass(hasUnits ? "available" : "unavailable")
      .html(
        hasUnits
          ? '<i class="fas fa-check-circle"></i> Units of measurement available'
          : '<i class="fas fa-times-circle"></i> No units available - Create units first'
      );
  });
}

// Function to check all dependencies based on current tab
function checkAllDependencies() {
  const currentTab = $(".tab-pane.active").attr("id");
  switch (currentTab) {
    case "receta":
      checkRecipeDependencies();
      break;
    case "receta_ingredientes":
      checkRecipeIngredientsDependencies();
      break;
    case "costos_receta":
      checkDependencyStatus();
      break;
    case "fact_sales":
      checkSalesDependencies();
      break;
    case "ingrediente":
      checkIngredientDependencies();
      break;
  }
}

// Initialize when document is ready
$(document).ready(function () {
    // Initialize Select2 for category dropdowns
    $('.category-select').select2({
        placeholder: "Search for a category",
        allowClear: true
    });

    // Initialize Select2 for recipe dropdowns
    $('.recipe-select').select2({
        placeholder: "Search for a product",
        allowClear: true
    });

    // Additional initialization code if needed
  // Initial load of select options
  loadSelectOptions();

  // Handle manual ID toggles
  document
    .querySelectorAll('.manual-id-toggle input[type="checkbox"]')
    .forEach((checkbox) => {
      checkbox.addEventListener("change", function () {
        const form = this.closest("form");
        const idInput = form.querySelector(".id-input");
        idInput.style.display = this.checked ? "block" : "none";
        if (!this.checked) {
          idInput.querySelector("input").value = "";
        }
      });
    });

  // Single form submission handler
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
          // Reset the form
          e.target.reset();
          // Reload select options
          loadSelectOptions();
          // Reset calculations if in costos_receta tab
          if ($("#costos_receta").hasClass("active")) {
            resetCalculations();
          }
          // Recheck dependencies
          checkAllDependencies();
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
    loadSelectOptions();
    checkAllDependencies();
  });

  // Auto-calculate total cost when ingredient and quantity change
  $("#ingrediente_select, #cantidad").on("change input", function () {
    const ingredienteId = $("#ingrediente_select").val();
    const cantidad = $("#cantidad").val();

    if (ingredienteId && cantidad) {
      $.get(
        "includes/get_ingredient_cost.php",
        { id: ingredienteId },
        function (data) {
          if (data.costo_unitario) {
            const totalCost =
              parseFloat(data.costo_unitario) * parseFloat(cantidad);
            $("#costo_total").val(totalCost.toFixed(2));
          }
        }
      );
    }
  });

  // Initial dependency check
  checkAllDependencies();
});

// Export functions for use in other scripts
window.loadSelectOptions = loadSelectOptions;
window.checkAllDependencies = checkAllDependencies;
window.checkDependencyStatus = checkDependencyStatus;
window.checkRecipeIngredientsDependencies = checkRecipeIngredientsDependencies;
window.checkSalesDependencies = checkSalesDependencies;
