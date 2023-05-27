function loadXMLDoc() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("address_container").innerHTML =
                this.responseText;
        }
    };
    xhttp.open("GET", "../server/address-update.php", true);
    xhttp.send();
}

window.onload = loadXMLDoc;

$('#btn-add-region').click(function () {
    setTimeout(loadXMLDoc, 500);
})

$(document).on('click', '.btn-add-province', function () {
    setTimeout(loadXMLDoc, 500);
})


$(document).on('click', '.btn-add-municipality', function (event) {
    setTimeout(loadXMLDoc, 500);
})