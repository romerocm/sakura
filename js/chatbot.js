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
    fetch("https://api.openai.com/v1/engines/davinci-codex/completions", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${apiKey}`,
      },
      body: JSON.stringify({
        prompt: message,
        max_tokens: 150,
      }),
    })
    .then(response => response.json())
    .then(data => {
      if (data.choices && data.choices.length > 0) {
        const aiMessage = data.choices[0].text.trim();
        $(".chat-messages").append(`<div class="chat-message ai-message">${aiMessage}</div>`);
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
          prompt: `Analyze the following Excel data and return a JSON format: ${excelData}`,
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

  function populateFormWithJsonData(jsonData) {
    // Populate form fields with JSON data
    $("#sale_date").val(jsonData.sale_date);
    $("#total_sales").val(jsonData.total_sales);
    $("#net_sales").val(jsonData.net_sales);
    $("#tips").val(jsonData.tips);
    $("#customer_count").val(jsonData.customer_count);

    // Populate categories
    jsonData.categories.forEach((category, index) => {
      const row = $("#categoriesTable tbody tr").eq(index);
      row.find(".category-select").val(category.category_id);
      row.find(".category-percentage").val(category.percentage);
      row.find(".category-quantity").val(category.quantity);
      row.find(".category-total").val(category.total);
    });

    // Populate products
    jsonData.products.forEach((product, index) => {
      const row = $("#productsTable tbody tr").eq(index);
      row.find(".recipe-select").val(product.recipe_id);
      row.find(".product-percentage").val(product.percentage);
      row.find(".product-quantity").val(product.quantity);
      row.find(".product-total").val(product.total);
    });

    updateTotals();
  }
});
