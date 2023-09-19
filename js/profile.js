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

  const closeFrmChangePw = () => {
    $(".frm-change-pw").css({ opacity: "0", "pointer-events": "none" });
    $(".frm-change-pw")[0].reset();
  };

  $("#open-frm-edit-profile").click(function (e) {
    e.preventDefault();
    $(".frm-edit-profile").css({
      opacity: "1",
      "pointer-events": "auto",
    });
    closeFrmChangePw();
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

  // change pw
  $("#open-frm-change-pw").click(function (e) {
    e.preventDefault();
    closeFrmEditProfile();
    $(".frm-change-pw").css({ opacity: "1", "pointer-events": "auto" });
    console.log("asd");
  });

  $("#close-frm-change-pw").click(function (e) {
    e.preventDefault();
    closeFrmChangePw();
  });

  $("#frm-change-pw").submit(function (e) {
    e.preventDefault();
    var oldPw = $("#old-pw").val();
    var newPw = $("#new-pw").val();

    var containsDigit = /\d/.test(newPw);
    var containsLetter = /[a-zA-Z]/.test(newPw);
    var containsSpecialSymbol = /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/.test(
      newPw
    );

    if (
      containsDigit &&
      containsLetter &&
      containsSpecialSymbol &&
      newPw.length >= 8
    ) {
      $.ajax({
        type: "POST",
        url: "../ajax-url/change-pw.php",
        data: {
          newPw: newPw,
          oldPw: oldPw,
        },
        success: function (response) {
          console.log(response);
          if (response === "200") {
            alert("Password Change", "bg-success");
            closeFrmChangePw();
          } else if (response === "405") {
            alert("Old Password Incorrect", "bg-danger");
          } else {
            alert("Something went wrong", "bg-danger");
            closeFrmChangePw();
          }
        },
      });
    } else {
      alert(
        "Password needs to have at least 8 characters, including letters, digits, and a special symbol.",
        "bg-danger"
      );
    }
  });

  updateUserDisplay();
});
