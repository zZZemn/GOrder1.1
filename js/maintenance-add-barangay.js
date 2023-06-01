$(document).ready(function () {
    $(document).on('click', '.btn-add-barangay', function (event) {
        console.log('clicked');
        event.preventDefault();
        var tableContainer = $(this).closest('.add-barangay');
        var municipality_id = tableContainer.find('.municipality_id').val();
        var df = parseFloat(tableContainer.find('.txt-df').val());
        var barangay = tableContainer.find('.txt-add-barangay').val();

        if (barangay.trim() !== '') {
            $.ajax({
                url: '../ajax-url/add-barangay-process.php',
                method: 'POST',
                data: {
                    municipality_id: municipality_id,
                    barangay: barangay,
                    df: df
                },
                success: function (response) {
                    if (response === 'inserted') {
                        console.log(df);
                        $('.txt-add-barangay').val('');
                        $('.alert-barangay').css('opacity', 1);

                        setTimeout(function () {
                            $('.alert-barangay').css('opacity', 0);
                        }, 1000);
                    } else {
                        console.log(response);
                        $('.alert-barangay-failed').css('opacity', 1);

                        setTimeout(function () {
                            $('.alert-barangay-failed').css('opacity', 0);
                        }, 1000);
                    }
                },
                error: function (xhr, status, error) {
                    // Handle error
                    console.log(xhr.responseText);
                }
            });
        } else {
            $('.alert-barangay-failed').css('opacity', 1);

            setTimeout(function () {
                $('.alert-barangay-failed').css('opacity', 0);
            }, 1000);
        }
    });
});