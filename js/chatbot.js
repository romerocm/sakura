$(document).ready(function () {
  // OpenAI Chatbot settings
  $("#openAISettings").on("click", function () {
    $("#settingsModal").modal("show");
  });

  $("#saveApiKey").on("click", function () {
    const apiKey = $("#apiKeyInput").val();
    if (apiKey.trim() !== "") {
      localStorage.setItem("openai_api_key", apiKey);
      alert("API Key saved successfully!");
      $("#settingsModal").modal("hide");
    } else {
      alert("Please enter a valid API Key.");
    }
  });
  $("#openAIChatbot").on("click", function () {
    $("#chatWindow").toggle();
  });

  $(document).on("click", ".close-chat", function () {
    $("#chatWindow").hide();
  });

  $("#sendChat").on("click", function () {
    const message = $("#chatInput").val();
    if (message.trim() === "") return;

    $(".chat-messages").append(`<div class="chat-message user-message">${message}</div>`);
    $("#chatInput").val("");

    // Send the message to OpenAI and handle the response
    const apiKey = localStorage.getItem("openai_api_key");
    if (!apiKey) {
      alert("API Key is not set. Please enter your OpenAI API Key in the settings.");
      return;
    }
    fetch("https://api.openai.com/v1/chat/completions", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${apiKey}`,
      },
      body: JSON.stringify({
        model: "gpt-4-turbo",
        messages: [
          { role: "system", content: "Please convert the following sales report data into a JSON format suitable for filling the fact_sales_form.php. Ensure the output is valid JSON." },
          { role: "user", content: message }
        ],
        max_tokens: 300,
      }),
    })
    .then(response => response.json())
    .then(data => {
      if (data.choices && data.choices.length > 0) {
        const aiMessage = data.choices[0].message.content.trim();
        $(".chat-messages").append(`<div class="chat-message ai-message">${aiMessage}</div>`);
        try {
          const jsonData = JSON.parse(aiMessage);
          if (jsonData && typeof jsonData === 'object') {
            populateFormWithJsonData(jsonData);
            updateFormFields(jsonData);
          } else {
            console.error("AI response is not in JSON format:", aiMessage);
            $(".chat-messages").append(`<div class="chat-message ai-message">AI response is not in JSON format. Please ensure the response is in JSON format.</div>`);
          }
        } catch (e) {
          console.error("Error parsing AI response:", e);
          $(".chat-messages").append(`<div class="chat-message ai-message">Error parsing AI response. Please ensure the response is in JSON format.</div>`);
          $(".chat-messages").append(`<div class="chat-message ai-message">Raw AI response: ${aiMessage}</div>`);
        }
      } else {
        $(".chat-messages").append(`<div class="chat-message ai-message">No response from AI.</div>`);
      }
    })
    .catch(error => {
      console.error("Error communicating with OpenAI:", error);
      $(".chat-messages").append(`<div class="chat-message ai-message">Error communicating with AI.</div>`);
    });
  });

  async function processDataWithOpenAI(apiKey, excelData) {
    try {
      const response = await fetch("https://api.openai.com/v1/engines/davinci-codex/completions", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Authorization": `Bearer ${apiKey}`,
        },
        body: JSON.stringify({
          prompt: `Convert the following sales report data into JSON format for the fields in the fact_sales_form.php. The data is structured as follows: 
          "Reporte de ventas - 1/10/2024
          Venta total: $52.94, Venta neta: $52.94, Propinas: $5.63, Descuentos: $3.26, Devoluciones: $0.00, Cantidad de clientes: 4, Promedio por clientes: $13.23, Ordenes: 3, Orden promedio: $17.65
          Categorías más vendidas: Bowls 53% 2 $27.90, Rolls 19% 1 $10.05, Sashimi 18% 1 $9.79, Bebidas 10% 2 $5.21
          Productos más vendidos: Tuna Steak Poke 26% 1 $13.95, Poke de Salmon 26% 1 $13.95, Sujin Roll 19% 1 $10.05, Yakisesamo 18% 1 $9.79, Michelob Ultra 6% 1 $3.25, Coca-Cola Zero 4% 1 $1.96"`,
          max_tokens: 150,
        }),
      });

      const result = await response.json();
      if (result.choices && result.choices.length > 0) {
        const jsonData = JSON.parse(result.choices[0].text);
        populateFormWithJsonData(jsonData);
      } else {
        alert("Failed to process data with OpenAI.");
      }
    } catch (error) {
      console.error("Error processing data with OpenAI:", error);
      alert("An error occurred while processing data.");
    }
  }

  function updateFormFields(jsonData) {
    // Update categories
    jsonData.categories.forEach(category => {
      $.ajax({
        url: 'process.php',
        type: 'POST',
        data: {
          form_type: 'categoria',
          categoria_id: category.category_id,
          nombre: category.name
        },
        success: function(response) {
          if (response.success) {
            showToast(`Category ${category.name} added successfully!`, "success");
          } else {
            showToast(`Error adding category ${category.name}: ${response.message}`, "danger");
          }
        },
        error: function() {
          showToast(`Error adding category ${category.name}`, "danger");
        }
      });
    });

    // Update products
    jsonData.products.forEach(product => {
      $.ajax({
        url: 'process.php',
        type: 'POST',
        data: {
          form_type: 'fact_sales',
          receta_id: product.recipe_id,
          order_type_id: product.order_type_id,
          quantity: product.quantity,
          total_amount: product.total,
          discount_amount: product.discount_amount || 0,
          tip_amount: jsonData.tips || 0,
          sale_date: jsonData.sale_date
        },
        success: function(response) {
          if (response.success) {
            showToast(`Product ${product.recipe_id} added successfully!`, "success");
          } else {
            showToast(`Error adding product ${product.recipe_id}: ${response.message}`, "danger");
          }
        },
        error: function() {
          showToast(`Error adding product ${product.recipe_id}`, "danger");
        }
      });
    });
  }

  function populateFormWithJsonData(jsonData) {
    // Populate form fields with JSON data
    if (jsonData.sale_date) $("#sale_date").val(jsonData.sale_date);
    if (jsonData.total_sales) $("#total_sales").val(jsonData.total_sales);
    if (jsonData.net_sales) $("#net_sales").val(jsonData.net_sales);
    if (jsonData.tips) $("#tips").val(jsonData.tips);
    if (jsonData.customer_count) $("#customer_count").val(jsonData.customer_count);

    // Populate categories
    if (jsonData.categories) {
      jsonData.categories.forEach((category, index) => {
        let row = $("#categoriesTable tbody tr").eq(index);
        if (row.length === 0) {
          $("#addCategory").click();
          row = $("#categoriesTable tbody tr").last();
        }
        row.find(".category-select").val(category.category_id);
        row.find(".category-percentage").val(category.percentage);
        row.find(".category-quantity").val(category.quantity);
        row.find(".category-total").val(category.total);
      });
    }

    // Populate products
    if (jsonData.products) {
      jsonData.products.forEach((product, index) => {
        let row = $("#productsTable tbody tr").eq(index);
        if (row.length === 0) {
          $("#addProduct").click();
          row = $("#productsTable tbody tr").last();
        }
        row.find(".recipe-select").val(product.recipe_id);
        row.find(".product-percentage").val(product.percentage);
        row.find(".product-quantity").val(product.quantity);
        row.find(".product-total").val(product.total);
      });
    }

    updateTotals();
  }
});
