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
  const closeModal = (modalId) => {
    if (modalId == "deleteModal") {
      $("#delete-this-so").data("sodid", "");
      $("#delete-this-so").data("soid", "");
      $("#delete-this-so").data("invid", "");
      $("#delete-this-so").data("qty", "");
      $("#delete-this-so").data("sellingprice", "");

      $("#deleteModal").modal("hide");
    }
  };

  const deleteStockOut = (inv_id, qty, soid, sodid, selling_price) => {
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
        closeModal("deleteModal");
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
  };

  $(document).on("click", ".btn-delete", function (e) {
    var inv_id = $(this).attr("data-invid"); //update qty of this
    var qty = $(this).attr("data-qty"); //qty
    var sodid = $(this).attr("data-sodid"); //delete this
    var soid = $(this).attr("data-soid"); // update qty of this
    var selling_price = $(this).attr("data-sellingprice");

    $("#delete-this-so").data("sodid", sodid);
    $("#delete-this-so").data("soid", soid);
    $("#delete-this-so").data("invid", inv_id);
    $("#delete-this-so").data("qty", qty);
    $("#delete-this-so").data("sellingprice", selling_price);

    $("#deleteModal").modal("show");
  });

  $("#delete-this-so").click(function (e) {
    e.preventDefault();
    var inv_id = $(this).data("invid"); //update qty of this
    var qty = $(this).data("qty"); //qty
    var sodid = $(this).data("sodid"); //delete this
    var soid = $(this).data("soid"); // update qty of this
    var selling_price = $(this).data("sellingprice");

    console.log(inv_id);
    console.log(qty);
    console.log(sodid);
    console.log(soid);
    console.log(selling_price);

    deleteStockOut(inv_id, qty, soid, sodid, selling_price);
  });

  $("#close-delete-this-so").click(function (e) {
    e.preventDefault();
    closeModal("deleteModal");
  });

  updateSODTable();
});
