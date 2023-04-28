$('#search-form').on('submit', function(e) {
        e.preventDefault(); // Prevent form from being submitted

        // Retrieve search value from input field
        var searchValue = $('#search-input').val();

        // Construct URL with search value as query parameter
        var url = 'products-allproducts.php?search=' + encodeURIComponent(searchValue);

        // Redirect to the constructed URL
        window.location.href = url;
    });
