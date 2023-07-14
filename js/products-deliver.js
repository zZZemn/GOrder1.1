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
        var $editDeliverLink = $(e.currentTarget);
        var del_id = $editDeliverLink.data('del_id');
        var del_date = $editDeliverLink.data('del_date');
        var supplier = $editDeliverLink.data('supp_lier');

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
        var search = $('#search-input').val();
        var supplier = $('#supplier-select').val();
        var priceSort = $('#by-filtering').val();

        var delivery_id = $('#deliver-id-input').val();
        var del_date = $('#deliveryDateEdit').val();
        var supplier_id = $('#edit-supplier-id').val();

        const currentDate = new Date();
        const sixMonthsLater = new Date();
        sixMonthsLater.setMonth(currentDate.getMonth() + 6);
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
                    deliver(search, supplier, priceSort);
                } else {
                    $('.del-not-edited').css('opacity', 1);
                    setTimeout(function () {
                        $('.del-not-edited').css('opacity', 0);
                    }, 2000);
                    $('#deliverEditForm').css('visibility', 'hidden');
                    deliver(search, supplier, priceSort);
                }
            },
            error: function (xhr, status, error) {
                console.log(error);
            }
        });
    })

    //add deliver
    $('#addDeliverOpen').click(() => {
        $('#deliverAddForm').css('visibility', 'visible');
    })

    $('#closeAddDeliver').click(() => {
        $('#deliverAddForm').css('visibility', 'hidden');
    })

    $('#add-deliver').click(() => {
        var supplier_id = $('#supplier_id').val();
        var del_date = $('#deliveryDate').val();

        const currentDate = new Date();

        if (supplier_id !== null && del_date !== '00-00-0000' && new Date(del_date) < currentDate) {
            $.ajax({
                url: '../process/add-deliver-process.php',
                method: 'POST',
                data: {
                    delivery_date: del_date,
                    supplier_id: supplier_id,
                    add_deliver: 'yes'
                },
                success: function (response) {
                    if (response === 'OK') {
                        $('.del-added').css('opacity', 1);
                        setTimeout(function () {
                            $('.del-added').css('opacity', 0);
                        }, 2000);
                        $('#supplier_id').val('');
                        $('#deliveryDate').val('');
                        $('#deliverAddForm').css('visibility', 'hidden');
                        deliver(search, supplier, priceSort);
                    } else {
                        $('.del-not-added').css('opacity', 1);
                        setTimeout(function () {
                            $('.del-not-added').css('opacity', 0);
                        }, 2000);
                        $('#deliverAddForm').css('visibility', 'hidden');
                        deliver(search, supplier, priceSort);
                    }
                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });
        } else {
            $('.del-not-added').css('opacity', 1);
            setTimeout(function () {
                $('.del-not-added').css('opacity', 0);
            }, 2000);
            $('#deliverAddForm').css('visibility', 'hidden');
            deliver(search, supplier, priceSort);
        }
    })

    //delete deliver
    $(document).on('click', '#delete-deliver', (e) => {
        e.preventDefault();
        var $btnDelete = $(e.currentTarget);
        var del_id = $btnDelete.data('del_id');
        console.log(del_id);

        var modalTitle = "Delete " + del_id;
        $('.modal-title').text(modalTitle);
        $('#delete-this-deliver').attr('data-del_id', del_id);
        $('#myModal').modal('show');
    })

    let deleteDeliver = (del_id) => {
        $.ajax({
            url: '../process/delete-deliver-process.php',
            method: 'GET',
            data: { del_id: del_id },
            success: function (response) {
                if (response === 'ok') {
                    $('.del-deleted').css('opacity', 1);
                    setTimeout(function () {
                        $('.del-deleted').css('opacity', 0);
                    }, 2000);
                    deliver(search, supplier, priceSort)
                } else {
                    $('.del-not-deleted').css('opacity', 1);
                    setTimeout(function () {
                        $('.del-not-deleted').css('opacity', 0);
                    }, 2000);
                    deliver(search, supplier, priceSort)
                }
                $('#myModal').modal('hide');
                $('#myModal').trigger('hidden.bs.modal');
            },
            error: function (xhr, status, error) {
                console.log(error);
            }
        });
    }

    $(document).on('click', '#delete-this-deliver', () => {
        var del_id = $('#delete-this-deliver').attr('data-del_id');
        deleteDeliver(del_id);
    });

    $('#myModal').on('hidden.bs.modal', () => {
        $('#delete-this-deliver').attr('data-product_id', '');
    });

    $('#myModal').on('click', '#close-delete-this-deliver', () => {
        $('#myModal').modal('hide');
        $('#myModal').trigger('hidden.bs.modal');
    })
})