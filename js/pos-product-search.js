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

    // listen for the submit event on the .product-select form
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
                        '<td>' + sellingPrice + '</td>' +
                        '<td><input type="number" name="quantity" class="form-control" value="1" min="1"></td>' +
                        '<td>' + sellingPrice + '</td>' +
                        '<td><button type="button" class="btn btn-danger btn-sm remove-row"><i class="fas fa-trash"></i></button></td>' +
                     '</tr>';
        $('.pos-orders-container tbody').append(newRow);
    });
});

$('.pos-orders-container').on('keyup', 'input[name="quantity"]', function() {
    // Get the quantity value and selling price from the current row
    var quantity = $(this).val();
    var sellingPrice = $(this).closest('tr').find('td:nth-child(2)').text();
  
    // Calculate the new amount based on the quantity and selling price
    var amount = quantity * sellingPrice;
  
    // Update the amount column with the new value
    $(this).closest('tr').find('td:nth-child(4)').text(amount);
});
