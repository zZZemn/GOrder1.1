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
                  updateSODTable();
                },
              });
            } else {
              console.log("Invalid Quantity");
            }
          });
        } else {
          console.log("Invalid Product ID");
        }
      },
    });
  });

  //   delete
  $(document).on("click", ".btn-delete", function (e) {
    console.log($(this).attr("data-invid"));
  });

  updateSODTable();
});
