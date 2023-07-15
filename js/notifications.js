$(document).ready(function () {
    function notifications() {
        $.ajax({
            url: "../server/notifications.php",
            success: function (response) {
                $('#notification-dropdown-container').html(response);
            }
        });
    }

    setInterval(function () {
        notifications();
    }, 1000);

    notifications();

    function notificationsCount() {
        $.ajax({
            url: "../server/notifications-count.php",
            success: function (response) {
                $('#notifications-count').html(response);
            }
        });
    }

    setInterval(function () {
        notificationsCount();
    }, 1000);

    notificationsCount();
});
