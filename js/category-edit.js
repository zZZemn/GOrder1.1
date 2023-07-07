$(document).ready(function () {
    function categoryDisplay() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("category-container").innerHTML =
                    this.responseText;
            }
        };
        xhttp.open("GET", "../server/category-update.php", true);
        xhttp.send();
    }

    window.onload = categoryDisplay();

    $(document).on('click', '.cat_name_edit_btn', function (event) {
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
                success: function (response) {
                    if (response === 'edited') {
                        $('.alert-cat').css('opacity', 1);

                        setTimeout(function () {
                            $('.alert-cat').css('opacity', 0);
                        }, 1000);
                        categoryDisplay();
                    } else {
                        $('.alert-cat-not-edit').css('opacity', 1);
                        setTimeout(function () {
                            $('.alert-cat-not-edit').css('opacity', 0);
                        }, 1000);
                    }
                },
                error: function (xhr, status, error) {
                    console.log('An error occurred while editing the category');
                }
            });
        } else {
            $('.alert-invalid-cat').css('opacity', 1);
            setTimeout(function () {
                $('.alert-invalid-cat').css('opacity', 0);
            }, 1000);
        }
    });

    $(document).on('click', '.sub_cat_edit_btn', function (event) {
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
                success: function (response) {
                    if (response === 'edited') {
                        $('.alert-sub-cat').css('opacity', 1);

                        setTimeout(function () {
                            $('.alert-sub-cat').css('opacity', 0);
                        }, 1000);
                        categoryDisplay();
                    } else {
                        $('.alert-invalid-subcat').css('opacity', 1);

                        setTimeout(function () {
                            $('.alert-invalid-subcat').css('opacity', 0);
                        }, 1000);
                    }

                },
                error: function (xhr, status, error) {
                    console.log('An error occurred while editing the subcategory');
                }
            });
        } else {
            $('.alert-invalid-subcat').css('opacity', 1);
            setTimeout(function () {
                $('.alert-invalid-subcat').css('opacity', 0);
            }, 1000);
        }
    });

    $('#btn-add-cat').click(function (event) {
        event.preventDefault();

        var new_cat = $('#txt-add-cat').val();

        if (new_cat.trim() !== '') {

            $.ajax({
                url: '../process/add-cat-process.php',
                method: 'POST',
                data: {
                    new_cat: new_cat
                },
                success: function (response) {
                    if (response === 'added') {
                        $('.alert-cat-added').css('opacity', 1);
                        setTimeout(function () {
                            $('.alert-cat-added').css('opacity', 0);
                        }, 1000);
                        categoryDisplay();
                        $('#txt-add-cat').val('');
                    } else {
                        $('.alert-cat-not-added').css('opacity', 1);
                        setTimeout(function () {
                            $('.alert-cat-not-added').css('opacity', 0);
                        }, 1000);
                    }
                },
                error: function (xhr, status, error) {
                    console.log('An error occurred while editing the subcategory');
                }
            });
        } else {
            $('.alert-invalid-cat').css('opacity', 1);
            setTimeout(function () {
                $('.alert-invalid-cat').css('opacity', 0);
            }, 1000);
        }
    });

    $(document).on('click', '#btn-add-subcat', function (event) {
        event.preventDefault();

        var row = $(this).closest('tr');
        var new_sub_cat = row.find('#add_sub_cat').val();
        var cat_id = row.find('#cat-id').val();

        if (new_sub_cat.trim() !== '') {
            $.ajax({
                url: '../process/add-subcat-process.php',
                method: 'POST',
                data: {
                    cat_id: cat_id,
                    new_sub_cat: new_sub_cat
                },
                success: function (response) {
                    if (response === 'added') {
                        $('.alert-sub-cat-added').css('opacity', 1);
                        setTimeout(function () {
                            $('.alert-sub-cat-added').css('opacity', 0);
                        }, 1000);
                        categoryDisplay();
                        $('#add_sub_cat').val('');
                    } else {
                        $('.alert-sub-cat-not-added').css('opacity', 1);
                        setTimeout(function () {
                            $('.alert-sub-cat-not-added').css('opacity', 0);
                        }, 1000);
                    }
                },
                error: function (xhr, status, error) {
                    console.log('An error occurred while editing the subcategory');
                }
            });
        } else {
            $('.alert-invalid-subcat').css('opacity', 1);
            setTimeout(function () {
                $('.alert-invalid-subcat').css('opacity', 0);
            }, 1000);
        }
    });

});
