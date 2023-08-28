$(document).ready(function () {
  function empUpdate() {
    var search = $("#search_emp").val();
    var filter = $("#emp_filter").val();
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("emp-container").innerHTML = this.responseText;
      }
    };
    var url = "../server/emp-update.php?filter=" + filter + "&search=" + search;
    console.log(url);
    xhttp.open("GET", url, true);
    xhttp.send();
  }

  window.onload = empUpdate();

  $("#emp_filter").change(function () {
    setTimeout(empUpdate, 500);
  });

  $("#search_emp").on("input", function () {
    setTimeout(empUpdate, 500);
  });

  $("#new_emloyee").click(function (event) {
    event.preventDefault();
    $(".add-emp-form").css("opacity", 1);
    $(".add-emp-form").css("pointer-events", "auto");
  });

  $("#close-add-emp-form").click(function (event) {
    event.preventDefault();
    $(".add-emp-form").css("opacity", 0);
    $(".add-emp-form").css("pointer-events", "none");
    $(
      '.add-emp-form input[type="text"], .add-emp-form input[type="password"], .add-emp-form input[type="email"], .add-emp-form input[type="number"], .add-emp-form input[type="date"], .add-emp-form select'
    ).val("");
  });

  $("#btn-save-employee").click(function (event) {
    event.preventDefault();

    var f_name = $("#f_name").val();
    var l_name = $("#l_name").val();
    var mi = $("#mi").val();
    var suffix = $("#suffix").val();
    var sex = $("#sex").val();
    var birthday = $("#birthday").val();
    var emp_type = $("#emp_type").val();
    var email = $("#email").val();
    var contact_no = $("#contact_no").val();
    var address = $("#address").val();
    var username = $("#username").val();
    var password = $("#password").val();

    if (
      f_name !== "" &&
      l_name !== "" &&
      sex !== "" &&
      birthday !== "" &&
      emp_type !== "" &&
      email !== "" &&
      contact_no !== "" &&
      username !== "" &&
      password !== ""
    ) {
      if (emailIsValid(email)) {
        if (username.length >= 8) {
          $.ajax({
            url: "../ajax-url/insert-emp.php",
            method: "POST",
            data: {
              f_name: f_name,
              l_name: l_name,
              mi: mi,
              suffix: suffix,
              sex: sex,
              birthday: birthday,
              emp_type: emp_type,
              email: email,
              contact_no: contact_no,
              address: address,
              username: username,
              password: password,
            },
            success: function (response) {
              if (response === "inserted") {
                $(".add-emp-form").css("opacity", 0);
                $(".add-emp-form").css("pointer-events", "none");
                $(
                  '.add-emp-form input[type="text"], .add-emp-form input[type="password"], .add-emp-form input[type="email"], .add-emp-form input[type="number"], .add-emp-form input[type="date"], .add-emp-form select'
                ).val("");
                empUpdate();
                $(".acc_created").css("opacity", 1);
                setTimeout(function () {
                  $(".acc_created").css("opacity", 0);
                }, 2000);
              } else {
                $(".acc_created_unsuccessful").css("opacity", 1);
                setTimeout(function () {
                  $(".acc_created_unsuccessful").css("opacity", 0);
                }, 1000);
                console.log(response);
              }
            },
            error: function (error) {
              // Handle error response
              console.log(error);
            },
          });
        } else {
          $(".invalid_username").css("opacity", 1);
          setTimeout(function () {
            $(".invalid_username").css("opacity", 0);
          }, 1000);
        }
      } else {
        $(".invalid_email").css("opacity", 1);
        setTimeout(function () {
          $(".invalid_email").css("opacity", 0);
        }, 1000);
      }
    } else {
      $(".input_empty").css("opacity", 1);
      setTimeout(function () {
        $(".input_empty").css("opacity", 0);
      }, 1000);
    }
  });

  function emailIsValid(email) {
    // Regular expression for email validation
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Test the email against the regex
    return emailRegex.test(email);
  }

  // edit employee
  const closeEditForm = () => {
    $(".edit-emp-form").css("opacity", 0);
    $(".edit-emp-form").css("pointer-events", "none");
    $(
      '.edit-emp-form input[type="text"], .edit-emp-form input[type="password"], .edit-emp-form input[type="email"], .edit-emp-form input[type="number"], .edit-emp-form input[type="date"], .edit-emp-form select'
    ).val("");

    $("#edit_btn-save-employee").attr("data-id", "");
  };

  $(document).on("click", "#btn-edit", function (e) {
    e.preventDefault();

    $("#edit_f_name").val($(e.currentTarget).data("fname"));
    $("#edit_l_name").val($(e.currentTarget).data("lname"));
    $("#edit_mi").val($(e.currentTarget).data("mi"));
    $("#edit_suffix").val($(e.currentTarget).data("suffix"));
    $("#edit_sex").val($(e.currentTarget).data("sex"));
    $("#edit_birthday").val($(e.currentTarget).data("birthday"));
    $("#edit_emp_type").val($(e.currentTarget).data("role"));
    $("#edit_email").val($(e.currentTarget).data("email"));
    $("#edit_contact_no").val($(e.currentTarget).data("contactno"));
    $("#edit_address").val($(e.currentTarget).data("address"));
    $("#edit_username").val($(e.currentTarget).data("username"));
    $("#edit_btn-save-employee").attr("data-id", $(e.currentTarget).data("id"));

    $(".edit-emp-form").css("opacity", 1);
    $(".edit-emp-form").css("pointer-events", "auto");
  });

  $("#edit_btn-save-employee").click(function (e) {
    e.preventDefault();
    $(".edit-emp-form").css("opacity", 0);
    $(".edit-emp-form").css("pointer-events", "none");

    var id = $(this).attr("data-id");
    var fname = $("#edit_f_name").val();
    var lname = $("#edit_l_name").val();
    var mi = $("#edit_mi").val();
    var suffix = $("#edit_suffix").val();
    var sex = $("#edit_sex").val();
    var bday = $("#edit_birthday").val();
    var emp_type = $("#edit_emp_type").val();
    var email = $("#edit_email").val();
    var contact_no = $("#edit_contact_no").val();
    var address = $("#edit_address").val();
    var username = $("#edit_username").val();

    $.ajax({
      type: "POST",
      url: "../ajax-url/edit-employee.php",
      data: {
        id: id,
        fname: fname,
        lname: lname,
        mi: mi,
        suffix: suffix,
        sex: sex,
        bday: bday,
        emp_type: emp_type,
        email: email,
        contact_no: contact_no,
        address: address,
        username: username,
      },
      success: function (response) {
        closeEditForm();
        if (response === "success") {
          var text = "Editing Success";
          $(".alert-success").css("opacity", 1).text(text);
          setTimeout(function () {
            $(".alert-success").css("opacity", 0).text("");
          }, 1000);
        } else {
          var text = "Invalid Editing";
          $(".alert-alert-danger").css("opacity", 1).text(text);
          setTimeout(function () {
            $(".alert-alert-danger").css("opacity", 0).text("");
          }, 1000);
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX Request Error:", status, error);
        // You can handle the error here, for example, display an error message to the user
      },
    });
  });

  $("#close-edit-emp-form").click(function (event) {
    event.preventDefault();
    closeEditForm();
  });
});
