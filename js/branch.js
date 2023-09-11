$(document).ready(function () {
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
          $(".alert-success")
            .css("opacity", "1")
            .text("Branch new status: " + action);
          setTimeout(function () {
            $(".alert-success").css("opacity", "0").text("");
          }, 1000);
        } else {
          $(".alert-danger").css("opacity", "1").text("Something went wrong.");
          setTimeout(function () {
            $(".alert-danger").css("opacity", "0").text("");
          }, 1000);
        }

        updateBranches();
      },
    });
  });

  updateBranches();
});
