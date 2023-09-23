$(document).ready(function () {
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }

  var isPasswordHidden = true;

  $("#viewPassword").click(function (e) {
    e.preventDefault();
    var icon = $(this).find("i");
    if (isPasswordHidden) {
      isPasswordHidden = false;
      $("#password").attr("type", "text");
      icon.removeClass("fa-eye").addClass("fa-eye-slash");
    } else {
      isPasswordHidden = true;
      $("#password").attr("type", "password");
      icon.removeClass("fa-eye-slash").addClass("fa-eye");
    }
  });
});
