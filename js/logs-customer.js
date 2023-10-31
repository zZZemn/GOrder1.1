$(document).ready(function () {
  const getLogs = () => {
    var logType = $("#logType").val();
    $.ajax({
      type: "GET",
      url: "../server/get-customer-log.php",
      data: {
        logType: logType,
      },
      success: function (response) {
        console.log(response);
        $("#table-response-container").html(response);
      },
    });
  };

  $("#logType").change(function (e) {
    e.preventDefault();
    getLogs();
  });

  getLogs();
});
