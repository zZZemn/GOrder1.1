$(document).ready(function () {
    $('#search_products').on('input', function () {
        var query = $(this).val();
        if (query.length >= 2) {
            $.ajax({
                url: '../ajax-url/pos-search.php',
                type: 'POST',
                data: {
                    query: query
                },
                success: function (data) {
                    $('#search_results').html(data);
                    console.log(data);
                }
            });
        } else {
            $('#search_results').empty();
        }
    });

    $('.pos-select-item-container').on('submit', '.product-select', function (e) {
        e.preventDefault(); // prevent the form from submitting
        var unitMeasurement = '';
        // extract the product details from the hidden input fields
        var productId = $(this).find('input[name="product_id"]').val();
        var productName = $(this).find('input[name="product_name"]').val();
        unitMeasurement = $(this).find('input[name="unit_meas"]').val();
        var sellingPrice = $(this).find('input[name="selling_price"]').val();
    
        // create a new table row and append it to the table's tbody
        var newRow = '<tr>' +
                '<td>' + productName + ' ' + unitMeasurement + '</td>' +
                '<td><input type="number" class="order-details-inputs form-control" name="selling_price" value='+ sellingPrice +' readonly></td>' +
                '<td><input type="number" name="quantity" class="order-details-inputs form-control" value="1" min="1"></td>' +
                '<td><input type="number" name="amount" class="order-details-inputs amount form-control" value='+ sellingPrice +' readonly></td>' +
                '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>' +
                '<input type="hidden" name="product_id" value='+ productId + '>';+
            '</tr>';
    
        $('.pos-orders-container tbody').append(newRow);
    
        // calculate and update subtotal
        var subtotal = 0;
        $('.pos-orders-container tbody tr').each(function () {
            var amount = $(this).find('.amount').val();
            subtotal += parseFloat(amount);
        });
        $('#subtotal').val(subtotal);
    });    
});

$('.pos-orders-container').on('keyup', 'input[name="quantity"]', function() {
    // Get the quantity value and selling price from the current row
    var quantity = $(this).val();
    var sellingPrice = $(this).closest('tr').find('input[name="selling_price"]').val();
    
    // Calculate the new amount based on the quantity and selling price
    var amount = quantity * sellingPrice;
    
    // Update the amount column with the new value
    $(this).closest('tr').find('.amount').val(amount);
    
    // Calculate subtotal
    var subtotal = 0;
    $('.pos-orders-container tbody tr').each(function() {
      var amount = $(this).find('.amount').val();
      subtotal += parseFloat(amount);
    });
    
    // Update the subtotal input value
    $('input[name="subtotal"]').val(subtotal);
  });
  
  $('.pos-orders-container').on('click', '.remove-row', function() {
    $(this).closest('tr').remove();
    
    // Calculate subtotal
    var subtotal = 0;
    $('.pos-orders-container tbody tr').each(function() {
      var amount = $(this).find('.amount').val();
      subtotal += parseFloat(amount);
    });
    
    // Update the subtotal input value
    $('input[name="subtotal"]').val(subtotal);
  });
  