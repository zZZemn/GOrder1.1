$(document).ready(function () {
    var selectedValue = $('#sales-filter').val();
    fetchSalesData(selectedValue);
});

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

function getSalesRT() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("sales-results-salesphp").innerHTML =
                this.responseText;
            }
        };
        xhttp.open("GET", "../server/sales-update.php", true);
        xhttp.send();
    };

window.onload = getSalesRT();

setInterval(function () {
    getSalesRT();
    // 1sec
}, 1000);