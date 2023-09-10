$(document).ready(function () {
  const updateReturnRequest = () => {
    var search = $("#txt-return-search").val();
    $.ajax({
      type: "GET",
      url: "../server/update-return-request.php",
      data: {
        search: search,
      },
      success: function (response) {
        $("#ret-return-results").html(response);
      },
    });
  };

  updateReturnRequest();
});
