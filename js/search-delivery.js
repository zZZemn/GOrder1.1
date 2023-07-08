$(document).ready(function () {
    var search = $('#search-input').val();
    var supplier = $('#supplier-select').val();
    var priceSort = $('#by-filtering').val();

    function deliver(search, supplier, priceSort) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("deliveries-container").innerHTML =
                    this.responseText;
            }
        };
        var url = `../server/get-deliver.php?search=${search}&supplier=${supplier}&priceSort=${priceSort}`;
        xhttp.open("GET", url, true);
        xhttp.send();
    }

    window.onload = deliver(search, supplier, priceSort);

    $('#search-input').on('input', () => {
        var search = $('#search-input').val();
        var supplier = $('#supplier-select').val();
        var priceSort = $('#by-filtering').val();
        deliver(search, supplier, priceSort)
    })

    $('#supplier-select').on('change', () => {
        var search = $('#search-input').val();
        var supplier = $('#supplier-select').val();
        var priceSort = $('#by-filtering').val();
        deliver(search, supplier, priceSort)
    })

    $('#by-filtering').on('change', () => {
        var search = $('#search-input').val();
        var supplier = $('#supplier-select').val();
        var priceSort = $('#by-filtering').val();
        deliver(search, supplier, priceSort)
    })

    //edit-product
    $(document).on('click', '#edit-deliver-link', (e) => {
        e.preventDefault();
        var del_id = $('#edit-deliver-link').data('del_id');
        var del_date = $('#edit-deliver-link').data('del_date');
        var supplier = $('#edit-deliver-link').data('supp_lier');

        $('#deliver-id-input').val(del_id);
        $('#edit-deliver-h5').text('Edit Deliver ' + del_id);
        $('#edit-supplier-id').val(supplier);
        $('#deliveryDateEdit').val(del_date);
        $('#deliverEditForm').css('visibility', 'visible');
    })

    $('#closeEditDeliver').click(() => {
        $('#deliverEditForm').css('visibility', 'hidden');
    })

    $('#edit-delivery').click(() => {
        var delivery_id = $('#deliver-id-input').val();
        var del_date = $('#deliveryDateEdit').val();
        var supplier_id = $('#edit-supplier-id').val();
        $.ajax({
            url: '../process/edit-deliver-process.php',
            method: 'POST',
            data: {
                deliver_id: delivery_id,
                delivery_date: del_date,
                supplier_id: supplier_id,
                edit_deliver: 'yes'
            },
            success: function (response) {
                if (response === 'OK') {
                    $('.del-edited').css('opacity', 1);
                    setTimeout(function () {
                        $('.del-edited').css('opacity', 0);
                    }, 2000);
                    $('#deliverEditForm').css('visibility', 'hidden');
                    deliver();
                } else {
                    $('.del-not-edited').css('opacity', 1);
                    setTimeout(function () {
                        $('.del-not-edited').css('opacity', 0);
                    }, 2000);
                    $('#deliverEditForm').css('visibility', 'hidden');
                    deliver();
                }
            },
            error: function (xhr, status, error) {
                console.log(error);
            }
        });
    })
})