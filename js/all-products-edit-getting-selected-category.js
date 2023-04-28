$(document).ready(function () {
    var cat_id = $('#category-select').val();
    var product_id = $('#product_id').val();

    // Make the AJAX request using the initial value of the category select
    $.ajax({
        url: '../process/get-cur-sub-categories.php',
        data: { cat_id: cat_id, product_id: product_id },
        type: 'POST',
        success: function (data) {
            console.log(data);
            $('#sub-category-select').html(data);
        }
    });
    
    // Update the sub-categories whenever the category select changes
    $('#category-select').on('change', function () {
        var cat_id = $(this).val();
        $.ajax({
            url: '../process/get-cur-sub-categories.php',
            data: { cat_id: cat_id, product_id: product_id },
            type: 'POST',
            success: function (data) {
                console.log(data);
                $('#sub-category-select').html(data);
            }
        });
    });
});

