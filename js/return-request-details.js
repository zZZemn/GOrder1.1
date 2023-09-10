$(document).ready(function () {
  $("#select-rider").change(function (e) {
    e.preventDefault();
    var rider_id = $("#select-rider").val();
    var return_id = $("#req-id").val();
    $.ajax({
      type: "POST",
      url: "../ajax-url/rrd-endpoint.php",
      data: {
        rider_id: rider_id,
        return_id: return_id,
      },
      success: function (response) {
        console.log(response);
      },
    });
  });
});
