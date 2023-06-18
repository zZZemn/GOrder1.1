$(document).ready(function () {
  var date = $('#sales_date').val();

  // Make the AJAX request using the initial value of the category select
  $.ajax({
    url: '../ajax-url/get-daily-sales.php',
    data: { date: date },
    type: 'POST',
    success: function (data) {
      $('#table-response-container').html(data);
    }
  });

});

$('#sales_date').on('change', function () {
  var date = $('#sales_date').val();
  $.ajax({
    url: '../ajax-url/get-daily-sales.php',
    data: { date: date },
    type: 'POST',
    success: function (data) {
      console.log(data);
      $('#table-response-container').html(data);
    }
  });
});