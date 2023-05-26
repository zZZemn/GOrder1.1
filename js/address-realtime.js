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

document.getElementById("btn-add-region").addEventListener("click", function () {
    loadXMLDoc();
});

document.addEventListener("keydown", function (event) {
    if (event.key === "Enter") {
        loadXMLDoc();
    }
});