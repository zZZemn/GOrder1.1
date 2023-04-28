$(document).ready(function() {
  var cat_id = $('#category-select').val();
  
  // Make the AJAX request using the initial value of the category select
  $.ajax({
    url: '../process/get-sub-categories.php',
    data: { cat_id: cat_id },
    type: 'POST',
    success: function(data) {
      $('#sub-category-select').html(data);
    }
  });
  
  // Update the sub-categories whenever the category select changes
  $('#category-select').on('change', function() {
    var cat_id = $(this).val();
    $.ajax({
      url: '../process/get-sub-categories.php',
      data: { cat_id: cat_id },
      type: 'POST',
      success: function(data) {
        $('#sub-category-select').html(data);
      }
    });
  });
});
