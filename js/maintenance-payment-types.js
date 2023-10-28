$(document).ready(function () {
  const getPaymentTypes = () => {
    $.ajax({
      type: "GET",
      url: "../server/get-payment-types.php",
      data: {
        id: "..",
      },
      success: function (response) {
        $("#paymentTypesContainer").html(response);
      },
    });
  };

  const closeEditPaymentType = () => {
    $(".add-discount").css("opacity", "0").css("pointer-events", "none");
    $("#edit-bank-no-title").text("");
    $("#walletNumber").val("");
    $("#bankId").val("");
  };

  $(document).on("click", ".edit-bank-no", function (e) {
    e.preventDefault();

    var bankId = $(this).attr("data_id");
    var bankNumber = $(this).attr("data_number");
    var paymentType = $(this).attr("data_name");

    $(".add-discount").css("opacity", "1").css("pointer-events", "auto");
    $("#edit-bank-no-title").text("Edit " + paymentType + " Number");
    $("#walletNumber").val(bankNumber);
    $("#bankId").val(bankId);
  });

  $(".close-add-discount").click(function (e) {
    e.preventDefault();
    closeEditPaymentType();
  });

  $("#saveNewBankNumber").click(function (e) {
    e.preventDefault();

    var bankNumber = $("#walletNumber").val();
    var bankId = $("#bankId").val();

    console.log(bankNumber);
    console.log(bankId);
    $.ajax({
      type: "POST",
      url: "../ajax-url/save-new-pt-number.php",
      data: {
        id: bankId,
        number: bankNumber,
      },
      success: function (response) {
        closeEditPaymentType();
        getPaymentTypes();
        console.log(response);
        if (response === "200") {
          $(".alert-success").css("opacity", "1").text("Editing Success!");
          setTimeout(function () {
            $(".alert-success").css("opacity", "0").text("");
          }, 2000);
        } else {
          $(".alert-danger").css("opacity", "1").text("Something went wrong!");
          setTimeout(function () {
            $(".alert-danger").css("opacity", "0").text("");
          }, 2000);
        }
      },
    });
  });

  //   disable enable

  $(document).on("click", ".btnChangeStatus", function (e) {
    e.preventDefault();
    var action = $(this).attr("data_action");
    var id = $(this).attr("data_id");

    $.ajax({
      type: "POST",
      url: "../ajax-url/save-new-pt-number.php",
      data: {
        action: action,
        id: id,
      },
      success: function (response) {
        if (response !== "200") {
          $(".alert-danger").css("opacity", "1").text("Something went wrong!");
          setTimeout(function () {
            $(".alert-danger").css("opacity", "0").text("");
          }, 2000);
        } else {
          getPaymentTypes();
        }
      },
    });
  });

  getPaymentTypes();
});
