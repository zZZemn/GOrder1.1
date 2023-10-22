$(document).ready(function () {
  const GetYearlySales = () => {
    var year = $("#selectYear").val();

    $.ajax({
      type: "GET",
      url: "../ajax-url/get-yearly-sales.php",
      data: {
        year: year,
      },
      success: function (response) {
        $("#table-response-container").html(response);
      },
    });
  };

  $("#selectYear").on("change", function () {
    GetYearlySales();
  });

  $("#printReport").click(function (e) {
    e.preventDefault();
    var year = $("#selectYear").val();
    var url = "../print.php?rpt_type=YearlySales&year=" + year;
    window.open(url, "_blank");
  });

  GetYearlySales();
});
