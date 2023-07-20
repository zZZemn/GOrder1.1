$(document).ready(function () {
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
          $('#email-checking').text(details['1']);
          $('#email-checking').removeClass('text-danger');
          $('#email-checking').addClass('text-success');
        } catch (error) {
          $("#submit-email").prop("disabled", true);
          $('#email-checking').text('Email not found.');
          $('#email-checking').removeClass('text-success');
          $('#email-checking').addClass('text-danger');
        }
      },
      error: function (xhr, status, error) {
        console.error("AJAX error:", error);
        $("#submit-email").prop("disabled", true);
      },
    });
  });
});
