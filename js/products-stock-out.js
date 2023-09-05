const nowInPH = new Date();
const philippinesTimeOffset = 8 * 60; // Philippines is UTC+8
nowInPH.setMinutes(nowInPH.getMinutes() + philippinesTimeOffset);

$(document).ready(function () {
  const closeForm = () => {
    $("#frm-add-stock-out").css("display", "none");
  };

  const closeEditForm = () => {
    $("#frm-edit-stock-out").css("display", "none");
    $("#edit-stock-out-id").text("");
    $("#edit_branch_select").val("");
    $("#edit_date").val("");
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

  //   add
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
          add: true,
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

  //   edit
  $("#so-edit-cancel").click(function () {
    closeEditForm();
  });

  $(document).on("click", "#btn-edit-stock-out", function (e) {
    e.preventDefault();
    $("#edit-stock-out-id").text($(this).data("id"));
    $("#edit_branch_select").val($(this).data("branch"));
    $("#edit_date").val($(this).data("date"));

    $("#frm-edit-stock-out").css("display", "block");
  });

  $("#frm-edit-stock-out").submit(function (e) {
    e.preventDefault();
    var branch = $("#edit_branch_select").val();
    var date = $("#edit_date").val();
    var id = $("#edit-stock-out-id").text();
    const inputDate = new Date(date); // Replace with your input date

    isInvalid = false;

    if (branch === null) {
      $("#edit_branch_select").addClass("is-invalid");
      isInvalid = true;
    }

    if (inputDate > nowInPH) {
      $("#edit_date").addClass("is-invalid");
      isInvalid = true;
    }

    if (!isInvalid) {
      closeEditForm();
      $.ajax({
        type: "POST",
        url: "../ajax-url/add-stock-out.php",
        data: {
          edit: true,
          id: id,
          branch: branch,
          date: date,
        },
        success: function (response) {
          if (response === "Editing Success") {
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

  //   delete
  const deleteStockOut = (id) => {
    $("#myModal").modal("hide");
    $("#myModal").trigger("hidden.bs.modal");
    
    $.ajax({
      type: "POST",
      url: "../ajax-url/add-stock-out.php",
      data: {
        delete: true,
        id: id,
      },
      success: function (response) {
        if (response === "Stock Out Report Deleted") {
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
  };

  $(document).on("click", "#btn-delete-stock-out", function (e) {
    e.preventDefault();
    var id = $(this).data("id");
    var modalTitle = "Delete " + id;
    $(".modal-title").text(modalTitle);
    $("#delete-this-stock-out").attr("data-id", id);
    $("#myModal").modal("show");
  });

  $(document).on("click", "#delete-this-stock-out", function (e) {
    e.preventDefault();
    var id = $(this).attr("data-id");

    deleteStockOut(id);
  });

  $("#myModal").on("hidden.bs.modal", () => {
    $("#delete-this-stock-out").attr("data-id", "");
  });

  $("#myModal").on("click", "#close-delete-this-stock-out", () => {
    $("#myModal").modal("hide");
    $("#myModal").trigger("hidden.bs.modal");
  });
});
