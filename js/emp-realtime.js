$(document).ready(function () {
    function empUpdate() {
        var search = $('#search_emp').val();
        var filter = $('#emp_filter').val();
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("emp-container").innerHTML =
                    this.responseText;
            }
        };
        var url = "../server/emp-update.php?filter=" + filter + "&search=" + search;
        console.log(url);
        xhttp.open("GET", url, true);
        xhttp.send();
    }

    window.onload = empUpdate;

    $('#emp_filter').change(function () {
        setTimeout(empUpdate, 500);
    })

    $('#search_emp').on('input', function() {
        setTimeout(empUpdate, 500);
    });

})