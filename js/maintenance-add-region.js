$(document).ready(function () {
    $('#btn-add-region').click(function () {
        event.preventDefault();
        var region = $('#txt-add-region').val();

        if (region.trim() !== '') {
            $.ajax({
                url: '../ajax-url/add-region-process.php',
                method: 'POST',
                data: { txt_add_region: region },
                success: function (response) {
                    if (response === 'inserted') {
                        console.log(response);
                        $('#txt-add-region').val('');
                        $('.alert-region').css('opacity', 1);

                        setTimeout(function () {
                            $('.alert-region').css('opacity', 0);
                        }, 1000);
                    } else {
                        console.log(response);
                        $('.alert-region-failed ').css('opacity', 1);

                        setTimeout(function () {
                            $('.alert-region-failed ').css('opacity', 0);
                        }, 1000);
                    }
                },
                error: function (xhr, status, error) {
                    // Handle error
                    console.log(xhr.responseText);
                }
            });
        } else {
            $('.alert-region-failed ').css('opacity', 1);

            setTimeout(function () {
                $('.alert-region-failed ').css('opacity', 0);
            }, 1000);
        }
    });
});