// Sales Entry Form Handler
const SalesEntry = {
  init: function () {
    this.initializeDateField();
    this.attachEventHandlers();
    this.loadSelectOptions();
  },

  loadSelectOptions: function () {
    // Load categories
    $.get("includes/get_categories.php", function (data) {
      $(".category-select").html(data);
    });

    // Load recipes
    $.get("includes/get_recipes.php", function (data) {
      $(".recipe-select").html(data);
    });
  },

  initializeDateField: function () {
    $("#sale_date").val(new Date().toISOString().split("T")[0]);
  },

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
          $("#dailySalesForm")[0].reset();
          SalesEntry.initializeDateField();
        } else {
          showToast(response.message || "Error saving sales data", "danger");
        }
      },
      error: function () {
        showToast("Error saving sales data", "danger");
      },
    });
  },

  attachEventHandlers: function () {
    const self = this;

    // Add new category row
    $("#addCategory").on("click", function () {
      const template = $(".category-row").first().clone();
      template.find("input").val("");
      template.find("select").val("");
      $("#categoriesTable tbody").append(template);
      self.loadSelectOptions();
    });

    // Add new product row
    $("#addProduct").on("click", function () {
      const template = $(".product-row").first().clone();
      template.find("input").val("");
      template.find("select").val("");
      $("#productsTable tbody").append(template);
      self.loadSelectOptions();
    });

    // Remove rows
    $(document).on("click", ".remove-row", function () {
      const tbody = $(this).closest("tbody");
      if (tbody.find("tr").length > 1) {
        $(this).closest("tr").remove();
        self.updateTotals();
      }
    });

    // Update totals on input
    $(document).on("input", "input", function () {
      self.updateTotals();
    });

    // Calculate average order
    $("#total_sales, #orders_count").on("input", function () {
      const total = parseFloat($("#total_sales").val()) || 0;
      const orders = parseInt($("#orders_count").val()) || 1;
      $("#average_order").val((total / orders).toFixed(2));
    });

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

    // Form submission
    $("#dailySalesForm").on("submit", function (e) {
      self.handleSubmit(e);
    });
  },
};

// Initialize when document is ready
$(document).ready(function () {
  SalesEntry.init();
});
