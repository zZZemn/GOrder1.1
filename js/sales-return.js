$(document).ready(function () {
    $('#submit_return').on('click', function () {
        var transaction_id = $('#transaction_id').val();
        var price = parseFloat($('#price').val());
        var inputs = $('input[type="number"]');

        var inventory = [
            {
                transaction_id: transaction_id
            },
            []
        ];

        inputs.each(function () {
            var value = $(this).val();
            var id = $(this).attr('id');
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

        if (inventory[1].length > 0) {
            $.ajax({
                url: '../ajax-url/return-process.php',
                type: 'POST',
                data: JSON.stringify(inventory),
                contentType: 'application/json',
                success: function (response) {
                    // Handle success response
                    console.log('Success:', response);
                },
                error: function (xhr, status, error) {
                    // Handle error response
                    console.log('Error:', error);
                }
            });
        }
    });

    $('.qty-td').on('input', 'input[name="quantity"]', function () {
        console.log('input');
        var maximumValue = $(this).attr('max');
        var quantity = $(this).val();
        var alertElement = $(this).closest('.qty-td').find('.alert-when-reach-maxlevel');

        if (quantity == maximumValue) {
            alertElement.css('opacity', 1);
            alertElement.css('pointer-events', 'auto');
        } else {
            alertElement.css('opacity', 0);
            alertElement.css('pointer-events', 'none');
        }
    });

});
