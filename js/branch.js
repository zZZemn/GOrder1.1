$(document).ready(function () {
  const notif = (alertType, text) => {
    $(alertType).css("opacity", "1").text(text);
    setTimeout(function () {
      $(alertType).css("opacity", "0").text("");
    }, 1000);
  };

  const closeFrmAddBranch = () => {
    $("#new_discount_name").val("");
    $(".add-branch").css({
      opacity: "0",
      "pointer-events": "none",
    });
  };

  const closeFrnEditBranch = () => {
    $("#edit_branch_name").val("");
    $("#edit_branch").data("id", "");

    $(".edit-branch").css({
      opacity: "0",
      "pointer-events": "none",
    });
  };

  const updateBranches = () => {
    $.ajax({
      type: "GET",
      url: "../server/update-branches.php",
      data: {
        getBranches: 9182039813,
      },
      success: function (response) {
        if (response !== "404") {
          $("#branch_container").html(response);
        }
      },
    });
  };

  //   deactivate
  $(document).on("click", ".deactivate-branch", function (e) {
    closeFrmAddBranch();
    e.preventDefault();
    var action = $(this).data("action");
    var id = $(this).data("id");

    $.ajax({
      type: "POST",
      url: "../ajax-url/branch-endpoint.php",
      data: {
        type: "deact",
        action: action,
        id: id,
      },
      success: function (response) {
        if (response == "200") {
          notif(".alert-success", "Branch new status: " + action);
        } else {
          notif(".alert-danger", "Something went wrong.");
        }
        updateBranches();
      },
    });
  });

  // add branch
  $("#btn-open-add-branch").click(function (e) {
    e.preventDefault();
    $(".add-branch").css({
      opacity: "1",
      "pointer-events": "auto",
    });
  });

  $(".close-add-branch").click(function (e) {
    e.preventDefault();
    closeFrmAddBranch();
  });

  $(".add-branch").submit(function (e) {
    e.preventDefault();
    var name = $("#new_branch_name").val();
    var type = "addBranch";
    closeFrmAddBranch();
    $.ajax({
      type: "POST",
      url: "../ajax-url/branch-endpoint.php",
      data: {
        name: name,
        type: type,
      },
      success: function (response) {
        if (response == "200") {
          notif(".alert-success", "Branch Added");
        } else {
          notif(".alert-danger", "Branch Adding Failed");
        }
        updateBranches();
      },
    });
  });

  // edit branch
  $(document).on("click", ".open-edit-branch", function (e) {
    e.preventDefault();
    var id = $(this).data("id");
    var name = $(this).data("name");

    $("#edit_branch_name").val(name);
    $("#edit_branch").data("id", id);

    $(".edit-branch").css({
      opacity: "1",
      "pointer-events": "auto",
    });
  });

  $(".close-edit-branch").click(function (e) {
    e.preventDefault();
    closeFrnEditBranch();
  });

  $(".edit-branch").submit(function (e) {
    e.preventDefault();
    var newBranchName = $("#edit_branch_name").val();
    var id = $("#edit_branch").data("id");

    closeFrnEditBranch();
    if (id !== "" && newBranchName !== "") {
      $.ajax({
        type: "POST",
        url: "../ajax-url/branch-endpoint.php",
        data: {
          type: "edit",
          name: newBranchName,
          id: id,
        },
        success: function (response) {
          if (response == "200") {
            notif(".alert-success", "Branch Edited");
          } else {
            notif(".alert-danger", "Branch Editing Failed");
          }
          updateBranches();
        },
      });
    } else {
      notif(".alert-danger", "Please input proper branch name");
    }
  });

  updateBranches();
});
