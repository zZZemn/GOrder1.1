$(document).ready(function () {
    var selectedValue = $('#sales-filter').val();
    var search = $('#txt-return-search').val();
    fetchSalesData(selectedValue, search);

    $('#sales-filter').on('change', function () {
        $('#txt-return-search').val('');
        var selectedValue = $(this).val();
        fetchSalesData(selectedValue, '');

        var timeDateTh = $('.time-date');

        if (selectedValue === 'today') {
            timeDateTh.text('Time');
        } else if (selectedValue === 'this-week') {
            timeDateTh.text('Date');
        }
    });

    function fetchSalesData(selectedValue, search) {
        $.ajax({
            url: '../ajax-url/pos-get-sales.php',
            method: 'POST',
            data: {
                value: selectedValue,
                search: search
            },
            success: function (response) {
                // Update the sales-results tbody with the fetched data
                $('#sales-results').html(response);
            },
            error: function (xhr, status, error) {
                // Handle the error if any
                console.log(error);
            }
        });
    }

    $('#txt-return-search').keyup((e) => {
        var search = $('#txt-return-search').val();
        fetchSalesData(selectedValue, search)
    });
});