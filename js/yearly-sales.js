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

  GetYearlySales();
});
