document.addEventListener("DOMContentLoaded", function () {
    function loadXMLDoc() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("status_container").innerHTML = this.responseText;
            }
        };

        var id = $('#transaction_id').val();
        console.log(id);
        var url = "../server/order-status-update.php?id=" + encodeURIComponent(id);
        xhttp.open("GET", url, true);
        xhttp.send();
    }

    window.onload = loadXMLDoc;

    setInterval(function () {
        loadXMLDoc();
    }, 1000);
});


$('#update-order-status').change(function () {
    var new_status = $('#update-order-status').val();
    var transaction_id = $('#transaction_id').val();

    $.ajax({
        url: '../ajax-url/order-status-update.php',
        data: {
            new_status: new_status,
            transaction_id: transaction_id
        },
        type: 'POST',
        success: function (data) {
            console.log(data);
        },
    });
})