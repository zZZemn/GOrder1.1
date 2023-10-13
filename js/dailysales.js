$(document).ready(function () {
  const getDailySales = () => {
    var date = $("#sales_date").val();
    var transactionType = $("#select-trans-type").val();
    var custType = $("#select-cust-type").val();
    var processBy = $("#select-process-by").val();
    $.ajax({
      url: "../ajax-url/get-daily-sales.php",
      data: {
        date: date,
        transactionType: transactionType,
        custType: custType,
        processBy: processBy,
      },
      type: "GET",
      success: function (data) {
        $("#table-response-container").html(data);
      },
    });
  };

  getDailySales();

  $("#sales_date").change(function (e) {
    e.preventDefault();
    getDailySales();
  });

  $("#select-trans-type").change(function (e) {
    e.preventDefault();
    getDailySales();
  });

  $("#select-cust-type").change(function (e) {
    e.preventDefault();
    getDailySales();
  });

  $("#select-process-by").change(function (e) {
    e.preventDefault();
    getDailySales();
  });

  $("#printReport").click(function (e) {
    e.preventDefault();
    var date = $("#sales_date").val();
    var transactionType = $("#select-trans-type").val();
    var custType = $("#select-cust-type").val();
    var processBy = $("#select-process-by").val();

    var url =
      "../print.php?rpt_type=DailySales&date=" +
      date +
      "&transaction_type=" +
      transactionType +
      "&cust_type=" +
      custType +
      "&process_by=" +
      processBy;
    window.open(url, "_blank");
  });
});
