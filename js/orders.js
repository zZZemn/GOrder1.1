$(document).ready(function () {
  const loadXMLDoc = () => {
    var orderFilter = $("#orders_filter").val();
    $.ajax({
      url: "../server/orders-update.php",
      method: "GET",
      data: {
        filter: orderFilter,
      },
      success: function (data) {
        $("#orders_result").html(data);
      },
    });
  };

  loadXMLDoc();

  setInterval(function () {
    loadXMLDoc();
  }, 3000);

  $("#orders_filter").change(function () {
    loadXMLDoc();
  });
});
