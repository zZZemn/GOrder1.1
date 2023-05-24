$(document).ready(function() {
    $('.cat_name_edit_btn').click(function(event) {
        event.preventDefault();

        var row = $(this).closest('tr');
        var updatedValue = row.find('.cat_name_edit').val();

        // Check if the updated value is not null or empty
        if (updatedValue.trim() !== '') {
            var cat_id = $(this).attr('class').split(' ')[1];

            // Make sure to include the Toastr library in your project
            $.ajax({
                url: '../ajax-url/edit-cat.php',
                method: 'POST',
                data: {
                    updatedValue: updatedValue,
                    cat_id: cat_id
                },
                success: function(response) {
                    console.log('Category edited successfully');
                    $('.alert-cat').css('opacity', 1);

                    setTimeout(function() {
                        $('.alert-cat').css('opacity', 0);
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    console.log('An error occurred while editing the category');
                }
            });
        } else {
            console.log('Updated value is empty');
        }
    });

    $('.sub_cat_edit_btn').click(function(event) {
        event.preventDefault();

        var row = $(this).closest('tr');
        var updatedValue = row.find('.sub_cat_edit').val();

        // Check if the updated value is not null or empty
        if (updatedValue.trim() !== '') {
            var sub_cat_id = $(this).attr('class').split(' ')[1];

            $.ajax({
                url: '../ajax-url/edit-subcat.php',
                method: 'POST',
                data: {
                    updatedValue: updatedValue,
                    sub_cat_id: sub_cat_id
                },
                success: function(response) {
                    console.log('Subcategory edited successfully');
                    $('.alert-sub-cat').css('opacity', 1);

                    setTimeout(function() {
                        $('.alert-sub-cat').css('opacity', 0);
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    console.log('An error occurred while editing the subcategory');
                }
            });
        } else {
            console.log('Updated value is empty');
        }
    });
});
