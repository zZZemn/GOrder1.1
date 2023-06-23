$(document).ready(function () {
    var selectedValue = $('#sales-filter').val();
    fetchSalesData(selectedValue);

    $('#sales-filter').on('change', function () {
        var selectedValue = $(this).val();
        fetchSalesData(selectedValue);

        var timeDateTh = $('.time-date');

        if (selectedValue === 'today') {
            timeDateTh.text('Time');
        } else if (selectedValue === 'this-week') {
            timeDateTh.text('Date');
        }
    });

    function fetchSalesData(selectedValue) {
        $.ajax({
            url: '../ajax-url/pos-get-sales.php',
            method: 'POST',
            data: { value: selectedValue },
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
});