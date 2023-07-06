$(document).ready(function () {
    var discountPercentage = $('#discount_percentage').val();
    var cust_type = $('#cust_type').val();
    var url;
    const transaction_id = $('#transaction_id_hidden').val();
    function loadXMLDoc() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("return_container").innerHTML =
                    this.responseText;
            }
        };
        const url = "../server/sales-return-update.php?id=" + encodeURIComponent(transaction_id);
        xhttp.open("GET", url, true);
        xhttp.send();
    }

    window.onload = function () {
        setTimeout(loadXMLDoc, 1000);
    };

    function delayedLoadXMLDoc() {
        setTimeout(loadXMLDoc, 2000);
    }


    var inventory = [
        {
            transaction_id: '',
            reason: '',
        },
        []
    ];

    $(document).on('click', '#submit_return', function (event) {
        event.preventDefault();

        var reason = $('#return_reason').val();
        var transaction_id = $('#transaction_id').val();
        var inputs = $('input[name="rtn_quantity"]');
        inventory[0].transaction_id = transaction_id;
        inventory[0].reason = reason;
        inventory[1] = [];

        inputs.each(function () {
            var value = $(this).val();
            var id = $(this).attr('id');
            var price = parseFloat($(this).closest('tr').find('#price').val());
            var amount = price * value;

            if (value != '' && value > 0) {
                var item = {
                    id: id,
                    qty: value,
                    amount: amount
                };
                inventory[1].push(item);
            }
        });

        if (inventory[1].length > 0 && inventory[0].reason != null && inventory[0].transaction_id != null) {
            $('#confirmModal').modal('show');
        } else {
            console.log('invalid');
        }
    });

    $('#confirmAddReturn').on('click', function () {
        $('#confirmModal').modal('hide');
        console.log('submitted');
        performAjax();
    });

    $('#cancelAddReturn').on('click', function () {
        $('#confirmModal').modal('hide');
        console.log('cancelled');
        loadXMLDoc();
    });

    function performAjax() {
        $.ajax({
            url: '../ajax-url/return-process.php',
            type: 'POST',
            data: JSON.stringify(inventory),
            contentType: 'application/json',
            success: function (response) {
                console.log('Success:', response);
                delayedLoadXMLDoc();
            },
            error: function (xhr, status, error) {
                console.log('Error:', error);
            }
        });
    }




    $(document).on('input', 'input[name="rtn_quantity"]', function () {
        var hasNegative = false;
        console.log('input');
        var maximumValue = $(this).attr('max');
        var quantity = $(this).val();
        var alertElement = $(this).closest('.qty-td').find('.alert-when-reach-maxlevel');
        var alertInvalidQtyInput = $(this).closest('.qty-td').find('.alert-when-invalid-qty');

        var inputParts = quantity.split("-");

        for (var i = 0; i < inputParts.length; i++) {
            var numericValue = parseFloat(inputParts[i]);
            if (isNaN(numericValue) || numericValue < 0) {
                hasNegative = true;
                break;
            }
        }

        if (hasNegative) {
            alertInvalidQtyInput.css('opacity', 1);
            alertInvalidQtyInput.css('pointer-events', 'auto');
        } else if (quantity == maximumValue) {
            alertElement.css('opacity', 1);
            alertElement.css('pointer-events', 'auto');
        } else {
            alertElement.css('opacity', 0);
            alertElement.css('pointer-events', 'none');

            alertInvalidQtyInput.css('opacity', 0);
            alertInvalidQtyInput.css('pointer-events', 'none');
        }
    });


    // sreplace

    $(document).on('input', '#search-product', function (e) {
        e.preventDefault();
        var query = $(this).val();
        if (query.length >= 2) {
            $.ajax({
                url: '../ajax-url/pos-search.php',
                type: 'POST',
                data: {
                    query: query
                },
                success: function (data) {
                    $('#search-response-container').html(data);
                }
            });
        } else {
            $('#search-response-container').empty();
        }
    })

    //pick product 
    $(document).on('submit', '.product-select', function (e) {
        e.preventDefault();
        var tax_percentage = $('#tax').val();
        var unitMeasurement = '';
        // extract the product details from the hidden input fields
        var productId = $(this).find('input[name="product_id"]').val();
        var productName = $(this).find('input[name="product_name"]').val();
        unitMeasurement = $(this).find('input[name="unit_meas"]').val();
        var sellingPrice = $(this).find('input[name="selling_price"]').val();
        var quantity_left = $(this).find('input[name="quantity_left"]').val();
        var isVatable = $(this).find('input[name="isVatable"]').val();
        var isDiscountable = $(this).find('input[name="isDiscountable"]').val();
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
                    "<td class='pro-name-receipt'>" + productName + " " + unitMeasurement + "</td>" +
                    "<input type='hidden' name='isDiscountable' id='isDiscountable' value='" + isDiscountable + "'>" +
                    "<input type='hidden' name='isVatable' id='isVatable' value='" + isVatable + "'>" +
                    "<td><input type='number' class='no-border order-details-inputs form-control' name='selling_price' value='" + sellingPrice + "' readonly></td>" +
                    "<td><input type='number' name='quantity' class='no-border order-details-inputs form-control' value='" + quantity + "' min='1' max='" + quantity_left + "' oninput=\"if(parseInt(this.value) > parseInt(this.max)) this.value = this.max;\"></td>" +
                    "<td><input type='number' name='amount' class='no-border order-details-inputs amount form-control' value='" + sellingPrice + "' readonly></td>" +
                    "<td class='remove-when-print'><button type='button' class='btn btn-danger btn-sm remove-row'><i class='fas fa-trash'></i></button></td>" +
                    "<input type='hidden' name='product_id' value='" + productId + "'>" +
                    "</tr>";

                $('.pos-orders-container tbody').append(newRow);
            }

            //subtotal
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

            $('input[name="subtotal"]').val(subtotal.toFixed(2));

            if (isVatable == 1) {
                var vat = vatableSubtotal * tax_percentage; // calculate the VAT value
                $('#vat').val(vat.toFixed(2)); // set the VAT input value to 2 decimal places
            }

            var discoutableSubtotal = 0;
            $('.pos-orders-container tbody tr').each(function () {
                var amount = $(this).find('.amount').val();
                var isDiscountable = $(this).find('input[name="isDiscountable"]').val();
                if (isDiscountable == 1) {
                    discoutableSubtotal += parseFloat(amount);
                }
            });

            var subtotal_val = parseFloat($('#subtotal').val());
            var vat_val = parseFloat($('#vat').val());

            var discountAmount = discoutableSubtotal * discountPercentage;
            $('#discount').val(discountAmount.toFixed(2));

            //set total
            var subtotal_val = parseFloat($('#subtotal').val());
            var vat_val = parseFloat($('#vat').val());
            var discount_val = parseFloat($('#discount').val());
            var total = (subtotal_val + vat_val) - discount_val;

            $('#total').val(total.toFixed(2));

            //payment notif
            var total = parseFloat($('#total').val());
            var voucher = parseFloat($('#voucher').val());

            var paymentRequired = false;
            if (total > voucher) {
                paymentRequired = true
                $('#payment-required-span').text('Payment is required');
                $('#replace').prop('disabled', true);
            } else {
                paymentRequired = false;
                $('#payment-required-span').text('');
                $('#replace').prop('disabled', false);
            }

            var total = parseFloat($('#total').val());
            var voucher = parseFloat($('#voucher').val());

            if (voucher < total) {
                $('#payment').prop('disabled', false);

                var payment = parseFloat($('#payment').val());
                payment = isNaN(payment) ? 0 : parseFloat($('#payment').val());
                var paymentAndVoucherSum = payment + voucher;
                if (total > paymentAndVoucherSum) {
                    $('#replace').prop('disabled', true);
                    $('#change').val('');
                    var minimum = total - voucher;
                    $('#payment-required-span').text('Minimum: ' + minimum);
                } else {
                    var change = paymentAndVoucherSum - total;
                    $('#change').val(change);
                    $('#replace').prop('disabled', false);
                    $('#payment-required-span').text('');
                }
            } else {
                $('#payment').val('');
                $('#change').val('');
                $('#payment').prop('disabled', true);
            }

        } else {
            $('.alert-no-qty-left').css('opacity', 1);
            $('.alert-no-qty-left').css('pointer-events', 'auto');

            setTimeout(function () {
                $('.alert-no-qty-left').css('opacity', 0);
                $('.alert-no-qty-left').css('pointer-events', 'none');
            }, 2000);
        }
    });

    $(document).on('input', 'input[name="quantity"]', function () {
        var tax_percentage = $('#tax').val();
        var maximumValue = $(this).attr('max');
        var quantity = $(this).val();

        if (quantity == maximumValue) {
            $('.alert-inv-qty-input').css('opacity', 1);
            $('.alert-inv-qty-input').css('pointer-events', 'auto');
            setTimeout(function () {
                $('.alert-inv-qty-input').css('opacity', 0);
                $('.alert-inv-qty-input').css('pointer-events', 'none');
            }, 1000);
        }

        var sellingPrice = $(this).closest('tr').find('input[name="selling_price"]').val();
        var isVatable = $(this).closest('tr').find('input[name="isVatable"]').val();

        if (quantity === "" || parseFloat(quantity) < 1) {
            // Set quantity to 1
            $(this).val(1);
            quantity = 1; // Update the quantity variable
        }

        var amount = quantity * sellingPrice;

        $(this).closest('tr').find('.amount').val(amount);

        //subtotal
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

        $('input[name="subtotal"]').val(subtotal.toFixed(2));

        if (isVatable == 1) {
            var vat = vatableSubtotal * tax_percentage; // calculate the VAT value
            $('#vat').val(vat.toFixed(2)); // set the VAT input value to 2 decimal places
        }

        var discoutableSubtotal = 0;
        $('.pos-orders-container tbody tr').each(function () {
            var amount = $(this).find('.amount').val();
            var isDiscountable = $(this).find('input[name="isDiscountable"]').val();
            if (isDiscountable == 1) {
                discoutableSubtotal += parseFloat(amount);
            }
        });

        var subtotal_val = parseFloat($('#subtotal').val());
        var vat_val = parseFloat($('#vat').val());

        var discountAmount = discoutableSubtotal * discountPercentage;
        $('#discount').val(discountAmount.toFixed(2));

        //set total
        var subtotal_val = parseFloat($('#subtotal').val());
        var vat_val = parseFloat($('#vat').val());
        var discount_val = parseFloat($('#discount').val());
        var total = (subtotal_val + vat_val) - discount_val;

        $('#total').val(total.toFixed(2));


        //payment notif
        var total = parseFloat($('#total').val());
        var voucher = parseFloat($('#voucher').val());

        var paymentRequired = false;
        if (total > voucher) {
            paymentRequired = true
            $('#payment-required-span').text('Payment is required');
            $('#replace').prop('disabled', true);
        } else {
            paymentRequired = false;
            $('#payment-required-span').text('');
            $('#replace').prop('disabled', false);
        }

        var total = parseFloat($('#total').val());
        var voucher = parseFloat($('#voucher').val());

        if (voucher < total) {
            $('#payment').prop('disabled', false);

            var payment = parseFloat($('#payment').val());
            payment = isNaN(payment) ? 0 : parseFloat($('#payment').val());
            var paymentAndVoucherSum = payment + voucher;
            if (total > paymentAndVoucherSum) {
                $('#replace').prop('disabled', true);
                $('#change').val('');
                var minimum = total - voucher;
                $('#payment-required-span').text('Minimum: ' + minimum);
            } else {
                var change = paymentAndVoucherSum - total;
                $('#change').val(change);
                $('#replace').prop('disabled', false);
                $('#payment-required-span').text('');
            }
        } else {
            $('#payment').val('');
            $('#change').val('');
            $('#payment').prop('disabled', true);
        }
    })


    $(document).on('click', '.remove-row', function () {
        $(this).closest('tr').remove();
        var tax_percentage = $('#tax').val();
        //subtotal
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

        $('input[name="subtotal"]').val(subtotal.toFixed(2));

        var vat = vatableSubtotal * tax_percentage; // calculate the VAT value
        $('#vat').val(vat.toFixed(2)); // set the VAT input value to 2 decimal places

        var discoutableSubtotal = 0;
        $('.pos-orders-container tbody tr').each(function () {
            var amount = $(this).find('.amount').val();
            var isDiscountable = $(this).find('input[name="isDiscountable"]').val();
            if (isDiscountable == 1) {
                discoutableSubtotal += parseFloat(amount);
            }
        });

        var subtotal_val = parseFloat($('#subtotal').val());
        var vat_val = parseFloat($('#vat').val());

        var discountAmount = discoutableSubtotal * discountPercentage;
        $('#discount').val(discountAmount.toFixed(2));

        //set total
        var subtotal_val = parseFloat($('#subtotal').val());
        var vat_val = parseFloat($('#vat').val());
        var discount_val = parseFloat($('#discount').val());
        var total = (subtotal_val + vat_val) - discount_val;

        $('#total').val(total.toFixed(2));

        //payment notif
        var total = parseFloat($('#total').val());
        var voucher = parseFloat($('#voucher').val());

        var paymentRequired = false;
        if (total > voucher) {
            paymentRequired = true
            $('#payment-required-span').text('Payment is required');
            $('#replace').prop('disabled', true);
        } else {
            paymentRequired = false;
            $('#payment-required-span').text('');
            $('#replace').prop('disabled', false);
        }

        if(total < 1){
            $('#replace').prop('disabled', true);
        }
    })

    $(document).on('input', 'input[name="payment"]', function () {
        var total = parseFloat($('#total').val());
        var voucher = parseFloat($('#voucher').val());
        var payment = parseFloat($('#payment').val());
        payment = isNaN(payment) ? 0 : parseFloat($('#payment').val());
        var paymentAndVoucherSum = payment + voucher;

        if (total > voucher) {
            if (total > paymentAndVoucherSum) {
                $('#replace').prop('disabled', true);
                $('#change').val('');
                var minimum = total - voucher;
                $('#payment-required-span').text('Minimum: ' + minimum);
            } else {
                var change = paymentAndVoucherSum - total;
                $('#change').val(change);
                $('#replace').prop('disabled', false);
                $('#payment-required-span').text('');
            }
        } else {
            $('#payment').val('');
            $('#change').val('');
            $('#payment').prop('disabled', true);
        }

        if (paymentAndVoucherSum < total) {
            $('#replace').prop('disabled', true);
        }
    })

    $(document).on('click', '#replace', function (event) {
        event.preventDefault();

        var returnID = $('#return_id').val();
        var voucher = $('#voucher').val();
        var payment = $('#payment').val();
        payment = payment === "" ? 0 : parseFloat(payment);
        final_payment = parseFloat(voucher) + payment;

        var salesData = {
            sales: {
                return_id: returnID,
                transaction_type: 'Replace',
                cust_type: cust_type,
                cust_id: $('#cust_id').val(),
                emp_id: $('#emp_id').val(),
                subtotal: $('#subtotal').val(),
                vat: $('#vat').val(),
                discount: $('#discount').val(),
                total: $('#total').val(),
                payment: final_payment,
                change: $('#change').val()
            },
            salesDetails: []
        };

        $('.pos-orders-container tbody tr').each(function (index, row) {
            var detailsData = {
                product_id: $(row).find('[name="product_id"]').val(),
                quantity: $(row).find('[name="quantity"]').val(),
                amount: $(row).find('[name="amount"]').val()
            };

            // Add the details to the salesData object
            salesData.salesDetails.push(detailsData);
        });

        console.log(salesData);

        $.ajax({
            type: 'POST',
            url: '../ajax-url/replace-process.php',
            data: JSON.stringify(salesData),
            contentType: 'application/json',
            success: function (response) {
                var responseData = JSON.parse(response);
                if (responseData.success) {
                    console.log(responseData);
                    // Get the date and time from the response
                    // var date = responseData.date;
                    // var time = responseData.time;

                    // $('#receipt-table tr td').addClass('border-0');
                    // // Rest of your code

                    // // Append the date and time to the HTML element with ID "date-time-print"
                    // $('.table').removeClass('table-striped');
                    // $('#ggd').append("Golden Gate Drugstore");
                    // $('#ggd-add').append("Patubig, Marilao, Bulacan");
                    // $('#date-time-print').append(date + '  |  ' + time);

                    // $('#receipt-subtotal').append("<p>Subtotal </p> <p>:</p><p>" + $('#subtotal').val() + "</p>");
                    // $('#receipt-vat').append("<p>VAT </p> <p>:</p><p>" + $('#vat').val() + "</p>");
                    // $('#receipt-discount').append("<p>Discount </p> <p>:</p><p>" + $('#discount').val() + "</p>");
                    // $('#receipt-total').append("<p>Total </p> <p>:</p><p>" + $('#total').val() + "</p>");
                    // $('#receipt-payment').append("<p>Payment </p> <p>:</p><p>" + $('#payment').val() + "</p>");
                    // $('#receipt-change').append("<p>Change </p> <p>:</p><p>" + $('#change').val() + "</p>");

                    // window.print();
                    location.reload();
                } else {
                    console.log(responseData.error);
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    })
});
