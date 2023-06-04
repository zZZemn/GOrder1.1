$(document).ready(function () {
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

    window.onload = loadXMLDoc;

    var inventory = [
        {
            transaction_id: ''
        },
        []
    ];

    $(document).on('click', '#submit_return', function (event) {
        event.preventDefault();

        var transaction_id = $('#transaction_id').val();
        var inputs = $('input[type="number"]');

        inventory[0].transaction_id = transaction_id;
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

        if (inventory[1].length > 0) {
            $('#confirmModal').modal('show');
        } else {
            console.log('invalid');
        }
    });

    $('#confirmAddReturn').on('click', function () {
        $('#confirmModal').modal('hide');
        console.log('submitted');
        performAjax();
        loadXMLDoc();
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
          },
          error: function (xhr, status, error) {
            console.log('Error:', error);
          }
        });
    }




    $(document).on('input', 'input[name="quantity"]', function () {
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
            console.log('asd');
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


});
