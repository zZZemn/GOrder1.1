$(document).ready(function () {
  const GetMonthlySales = () => {
    var year = $("#sales_year").val();
    var month = $("#monthlySalesMonth").val();
    var transacationType = $("#select-trans-type").val();
    var custType = $("#select-cust-type").val();
    var processBy = $("#select-process-by").val();

    $.ajax({
      url: "../ajax-url/get-monthly-sales.php",
      data: {
        year: year,
        month: month,
        transactionType: transacationType,
        custType: custType,
        processBy: processBy,
      },
      type: "GET",
      success: function (data) {
        $("#table-response-container").html(data);
      },
    });
  };

  $(
    "#sales_year, #monthlySalesMonth, #select-trans-type, #select-cust-type, #select-process-by"
  ).on("change", function () {
    GetMonthlySales();
  });

  $("#printReport").click(function (e) {
    e.preventDefault();
    var year = $("#sales_year").val();
    var url = "../print.php?rpt_type=MonthlySales&year=" + year;

    window.open(url, "_blank");
  });

  GetMonthlySales();
});
