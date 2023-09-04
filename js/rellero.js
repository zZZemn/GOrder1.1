$(document).ready(function () {
  const getRellero = () => {
    var date = $("#rellero_date").val();
    var type = $("#select-type").val();

    $.ajax({
      type: "GET",
      url: "../server/get-rellero.php",
      data: {
        date: date,
        type: type,
      },
      success: function (response) {
        $("#table-response-container").html(response);
      },
    });
  };

  getRellero();

  $("#rellero_date").change(function (e) {
    e.preventDefault();
    getRellero();
  });

  $("#select-type").change(function (e) {
    e.preventDefault();
    getRellero();
  });
});
