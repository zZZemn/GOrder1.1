$(document).ready(function () {
  const notif = (notifType, text) => {
    $(notifType).css("opacity", 1).text(text);
    setTimeout(function () {
      $(notifType).css("opacity", 0).text("");
    }, 2000);
  };

  $("#region").on("change", function () {
    var regionID = $(this).val();

    if (regionID != "") {
      $.ajax({
        url: "ajax-url/get-provinces.php",
        type: "POST",
        data: { regionID: regionID },
        success: function (data) {
          var provinces = JSON.parse(data);

          $("#province").empty();
          $("#municipality").empty();
          $("#barangay").empty();

          $("#province").append(
            $("<option>", {
              value: "",
              text: "",
            })
          );

          for (var i = 0; i < provinces.length; i++) {
            $("#province").append(
              $("<option>", {
                value: provinces[i].provinceID,
                text: provinces[i].province,
              })
            );
          }
          $('#province option[value=""]').prop("disabled", true);
        },
      });
    } else {
      $("#province").empty();
      $("#municipality").empty();
      $("#barangay").empty();
    }
  });

  $("#province").on("change", function () {
    var provinceID = $(this).val();

    if (provinceID != "") {
      $.ajax({
        url: "ajax-url/get-municipalities.php",
        type: "POST",
        data: { provinceID: provinceID },
        success: function (data) {
          var municipalities = JSON.parse(data);

          $("#municipality").empty();

          $("#municipality").append(
            $("<option>", {
              value: "",
              text: "",
            })
          );

          for (var i = 0; i < municipalities.length; i++) {
            $("#municipality").append(
              $("<option>", {
                value: municipalities[i].municipalityID,
                text: municipalities[i].municipality,
              })
            );
          }
          $('#municipality option[value=""]').prop("disabled", true);
        },
      });
    } else {
      $("#municipality").empty();
      $("#barangay").empty();
    }
  });

  $("#municipality").on("change", function () {
    var municipalityID = $(this).val();

    if (municipalityID != "") {
      $.ajax({
        url: "ajax-url/get-barangay.php",
        type: "POST",
        data: { municipalityID: municipalityID },
        success: function (data) {
          var barangays = JSON.parse(data);

          $("#barangay").empty();

          $("#barangay").append(
            $("<option>", {
              value: "",
              text: "",
            })
          );

          for (var i = 0; i < barangays.length; i++) {
            $("#barangay").append(
              $("<option>", {
                value: barangays[i].barangayID,
                text: barangays[i].barangay,
              })
            );
          }
          $('#barangay option[value=""]').prop("disabled", true);
        },
      });
    } else {
      $("#barangay").empty();
    }
  });

  $("#btn-create-account").on("click", function (event) {
    event.preventDefault();
    var form = $(this);

    var first_name = $("#first_name").val();
    var last_name = $("#last_name").val();
    var mi = $("#mi").val();
    var suffix = $("#suffix").val();

    var birthday = $("#birthday").val();
    var sex = $("#sex").val();
    var contact = $("#contact").val();
    var email = $("#email").val();

    var unit = $("#unit").val();
    var region = $("#region").val();
    var province = $("#province").val();
    var municipality = $("#municipality").val();
    var barangay = $("#barangay").val();

    var username = $("#username").val();
    var password = $("#password").val();

    var birthDate = new Date(birthday);
    var timeDiff = Date.now() - birthDate.getTime();
    var age = Math.floor(timeDiff / (1000 * 60 * 60 * 24 * 365.25));
    if (
      region !== null &&
      province !== null &&
      municipality !== null &&
      barangay !== null
    ) {
      if (age >= 16) {
        if (contact.length === 10) {
          if (username.length > 6) {
            var containsSpecialChars =
              /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(username);
            var containsOnlyNumbers = /^\d+$/.test(username);

            if (!containsSpecialChars && !containsOnlyNumbers) {
              var containsDigit = /\d/.test(password);
              var containsLetter = /[a-zA-Z]/.test(password);
              var containsSpecialSymbol =
                /[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]/.test(password);
              if (
                containsDigit &&
                containsLetter &&
                containsSpecialSymbol &&
                password.length >= 8
              ) {
                $.ajax({
                  url: "ajax-url/check-existence.php",
                  data: {
                    username: username,
                    email: email,
                  },
                  type: "POST",
                  success: function (response) {
                    if (response === "1") {
                      notif(
                        ".alert-danger",
                        "The email you entered already exists. Please use a different email address."
                      );
                    } else if (response === "2") {
                      notif(
                        ".alert-danger",
                        "The username you entered already exists. Please choose a different username address."
                      );
                    } else if (response === "0") {
                      $(".loading-overlay").css("display", "block");
                      $.ajax({
                        type: "POST",
                        url: "process/signup-process.php",
                        data: {
                          email: email,
                          first_name: first_name,
                          last_name: last_name,
                          getVerificationCode: 1265376512,
                        },
                        success: function (response) {
                          if (response !== "400") {
                            try {
                              var data = JSON.parse(response);
                              var verificationCode = data[0];
                              var verificationeEmail = data[1];

                              $("#verificationEmail").text(verificationeEmail);
                              $("#sign-up-form").css("display", "none");
                              $(".verification-container").css(
                                "display",
                                "block"
                              );

                              setTimeout(() => {
                                $(".loading-overlay").css("display", "none");
                              }, 2000);

                              $("#txtVerificationCode").on(
                                "input",
                                function (e) {
                                  var inputData = parseInt($(this).val());
                                  if (verificationCode !== "") {
                                    if (inputData === verificationCode) {
                                      if (
                                        $(this).val().length ===
                                        verificationCode.toString().length
                                      ) {
                                        $("#sign-up-form").submit();
                                        $("#txtVerificationCode").prop(
                                          "disabled",
                                          true
                                        );
                                      }
                                    } else {
                                      $(this).css("outline", "red");
                                    }
                                  } else {
                                    notif(
                                      ".alert-danger",
                                      "Something when wrong."
                                    );
                                  }
                                }
                              );
                            } catch {
                              notif(".alert-danger", "Something when wrong.");
                              setTimeout(() => {
                                $(".loading-overlay").css("display", "none");
                              }, 2000);
                            }
                          } else {
                            notif(".alert-danger", "Something when wrong.");
                            setTimeout(() => {
                              $(".loading-overlay").css("display", "none");
                            }, 2000);
                          }
                        },
                      });
                    } else {
                    }
                  },
                });
              } else {
                notif(
                  ".alert-danger",
                  "Password needs to have at least 8 characters, including letters, digits, and a special symbol."
                );
              }
            } else {
              notif(
                ".alert-danger",
                "Invalid username. Please ensure that the username is 7 characters or more, does not contain special characters, and is not comprised only of numbers."
              );
            }
          } else {
            notif(
              ".alert-danger",
              "Invalid username. Please ensure that the username is 7 characters or more, does not contain special characters, and is not comprised only of numbers."
            );
          }
        } else {
          notif(
            ".alert-danger",
            "Please ensure that the contact number entered contains a minimum of 10 digits."
          );
        }
      } else {
        notif(
          ".alert-danger",
          "You're not allowed to sign up for Gorder. Minimum age requirement is 16 years old."
        );
      }
    } else {
      notif(
        ".alert-danger",
        "Please set up your address to proceed with the registration process."
      );
    }
  });

  setTimeout(function () {
    $(".dash-board-container").animate({ opacity: 1 }, 500);
    $(".loading-overlay").hide();
  }, 500);
});
