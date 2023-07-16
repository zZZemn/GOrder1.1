$(document).ready(function () {
    const updateInventory = (search, subcat) => {
        $.ajax({
            type: "GET",
            url: "../server/inventory-update.php?search=" + search + '&subcat=' + subcat,
            dataType: "HTML",
            success: function (response) {
                console.log(response);
                $('#inventory_container').html(response);
            }
        });
    }



    updateInventory('', '');

    setInterval(() => {
        var search = $('#inv-search').val();
        var sub_cat = $('#subcat-select').val();
        updateInventory(search, sub_cat);
    }, 2000);

    $('#inv-search').keyup((e) => {
        e.preventDefault();
        var search = $('#inv-search').val();
        var sub_cat = $('#subcat-select').val();
        updateInventory(search, sub_cat);
    });

    $('#category-pick').on('change', (e) => {
        $('#subcat-select option:not(:first-child)').remove();
        var cat_id = $('#category-pick').val();
        $.ajax({
            type: "GET",
            url: "../process/get-subcat.php?id=" + cat_id,
            success: function (response) {
                if ($(response).is('option')) {
                    $('#subcat-select').append(response);
                }
            }
        });
    })

    $('#subcat-select').on('change', (e) => {
        var search = $('#inv-search').val();
        var sub_cat = $('#subcat-select').val();
        updateInventory(search, sub_cat);
    })
});