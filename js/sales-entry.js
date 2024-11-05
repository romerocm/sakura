// Sales Entry Form Handler
$(document).ready(function () {
  // Initialize form
  function initializeForm() {
    const lastSaleDate = localStorage.getItem("lastSaleDate");
    $("#sale_date").val(lastSaleDate || new Date().toISOString().split("T")[0]);
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

  // Update totals and percentages
  function updateTotals() {
    const totalSales = parseFloat($("#total_sales").val()) || 0;

    // Category totals
    let categoryPercentageTotal = 0;
    let categoryQuantityTotal = 0;
    let categoryAmountTotal = 0;

    $("#categoriesTable tbody tr.category-row").each(function () {
      const total = parseFloat($(this).find(".category-total").val()) || 0;
      const quantity = parseInt($(this).find(".category-quantity").val()) || 0;
      const percentage = totalSales > 0 ? (total / totalSales) * 100 : 0;

      $(this).find(".category-percentage").val(percentage.toFixed(1));

      categoryPercentageTotal += percentage;
      categoryQuantityTotal += quantity;
      categoryAmountTotal += total;
    });

    $("#categoryPercentageTotal").text(categoryPercentageTotal.toFixed(1));
    $("#categoryQuantityTotal").text(categoryQuantityTotal);
    $("#categoryTotalAmount").text(categoryAmountTotal.toFixed(2));

    // Product totals
    let productPercentageTotal = 0;
    let productQuantityTotal = 0;
    let productAmountTotal = 0;

    $("#productsTable tbody tr.product-row").each(function () {
      const total = parseFloat($(this).find(".product-total").val()) || 0;
      const quantity = parseInt($(this).find(".product-quantity").val()) || 0;
      const percentage = totalSales > 0 ? (total / totalSales) * 100 : 0;

      $(this).find(".product-percentage").val(percentage.toFixed(1));

      productPercentageTotal += percentage;
      productQuantityTotal += quantity;
      productAmountTotal += total;
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

    // Only provide feedback if there's a total sales amount
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

  // Calculate average order
  function updateAverageOrder() {
    const totalSales = parseFloat($("#total_sales").val()) || 0;
    const ordersCount = parseInt($("#orders_count").val()) || 1;
    const averageOrder = totalSales / ordersCount;
    $("#average_order").val(averageOrder.toFixed(2));
  }

  // Event Handlers for day navigation
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
  $("#addCategory").on("click", function () {
    const template = $("#categoriesTable tbody tr.category-row:first").clone(
      true
    );
    template.find("input").val("");
    template.find("select").val("");
    $("#categoriesTable tbody").append(template);
    updateRowNumbers();
  });

  $("#addProduct").on("click", function () {
    const template = $("#productsTable tbody tr.product-row:first").clone(true);
    template.find("input").val("");
    template.find("select").val("");
    $("#productsTable tbody").append(template);
    updateRowNumbers();
  });

  $(document).on("click", ".remove-row", function () {
    const tbody = $(this).closest("tbody");
    if (tbody.find("tr").length > 1) {
      $(this).closest("tr").remove();
      updateRowNumbers();
      updateTotals();
    }
  });

  // Save current form data to local storage
  $("#saveData").on("click", function () {
    const formData = {
      sale_date: $("#sale_date").val(),
      total_sales: $("#total_sales").val(),
      net_sales: $("#net_sales").val(),
      tips: $("#tips").val(),
      customer_count: $("#customer_count").val(),
      orders_count: $("#orders_count").val(),
      categories: [],
      products: []
    };

    $("#categoriesTable tbody tr.category-row").each(function () {
      formData.categories.push({
        category_id: $(this).find(".category-select").val(),
        percentage: $(this).find(".category-percentage").val(),
        quantity: $(this).find(".category-quantity").val(),
        total: $(this).find(".category-total").val()
      });
    });

    $("#productsTable tbody tr.product-row").each(function () {
      formData.products.push({
        recipe_id: $(this).find(".recipe-select").val(),
        percentage: $(this).find(".product-percentage").val(),
        quantity: $(this).find(".product-quantity").val(),
        total: $(this).find(".product-total").val()
      });
    });

    localStorage.setItem("salesFormData", JSON.stringify(formData));
    showToast("Data saved successfully!", "success");
  });

  // Load saved form data from local storage
  $("#loadData").on("click", function () {
    const savedData = localStorage.getItem("salesFormData");
    if (savedData) {
      const formData = JSON.parse(savedData);
      $("#sale_date").val(formData.sale_date);
      $("#total_sales").val(formData.total_sales);
      $("#net_sales").val(formData.net_sales);
      $("#tips").val(formData.tips);
      $("#customer_count").val(formData.customer_count);
      $("#orders_count").val(formData.orders_count);

      // Load select options first
      loadSelectOptions();

      // Use a timeout to ensure options are loaded before setting values
      setTimeout(() => {
        // Clear existing rows but keep the template row
        $("#categoriesTable tbody").find("tr:gt(0)").remove();
        $("#productsTable tbody").find("tr:gt(0)").remove();

        // Populate categories
        formData.categories.forEach((category) => {
          const template = $("#categoriesTable tbody tr.category-row:first").clone(true);
          template.find(".category-select").val(category.category_id);
          template.find(".category-percentage").val(category.percentage);
          template.find(".category-quantity").val(category.quantity);
          template.find(".category-total").val(category.total);
          $("#categoriesTable tbody").append(template);
        });

        // Populate products
        formData.products.forEach((product) => {
          const template = $("#productsTable tbody tr.product-row:first").clone(true);
          template.find(".recipe-select").val(product.recipe_id);
          template.find(".product-percentage").val(product.percentage);
          template.find(".product-quantity").val(product.quantity);
          template.find(".product-total").val(product.total);
          $("#productsTable tbody").append(template);
        });

        // Add an empty row for new entries
        $("#addCategory").click();
        $("#addProduct").click();

        updateRowNumbers();
        updateTotals();
        showToast("Data loaded successfully!", "success");
      }, 500); // Adjust the timeout as needed
    } else {
      showToast("No saved data found", "warning");
    }
  });

  // Input event handlers
  $("#total_sales, #orders_count").on("input", updateAverageOrder);
  $(document).on(
    "input",
    "#total_sales, .category-total, .category-quantity, .product-total, .product-quantity",
    updateTotals
  );

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
          localStorage.setItem("lastSaleDate", formData.sale_date);
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
