$(document).ready(function () {
    var cust_type = $('#cust_filter').val();
    var search = $('#search_cust').val();

    const users_cust = (cust_type, search) => {
        $.ajax({
            type: "POST",
            url: "../server/customer-update.php",
            data: {
                cust_type: cust_type,
                search: search
            },
            success: function (response) {
                $('#customer-container').html(response);
            }
        });
    }

    const changeStatus = (cust_id, new_stats) => {
        var cust_type = $('#cust_filter').val();
        var search = $('#search_cust').val();
        $.ajax({
            type: "POST",
            url: "../process/users-customer-change-status-process.php",
            data: {
                cust_id: cust_id,
                new_stats: new_stats
            },
            success: function (response) {
                console.log(response);
                if (response !== 'not') {
                    users_cust(cust_type, search);
                    $('.'+response).css('opacity', 1);
                    setTimeout(function () {
                        $('.'+response).css('opacity', 0);
                    }, 2000);
                }
            }
        });
    }

    users_cust(cust_type, search);

    $('#cust_filter').on('change', () => {
        $('#search_cust').val('');
        var cust_type = $('#cust_filter').val();
        users_cust(cust_type, '');
    })

    $('#search_cust').keyup((e) => {
        $('#cust_filter').val('');
        var search = $('#search_cust').val();
        users_cust('', search);
    });

    $(document).on('click', '#change-status', (e) => {
        e.preventDefault();

        var cust_id = $(e.currentTarget).attr('data-cust_id');
        var new_stats = $(e.currentTarget).attr('data-new_status');

        changeStatus(cust_id, new_stats);
    })

});