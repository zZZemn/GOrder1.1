function loadXMLDoc() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("inventory_container").innerHTML =
                this.responseText;
        }
    };
    xhttp.open("GET", "../server/inventory-update.php", true);
    xhttp.send();
}
setInterval(function () {
    loadXMLDoc();
    // 1sec
}, 1000);
window.onload = loadXMLDoc;