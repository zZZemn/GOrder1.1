$(document).ready(function () {
    $(document).on('click', '.btn-add-municipality', function (event) {
        event.preventDefault();
        var tableContainer = $(this).closest('.add-municipality');
        var province_id = tableContainer.find('.province_id').val();
        var municipality = tableContainer.find('.txt-add-municipality').val();

        if (municipality.trim() !== '') {
            $.ajax({
                url: '../ajax-url/add-municipality-process.php',
                method: 'POST',
                data: {
                    txt_add_municipality: municipality,
                    province_id: province_id
                },
                success: function (response) {
                    if (response === 'inserted') {
                        $('.txt-add-municipality').val('');
                        $('.alert-municipality').css('opacity', 1);

                        setTimeout(function () {
                            $('.alert-municipality').css('opacity', 0);
                        }, 1000);
                    } else {
                        $('.alert-municipality-failed').css('opacity', 1);

                        setTimeout(function () {
                            $('.alert-municipality-failed').css('opacity', 0);
                        }, 1000);
                    }
                },
                error: function (xhr, status, error) {
                    // Handle error
                    console.log(xhr.responseText);
                }
            });
        } else {
            $('.alert-municipality-failed').css('opacity', 1);

            setTimeout(function () {
                $('.alert-municipality-failed').css('opacity', 0);
            }, 1000);
        }
    });
});