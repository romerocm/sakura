// Sales Entry Form Handler
const SalesEntry = {
  // Initialize the form and attach event handlers
  init: function () {
    this.loadSelectOptions();
    this.initializeDateField();
    this.attachEventHandlers();
    this.loadDayData();
  },

  // Load categories and recipes into select elements
  loadSelectOptions: function () {
    $.get("includes/get_categories.php", function (data) {
      $(".category-select").html(data);
    });

    $.get("includes/get_recipes.php", function (data) {
      $(".recipe-select").html(data);
    });
  },

  // Set initial date to today
  initializeDateField: function () {
    $("#sale_date").val(new Date().toISOString().split("T")[0]);
  },

  // Update all totals for categories and products
  updateTotals: function () {
    // Category totals
    let categoryPercentageTotal = 0;
    let categoryQuantityTotal = 0;
    let categoryAmountTotal = 0;

    $(".category-row").each(function () {
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

    $(".product-row").each(function () {
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
  },

  // Fill form with provided data
  fillFormWithData: function (data) {
    if (data.summary) {
      $("#total_sales").val(data.summary.total_sales);
      $("#net_sales").val(data.summary.net_sales);
      $("#tips").val(data.summary.tips);
      $("#customer_count").val(data.summary.customer_count);
      $("#orders_count").val(data.summary.orders_count);

      const avgOrder = data.summary.total_sales / data.summary.orders_count;
      $("#average_order").val(avgOrder.toFixed(2));
    }

    // Fill categories
    if (data.categories && data.categories.length > 0) {
      const firstCategoryRow = $(".category-row").first();
      $("#categoriesTable tbody").empty().append(firstCategoryRow);

      data.categories.forEach((category, index) => {
        const row = index === 0 ? firstCategoryRow : firstCategoryRow.clone();
        row.find(".category-select").val(category.categoria_id);
        row.find(".category-percentage").val(category.percentage);
        row.find(".category-quantity").val(category.quantity);
        row.find(".category-total").val(category.total);

        if (index > 0) {
          $("#categoriesTable tbody").append(row);
        }
      });
    }

    // Fill products
    if (data.products && data.products.length > 0) {
      const firstProductRow = $(".product-row").first();
      $("#productsTable tbody").empty().append(firstProductRow);

      data.products.forEach((product, index) => {
        const row = index === 0 ? firstProductRow : firstProductRow.clone();
        row.find(".recipe-select").val(product.receta_id);
        row.find(".product-percentage").val(product.percentage);
        row.find(".product-quantity").val(product.quantity);
        row.find(".product-total").val(product.total);

        if (index > 0) {
          $("#productsTable tbody").append(row);
        }
      });
    }

    this.updateTotals();
  },

  // Load data for a specific day
  loadDayData: function () {
    const currentDate = $("#sale_date").val();
    $.get("includes/get_daily_summary.php", { date: currentDate }, (data) => {
      if (data.summary) {
        this.fillFormWithData(data);
      } else {
        $("#dailySalesForm")[0].reset();
        $("#sale_date").val(currentDate);

        $(".category-row:not(:first)").remove();
        $(".product-row:not(:first)").remove();

        this.updateTotals();
      }
    });
  },

  // Handle form submission
  handleSubmit: function (e) {
    e.preventDefault();

    const formData = {
      form_type: "daily_sales_summary",
      sale_date: $("#sale_date").val(),
      total_sales: parseFloat($("#total_sales").val()),
      net_sales: parseFloat($("#net_sales").val()),
      tips: parseFloat($("#tips").val()),
      customer_count: parseInt($("#customer_count").val()),
      orders_count: parseInt($("#orders_count").val()),
      categories: [],
      products: [],
    };

    // Collect categories
    $(".category-row").each(function () {
      const row = $(this);
      if (row.find(".category-select").val()) {
        formData.categories.push({
          category_id: row.find(".category-select").val(),
          percentage: parseFloat(row.find(".category-percentage").val()) || 0,
          quantity: parseInt(row.find(".category-quantity").val()) || 0,
          total: parseFloat(row.find(".category-total").val()) || 0,
        });
      }
    });

    // Collect products
    $(".product-row").each(function () {
      const row = $(this);
      if (row.find(".recipe-select").val()) {
        formData.products.push({
          recipe_id: row.find(".recipe-select").val(),
          percentage: parseFloat(row.find(".product-percentage").val()) || 0,
          quantity: parseInt(row.find(".product-quantity").val()) || 0,
          total: parseFloat(row.find(".product-total").val()) || 0,
        });
      }
    });

    // Submit data
    $.ajax({
      url: "process.php",
      type: "POST",
      data: JSON.stringify(formData),
      contentType: "application/json",
      success: function (response) {
        if (response.success) {
          showToast("Sales data saved successfully!", "success");
          $("#nextDay").click();
        } else {
          showToast(response.message || "Error saving sales data", "danger");
        }
      },
      error: function () {
        showToast("Error saving sales data", "danger");
      },
    });
  },

  // Attach all event handlers
  attachEventHandlers: function () {
    // Navigation buttons
    $("#prevDay").click(() => {
      const currentDate = new Date($("#sale_date").val());
      currentDate.setDate(currentDate.getDate() - 1);
      $("#sale_date").val(currentDate.toISOString().split("T")[0]);
      this.loadDayData();
    });

    $("#nextDay").click(() => {
      const currentDate = new Date($("#sale_date").val());
      currentDate.setDate(currentDate.getDate() + 1);
      $("#sale_date").val(currentDate.toISOString().split("T")[0]);
      this.loadDayData();
    });

    // Add new rows
    $("#addCategory").click(() => {
      const newRow = $(".category-row").first().clone();
      newRow.find("input").val("");
      newRow.find("select").val("");
      $("#categoriesTable tbody").append(newRow);
      this.updateTotals();
    });

    $("#addProduct").click(() => {
      const newRow = $(".product-row").first().clone();
      newRow.find("input").val("");
      newRow.find("select").val("");
      $("#productsTable tbody").append(newRow);
      this.updateTotals();
    });

    // Remove rows
    $(document).on("click", ".remove-row", (e) => {
      const tbody = $(e.target).closest("tbody");
      if (tbody.find("tr").length > 1) {
        $(e.target).closest("tr").remove();
        this.updateTotals();
      }
    });

    // Update totals on input
    $(document).on("input", "input", () => this.updateTotals());

    // Calculate average order
    $("#total_sales, #orders_count").on("input", () => {
      const total = parseFloat($("#total_sales").val()) || 0;
      const orders = parseInt($("#orders_count").val()) || 1;
      $("#average_order").val((total / orders).toFixed(2));
    });

    // Copy previous day data
    $("#copyPrevious").click(() => {
      const currentDate = new Date($("#sale_date").val());
      const previousDate = new Date(currentDate);
      previousDate.setDate(previousDate.getDate() - 1);

      $.get(
        "includes/get_daily_summary.php",
        {
          date: previousDate.toISOString().split("T")[0],
        },
        (data) => {
          if (data.summary) {
            this.fillFormWithData(data);
          }
        }
      );
    });

    // Verify totals
    $("#verifyTotals").click(() => {
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

    // Form submission
    $("#dailySalesForm").submit((e) => this.handleSubmit(e));
  },
};

// Initialize when document is ready
$(document).ready(() => SalesEntry.init());
