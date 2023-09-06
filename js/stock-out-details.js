$(document).ready(function () {
  var id = $("#stock-out-id").val();
  $.ajax({
    type: "GET",
    url: "../server/get-stock-out-details.php",
    data: { id: id },
    success: function (response) {
      $("#sod-container").html(response);
    },
  });

  $(document).on("click", ".btn-delete", function (e) {
    console.log($(this).attr("data-invid"));
  });
});
