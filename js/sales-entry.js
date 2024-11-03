// Sales Entry Form Handler
$(document).ready(function () {
  // Initialize form
  function initializeForm() {
    $("#sale_date").val(new Date().toISOString().split("T")[0]);
    loadSelectOptions();
    updateRowNumbers();
  }

  // Load select options
  function loadSelectOptions() {
    $.get("includes/get_categories.php", function (data) {
      $(".category-select").each(function () {
        const currentValue = $(this).val(); // Store current selection
        $(this).html(data);
        if (currentValue) {
          $(this).val(currentValue); // Restore selection
        }
      });
    });

    $.get("includes/get_recipes.php", function (data) {
      $(".recipe-select").each(function () {
        const currentValue = $(this).val(); // Store current selection
        $(this).html(data);
        if (currentValue) {
          $(this).val(currentValue); // Restore selection
        }
      });
    });
  }

  // Update row numbers
  function updateRowNumbers() {
    // Update category row numbers
    $("#categoryEntries .category-entry").each(function (index) {
      const rowNum = index + 1;
      let rowLabel = $(this).find(".row-number");
      if (rowLabel.length === 0) {
        $(this)
          .find(".col-md-3:first")
          .prepend(
            '<span class="row-number badge bg-secondary me-2">' +
              rowNum +
              "</span>"
          );
      } else {
        rowLabel.text(rowNum);
      }
    });

    // Update product row numbers
    $("#productEntries .product-entry").each(function (index) {
      const rowNum = index + 1;
      let rowLabel = $(this).find(".row-number");
      if (rowLabel.length === 0) {
        $(this)
          .find(".col-md-3:first")
          .prepend(
            '<span class="row-number badge bg-secondary me-2">' +
              rowNum +
              "</span>"
          );
      } else {
        rowLabel.text(rowNum);
      }
    });
  }

  // Add new category row
  $("#addCategoryButton").on("click", function () {
    const template = $("#categoryEntries .category-entry:first").clone(true);
    template.find("input").val(""); // Clear input values
    template.find("select").each(function () {
      $(this).val(""); // Clear select values
    });
    $("#categoryEntries").append(template);
    updateRowNumbers();
  });

  // Add new product row
  $("#addProductButton").on("click", function () {
    const template = $("#productEntries .product-entry:first").clone(true);
    template.find("input").val(""); // Clear input values
    template.find("select").each(function () {
      $(this).val(""); // Clear select values
    });
    $("#productEntries").append(template);
    updateRowNumbers();
  });

  // Remove row handler
  $(".remove-category, .remove-product").on("click", function () {
    const parentContainer = $(this)
      .closest(".category-entry, .product-entry")
      .parent();
    if (parentContainer.children().length > 1) {
      $(this).closest(".category-entry, .product-entry").remove();
      updateRowNumbers();
      updateTotals();
    }
  });

  // Update totals
  function updateTotals() {
    // Category totals
    let categoryPercentageTotal = 0;
    let categoryQuantityTotal = 0;
    let categoryAmountTotal = 0;

    $("#categoryEntries .category-entry").each(function () {
      categoryPercentageTotal +=
        parseFloat($(this).find(".category-percentage").val()) || 0;
      categoryQuantityTotal +=
        parseInt($(this).find(".category-quantity").val()) || 0;
      categoryAmountTotal +=
        parseFloat($(this).find(".category-total").val()) || 0;
    });

    $("#categoryPercentageTotal").text(categoryPercentageTotal.toFixed(1));
    $("#categoryQuantityTotal").text(categoryQuantityTotal);
    $("#categoryTotalAmount").text(categoryAmountTotal.toFixed(2));

    // Product totals
    let productPercentageTotal = 0;
    let productQuantityTotal = 0;
    let productAmountTotal = 0;

    $("#productEntries .product-entry").each(function () {
      productPercentageTotal +=
        parseFloat($(this).find(".product-percentage").val()) || 0;
      productQuantityTotal +=
        parseInt($(this).find(".product-quantity").val()) || 0;
      productAmountTotal +=
        parseFloat($(this).find(".product-total").val()) || 0;
    });

    $("#productPercentageTotal").text(productPercentageTotal.toFixed(1));
    $("#productQuantityTotal").text(productQuantityTotal);
    $("#productTotalAmount").text(productAmountTotal.toFixed(2));
  }

  // Handle form submission
  $("#dailySalesForm").on("submit", function (e) {
    e.preventDefault();

    const formData = {
      form_type: "daily_sales_summary",
      sale_date: $("#sale_date").val(),
      total_sales: parseFloat($("#total_sales").val()) || 0,
      net_sales: parseFloat($("#net_sales").val()) || 0,
      tips: parseFloat($("#tips").val()) || 0,
      customer_count: parseInt($("#customer_count").val()) || 0,
      categories: [],
      products: [],
    };

    // Collect categories data
    $("#categoryEntries .category-entry").each(function () {
      const categoryId = $(this).find(".category-select").val();
      if (categoryId) {
        formData.categories.push({
          category_id: categoryId,
          percentage:
            parseFloat($(this).find(".category-percentage").val()) || 0,
          quantity: parseInt($(this).find(".category-quantity").val()) || 0,
          total: parseFloat($(this).find(".category-total").val()) || 0,
        });
      }
    });

    // Collect products data
    $("#productEntries .product-entry").each(function () {
      const recipeId = $(this).find(".recipe-select").val();
      if (recipeId) {
        formData.products.push({
          recipe_id: recipeId,
          costo_receta_id: $(this).find(".costo-receta-select").val(),
          percentage:
            parseFloat($(this).find(".product-percentage").val()) || 0,
          quantity: parseInt($(this).find(".product-quantity").val()) || 0,
          total: parseFloat($(this).find(".product-total").val()) || 0,
        });
      }
    });

    // Submit form data
    $.ajax({
      url: "process.php",
      type: "POST",
      data: JSON.stringify(formData),
      contentType: "application/json",
      success: function (response) {
        if (response.success) {
          showToast("Daily sales summary saved successfully!", "success");
          $("#dailySalesForm")[0].reset();
          initializeForm();
        } else {
          showToast(response.message || "Error saving sales summary", "danger");
        }
      },
      error: function () {
        showToast("Error saving sales summary", "danger");
      },
    });
  });

  // Initialize form on page load
  initializeForm();

  // Update calculations when inputs change
  $(document).on(
    "input",
    ".category-total, .product-total, #total_sales",
    function () {
      updateTotals();
    }
  );
});
