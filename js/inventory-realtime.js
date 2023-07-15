$(document).ready(function () {
    const updateInventory = (search) => {
        $.ajax({
            type: "GET",
            url: "../server/inventory-update.php?search=" + search,
            dataType: "HTML",
            success: function (response) {
                $('#inventory_container').html(response);
            }
        });
    }

    updateInventory('');

    setInterval(() => {
        var search = $('#inv-search').val();
        updateInventory(search);
    }, 2000);

    $('#inv-search').keyup((e) => {
        e.preventDefault();
        var search = $('#inv-search').val();
        updateInventory(search);
    });
});