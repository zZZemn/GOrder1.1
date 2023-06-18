$(document).ready(function () {
    function orderSelect() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("select_status_container").innerHTML = this.responseText;
            }
        };

        var id = $('#transaction_id').val();
        var url = "../server/order-status-select.php?id=" + encodeURIComponent(id);
        xhttp.open("GET", url, true);
        xhttp.send();
    }

    window.onload = orderSelect();

    function loadXMLDoc() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("status_container").innerHTML = this.responseText;
            }
        };

        var id = $('#transaction_id').val();
        var url = "../server/order-status-update.php?id=" + encodeURIComponent(id);
        xhttp.open("GET", url, true);
        xhttp.send();
    }

    window.onload = loadXMLDoc;

    setInterval(function () {
        loadXMLDoc();
    }, 1000);

    $('#select_status_container').on('change', '#update-order-status', function() {
        console.log('clicked');
        var new_status = $(this).val();
        var transaction_id = $('#transaction_id').val();
      
        // Rest of your AJAX code
        $.ajax({
          url: '../ajax-url/order-status-update.php',
          data: {
            new_status: new_status,
            transaction_id: transaction_id
          },
          type: 'POST',
          success: function(data) {
            if (data === 'Picked Up') {
              $('#update-order-status').prop('disabled', true);
            }
      
            console.log(data);
            orderSelect();
          },
        });
      });
      

    $('#pick-delivery-man').change(function () {
        var rider = $('#pick-delivery-man').val();
        var transaction_id = $('#transaction_id').val();

        $.ajax({
            url: '../ajax-url/order-status-update.php',
            data: {
                rider: rider,
                transaction_id: transaction_id
            },
            type: 'POST',
            success: function (data) {
                if (data === 'Picked Up') {
                    $('#update-order-status').prop('disabled', true);
                }
            },
        });
    });

    function loadOrderDetails() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("fourt_container").innerHTML = this.responseText;
            }
        };

        var id = $('#transaction_id').val();
        var url = "../server/order-pay-update.php?id=" + encodeURIComponent(id);
        xhttp.open("GET", url, true);
        xhttp.send();
    }

    window.onload = loadOrderDetails;

    $(document).on('click', '#payment_submit', function () {
        // Get the values from the total_hidden and payment elements
        var transaction_id = $('#transaction_id').val();
        const total = parseFloat($('#total_hidden').val());
        const payment = parseFloat($('#payment').val());

        if (payment >= total) {
            $.ajax({
                url: '../ajax-url/order-payment-update.php', // Replace with the actual URL of your server-side script
                method: 'POST',
                data: {
                    total: total,
                    payment: payment,
                    transaction_id: transaction_id
                },
                success: function(response) {
                    console.log('Data logged successfully:', response);
                    loadOrderDetails();
                },
                error: function(xhr, status, error) {
                    console.error('Error logging data:', error);
                }
            });
        } else {
            $('.alert-payment-invalid').css('opacity', 1).css('pointer-events', 'auto');
            setTimeout(function () {
                $('.alert-payment-invalid').css('opacity', 0).css('pointer-events', 'none');
            }, 2000);
        }
    });
});
