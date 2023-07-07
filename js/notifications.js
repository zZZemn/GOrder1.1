$(document).ready(function () { 

    function notifications() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                $('#notification-dropdown-container').html(this.responseText);
            }
        };
        xhttp.open("GET", "../server/notifications.php", true);
        xhttp.send();
    }
    setInterval(function () {
        notifications();
    }, 1000);
    window.onload = notifications;



    function notificationsCount() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                $('#notifications-count').html(this.responseText);
            }
        };
        xhttp.open("GET", "../server/notifications-count.php", true);
        xhttp.send();
    }
    setInterval(function () {
        notifications();
    }, 1000);
    window.onload = notificationsCount;
})