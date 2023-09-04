var vatRate = document.getElementById("vatRate").value;

$(document).ready(function () {
  $("#search_products").on("input", function () {
    var query = $(this).val();
    if (query.length >= 2) {
      $.ajax({
        url: "../ajax-url/pos-search.php",
        type: "POST",
        data: {
          query: query,
        },
        success: function (data) {
          $("#search_results").html(data);
        },
      });
    } else {
      $("#search_results").empty();
    }
  });

  $("#search_products").on("keypress", function (event) {
    // Check if the Enter key was pressed
    if (event.which === 13) {
      // Prevent the form from being submitted
      event.preventDefault();

      // Get the search query from the input field
      var query = $(this).val();

      // Send the search query to the server and update the search results
      if (query.length >= 2) {
        $.ajax({
          url: "../ajax-url/pos-search.php",
          type: "POST",
          data: {
            query: query,
          },
          success: function (data) {
            $("#search_results").html(data);

            var searchValue = $("#search_products").val();

            $(".product-select").each(function () {
              var productCode = $(this).find('input[name="productCode"]').val();

              if (productCode === searchValue) {
                var unitMeasurement = "";
                // extract the product details from the hidden input fields
                var productId = $(this).find('input[name="product_id"]').val();
                var productName = $(this)
                  .find('input[name="product_name"]')
                  .val();
                unitMeasurement = $(this).find('input[name="unit_meas"]').val();
                var sellingPrice = $(this)
                  .find('input[name="selling_price"]')
                  .val();
                var quantity_left = $(this)
                  .find('input[name="quantity_left"]')
                  .val();
                var isVatable = $(this).find('input[name="isVatable"]').val();
                var isDiscountable = $(this)
                  .find('input[name="isDiscountable"]')
                  .val();
                var quantity = 1;
                var existingOrderItem = $(
                  '.pos-orders-container tbody tr[data-product-id="' +
                    productId +
                    '"]'
                );

                if (quantity_left > 0) {
                  if (existingOrderItem.length > 0) {
                    // the product has already been added to the order
                    quantity =
                      parseInt(
                        existingOrderItem.find('input[name="quantity"]').val()
                      ) + 1;
                    existingOrderItem
                      .find('input[name="quantity"]')
                      .val(quantity);
                    existingOrderItem
                      .find(".amount")
                      .val(sellingPrice * quantity);
                  } else {
                    // create a new table row and append it to the table's tbody
                    var newRow =
                      "<tr data-product-id='" +
                      productId +
                      "'>" +
                      "<td class='pro-name-receipt'>" +
                      productName +
                      " " +
                      unitMeasurement +
                      "</td>" +
                      "<input type='hidden' name='isDiscountable' id='isDiscountable' value='" +
                      isDiscountable +
                      "'>" +
                      "<input type='hidden' name='isVatable' id='isVatable' value='" +
                      isVatable +
                      "'>" +
                      "<td><input type='number' class='no-border order-details-inputs form-control' name='selling_price' value='" +
                      sellingPrice +
                      "' readonly></td>" +
                      "<td><input type='number' name='quantity' class='no-border order-details-inputs form-control' value='" +
                      quantity +
                      "' min='1' max='" +
                      quantity_left +
                      '\' oninput="if(parseInt(this.value) > parseInt(this.max)) this.value = this.max;"></td>' +
                      "<td><input type='number' name='amount' class='no-border order-details-inputs amount form-control' value='" +
                      sellingPrice +
                      "' readonly></td>" +
                      "<td class='remove-when-print'><button type='button' class='btn btn-danger btn-sm remove-row'><i class='fas fa-trash'></i></button></td>" +
                      "<input type='hidden' name='product_id' value='" +
                      productId +
                      "'>" +
                      "</tr>";

                    $(".pos-orders-container tbody").append(newRow);
                  }

                  // calculate and update subtotal
                  var subtotal = 0;
                  var vatableSubtotal = 0;
                  $(".pos-orders-container tbody tr").each(function () {
                    var amount = $(this).find(".amount").val();
                    subtotal += parseFloat(amount);
                    var isVatableItem = $(this)
                      .find('input[name="isVatable"]')
                      .val();
                    if (isVatableItem == 1) {
                      vatableSubtotal += parseFloat(amount);
                    }
                  });

                  // Update the subtotal input value
                  $('input[name="subtotal"]').val(subtotal.toFixed(2));

                  // Update the VAT value if applicable
                  if (isVatable == 1) {
                    var vat = vatableSubtotal * vatRate; // calculate the VAT value
                    $("#vat").val(vat.toFixed(2)); // set the VAT input value to 2 decimal places
                  }

                  //discount
                  var discoutableSubtotal = 0;
                  $(".pos-orders-container tbody tr").each(function () {
                    var amount = $(this).find(".amount").val();
                    var isDiscountable = $(this)
                      .find('input[name="isDiscountable"]')
                      .val();
                    if (isDiscountable == 1) {
                      discoutableSubtotal += parseFloat(amount);
                    }
                  });

                  var discountRate = $("#cust_type").val();
                  var subtotal_val = parseFloat($("#subtotal").val());
                  var vat_val = parseFloat($("#vat").val());

                  var discountAmount = discoutableSubtotal * discountRate;
                  $("#discount").val(discountAmount.toFixed(2));

                  //set total
                  var subtotal_val = parseFloat($("#subtotal").val());
                  var vat_val = parseFloat($("#vat").val());
                  var discount_val = parseFloat($("#discount").val());
                  var total = subtotal_val + vat_val - discount_val;

                  $("#total").val(total.toFixed(2)); //set total

                  var payment = parseFloat($("#payment").val()); //parse input value to float
                  var total = parseFloat($("#total").val());

                  var change = 0.0;

                  if (total > 0) {
                    if (payment >= total) {
                      change = payment - total;
                      parseFloat($("#change").val(change.toFixed(2)));

                      $("#save").prop("disabled", false);
                      $("#save_print").prop("disabled", false);
                    } else {
                      parseFloat($("#change").val(change.toFixed(2)));

                      $("#save").prop("disabled", true);
                      $("#save_print").prop("disabled", true);
                    }
                  } else {
                    parseFloat($("#change").val(change.toFixed(2)));
                  }

                  $("#search_products").val("");
                  $("#search_results").html("");
                } else {
                  $(".alert-no-qty-left").css("opacity", 1);
                  $(".alert-no-qty-left").css("pointer-events", "auto");

                  setTimeout(function () {
                    $(".alert-no-qty-left").css("opacity", 0);
                    $(".alert-no-qty-left").css("pointer-events", "none");
                  }, 2000);
                  $("#search_products").val("");
                  $("#search_results").html("");
                }
              }
            });
          },
        });
      } else {
        $("#search_results").empty();
      }
    }
  });

  $(".pos-select-item-container").on("submit", ".product-select", function (e) {
    e.preventDefault(); // prevent the form from submitting
    var unitMeasurement = "";
    // extract the product details from the hidden input fields
    var productId = $(this).find('input[name="product_id"]').val();
    var productName = $(this).find('input[name="product_name"]').val();
    unitMeasurement = $(this).find('input[name="unit_meas"]').val();
    var sellingPrice = $(this).find('input[name="selling_price"]').val();
    var quantity_left = $(this).find('input[name="quantity_left"]').val();
    var isVatable = $(this).find('input[name="isVatable"]').val();
    var isDiscountable = $(this).find('input[name="isDiscountable"]').val();
    var quantity = 1;
    var existingOrderItem = $(
      '.pos-orders-container tbody tr[data-product-id="' + productId + '"]'
    );

    if (quantity_left > 0) {
      if (existingOrderItem.length > 0) {
        // the product has already been added to the order
        quantity =
          parseInt(existingOrderItem.find('input[name="quantity"]').val()) + 1;
        existingOrderItem.find('input[name="quantity"]').val(quantity);
        existingOrderItem.find(".amount").val(sellingPrice * quantity);
      } else {
        // create a new table row and append it to the table's tbody
        var newRow =
          "<tr data-product-id='" +
          productId +
          "'>" +
          "<td class='pro-name-receipt'>" +
          productName +
          " " +
          unitMeasurement +
          "</td>" +
          "<input type='hidden' name='isDiscountable' id='isDiscountable' value='" +
          isDiscountable +
          "'>" +
          "<input type='hidden' name='isVatable' id='isVatable' value='" +
          isVatable +
          "'>" +
          "<td><input type='number' class='no-border order-details-inputs form-control' name='selling_price' value='" +
          sellingPrice +
          "' readonly></td>" +
          "<td><input type='number' name='quantity' class='no-border order-details-inputs form-control' value='" +
          quantity +
          "' min='1' max='" +
          quantity_left +
          '\' oninput="if(parseInt(this.value) > parseInt(this.max)) this.value = this.max;"></td>' +
          "<td><input type='number' name='amount' class='no-border order-details-inputs amount form-control' value='" +
          sellingPrice +
          "' readonly></td>" +
          "<td class='remove-when-print'><button type='button' class='btn btn-danger btn-sm remove-row'><i class='fas fa-trash'></i></button></td>" +
          "<input type='hidden' name='product_id' value='" +
          productId +
          "'>" +
          "</tr>";

        $(".pos-orders-container tbody").append(newRow);
      }

      // calculate and update subtotal
      var subtotal = 0;
      var vatableSubtotal = 0;
      $(".pos-orders-container tbody tr").each(function () {
        var amount = $(this).find(".amount").val();
        subtotal += parseFloat(amount);
        var isVatableItem = $(this).find('input[name="isVatable"]').val();
        if (isVatableItem == 1) {
          vatableSubtotal += parseFloat(amount);
        }
      });

      // Update the subtotal input value
      $('input[name="subtotal"]').val(subtotal.toFixed(2));

      // Update the VAT value if applicable
      if (isVatable == 1) {
        var vat = vatableSubtotal * vatRate; // calculate the VAT value
        $("#vat").val(vat.toFixed(2)); // set the VAT input value to 2 decimal places
      }

      //discount
      var discoutableSubtotal = 0;
      $(".pos-orders-container tbody tr").each(function () {
        var amount = $(this).find(".amount").val();
        var isDiscountable = $(this).find('input[name="isDiscountable"]').val();
        if (isDiscountable == 1) {
          discoutableSubtotal += parseFloat(amount);
        }
      });

      var discountRate = $("#cust_type").val();
      var subtotal_val = parseFloat($("#subtotal").val());
      var vat_val = parseFloat($("#vat").val());

      var discountAmount = discoutableSubtotal * discountRate;
      $("#discount").val(discountAmount.toFixed(2));

      //set total
      var subtotal_val = parseFloat($("#subtotal").val());
      var vat_val = parseFloat($("#vat").val());
      var discount_val = parseFloat($("#discount").val());
      var total = subtotal_val + vat_val - discount_val;

      $("#total").val(total.toFixed(2)); //set total

      var payment = parseFloat($("#payment").val()); //parse input value to float
      var total = parseFloat($("#total").val());

      var change = 0.0;

      if (total > 0) {
        if (payment >= total) {
          change = payment - total;
          parseFloat($("#change").val(change.toFixed(2)));

          $("#save").prop("disabled", false);
          $("#save_print").prop("disabled", false);
        } else {
          parseFloat($("#change").val(change.toFixed(2)));

          $("#save").prop("disabled", true);
          $("#save_print").prop("disabled", true);
        }
      } else {
        parseFloat($("#change").val(change.toFixed(2)));
      }
    } else {
      $(".alert-no-qty-left").css("opacity", 1);
      $(".alert-no-qty-left").css("pointer-events", "auto");

      setTimeout(function () {
        $(".alert-no-qty-left").css("opacity", 0);
        $(".alert-no-qty-left").css("pointer-events", "none");
      }, 2000);
    }
  });

  $(".pos-orders-container").on("input", 'input[name="quantity"]', function () {
    // Get the quantity value and selling price from the current row
    var maximumValue = $(this).attr("max");
    var quantity = $(this).val();

    if (quantity == maximumValue) {
      $(".alert-inv-qty-input").css("opacity", 1);
      $(".alert-inv-qty-input").css("pointer-events", "auto");
      setTimeout(function () {
        $(".alert-inv-qty-input").css("opacity", 0);
        $(".alert-inv-qty-input").css("pointer-events", "none");
      }, 1000);
    }

    var sellingPrice = $(this)
      .closest("tr")
      .find('input[name="selling_price"]')
      .val();
    var isVatable = $(this).closest("tr").find('input[name="isVatable"]').val();

    if (quantity === "" || parseFloat(quantity) < 1) {
      // Set quantity to 1
      $(this).val(1);
      quantity = 1; // Update the quantity variable
    }

    // Calculate the new amount based on the quantity and selling price
    var amount = quantity * sellingPrice;

    // Update the amount column with the new value
    $(this).closest("tr").find(".amount").val(amount);

    // Calculate subtotal and vatable subtotal
    var subtotal = 0;
    var vatableSubtotal = 0;
    $(".pos-orders-container tbody tr").each(function () {
      var amount = $(this).find(".amount").val();
      subtotal += parseFloat(amount);
      var isVatableItem = $(this).find('input[name="isVatable"]').val();
      if (isVatableItem == 1) {
        vatableSubtotal += parseFloat(amount);
      }
    });

    // Update the subtotal input value
    $('input[name="subtotal"]').val(subtotal.toFixed(2));

    // Update the VAT value if applicable
    if (isVatable == 1) {
      var vat = vatableSubtotal * vatRate; // calculate the VAT value
      $("#vat").val(vat.toFixed(2)); // set the VAT input value to 2 decimal places
    }

    //discount
    var discoutableSubtotal = 0;
    $(".pos-orders-container tbody tr").each(function () {
      var amount = $(this).find(".amount").val();
      var isDiscountable = $(this).find('input[name="isDiscountable"]').val();
      if (isDiscountable == 1) {
        discoutableSubtotal += parseFloat(amount);
      }
    });

    var discountRate = $("#cust_type").val();
    var subtotal_val = parseFloat($("#subtotal").val());
    var vat_val = parseFloat($("#vat").val());

    var discountAmount = discoutableSubtotal * discountRate;
    $("#discount").val(discountAmount.toFixed(2));

    //set total
    var subtotal_val = parseFloat($("#subtotal").val());
    var vat_val = parseFloat($("#vat").val());
    var discount_val = parseFloat($("#discount").val());
    var total = subtotal_val + vat_val - discount_val;

    $("#total").val(total.toFixed(2)); //set total

    var payment = parseFloat($("#payment").val()); //parse input value to float
    var total = parseFloat($("#total").val());

    var change = 0.0;

    if (total > 0) {
      if (payment >= total) {
        change = payment - total;
        parseFloat($("#change").val(change.toFixed(2)));

        $("#save").prop("disabled", false);
        $("#save_print").prop("disabled", false);
      } else {
        parseFloat($("#change").val(change.toFixed(2)));

        $("#save").prop("disabled", true);
        $("#save_print").prop("disabled", true);
      }
    } else {
      parseFloat($("#change").val(change.toFixed(2)));
    }
  });

  $(".pos-orders-container").on("click", ".remove-row", function () {
    $(this).closest("tr").remove();

    var subtotal = 0;
    var vatableSubtotal = 0;
    $(".pos-orders-container tbody tr").each(function () {
      var amount = $(this).find(".amount").val();
      subtotal += parseFloat(amount);
      var isVatableItem = $(this).find('input[name="isVatable"]').val();
      if (isVatableItem == 1) {
        vatableSubtotal += parseFloat(amount);
      }
    });

    // Update the subtotal input value
    $('input[name="subtotal"]').val(subtotal.toFixed(2));

    var vat = vatableSubtotal * vatRate; // calculate the VAT value
    $("#vat").val(vat.toFixed(2)); // set the VAT input value to 2 decimal places

    //discount
    var discoutableSubtotal = 0;
    $(".pos-orders-container tbody tr").each(function () {
      var amount = $(this).find(".amount").val();
      var isDiscountable = $(this).find('input[name="isDiscountable"]').val();
      if (isDiscountable == 1) {
        discoutableSubtotal += parseFloat(amount);
      }
    });

    var discountRate = $("#cust_type").val();
    var subtotal_val = parseFloat($("#subtotal").val());
    var vat_val = parseFloat($("#vat").val());

    var discountAmount = discoutableSubtotal * discountRate;
    $("#discount").val(discountAmount.toFixed(2));

    //set total
    var subtotal_val = parseFloat($("#subtotal").val());
    var vat_val = parseFloat($("#vat").val());
    var discount_val = parseFloat($("#discount").val());
    var total = subtotal_val + vat_val - discount_val;

    $("#total").val(total.toFixed(2)); //set total

    var payment = parseFloat($("#payment").val()); //parse input value to float
    var total = parseFloat($("#total").val());

    var change = 0.0;

    if (total > 0) {
      if (payment >= total) {
        change = payment - total;
        parseFloat($("#change").val(change.toFixed(2)));

        $("#save").prop("disabled", false);
        $("#save_print").prop("disabled", false);
      } else {
        parseFloat($("#change").val(change.toFixed(2)));

        $("#save").prop("disabled", true);
        $("#save_print").prop("disabled", true);
      }
    } else {
      parseFloat($("#change").val(change.toFixed(2)));
    }
  });

  $("#cust_type").on("change", function () {
    var discoutableSubtotal = 0;
    $(".pos-orders-container tbody tr").each(function () {
      var amount = $(this).find(".amount").val();
      var isDiscountable = $(this).find('input[name="isDiscountable"]').val();
      if (isDiscountable == 1) {
        discoutableSubtotal += parseFloat(amount);
      }
    });

    var discountRate = $("#cust_type").val();
    var subtotal_val = parseFloat($("#subtotal").val());
    var vat_val = parseFloat($("#vat").val());

    var discountAmount = discoutableSubtotal * discountRate;
    $("#discount").val(discountAmount.toFixed(2));

    // Recalculate the total
    var total = subtotal_val + vat_val - parseFloat($("#discount").val());
    $("#total").val(total.toFixed(2));

    //update change
    var payment = parseFloat($("#payment").val()); //parse input value to float
    var total = parseFloat($("#total").val());

    var change = 0.0;

    if (total > 0) {
      if (payment >= total) {
        change = payment - total;
        parseFloat($("#change").val(change.toFixed(2)));

        $("#save").prop("disabled", false);
        $("#save_print").prop("disabled", false);
      } else {
        parseFloat($("#change").val(change.toFixed(2)));

        $("#save").prop("disabled", true);
        $("#save_print").prop("disabled", true);
      }
    } else {
      parseFloat($("#change").val(change.toFixed(2)));
    }
  });

  $("#payment").on("input", function () {
    var payment = parseFloat($(this).val()); //parse input value to float
    var total = parseFloat($("#total").val());

    var change = 0.0;

    if (total > 0) {
      if (payment >= total) {
        change = payment - total;
        parseFloat($("#change").val(change.toFixed(2)));

        $("#save").prop("disabled", false);
        $("#save_print").prop("disabled", false);
      } else {
        parseFloat($("#change").val(change.toFixed(2)));

        $("#save").prop("disabled", true);
        $("#save_print").prop("disabled", true);
      }
    } else {
      parseFloat($("#change").val(change.toFixed(2)));
    }
  });

  $("#cust_id").on("keyup", function () {
    // Get the value of the input element
    var custId = $(this).val();

    // Send an AJAX request to the server to check if the customer ID exists
    $.ajax({
      url: "../ajax-url/pos-check-cust-id.php",
      method: "POST",
      data: { cust_id: custId },
      success: function (data) {
        if (custId == "") {
          $("#cust_id")
            .removeClass("outline-danger")
            .addClass("outline-primary");

          $("#save").prop("disabled", false);
          $("#save_print").prop("disabled", false);
        } else if (data == "exists") {
          // If the customer ID exists, set the border color to blue
          $("#cust_id")
            .removeClass("outline-danger")
            .addClass("outline-primary");

          $("#save").prop("disabled", false);
          $("#save_print").prop("disabled", false);
        } else {
          // If the customer ID does not exist, set the border color to red
          $("#cust_id")
            .removeClass("outline-primary")
            .addClass("outline-danger");

          $("#save").prop("disabled", true);
          $("#save_print").prop("disabled", true);
        }
      },
    });
  });

  $("#payment").on("keyup", function () {
    // Get the total value
    var total = $("#total").val();
    var payment = parseFloat($(this).val());
    // Enable/disable the button based on the total value
    if (total > 0) {
      if (payment >= total) {
        $("#save").prop("disabled", false);
        $("#save_print").prop("disabled", false);
      } else {
        $("#save").prop("disabled", true);
        $("#save_print").prop("disabled", true);
      }
    }
  });
});

$(document).ready(function () {
  $("#save_print").click(function (event) {
    event.preventDefault();
    var cust_type = $("#cust_type").children("option:selected").text();
    // Create an object to store the sales and sales details data
    var salesData = {
      sales: {
        transaction_type: "POS",
        cust_type: cust_type,
        cust_id: $("#cust_id").val(),
        emp_id: $("#emp_id").val(),
        subtotal: $("#subtotal").val(),
        vat: $("#vat").val(),
        discount: $("#discount").val(),
        total: $("#total").val(),
        payment: $("#payment").val(),
        change: $("#change").val(),
      },
      salesDetails: [],
    };

    // Loop through each row in the table and add the details to the object
    $(".pos-orders-container tbody tr").each(function (index, row) {
      var detailsData = {
        product_id: $(row).find('[name="product_id"]').val(),
        quantity: $(row).find('[name="quantity"]').val(),
        amount: $(row).find('[name="amount"]').val(),
      };

      // Add the details to the salesData object
      salesData.salesDetails.push(detailsData);
    });

    // Send the AJAX request to the server
    $.ajax({
      type: "POST",
      url: "../ajax-url/pos-save-process.php",
      data: JSON.stringify(salesData),
      contentType: "application/json",
      success: function (response) {
        // Parse the response JSON object
        console.log(response);
        var responseData = JSON.parse(response);
        if (responseData.success) {
          // Get the date and time from the response
          var date = responseData.date;
          var time = responseData.time;

          $("#receipt-table tr td").addClass("border-0");
          // Rest of your code

          // Append the date and time to the HTML element with ID "date-time-print"
          $(".table").removeClass("table-striped");
          $("#ggd").append("Golden Gate Drugstore");
          $("#ggd-add").append("Patubig, Marilao, Bulacan");
          $("#date-time-print").append(date + "  |  " + time);

          $("#receipt-subtotal").append(
            "<p>Subtotal </p> <p>:</p><p>" + $("#subtotal").val() + "</p>"
          );
          $("#receipt-vat").append(
            "<p>VAT </p> <p>:</p><p>" + $("#vat").val() + "</p>"
          );
          $("#receipt-discount").append(
            "<p>Discount </p> <p>:</p><p>" + $("#discount").val() + "</p>"
          );
          $("#receipt-total").append(
            "<p>Total </p> <p>:</p><p>" + $("#total").val() + "</p>"
          );
          $("#receipt-payment").append(
            "<p>Payment </p> <p>:</p><p>" + $("#payment").val() + "</p>"
          );
          $("#receipt-change").append(
            "<p>Change </p> <p>:</p><p>" + $("#change").val() + "</p>"
          );

          window.print();
          location.reload();
        } else {
          console.log(responseData.error);
        }
      },
      error: function (error) {
        console.log(error);
      },
    });
  });
});

$(document).ready(function () {
  $("#reset").click(function (event) {
    location.reload();
  });

  //add customer
  const closeFrmAdd = () => {
    $("#fname").val("");
    $("#lname").val("");
    $("#mi").val("");
    $("#suffix").val("");
    $("#birthday").val("");
    $("#discount-type").val("");
    $("#contact-no").val("");
    $("#region").val("");
    $("#province").val("");
    $("#municipality").val("");
    $("#barangay").val("");
    $("#unit").val("");
    $("#frm-add-cust").css("display", "none");
  };

  const validateAddCustomerInput = (fname, lname, birthday, barangay, unit) => {
    if (fname.trim() === "") {
      $(".enter-fname").css("opacity", 1);
      setTimeout(function () {
        $(".enter-fname").css("opacity", 0);
      }, 2000);
      return false;
    }

    if (lname.trim() === "") {
      $(".enter-lname").css("opacity", 1);
      setTimeout(function () {
        $(".enter-lname").css("opacity", 0);
      }, 2000);
      return false;
    }

    if (birthday.trim() === "") {
      $(".enter-birthday").css("opacity", 1);
      setTimeout(function () {
        $(".enter-birthday").css("opacity", 0);
      }, 2000);
      return false;
    }

    if (barangay.trim() === "") {
      $(".enter-bgy").css("opacity", 1);
      setTimeout(function () {
        $(".enter-bgy").css("opacity", 0);
      }, 2000);
      return false;
    }

    if (unit.trim() === "") {
      $(".enter-unit").css("opacity", 1);
      setTimeout(function () {
        $(".enter-unit").css("opacity", 0);
      }, 2000);
      return false;
    }

    return true;
  };

  $("#region").on("change", () => {
    $("#province").val("");
    $("#municipality").val("");
    $("#barangay").val("");

    var region = $("#region").val();
    $.ajax({
      type: "POST",
      url: "../ajax-url/get-provinces.php",
      data: { regionID: region },
      success: function (response) {
        var responseData = JSON.parse(response);
        var selectElement = $("#province");
        selectElement.empty();
        selectElement.append("<option></option>");
        $.each(responseData, function (index, item) {
          var option = $("<option></option>");

          option.val(item.provinceID);
          option.text(item.province);
          selectElement.append(option);
        });
      },
    });
  });

  $("#province").on("change", () => {
    $("#municipality").val("");
    $("#barangay").val("");

    var province = $("#province").val();
    $.ajax({
      type: "POST",
      url: "../ajax-url/get-municipalities.php",
      data: { provinceID: province },
      success: function (response) {
        var responseData = JSON.parse(response);
        var selectElement = $("#municipality");
        selectElement.empty();
        selectElement.append("<option></option>");
        $.each(responseData, function (index, item) {
          var option = $("<option></option>");

          option.val(item.municipalityID);
          option.text(item.municipality);
          selectElement.append(option);
        });
      },
    });
  });

  $("#municipality").on("change", () => {
    $("#barangay").val("");

    var municipality = $("#municipality").val();
    $.ajax({
      type: "POST",
      url: "../ajax-url/get-barangay.php",
      data: { municipalityID: municipality },
      success: function (response) {
        var responseData = JSON.parse(response);
        var selectElement = $("#barangay");
        selectElement.empty();
        selectElement.append("<option></option>");
        $.each(responseData, function (index, item) {
          var option = $("<option></option>");

          option.val(item.barangayID);
          option.text(item.barangay);
          selectElement.append(option);
        });
      },
    });
  });

  $("#close-frm-add-cust").click((e) => {
    e.preventDefault();
    closeFrmAdd();
  });

  $("#btn-cancel").click((e) => {
    e.preventDefault();
    closeFrmAdd();
  });

  $("#btn-add-customer").click((e) => {
    e.preventDefault();
    $("#frm-add-cust").css("display", "flex");
  });

  $("#btn-submit").click((e) => {
    e.preventDefault();
    var fname = $("#fname").val();
    var lname = $("#lname").val();
    var mi = $("#mi").val();
    var suffix = $("#suffix").val();
    var birthday = $("#birthday").val();
    var discount_type = $("#discount-type").val();
    var contact_no = $("#contact-no").val();
    var barangay = $("#barangay").val();
    var unit = $("#unit").val();
    var sex = $("#sex").val();
    // console.log("fname:", fname);
    // console.log("lname:", lname);
    // console.log("mi:", mi);
    // console.log("suffix:", suffix);
    // console.log("birthday:", birthday);
    // console.log("discount_type:", discount_type);
    // console.log("contact_no:", contact_no);
    // console.log("region:", region);
    // console.log("province:", province);
    // console.log("municipality:", municipality);
    // console.log("barangay:", barangay);
    // console.log("unit:", unit);

    if (validateAddCustomerInput(fname, lname, birthday, barangay, unit)) {
      $.ajax({
        type: "POST",
        url: "../process/add-customer-process.php",
        data: {
          fname: fname,
          lname: lname,
          mi: mi,
          suffix: suffix,
          birthday: birthday,
          discount_type: discount_type,
          contact_no: contact_no,
          barangay: barangay,
          unit: unit,
          sex: sex,
        },
        success: function (response) {
          if (response === "added") {
            $(".cust-added").css("opacity", 1);
            setTimeout(function () {
              $(".cust-added").css("opacity", 0);
            }, 2000);
            closeFrmAdd();
          } else {
            $(".cust-not-added").css("opacity", 1);
            setTimeout(function () {
              $(".cust-not-added").css("opacity", 0);
            }, 2000);
            closeFrmAdd();
          }
        },
      });
    }
  });

  $("#btn-save-money").click(function () {
    $("#frm-add-money").css("display", "block");
  });

  $("#close-add-money").click(function () {
    $("#frm-add-money").css("display", "none");
  });

  $("#frm-add-money").submit(function (e) {
    e.preventDefault();
    var buttonType = $(document.activeElement).data("type");
    var isAnyInputNotEmpty = false;

    var formData = {
      serializedData: $("#frm-add-money").serialize(),
      buttonType: buttonType,
    };

    $('#frm-add-money input[type="number"]').each(function () {
      if ($(this).val() !== "") {
        isAnyInputNotEmpty = true;
        return false;
      }
    });

    if (isAnyInputNotEmpty) {
      $.ajax({
        type: "POST",
        url: "../ajax-url/add-money.php",
        data: formData,
        success: function (response) {
          console.log(response);
          $("." + response).css("opacity", 1);
          setTimeout(function () {
            $("." + response).css("opacity", 0);
          }, 2000);
          $("#frm-add-money").css("display", "none");
          $("#frm-add-money")[0].reset();
        },
      });
    } else {
      $(".all-input-empty").css("opacity", 1);
      setTimeout(function () {
        $(".all-input-empty").css("opacity", 0);
      }, 2000);
    }
  });
});
