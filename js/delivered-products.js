$(document).ready(function () {
    var delivery_id = $('#del_id').val();

    const expirationDateMin = () => {
        var today = new Date();
        var minimumDate = new Date();
        minimumDate.setMonth(today.getMonth() + 5);
        var minimumDateString = minimumDate.toISOString().slice(0, 10);

        return minimumDateString;
    }

    const closeEdit = () => {
        $('#inv-id').text('');
        $('#product-name').text('');
        $('#expiration-date').val('');
        $('#supp-price').val('');
        $('#edit_del_qty').val('');
        $('#inv-id-hidden').val('');
        $('.edit-product-container').css('display', 'none');
    }

    const deliveredProducts = (delivery_id) => {
        var url = "../server/delivered-update.php?id=" + delivery_id;
        $.ajax({
            url: url,
            method: "GET",
            dataType: "html",
            success: function (response) {
                $("#delivered-containainer").html(response);
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText);
            }
        });
    };

    deliveredProducts(delivery_id);


    $('#expiration_date').attr('min', expirationDateMin());
    $('#expiration-date').attr('min', expirationDateMin());

    $('#add_delivered').on('click', (e) => {
        e.preventDefault();

        var product_id = $('#product_id').val();
        var exp_date = $('#expiration_date').val();
        var supplier_price = $('#supp_price').val();
        var qty = $('#del_qty').val();

        if (product_id && delivery_id && supplier_price && qty) {
            var today = new Date();
            var inputDate = new Date(exp_date);

            if (inputDate >= today) {
                $.ajax({
                    url: "../process/delivered-add-process.php",
                    type: "POST",
                    data: {
                        del_id: delivery_id,
                        product_id: product_id,
                        expiration_date: exp_date,
                        supp_price: supplier_price,
                        del_qty: qty,
                        add_delivered: true
                    },
                    success: function (response) {
                        if (response === 'ok') {
                            $('.adding-success').css('opacity', 1);
                            setTimeout(function () {
                                $('.adding-success').css('opacity', 0);
                            }, 1000);
                            deliveredProducts(delivery_id);
                            $('#product_id').val('');
                            $('#expiration_date').val('');
                            $('#supp_price').val('');
                            $('#del_qty').val('');
                        } else if (response === 'not_exist') {
                            $('.product-not-exist').css('opacity', 1);
                            setTimeout(function () {
                                $('.product-not-exist').css('opacity', 0);
                            }, 1000);
                            deliveredProducts(delivery_id);
                        } else {
                            $('.adding-failed').css('opacity', 1);
                            setTimeout(function () {
                                $('.adding-failed').css('opacity', 0);
                            }, 1000);
                            deliveredProducts(delivery_id);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(error);
                    }
                });
            } else {
                $('.invalid-exp-date').css('opacity', 1);
                setTimeout(function () {
                    $('.invalid-exp-date').css('opacity', 0);
                }, 1000);
                deliveredProducts(delivery_id);
            }
        } else {
            $('.adding-failed').css('opacity', 1);
            setTimeout(function () {
                $('.adding-failed').css('opacity', 0);
            }, 1000);
            deliveredProducts(delivery_id);
        }
    })


    //editing delivered products
    $(document).on('click', '#edit-delivered', (e) => {
        e.preventDefault();
        var inv_id = $(e.currentTarget).attr('data-inv_id');
        $('#inv-id').text('INV-' + inv_id);
        $('#inv-id-hidden').val(inv_id);

        $.ajax({
            type: "post",
            url: "../server/get-inventory-details.php",
            data: { inv_id: inv_id },
            success: function (response) {
                if (response === 'no_edit') {
                    $('.no-edit').css('opacity', 1);
                    setTimeout(function () {
                        $('.no-edit').css('opacity', 0);
                    }, 2000);
                } else if (response === 'not_exist') {
                    $('.not-exist').css('opacity', 1);
                    setTimeout(function () {
                        $('.not-exist').css('opacity', 0);
                    }, 2000);
                } else if (response === 'error') {
                    $('.problem').css('opacity', 1);
                    setTimeout(function () {
                        $('.problem').css('opacity', 0);
                    }, 2000);
                } else {
                    var data = JSON.parse(response);
                    var productName = data[4];
                    var expirationDate = data[1];
                    var price = data[2];
                    var quantity = data[3];

                    $('#product-name').text(productName);
                    $('#expiration-date').val(expirationDate);
                    $('#supp-price').val(price);
                    $('#edit_del_qty').val(quantity);

                    $('.edit-product-container').css('display', 'block');
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
            }
        });
    })

    $('#closeEditInventory').click((e) => {
        e.preventDefault();
        closeEdit();
    });

    $('#save-change').click((e) => {
        e.preventDefault();

        var inv_id = $('#inv-id-hidden').val();
        var expiration_date = $('#expiration-date').val();
        var supplier_price = parseFloat($('#supp-price').val());
        var del_qty = parseInt($('#edit_del_qty').val());

        if (inv_id && supplier_price && del_qty) {
            $.ajax({
                type: "post",
                url: "../process/edit-delivered.php",
                data: {
                    inv_id: inv_id,
                    expiration_date: expiration_date,
                    supplier_price: supplier_price,
                    del_qty: del_qty
                },
                success: function (response) {
                    if (response === 'updated') {
                        $('.editing-success').css('opacity', 1);
                        setTimeout(function () {
                            $('.editing-success').css('opacity', 0);
                        }, 2000);
                        deliveredProducts(delivery_id);
                        closeEdit();
                    } else if (response === 'not_updated') {
                        $('.editing-failed').css('opacity', 1);
                        setTimeout(function () {
                            $('.editing-failed').css('opacity', 0);
                        }, 2000);
                        deliveredProducts(delivery_id);
                        closeEdit();
                    } else {
                        $('.problem').css('opacity', 1);
                        setTimeout(function () {
                            $('.problem').css('opacity', 0);
                        }, 1000);
                        deliveredProducts(delivery_id);
                        closeEdit();
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        } else {
            $('.editing-failed').css('opacity', 1);
            setTimeout(function () {
                $('.editing-failed').css('opacity', 0);
            }, 2000);
            closeEdit();
        }

    })
})