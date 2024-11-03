// Sales Entry Form Handler
$(document).ready(function () {
  // Initialize form
  function initializeForm() {
    $("#sale_date").val(new Date().toISOString().split("T")[0]);
    loadSelectOptions();
    updateRowNumbers();
    updateTotals();
  }

  // Load select options
  function loadSelectOptions() {
    $.get("includes/get_categories.php", function (data) {
      $(".category-select").each(function () {
        const currentValue = $(this).val();
        $(this).html(data);
        if (currentValue) {
          $(this).val(currentValue);
        }
      });
    });

    $.get("includes/get_recipes.php", function (data) {
      $(".recipe-select").each(function () {
        const currentValue = $(this).val();
        $(this).html(data);
        if (currentValue) {
          $(this).val(currentValue);
        }
      });
    });
  }

  // Update row numbers
  function updateRowNumbers() {
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

  // Update totals
  function updateTotals() {
    const totalSales = parseFloat($("#total_sales").val()) || 0;

    // Category totals
    let categoryQuantityTotal = 0;
    let categoryAmountTotal = 0;

    // First calculate the total amount
    $("#categoriesTable tbody tr.category-row").each(function () {
      const total = parseFloat($(this).find(".category-total").val()) || 0;
      const quantity = parseInt($(this).find(".category-quantity").val()) || 0;

      categoryQuantityTotal += quantity;
      categoryAmountTotal += total;
    });

    // Then calculate percentages based on categoryAmountTotal
    let categoryPercentageTotal = 0;
    $("#categoriesTable tbody tr.category-row").each(function () {
      const total = parseFloat($(this).find(".category-total").val()) || 0;
      const percentage =
        categoryAmountTotal > 0 ? (total / categoryAmountTotal) * 100 : 0;

      $(this).find(".category-percentage").val(percentage.toFixed(1));
      categoryPercentageTotal += percentage;
    });

    $("#categoryPercentageTotal").text(categoryPercentageTotal.toFixed(1));
    $("#categoryQuantityTotal").text(categoryQuantityTotal);
    $("#categoryTotalAmount").text(categoryAmountTotal.toFixed(2));

    // Product totals
    let productQuantityTotal = 0;
    let productAmountTotal = 0;

    // First calculate the total amount
    $("#productsTable tbody tr.product-row").each(function () {
      const total = parseFloat($(this).find(".product-total").val()) || 0;
      const quantity = parseInt($(this).find(".product-quantity").val()) || 0;

      productQuantityTotal += quantity;
      productAmountTotal += total;
    });

    // Then calculate percentages based on productAmountTotal
    let productPercentageTotal = 0;
    $("#productsTable tbody tr.product-row").each(function () {
      const total = parseFloat($(this).find(".product-total").val()) || 0;
      const percentage =
        productAmountTotal > 0 ? (total / productAmountTotal) * 100 : 0;

      $(this).find(".product-percentage").val(percentage.toFixed(1));
      productPercentageTotal += percentage;
    });

    $("#productPercentageTotal").text(productPercentageTotal.toFixed(1));
    $("#productQuantityTotal").text(productQuantityTotal);
    $("#productTotalAmount").text(productAmountTotal.toFixed(2));

    // Update visual feedback for totals
    updateTotalsFeedback();
  }

  // Update visual feedback for totals
  function updateTotalsFeedback() {
    const totalSales = parseFloat($("#total_sales").val()) || 0;
    const categoryTotal = parseFloat($("#categoryTotalAmount").text()) || 0;
    const productTotal = parseFloat($("#productTotalAmount").text()) || 0;

    if (totalSales > 0) {
      const categoryRow = $("#categoriesTable tfoot tr");
      const productRow = $("#productsTable tfoot tr");

      if (Math.abs(totalSales - categoryTotal) <= 0.01) {
        categoryRow.removeClass("table-danger").addClass("table-success");
      } else {
        categoryRow.removeClass("table-success").addClass("table-danger");
      }

      if (Math.abs(totalSales - productTotal) <= 0.01) {
        productRow.removeClass("table-danger").addClass("table-success");
      } else {
        productRow.removeClass("table-success").addClass("table-danger");
      }
    }
  }

  // Add new category row
  $("#addCategory").on("click", function () {
    const template = $("#categoriesTable tbody tr.category-row:first").clone(
      true
    );
    template.find("input").val("");
    template.find("select").val("");
    $("#categoriesTable tbody").append(template);
    updateRowNumbers();
  });

  // Add new product row
  $("#addProduct").on("click", function () {
    const template = $("#productsTable tbody tr.product-row:first").clone(true);
    template.find("input").val("");
    template.find("select").val("");
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

  // Calculate average order
  function updateAverageOrder() {
    const totalSales = parseFloat($("#total_sales").val()) || 0;
    const ordersCount = parseInt($("#orders_count").val()) || 1;
    const averageOrder = totalSales / ordersCount;
    $("#average_order").val(averageOrder.toFixed(2));
  }

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
    updateTotals();
  });

  $(document).on("input", ".product-total, #total_sales", function () {
    updateTotals();
  });

  // Update calculations when inputs change
  $("#total_sales, #orders_count").on("input", updateAverageOrder);
  $(document).on(
    "input",
    ".category-total, .category-quantity, .product-total, .product-quantity",
    updateTotals
  );

  // Form submission handler
  $("#dailySalesForm").on("submit", async function (e) {
    e.preventDefault();

    try {
      // Get all recipe costs first
      const recipeCosts = {};
      const recipePromises = [];

      $("#productsTable tbody tr.product-row").each(function () {
        const recipeId = $(this).find(".recipe-select").val();
        if (recipeId) {
          const promise = $.get("includes/get_recipe_cost_id.php", {
            recipe_id: recipeId,
          }).then((response) => {
            recipeCosts[recipeId] = response.costo_receta_id;
          });
          recipePromises.push(promise);
        }
      });

      // Wait for all recipe costs to be fetched
      await Promise.all(recipePromises);

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
        if (recipeId && recipeCosts[recipeId]) {
          formData.products.push({
            recipe_id: recipeId,
            costo_receta_id: recipeCosts[recipeId],
            percentage:
              parseFloat($(this).find(".product-percentage").val()) || 0,
            quantity: parseInt($(this).find(".product-quantity").val()) || 0,
            total: parseFloat($(this).find(".product-total").val()) || 0,
          });
        }
      });

      // Submit the form data
      const response = await $.ajax({
        url: "process.php",
        type: "POST",
        data: JSON.stringify(formData),
        contentType: "application/json",
      });

      if (response.success) {
        showToast("Daily sales summary saved successfully!", "success");
        $("#dailySalesForm")[0].reset();
        initializeForm();
      } else {
        showToast(response.message || "Error saving sales summary", "danger");
      }
    } catch (error) {
      showToast(
        "Error processing form: " + (error.message || "Unknown error"),
        "danger"
      );
      console.error("Form submission error:", error);
    }
  });

  // Initialize form on page load
  initializeForm();
});
