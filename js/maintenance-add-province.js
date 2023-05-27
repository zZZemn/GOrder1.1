$(document).ready(function () {
    $(document).on('click', '.btn-add-province', function (event) {
        event.preventDefault();
        var tableContainer = $(this).closest('.add-province-tr');
        var region_id = tableContainer.find('.region_id').val();
        var province = tableContainer.find('.txt-add-province').val();

        if (province.trim() !== '') {
            var tableContainer = $(this).closest('.table-container');
            $.ajax({
                url: '../ajax-url/add-province-process.php',
                method: 'POST',
                data: {
                    txt_add_province: province,
                    region_id: region_id
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
