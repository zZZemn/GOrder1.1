$(document).ready(function () {
  $(".loading-overlay").css("display", "none");

  const checkPassword = (password) => {
    var containsLetter = /[a-zA-Z]/.test(password);
    var containsSpecialSymbol = /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/.test(
      password
    );
    var containsDigit = /\d/.test(password);

    if (!containsDigit) {
      return false;
    }

    if (!containsLetter) {
      return false;
    }

    if (!containsSpecialSymbol) {
      return false;
    }

    if (!password.length >= 8) {
      return false;
    }

    return true;
  };

  const newPassword = (user_id, acc_type, password) => {
    $.ajax({
      type: "POST",
      url: "process/cp.php",
      data: {
        uid: user_id,
        acc_type: acc_type,
        password: password,
        data: "asdjagsdhashdgahsgdajgdsjghasydtqtwye",
      },
      success: function (response) {
        if (response === "Password Changed") {
          $(".pw-changed").css("opacity", 1).css("pointer-events", "auto");
          setTimeout(function () {
            $(".pw-changed").css("opacity", 0).css("pointer-events", "none");
            window.location.href = "index.php";
          }, 2000);
        } else {
          $(".pw-not-changed").css("opacity", 1).css("pointer-events", "auto");
          setTimeout(function () {
            $(".pw-not-changed").css("opacity", 0).css("pointer-events", "none");
            window.location.href = "index.php";
          }, 2000);
        }
      },
    });
  };

  $("#save-new-pw").click(function (e) {
    e.preventDefault();
    var user_id = $("#u_id").attr("data-uid");
    var acc_type = $("#acc_type").attr("data-acc-type");
    var pass = $("#txt-repeat-passwrod").val();
    newPassword(user_id, acc_type, pass);
  });

  const sendVerificationCode = (user_id, email, acc_type) => {
    $(".loading-overlay").css("display", "block");
    $.ajax({
      type: "POST",
      url: "process/cp-send-vc.php",
      data: {
        user_id: user_id,
        email: email,
        acc_type: acc_type,
        id: "ajskhdjkashznbxcnzbxchasd",
      },
      success: function (response) {
        var data = JSON.parse(response);
        console.log(response);
        $("#submit-email").prop("disabled", true);
        $("#frm-input-email").css("display", "none");

        var verification_code = data[0];
        var user_id = data[1];
        var acc_type = data[3];
        var name = data[4] + " " + data[5];
        $("#txt-cp-verification-code-email-add").text(data[2]);
        $("#frm-input-verification-code").css("display", "flex");
        setTimeout(() => {
          $(".loading-overlay").css("display", "none");
        }, 2000);

        $("#txt-cp-verification-code").on("input", () => {
          var vc_input = parseInt($("#txt-cp-verification-code").val());
          if (vc_input === verification_code) {
            $(".loading-overlay").css("display", "block");
            $("#frm-input-verification-code").css("display", "none");
            $("#input-new-password-title").text(
              "Hello " + name + "! Please input your new password."
            );
            $("#u_id").attr("data-uid", user_id);
            $("#acc_type").attr("data-acc-type", acc_type);
            $("#frm-change-password-input").css("display", "flex");
            setTimeout(() => {
              $(".loading-overlay").css("display", "none");
            }, 2000);
            $("#txt-repeat-passwrod, #txt-np-input").on("input", () => {
              var pass1 = $("#txt-np-input").val();
              var pass2 = $("#txt-repeat-passwrod").val();
              if (pass1 !== "" && pass2 !== "") {
                if (pass1 === pass2) {
                  if (checkPassword(pass1) && checkPassword(pass2)) {
                    $("#save-new-pw").prop("disabled", false);
                  } else {
                    $(".password-format")
                      .css("opacity", 1)
                      .css("pointer-events", "auto");
                    setTimeout(function () {
                      $(".password-format")
                        .css("opacity", 0)
                        .css("pointer-events", "none");
                    }, 2000);
                    $("#save-new-pw").prop("disabled", true);
                  }
                } else {
                  $(".pw-notmatch")
                    .css("opacity", 1)
                    .css("pointer-events", "auto");
                  setTimeout(function () {
                    $(".pw-notmatch")
                      .css("opacity", 0)
                      .css("pointer-events", "none");
                  }, 2000);
                  $("#save-new-pw").prop("disabled", true);
                }
              }
            });
          }
        });
      },
    });
  };

  $("#email").on("input", () => {
    var email = $("#email").val();
    var acc_type = $("#account-type").val();
    $.ajax({
      type: "POST",
      url: "process/get-inputed-email.php",
      data: {
        email: email,
        acc_type: acc_type,
      },
      success: function (response) {
        try {
          var details = JSON.parse(response);
          console.log(details);
          $("#submit-email").prop("disabled", false);
          $("#email-checking").text(details[1]);
          $("#user-id").val(details[0]);
          $("#acc-type").val(details[2]);
          $("#email-checking").removeClass("text-danger");
          $("#email-checking").addClass("text-success");
        } catch (error) {
          $("#submit-email").prop("disabled", true);
          $("#email-checking").text("Email not found.");
          $("#user-id").val("");
          $("#acc-type").val("");
          $("#email-checking").removeClass("text-success");
          $("#email-checking").addClass("text-danger");
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX error:", error);
        $("#submit-email").prop("disabled", true);
      },
    });
  });

  $("#submit-email").click((e) => {
    e.preventDefault();
    var user_id = $("#user-id").val();
    var email = $("#email").val();
    var acc_type = $("#acc-type").val();
    if (user_id !== "" && email !== "") {
      sendVerificationCode(user_id, email, acc_type);
    }
  });
});
