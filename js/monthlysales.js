$(document).ready(function () {
  var year = $("#sales_year").val();

  // Make the AJAX request using the initial value of the category select
  $.ajax({
    url: "../ajax-url/get-monthly-sales.php",
    data: { year: year },
    type: "POST",
    success: function (data) {
      console.log(data);
      $("#table-response-container").html(data);
    },
  });
});

$("#sales_year").on("change", function () {
  var year = $("#sales_year").val();
  $.ajax({
    url: "../ajax-url/get-monthly-sales.php",
    data: { year: year },
    type: "POST",
    success: function (data) {
      console.log(data);
      $("#table-response-container").html(data);
    },
  });
});

$("#printReport").click(function (e) {
  e.preventDefault();
  var year = $("#sales_year").val();
  var url = "../print.php?rpt_type=MonthlySales&year=" + year;

  window.open(url, "_blank");
});
