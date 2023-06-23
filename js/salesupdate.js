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