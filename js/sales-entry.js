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
    $("#categoriesTable tbody tr.category-row").each(function (index) {
      const rowNum = index + 1;
      let rowLabel = $(this).find(".row-number");
      if (rowLabel.length === 0) {
        $(this)
          .find("td:first")
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
    $("#productsTable tbody tr.product-row").each(function (index) {
      const rowNum = index + 1;
      let rowLabel = $(this).find(".row-number");
      if (rowLabel.length === 0) {
        $(this)
          .find("td:first")
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
  $("#addCategory").on("click", function () {
    const template = $("#categoriesTable tbody tr.category-row:first").clone(
      true
    );
    template.find("input").val(""); // Clear input values
    template.find("select").val(""); // Clear select values
    $("#categoriesTable tbody").append(template);
    updateRowNumbers();
  });

  // Add new product row
  $("#addProduct").on("click", function () {
    const template = $("#productsTable tbody tr.product-row:first").clone(true);
    template.find("input").val(""); // Clear input values
    template.find("select").val(""); // Clear select values
    $("#productsTable tbody").append(template);
    updateRowNumbers();
  });

  // Remove row handler
  $(document).on("click", ".remove-row", function () {
    const tbody = $(this).closest("tbody");
    if (tbody.find("tr").length > 1) {
      $(this).closest("tr").remove();
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

    $("#categoriesTable tbody tr.category-row").each(function () {
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

    $("#productsTable tbody tr.product-row").each(function () {
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

  // Calculate average order
  function updateAverageOrder() {
    const totalSales = parseFloat($("#total_sales").val()) || 0;
    const ordersCount = parseInt($("#orders_count").val()) || 1;
    const averageOrder = totalSales / ordersCount;
    $("#average_order").val(averageOrder.toFixed(2));
  }

  // Verify totals
  $("#verifyTotals").on("click", function () {
    const totalSales = parseFloat($("#total_sales").val()) || 0;
    const categoryTotal = parseFloat($("#categoryTotalAmount").text()) || 0;
    const productTotal = parseFloat($("#productTotalAmount").text()) || 0;

    if (
      Math.abs(totalSales - categoryTotal) > 0.01 ||
      Math.abs(totalSales - productTotal) > 0.01
    ) {
      showToast("Warning: Totals do not match!", "warning");
    } else {
      showToast("Totals verified successfully!", "success");
    }
  });

  // Previous/Next Day Navigation
  $("#prevDay").on("click", function () {
    const currentDate = new Date($("#sale_date").val());
    currentDate.setDate(currentDate.getDate() - 1);
    $("#sale_date").val(currentDate.toISOString().split("T")[0]);
  });

  $("#nextDay").on("click", function () {
    const currentDate = new Date($("#sale_date").val());
    currentDate.setDate(currentDate.getDate() + 1);
    $("#sale_date").val(currentDate.toISOString().split("T")[0]);
  });

  // Auto-calculate percentages
  $(document).on("input", ".category-total, #total_sales", function () {
    const totalSales = parseFloat($("#total_sales").val()) || 0;

    $(".category-total").each(function () {
      const total = parseFloat($(this).val()) || 0;
      const percentage = totalSales > 0 ? (total / totalSales) * 100 : 0;
      $(this)
        .closest("tr")
        .find(".category-percentage")
        .val(percentage.toFixed(1));
    });

    updateTotals();
  });

  $(document).on("input", ".product-total, #total_sales", function () {
    const totalSales = parseFloat($("#total_sales").val()) || 0;

    $(".product-total").each(function () {
      const total = parseFloat($(this).val()) || 0;
      const percentage = totalSales > 0 ? (total / totalSales) * 100 : 0;
      $(this)
        .closest("tr")
        .find(".product-percentage")
        .val(percentage.toFixed(1));
    });

    updateTotals();
  });

  // Update calculations when relevant inputs change
  $("#total_sales, #orders_count").on("input", updateAverageOrder);
  $(document).on(
    "input",
    ".category-total, .product-total, #total_sales",
    updateTotals
  );

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
    $("#categoriesTable tbody tr.category-row").each(function () {
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
    $("#productsTable tbody tr.product-row").each(function () {
      const recipeId = $(this).find(".recipe-select").val();
      if (recipeId) {
        formData.products.push({
          recipe_id: recipeId,
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
});
