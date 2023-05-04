var vatRate = document.getElementById('vatRate').value;
var discountRate = document.getElementById('discountRate').value;

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
        var quantity_left = $(this).find('input[name="quantity_left"]').val();
        var isVatable = $(this).find('input[name="isVatable"]').val();
        var quantity = 1;
        var existingOrderItem = $('.pos-orders-container tbody tr[data-product-id="' + productId + '"]');

        if (quantity_left > 0) {
            if (existingOrderItem.length > 0) {
                // the product has already been added to the order
                quantity = parseInt(existingOrderItem.find('input[name="quantity"]').val()) + 1;
                existingOrderItem.find('input[name="quantity"]').val(quantity);
                existingOrderItem.find('.amount').val(sellingPrice * quantity);
            } else {
                // create a new table row and append it to the table's tbody
                var newRow = "<tr data-product-id='" + productId + "'>" +
                    "<td>" + productName + " " + unitMeasurement + "</td>" +
                    "<input type='hidden' name='isVatable' id='isVatable' value='" + isVatable + "'>" +
                    "<td><input type='number' class='order-details-inputs form-control' name='selling_price' value='" + sellingPrice + "' readonly></td>" +
                    "<td><input type='number' name='quantity' class='order-details-inputs form-control' value='" + quantity + "' min='1' max='" + quantity_left + "' oninput=\"if(parseInt(this.value) > parseInt(this.max)) this.value = this.max;\"></td>" +
                    "<td><input type='number' name='amount' class='order-details-inputs amount form-control' value='" + sellingPrice + "' readonly></td>" +
                    "<td><button type='button' class='btn btn-danger btn-sm remove-row'><i class='fas fa-trash'></i></button></td>" +
                    "<input type='hidden' name='product_id' value='" + productId + "'>" +
                    "</tr>";

                $('.pos-orders-container tbody').append(newRow);
            }

            // calculate and update subtotal
            var subtotal = 0;
            var vatableSubtotal = 0;
            $('.pos-orders-container tbody tr').each(function () {
                var amount = $(this).find('.amount').val();
                subtotal += parseFloat(amount);
                var isVatableItem = $(this).find('input[name="isVatable"]').val();
                if (isVatableItem == 1) {
                    vatableSubtotal += parseFloat(amount);
                }
            });

            // Update the subtotal input value
            $('input[name="subtotal"]').val(subtotal.toFixed(2));

            // Update the VAT value if applicable
            if (isVatable == 1) {
                var vat = vatableSubtotal * vatRate; // calculate the VAT value
                $('#vat').val(vat.toFixed(2)); // set the VAT input value to 2 decimal places
            }

        //discount
        var isDiscounted = $('#discount-check').is(':checked');
        var subtotal_val = parseFloat($('#subtotal').val());
        var vat_val = parseFloat($('#vat').val());

        var new_subtotal = subtotal_val + vat_val;
      
        // Calculate the discount if applicable
        if (isDiscounted) {
          var discountAmount = new_subtotal * discountRate;
          $('#discount').val(discountAmount.toFixed(2));
        } else {
          $('#discount').val('0.00');
        }

            //set total
        var subtotal_val = parseFloat($('#subtotal').val());
        var vat_val = parseFloat($('#vat').val());
        var discount_val = parseFloat($('#discount').val());
        var total = (subtotal_val + vat_val) - discount_val;

        $('#total').val(total.toFixed(2)); //set total

        } else {
            alert('Please enter a quantity greater than 0.'); // show an error message
        }
    });


    $('.pos-orders-container').on('input', 'input[name="quantity"]', function () {
        // Get the quantity value and selling price from the current row
        var quantity = $(this).val();
        var sellingPrice = $(this).closest('tr').find('input[name="selling_price"]').val();
        var isVatable = $(this).closest('tr').find('input[name="isVatable"]').val();

        // Calculate the new amount based on the quantity and selling price
        var amount = quantity * sellingPrice;

        // Update the amount column with the new value
        $(this).closest('tr').find('.amount').val(amount);

        // Calculate subtotal and vatable subtotal
        var subtotal = 0;
        var vatableSubtotal = 0;
        $('.pos-orders-container tbody tr').each(function () {
            var amount = $(this).find('.amount').val();
            subtotal += parseFloat(amount);
            var isVatableItem = $(this).find('input[name="isVatable"]').val();
            if (isVatableItem == 1) {
                vatableSubtotal += parseFloat(amount);
            }
        });

        // Update the subtotal input value
        $('input[name="subtotal"]').val(subtotal.toFixed(2));

        // Update the VAT value if applicable
        if (isVatable == 1) {
            var vat = vatableSubtotal * vatRate; // calculate the VAT value
            $('#vat').val(vat.toFixed(2)); // set the VAT input value to 2 decimal places
        }

        //discount
        var isDiscounted = $('#discount-check').is(':checked');
        var subtotal_val = parseFloat($('#subtotal').val());
        var vat_val = parseFloat($('#vat').val());

        var new_subtotal = subtotal_val + vat_val;
      
        // Calculate the discount if applicable
        if (isDiscounted) {
          var discountAmount = new_subtotal * discountRate;
          $('#discount').val(discountAmount.toFixed(2));
        } else {
          $('#discount').val('0.00');
        }

        //set total
        var subtotal_val = parseFloat($('#subtotal').val());
        var vat_val = parseFloat($('#vat').val());
        var discount_val = parseFloat($('#discount').val());
        var total = (subtotal_val + vat_val) - discount_val;

        $('#total').val(total.toFixed(2)); //set total
    });



    $('.pos-orders-container').on('click', '.remove-row', function () {
        $(this).closest('tr').remove();

        var subtotal = 0;
        var vatableSubtotal = 0;
        $('.pos-orders-container tbody tr').each(function () {
            var amount = $(this).find('.amount').val();
            subtotal += parseFloat(amount);
            var isVatableItem = $(this).find('input[name="isVatable"]').val();
            if (isVatableItem == 1) {
                vatableSubtotal += parseFloat(amount);
            }
        });

        // Update the subtotal input value
        $('input[name="subtotal"]').val(subtotal.toFixed(2));

        var vat = vatableSubtotal * vatRate; // calculate the VAT value
        $('#vat').val(vat.toFixed(2)); // set the VAT input value to 2 decimal places


        //discount
        var isDiscounted = $('#discount-check').is(':checked');
        var subtotal_val = parseFloat($('#subtotal').val());
        var vat_val = parseFloat($('#vat').val());

        var new_subtotal = subtotal_val + vat_val;
      
        // Calculate the discount if applicable
        if (isDiscounted) {
          var discountAmount = new_subtotal * discountRate;
          $('#discount').val(discountAmount.toFixed(2));
        } else {
          $('#discount').val('0.00');
        }


        //set total
        var subtotal_val = parseFloat($('#subtotal').val());
        var vat_val = parseFloat($('#vat').val());
        var discount_val = parseFloat($('#discount').val());
        var total = (subtotal_val + vat_val) - discount_val;

        $('#total').val(total.toFixed(2)); //set total

    });

    $('#discount-check').on('change', function() {
        var isDiscounted = $(this).is(':checked');
      
        var subtotal_val = parseFloat($('#subtotal').val());
        var vat_val = parseFloat($('#vat').val());

        var new_subtotal = subtotal_val + vat_val;
      
        // Calculate the discount if applicable
        if (isDiscounted) {
          var discountAmount = new_subtotal * discountRate;
          $('#discount').val(discountAmount.toFixed(2));
        } else {
          $('#discount').val('0.00');
        }
      
        // Recalculate the total
        var total = (subtotal_val + vat_val) - parseFloat($('#discount').val());
        $('#total').val(total.toFixed(2));
      });
      
});


