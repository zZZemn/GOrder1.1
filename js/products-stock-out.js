const nowInPH = new Date();
const philippinesTimeOffset = 8 * 60; // Philippines is UTC+8
nowInPH.setMinutes(nowInPH.getMinutes() + philippinesTimeOffset);

$(document).ready(function () {
  const closeForm = () => {
    $("#frm-add-stock-out").css("display", "none");
  };

  const updateStockOutTable = () => {
    var search = $("#search-input").val();
    var branch = $("#branch_select").val();
    var emp = $("#emp_select").val();

    $.ajax({
      type: "GET",
      url: "../server/get-stock-out.php",
      data: {
        search: search,
        branch: branch,
        emp: emp,
      },
      success: function (response) {
        $("#stock-out-container").html(response);
      },
    });
  };

  updateStockOutTable();

  $("#search-input").keyup(function () {
    updateStockOutTable();
  });

  $("#branch_select").change(function () {
    updateStockOutTable();
  });

  $("#emp_select").change(function () {
    updateStockOutTable();
  });

  $("#addStockOutOpen").click(function (e) {
    e.preventDefault();
    $("#frm-add-stock-out").css("display", "block");
  });

  $("#so-add-cancel").click(function (e) {
    closeForm();
  });

  $("#add_date").change(function (e) {
    e.preventDefault();
    $(this).removeClass("is-invalid");
  });

  $("#add_branch_select").change(function (e) {
    e.preventDefault();
    $(this).removeClass("is-invalid");
  });

  $("#frm-add-stock-out").submit(function (e) {
    e.preventDefault();
    var branch = $("#add_branch_select").val();
    var date = $("#add_date").val();
    const inputDate = new Date(date); // Replace with your input date

    isInvalid = false;

    if (branch === null) {
      $("#add_branch_select").addClass("is-invalid");
      isInvalid = true;
    }

    if (inputDate > nowInPH) {
      $("#add_date").addClass("is-invalid");
      isInvalid = true;
    }

    if (!isInvalid) {
      closeForm();
      $.ajax({
        type: "POST",
        url: "../ajax-url/add-stock-out.php",
        data: {
          branch: branch,
          date: date,
        },
        success: function (response) {
          if (response === "Stock out added") {
            $(".alert-success").css("opacity", "1");
            $(".alert-success").text(response);
            updateStockOutTable();

            setTimeout(function () {
              $(".alert-success").css("opacity", "0");
            }, 2000);
          } else {
            $(".alert-danger").css("opacity", "1");
            $(".alert-success").text(response);

            setTimeout(function () {
              $(".alert-danger").css("opacity", "0");
            }, 2000);
          }
        },
      });
    }
  });
});
