$(document).ready(function () {
  var selectedTable = $("#selectedTable").val();
  const getBackupTable = (selectedTable) => {
    $.ajax({
      type: "GET",
      url: "../server/get-backup-table.php",
      data: {
        selectedTable: selectedTable,
      },
      success: function (response) {
        $("#selectedContainer").html(response);
      },
    });
  };

  $("#selectedTable").change(function (e) {
    e.preventDefault();
    getBackupTable($(this).val());
  });

  $(document).on("click", "#restore", function (e) {
    e.preventDefault();
    var selectedTable = $("#selectedTable").val();
    var table = $(this).data("table");
    var id = $(this).data("id");
    $.ajax({
      type: "POST",
      url: "../ajax-url/activate-process.php",
      data: {
        table: table,
        id: id,
      },
      success: function (response) {
        if (response == "200") {
          getBackupTable(selectedTable);
          $(".alert-success").css("opacity", "1").text("Activated");
          setTimeout(function () {
            $(".alert-success").css("opacity", "0").text("");
          }, 2000);
        } else {
          $(".alert-danger")
            .css("opacity", "1")
            .text("Something went wrong :<");
          setTimeout(function () {
            $(".alert-danger").css("opacity", "0").text("");
          }, 2000);
        }
      },
    });
  });

  getBackupTable(selectedTable);
});
