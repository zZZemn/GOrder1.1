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
                if (response == 'alert-act' || response == 'alert-deact') {
                    users_cust(cust_type, search);
                    $('.' + response).css('opacity', 1);
                    setTimeout(function () {
                        $('.' + response).css('opacity', 0);
                    }, 2000);
                }
            }
        });
    }

    const getCustDetails = (cust_id) => {
        $.ajax({
            type: "POST",
            url: "../process/customer-get-details.php",
            data: { cust_id: cust_id },
            success: function (response) {
                var data = JSON.parse(response);
                console.log(data);
            }
        });
    }

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

    //editing cust
    $(document).on('click', '#edit-customer', (e) => {
        e.preventDefault();
        var cust_id = $(e.currentTarget).attr('data-cust_id');
        getCustDetails(cust_id);

        $('#frm-edit-cust').css('display', 'flex');
    })

    $('#close-frm-edit-cust').click((e)=>{
        e.preventDefault();
        $('#frm-edit-cust').css('display', 'none');
    })
    users_cust(cust_type, search);
});