$(document).ready(function () {

    $(".save-discount").click(function () {
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
                url: "../ajax-url/add-discount.php", // Replace with your actual form processing URL
                type: "POST",
                data: {
                    discountName: discountName,
                    discountPercentage: discountPercentage
                },
                success: function (response) {
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