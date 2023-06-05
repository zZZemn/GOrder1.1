$(document).ready(function () {
    var today = new Date().toISOString().split('T')[0];
    $('#sales_date').val(today);

    function loadXMLDoc() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("table-response-container").innerHTML = this.responseText;
            }
        };

        var date = $('#sales_date').val();
        var url = "../server/return-update.php?id=" + encodeURIComponent(date);
        xhttp.open("GET", url, true);
        xhttp.send();
    }

    setInterval(function () {
        loadXMLDoc();
    }, 1000);
    window.onload = loadXMLDoc;


    });