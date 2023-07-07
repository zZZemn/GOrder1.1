$(document).ready(function () {
    function discountUpdate() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("discounts-container").innerHTML =
                    this.responseText;
            }
        };
        xhttp.open("GET", "../server/maintenance-discount-realtime.php", true);
        xhttp.send();
    }

    window.onload = discountUpdate();

    // $('#btn-add-region').click(function () {
    //     setTimeout(loadXMLDoc, 500);
    // })


    $(document).on("click", ".delete-discount", function () {
        var discountID = $(this).attr("id");

        $.ajax({
            type: "POST",
            url: "../process/maintenance-discount-process-disable.php",
            data: {
                id: discountID
            },
            success: function (response) {
                console.log(response);
                if (response === 'edited') {
                    $('.alert-disabled').css('opacity', 1);
                    $('.alert-disabled').css('pointer-events', 'auto');
                    setTimeout(function () {
                        $('.alert-disabled').css('opacity', 0);
                        $('.alert-disabled').css('pointer-events', 'none');
                    }, 1000);
                    discountUpdate();
                } else {
                    $('.alert-invalid-edit').css('opacity', 1);
                    $('.alert-invalid-edit').css('pointer-events', 'auto');
                    setTimeout(function () {
                        $('.alert-invalid-edit').css('opacity', 0);
                        $('.alert-invalid-edit').css('pointer-events', 'none');
                    }, 1000);
                }
            }
        });
    })

    $(document).on("click", ".enable-discount", function () {
        var discountID = $(this).attr("id");

        $.ajax({
            type: "POST",
            url: "../process/maintenance-discount-process-enable.php",
            data: {
                id: discountID
            },
            success: function (response) {
                console.log(response);
                if (response === 'edited') {
                    $('.alert-enabled').css('opacity', 1);
                    $('.alert-enabled').css('pointer-events', 'auto');
                    setTimeout(function () {
                        $('.alert-enabled').css('opacity', 0);
                        $('.alert-enabled').css('pointer-events', 'none');
                    }, 1000);
                    discountUpdate();
                } else {
                    $('.alert-invalid-edit').css('opacity', 1);
                    $('.alert-invalid-edit').css('pointer-events', 'auto');
                    setTimeout(function () {
                        $('.alert-invalid-edit').css('opacity', 0);
                        $('.alert-invalid-edit').css('pointer-events', 'none');
                    }, 1000);
                }
            }
        });
    })

    $(document).on("click", ".save-discount", function () {
        var discountID = $(this).attr("id");
        var discountValue = $(this).parent().prev().children("input").val();

        if (!isValidDiscount(discountValue)) {
            $('.alert-invalid-decimal').css('opacity', 1);
            $('.alert-invalid-decimal').css('pointer-events', 'auto');
            setTimeout(function () {
                $('.alert-invalid-decimal').css('opacity', 0);
                $('.alert-invalid-decimal').css('pointer-events', 'none');
            }, 1000);
        } else {
            $.ajax({
                type: "POST",
                url: "../process/maintenance-discount-process.php", // Replace with the URL to your PHP file for saving the discount
                data: {
                    id: discountID,
                    value: discountValue
                },
                success: function (response) {
                    console.log(response);
                    if (response === 'edited') {
                        $('.alert-edited').css('opacity', 1);
                        $('.alert-edited').css('pointer-events', 'auto');
                        setTimeout(function () {
                            $('.alert-edited').css('opacity', 0);
                            $('.alert-edited').css('pointer-events', 'none');
                        }, 1000);
                        discountUpdate();
                    } else {
                        $('.alert-invalid-edit').css('opacity', 1);
                        $('.alert-invalid-edit').css('pointer-events', 'auto');
                        setTimeout(function () {
                            $('.alert-invalid-edit').css('opacity', 0);
                            $('.alert-invalid-edit').css('pointer-events', 'none');
                        }, 1000);
                    }
                }
            });
        }
    });

    $(".add-discount-open").click(function (e) {
        e.preventDefault();
        $(".add-discount").css({
            "opacity": 1,
            "pointer-events": "auto"
        });
    });

    $(".close-add-discount").click(function (e) {
        e.preventDefault();
        $('#new_discount_name').val('');
        $('#new_discount_percentage').val('');
        $(".add-discount").css({
            "opacity": 0,
            "pointer-events": "none"
        });
    });

    $("#add_new_discount").click(function (e) {
        e.preventDefault();
        // Get the form values
        var discountName = $("#new_discount_name").val();
        var discountPercentage = $("#new_discount_percentage").val();

        if (!isValidDiscount(discountPercentage)) {
            $('.alert-invalid-decimal').css('opacity', 1);
            $('.alert-invalid-decimal').css('pointer-events', 'auto');
            setTimeout(function () {
                $('.alert-invalid-decimal').css('opacity', 0);
                $('.alert-invalid-decimal').css('pointer-events', 'none');
            }, 1000);
        } else {
            $.ajax({
                url: "../ajax-url/add-discount.php",
                type: "POST",
                data: {
                    discountName: discountName,
                    discountPercentage: discountPercentage
                },
                success: function (response) {
                    console.log(response);
                    if (response === 'inserted') {
                        $('#new_discount_name').val('');
                        $('#new_discount_percentage').val('');
                        $(".add-discount").css({
                            "opacity": 0,
                            "pointer-events": "none"
                        });

                        $('.alert-inserted').css('opacity', 1);
                        $('.alert-inserted').css('pointer-events', 'auto');
                        setTimeout(function () {
                            $('.alert-inserted').css('opacity', 0);
                            $('.alert-inserted').css('pointer-events', 'none');
                        }, 1000);
                        discountUpdate();
                    } else if(response === 'exisiting'){
                        $('.alert-disc-exist').css('opacity', 1);
                        $('.alert-disc-exist').css('pointer-events', 'auto');
                        setTimeout(function () {
                            $('.alert-disc-exist').css('opacity', 0);
                            $('.alert-disc-exist').css('pointer-events', 'none');
                        }, 1000);
                    } else {
                        $('.alert-invalid-edit').css('opacity', 1);
                        $('.alert-invalid-edit').css('pointer-events', 'auto');
                        setTimeout(function () {
                            $('.alert-invalid-edit').css('opacity', 0);
                            $('.alert-invalid-edit').css('pointer-events', 'none');
                        }, 1000);
                    }
                },
                error: function (xhr, status, error) {
                    // Handle the error response here
                    console.error(error);
                }
            });
        }
    });


    function isValidDiscount(value) {
        var decimalRegex = /^\d+(\.\d{1,2})?$/;
        return decimalRegex.test(value);
    }
});