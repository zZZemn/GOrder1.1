$(document).ready(function () {
    $(document).on('click', '.btn-add-municipality', function (event) {
        event.preventDefault();
        var tableContainer = $(this).closest('.add-municipality-tr');
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
                        $('.txt-add-province').val('');
                        $('.alert-province').css('opacity', 1);

                        setTimeout(function () {
                            $('.alert-province').css('opacity', 0);
                        }, 1000);
                    } else {
                        $('.alert-province-failed').css('opacity', 1);

                        setTimeout(function () {
                            $('.alert-province-failed').css('opacity', 0);
                        }, 1000);
                    }
                },
                error: function (xhr, status, error) {
                    // Handle error
                    console.log(xhr.responseText);
                }
            });
        } else {
            $('.alert-province-failed').css('opacity', 1);

            setTimeout(function () {
                $('.alert-province-failed').css('opacity', 0);
            }, 1000);
        }
    });
});
