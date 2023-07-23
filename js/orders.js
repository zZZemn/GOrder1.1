function loadXMLDoc() {

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("orders_result").innerHTML =
                this.responseText;
        }
    };
    var selectedValue = $('#orders_filter').val();
    var url = "../server/orders-update.php?filter=" + encodeURIComponent(selectedValue);
    xhttp.open("GET", url, true);
    xhttp.send();
}

window.onload = loadXMLDoc;


setInterval(function () {
    loadXMLDoc();
}, 3000);

$('#orders_filter').change(function () {
    loadXMLDoc();
});





// $('#btn-add-region').click(function () {
//     setTimeout(loadXMLDoc, 500);
// })

// $(document).on('click', '.btn-add-province', function () {
//     setTimeout(loadXMLDoc, 500);
// })


// $(document).on('click', '.btn-add-municipality', function () {
//     setTimeout(loadXMLDoc, 500);
// })

// $(document).on('click', '.btn-add-barangay', function () {
//     setTimeout(loadXMLDoc, 500);
// })

// $(document).ready(function() {
//     // Make an AJAX request to fetch the sales data for the initial selected value
//     var selectedValue = $('#sales-filter').val();
//     fetchSalesData(selectedValue);
// });

// $('#sales-filter').on('change', function() {
//     var selectedValue = $(this).val();
//     fetchSalesData(selectedValue);
// });

// function fetchSalesData(selectedValue) {
//     $.ajax({
//         url: '../ajax-url/pos-get-sales.php',
//         method: 'POST',
//         data: { value: selectedValue },
//         success: function(response) {
//             // Update the sales-results tbody with the fetched data
//             $('#sales-results').html(response);
//         },
//         error: function(xhr, status, error) {
//             // Handle the error if any
//             console.log(error);
//         }
//     });
// }