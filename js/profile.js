$(document).ready(function () {
  const updateUserDisplay = () => {
    $.ajax({
      type: "GET",
      url: "../server/profile-update.php",
      success: function (response) {
        console.log(response);
        $(".profile-container").html(response);
      },
    });
  };

  const closeFrmEditProfile = () => {
    $(".frm-edit-profile").css({
      opacity: "0",
      "pointer-events": "none",
    });
  };

  const alert = (text, bg) => {
    $(".alert").css("opacity", "1").text(text).addClass(bg);
    setTimeout(function () {
      $(".alert").css("opacity", "0").text("").removeClass(bg);
    }, 2000);
  };

  $("#open-frm-edit-profile").click(function (e) {
    e.preventDefault();
    $(".frm-edit-profile").css({
      opacity: "1",
      "pointer-events": "auto",
    });
  });

  $(document).on("click", "#close-frm-edit-profile", function (e) {
    e.preventDefault();
    closeFrmEditProfile();
    updateUserDisplay();
  });

  $(document).on("submit", ".frm-edit-profile", function (e) {
    e.preventDefault();
    closeFrmEditProfile();
    var formData = $(this).serialize();

    $.ajax({
      type: "POST",
      url: "../ajax-url/edit-profile.php",
      data: formData,
      success: function (response) {
        if (response == "200") {
          alert("Success", "bg-success");
        } else {
          alert("Something went wrong", "bg-danger");
        }
        updateUserDisplay();
      },
    });
  });

  updateUserDisplay();
});
