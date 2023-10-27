$(document).ready(function () {
  function orderSelect() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("select_status_container").innerHTML =
          this.responseText;
      }
    };

    var id = $("#transaction_id").val();
    var url = "../server/order-status-select.php?id=" + encodeURIComponent(id);
    xhttp.open("GET", url, true);
    xhttp.send();
  }

  window.onload = orderSelect();

  function loadXMLDoc() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("status_container").innerHTML =
          this.responseText;
      }
    };

    var id = $("#transaction_id").val();
    var url = "../server/order-status-update.php?id=" + encodeURIComponent(id);
    xhttp.open("GET", url, true);
    xhttp.send();
  }

  window.onload = loadXMLDoc;

  setInterval(function () {
    loadXMLDoc();
  }, 1000);

  function loadOrderDetails(response) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("fourt_container").innerHTML =
          this.responseText;
        if (response === "OK") {
          setTimeout(function () {
            $(".alert-transaction-complete")
              .css("opacity", 1)
              .css("pointer-events", "auto");
            setTimeout(function () {
              $(".alert-transaction-complete")
                .css("opacity", 0)
                .css("pointer-events", "none");
            }, 2000);
          }, 500);
        }
      }
    };

    var id = $("#transaction_id").val();
    var url = "../server/order-pay-update.php?id=" + encodeURIComponent(id);
    xhttp.open("GET", url, true);
    xhttp.send();
  }

  $("#select_status_container").on("click", "a", function () {
    var orderID = $(this).data("id");
    var new_status = $(this).data("status");
    var action = $(this).data("action");
    var deliverySelect = $("#pick-delivery-man").val();
    console.log("New Status:" + new_status);
    // console.log(orderID);
    // console.log(action);
    console.log("Delivery:" + deliverySelect);

    if (new_status === "Shipped") {
      if (deliverySelect == null) {
        console.log("Please Select Rider");
        $(".alert-danger")
          .text("Please Select Rider")
          .css("opacity", 1)
          .css("pointer-events", "auto");
        setTimeout(function () {
          $(".alert-danger")
            .css("opacity", 0)
            .css("pointer-events", "none")
            .text("");
        }, 2000);
      } else {
        console.log(deliverySelect);
        $.ajax({
          url: "../ajax-url/order-status-update.php",
          data: {
            new_status: new_status,
            transaction_id: orderID,
            action: action,
          },
          type: "POST",
          success: function (data) {
            $("#pick-delivery-man").prop("disabled", true);
            orderSelect();
            setTimeout(function () {
              loadOrderDetails();
            }, 1000);
          },
        });
      }
    } else {
      console.log(deliverySelect);
      $.ajax({
        url: "../ajax-url/order-status-update.php",
        data: {
          new_status: new_status,
          transaction_id: orderID,
          action: action,
        },
        type: "POST",
        success: function (data) {
          orderSelect();
          setTimeout(function () {
            loadOrderDetails();
          }, 1000);
        },
      });
    }
  });

  $("#pick-delivery-man").change(function () {
    var rider = $("#pick-delivery-man").val();
    var transaction_id = $("#transaction_id").val();

    $.ajax({
      url: "../ajax-url/order-status-update.php",
      data: {
        rider: rider,
        transaction_id: transaction_id,
      },
      type: "POST",
      success: function (data) {
        if (data === "Picked Up") {
          $("#update-order-status").prop("disabled", true);
        }
      },
    });
  });

  window.onload = loadOrderDetails;

  $(document).on("click", "#payment_submit", function () {
    // Get the values from the total_hidden and payment elements
    var transaction_id = $("#transaction_id").val();
    const total = parseFloat($("#total_hidden").val());
    const payment = parseFloat($("#payment").val());

    if (payment >= total) {
      $.ajax({
        url: "../ajax-url/order-payment-update.php", // Replace with the actual URL of your server-side script
        method: "POST",
        data: {
          total: total,
          payment: payment,
          transaction_id: transaction_id,
        },
        success: function (response) {
          console.log("Data logged successfully:", response);
          loadOrderDetails(response);
        },
        error: function (xhr, status, error) {
          console.error("Error logging data:", error);
        },
      });
    } else {
      $(".alert-payment-invalid")
        .css("opacity", 1)
        .css("pointer-events", "auto");
      setTimeout(function () {
        $(".alert-payment-invalid")
          .css("opacity", 0)
          .css("pointer-events", "none");
      }, 2000);
    }
  });

  $("#btn-print").click(function (e) {
    e.preventDefault();
    var rider = $("#pick-delivery-man").val();
    if (rider == null) {
      $(".alert-danger").css("opacity", 1).text("Please pick a rider!");
      setTimeout(function () {
        $(".alert-danger").css("opacity", 0).text("");
      }, 2000);
    } else {
      console.log(rider);
      window.print();
    }
  });

  // cancel order
  const closeFrmCancelOrder = () => {
    $("#frmCancelOrder").modal("hide");
    $("#frmCancelOrder").trigger("hidden.bs.modal");
    $("#txtReason").val("");
  };

  const cancelOrder = (id, reason) => {
    closeFrmCancelOrder();
    $.ajax({
      type: "POST",
      url: "../ajax-url/cancel-order.php",
      data: {
        order_id: id,
        reason: reason,
      },
      success: function (response) {
        console.log(response);
        if (response === "200") {
          $(".alert-success").css("opacity", 1).text("Order Cancelled!");
          setTimeout(function () {
            $(".alert-success").css("opacity", 0).text("");
          }, 2000);
          $("#btnCancelOrder").css("display", "none");
          loadXMLDoc();
          orderSelect();
        } else {
          $(".alert-danger").css("opacity", 1).text("Somthing Went Wrong.");
          setTimeout(function () {
            $(".alert-danger").css("opacity", 0).text("");
          }, 2000);
        }
      },
    });
  };

  $("#btnCancelOrder").click(function (e) {
    e.preventDefault();
    $("#frmCancelOrder").modal("show");
  });

  $("#frmCancelOrder").on("click", "#closeModal", () => {
    closeFrmCancelOrder();
  });

  $("#cancelOrderModalSaveChanges").click(function (e) {
    e.preventDefault();
    var txtReason = $("#txtReason").val();
    var orderId = $("#transaction_id").val();

    txtReason !== ""
      ? cancelOrder(orderId, txtReason)
      : console.log("Please Input Reason!");
  });

  // upload refund
  $("#frmUploadRefund").submit(function (e) {
    e.preventDefault();

    var orderId = $("#transaction_id").val();
    var formData = new FormData(this);
    formData.append("transaction_id", orderId);

    $.ajax({
      type: "POST",
      url: "../ajax-url/upload-refund.php",
      data: formData, // Use the FormData object directly
      processData: false,
      contentType: false,
      success: function (response) {
        if (response === "200") {
          $(".alert-success").css("opacity", 1).text("Upload Success!");
          setTimeout(function () {
            $(".alert-success").css("opacity", 0).text("");
          }, 2000);
          $("#btnCancelOrder").css("display", "none");
          loadXMLDoc();
          orderSelect();
          loadOrderDetails();
          $("#frmUploadRefund").css("display", "none");
        } else {
          $(".alert-danger").css("opacity", 1).text("Somthing Went Wrong.");
          setTimeout(function () {
            $(".alert-danger").css("opacity", 0).text("");
          }, 2000);
        }
      },
    });
  });
});
