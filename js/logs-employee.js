$(document).ready(function () {
  const getLogs = () => {
    var empId = $("#selectEmployee").val();
    var logType = $("#logType").val();

    $.ajax({
      type: "GET",
      url: "../server/get-employee-log.php",
      data: {
        emp_id: empId,
        log_type: logType,
      },
      success: function (response) {
        $("#table-response-container").html(response);
      },
    });
  };

  $("#selectEmployee, #logType").on("change", function (e) {
    e.preventDefault();
    getLogs();
    console.log("change");
  });

  getLogs();

  //Print Report
  $("#printReport").click(function (e) {
    e.preventDefault();
    var empId = $("#selectEmployee").val();
    var logType = $("#logType").val();

    var url =
      "../print.php?rpt_type=EmpLogs&emp_id=" + empId + "&log_type=" + logType;
    window.open(url, "_blank");
  });
});
