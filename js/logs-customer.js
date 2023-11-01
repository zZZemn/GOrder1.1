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

  //Print Report
  $("#printReport").click(function (e) {
    e.preventDefault();
    var logType = $("#logType").val();

    var url = "../print.php?rpt_type=CustLogs&log_type=" + logType;
    window.open(url, "_blank");
  });

  getLogs();
});
