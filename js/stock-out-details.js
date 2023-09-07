$(document).ready(function () {
  const updateSODTable = () => {
    var id = $("#stock-out-id").val();
    $.ajax({
      type: "GET",
      url: "../server/get-stock-out-details.php",
      data: { id: id },
      success: function (response) {
        $("#sod-container").html(response);
      },
    });
  };

  const checkInvQty = (product_id, quantity, callback) => {
    var ret = false;
    $.ajax({
      type: "GET",
      url: "../ajax-url/get-inv-qty.php",
      data: {
        product_id: product_id,
        quantity: quantity,
      },
      success: function (response) {
        if (response == 1) {
          ret = true;
        } else {
          ret = false;
        }
        callback(ret); // Call the callback function with the result
      },
      error: function (error) {
        console.error("Error:", error);
        callback(false); // Call the callback function with an error value
      },
    });
  };

  //   add
  $("#frm-add-sod").submit(function (e) {
    e.preventDefault();
    var product_id = $("#product_name").val();
    var quantity = $("#qty").val();
    var soid = $("#stock-out-id").val();
    $.ajax({
      type: "GET",
      url: "../ajax-url/check-product-id.php",
      data: {
        id: product_id,
      },
      success: function (response) {
        if (response == 1) {
          checkInvQty(product_id, quantity, function (result) {
            if (result) {
              $.ajax({
                type: "POST",
                url: "../ajax-url/add-sod.php",
                data: {
                  id: product_id,
                  quantity: quantity,
                  soid: soid,
                },
                success: function (response) {
                  $("#frm-add-sod")[0].reset();
                  console.log(response);
                  updateSODTable();
                  if (response == 1) {
                    $(".alert-success").css("opacity", 1).text("Success!");
                    setTimeout(function () {
                      $(".alert-success").css("opacity", 0).text("");
                    }, 1000);
                  } else {
                    $(".alert-danger")
                      .css("opacity", 1)
                      .text("Something went wrong :<");
                    setTimeout(function () {
                      $(".alert-danger").css("opacity", 0).text("");
                    }, 1000);
                  }
                },
              });
            } else {
              console.log("Invalid Quantity");
              $("#qty").addClass("is-invalid");
            }
          });
        } else {
          console.log("Invalid Product ID");
          $("#product_name").addClass("is-invalid");
        }
      },
    });
  });

  $("#qty").keydown(function (e) {
    $(this).removeClass("is-invalid");
  });

  $("#product_name").keydown(function (e) {
    $(this).removeClass("is-invalid");
  });

  //   delete
  $(document).on("click", ".btn-delete", function (e) {
    var inv_id = $(this).attr("data-invid"); //update qty of this
    var qty = $(this).attr("data-qty"); //qty
    var sodid = $(this).attr("data-sodid"); //delete this
    var soid = $(this).attr("data-soid"); // update qty of this
    var selling_price = $(this).attr("data-sellingprice");
    // console.log(inv_id);
    // console.log(qty);
    // console.log(sodid);
    // console.log(soid);

    $.ajax({
      type: "POST",
      url: "../ajax-url/delete-sod.php",
      data: {
        inv_id: inv_id,
        qty: qty,
        soid: soid,
        sodid: sodid,
        selling_price: selling_price,
      },
      success: function (response) {
        console.log(response);
        updateSODTable();
        if (response == "Deletion Success!") {
          $(".alert-success").css("opacity", 1).text(response);
          setTimeout(function () {
            $(".alert-success").css("opacity", 0).text("");
          }, 1000);
        } else {
          $(".alert-danger").css("opacity", 1).text(response);
          setTimeout(function () {
            $(".alert-danger").css("opacity", 0).text("");
          }, 1000);
        }
      },
    });
  });

  updateSODTable();
});
