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
                }
            });
        } else {
            $('#search_results').empty();
        }
    });

    $('#search_products').on('keypress', function (event) {
        // Check if the Enter key was pressed
        if (event.which === 13) {
            // Prevent the form from being submitted
            event.preventDefault();

            // Get the search query from the input field
            var query = $(this).val();

            // Send the search query to the server and update the search results
            if (query.length >= 2) {
                $.ajax({
                    url: '../ajax-url/pos-search.php',
                    type: 'POST',
                    data: {
                        query: query
                    },
                    success: function (data) {
                        $('#search_results').html(data);

                        var searchValue = $('#search_products').val();

                        $('.product-select').each(function () {
                            var productCode = $(this).find('input[name="productCode"]').val();

                            if (productCode === searchValue) {

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
                                    var cust_type = $('#cust_type').val();

                                    var subtotal_val = parseFloat($('#subtotal').val());
                                    var vat_val = parseFloat($('#vat').val());

                                    var new_subtotal = subtotal_val + vat_val;

                                    // Calculate the discount if applicable
                                    if (cust_type == "pwd" || cust_type == "senior") {
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

                                    var payment = parseFloat($('#payment').val()); //parse input value to float
                                    var total = parseFloat($('#total').val());

                                    var change = 0.00;

                                    if (total > 0) {
                                        if (payment >= total) {
                                            change = payment - total;
                                            parseFloat($('#change').val(change.toFixed(2)));

                                            $('#save').prop('disabled', false);
                                            $('#save_print').prop('disabled', false);
                                        } else {
                                            parseFloat($('#change').val(change.toFixed(2)));

                                            $('#save').prop('disabled', true);
                                            $('#save_print').prop('disabled', true);
                                        }
                                    } else {
                                        parseFloat($('#change').val(change.toFixed(2)));
                                    }

                                    $('#search_products').val('');
                                    $('#search_results').html('');

                                } else {
                                    alert('Please enter a quantity greater than 0.'); // show an error message
                                    $('#search_products').val('');
                                    $('#search_results').html('');
                                }
                            }
                        });
                    }
                });
            } else {
                $('#search_results').empty();
            }


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
            var cust_type = $('#cust_type').val();

            var subtotal_val = parseFloat($('#subtotal').val());
            var vat_val = parseFloat($('#vat').val());

            var new_subtotal = subtotal_val + vat_val;

            // Calculate the discount if applicable
            if (cust_type == "pwd" || cust_type == "senior") {
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

            var payment = parseFloat($('#payment').val()); //parse input value to float
            var total = parseFloat($('#total').val());

            var change = 0.00;

            if (total > 0) {
                if (payment >= total) {
                    change = payment - total;
                    parseFloat($('#change').val(change.toFixed(2)));

                    $('#save').prop('disabled', false);
                    $('#save_print').prop('disabled', false);
                } else {
                    parseFloat($('#change').val(change.toFixed(2)));

                    $('#save').prop('disabled', true);
                    $('#save_print').prop('disabled', true);
                }
            } else {
                parseFloat($('#change').val(change.toFixed(2)));
            }

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
        var cust_type = $('#cust_type').val();

        var subtotal_val = parseFloat($('#subtotal').val());
        var vat_val = parseFloat($('#vat').val());

        var new_subtotal = subtotal_val + vat_val;

        // Calculate the discount if applicable
        if (cust_type == "pwd" || cust_type == "senior") {
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

        var payment = parseFloat($('#payment').val()); //parse input value to float
        var total = parseFloat($('#total').val());

        var change = 0.00;

        if (total > 0) {
            if (payment >= total) {
                change = payment - total;
                parseFloat($('#change').val(change.toFixed(2)));

                $('#save').prop('disabled', false);
                $('#save_print').prop('disabled', false);
            } else {
                parseFloat($('#change').val(change.toFixed(2)));

                $('#save').prop('disabled', true);
                $('#save_print').prop('disabled', true);
            }
        } else {
            parseFloat($('#change').val(change.toFixed(2)));
        }
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
        var cust_type = $('#cust_type').val();

        var subtotal_val = parseFloat($('#subtotal').val());
        var vat_val = parseFloat($('#vat').val());

        var new_subtotal = subtotal_val + vat_val;

        // Calculate the discount if applicable
        if (cust_type == "pwd" || cust_type == "senior") {
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

        var payment = parseFloat($('#payment').val()); //parse input value to float
        var total = parseFloat($('#total').val());

        var change = 0.00;

        if (total > 0) {
            if (payment >= total) {
                change = payment - total;
                parseFloat($('#change').val(change.toFixed(2)));

                $('#save').prop('disabled', false);
                $('#save_print').prop('disabled', false);
            } else {
                parseFloat($('#change').val(change.toFixed(2)));

                $('#save').prop('disabled', true);
                $('#save_print').prop('disabled', true);
            }
        } else {
            parseFloat($('#change').val(change.toFixed(2)));
        }

    });

    $('#cust_type').on('change', function () {
        var cust_type = $(this).val();

        var subtotal_val = parseFloat($('#subtotal').val());
        var vat_val = parseFloat($('#vat').val());

        var new_subtotal = subtotal_val + vat_val;

        // Calculate the discount if applicable
        if (cust_type == "pwd" || cust_type == "senior") {
            var discountAmount = new_subtotal * discountRate;
            $('#discount').val(discountAmount.toFixed(2));
        } else {
            $('#discount').val('0.00');
        }

        // Recalculate the total
        var total = (subtotal_val + vat_val) - parseFloat($('#discount').val());
        $('#total').val(total.toFixed(2));


        //update change
        var payment = parseFloat($('#payment').val()); //parse input value to float
        var total = parseFloat($('#total').val());

        var change = 0.00;

        if (total > 0) {
            if (payment >= total) {
                change = payment - total;
                parseFloat($('#change').val(change.toFixed(2)));

                $('#save').prop('disabled', false);
                $('#save_print').prop('disabled', false);
            } else {
                parseFloat($('#change').val(change.toFixed(2)));

                $('#save').prop('disabled', true);
                $('#save_print').prop('disabled', true);
            }
        } else {
            parseFloat($('#change').val(change.toFixed(2)));
        }
    });

    $('#payment').on('input', function () {
        var payment = parseFloat($(this).val()); //parse input value to float
        var total = parseFloat($('#total').val());

        var change = 0.00;

        if (total > 0) {
            if (payment >= total) {
                change = payment - total;
                parseFloat($('#change').val(change.toFixed(2)));

                $('#save').prop('disabled', false);
                $('#save_print').prop('disabled', false);
            } else {
                parseFloat($('#change').val(change.toFixed(2)));

                $('#save').prop('disabled', true);
                $('#save_print').prop('disabled', true);
            }
        } else {
            parseFloat($('#change').val(change.toFixed(2)));
        }
    });

    $('#cust_id').on('keyup', function () {
        // Get the value of the input element
        var custId = $(this).val();

        // Send an AJAX request to the server to check if the customer ID exists
        $.ajax({
            url: '../ajax-url/pos-check-cust-id.php',
            method: 'POST',
            data: { cust_id: custId },
            success: function (data) {
                if (custId == '') {
                    $('#cust_id').removeClass('outline-danger').addClass('outline-primary');

                    $('#save').prop('disabled', false);
                    $('#save_print').prop('disabled', false);
                }
                else if (data == 'exists') {
                    // If the customer ID exists, set the border color to blue
                    $('#cust_id').removeClass('outline-danger').addClass('outline-primary');

                    $('#save').prop('disabled', false);
                    $('#save_print').prop('disabled', false);
                } else {
                    // If the customer ID does not exist, set the border color to red
                    $('#cust_id').removeClass('outline-primary').addClass('outline-danger');

                    $('#save').prop('disabled', true);
                    $('#save_print').prop('disabled', true);
                }
            }
        });
    });

    $('#payment').on('keyup', function () {
        // Get the total value
        var total = $('#total').val();
        var payment = parseFloat($(this).val());
        // Enable/disable the button based on the total value
        if (total > 0) {
            if (payment > total) {
                $('#save').prop('disabled', false);
                $('#save_print').prop('disabled', false);
            } else {
                $('#save').prop('disabled', true);
                $('#save_print').prop('disabled', true);
            }
        }
    });

})

$(document).ready(function () {
    // When the Save button is clicked
    $('#save').click(function (event) {
        event.preventDefault();

        // Create an object to store the sales and sales details data
        var salesData = {
            sales: {
                transaction_type: 'POS',
                cust_type: $('#cust_type').val(),
                cust_id: $('#cust_id').val(),
                emp_id: $('#emp_id').val(),
                subtotal: $('#subtotal').val(),
                vat: $('#vat').val(),
                discount: $('#discount').val(),
                total: $('#total').val(),
                payment: $('#payment').val(),
                change: $('#change').val()
            },
            salesDetails: []
        };

        // Loop through each row in the table and add the details to the object
        $('.pos-orders-container tbody tr').each(function (index, row) {
            var detailsData = {
                product_id: $(row).find('[name="product_id"]').val(),
                quantity: $(row).find('[name="quantity"]').val(),
                amount: $(row).find('[name="amount"]').val()
            };

            // Add the details to the salesData object
            salesData.salesDetails.push(detailsData);
        });

        // Send the AJAX request to the server
        $.ajax({
            type: 'POST',
            url: '../ajax-url/pos-save-process.php',
            data: JSON.stringify(salesData),
            contentType: 'application/json',
            success: function (response) {
                // Do something with the response
                console.log(response);

            },
            error: function (error) {
                // Handle errors
                console.log(error);
            }
        });
    });
});



