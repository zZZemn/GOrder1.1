// Get references to the select elements
var select1 = document.getElementById('select1');
var select2 = document.getElementById('select2');

// Add event listener for changes in select1
select1.addEventListener('change', function() {
    // Get the selected value from select1
    var catId = select1.value;
    var subCatId = select2.value;

    window.location.href = "products-allproducts.php?CAT_ID=" + catId + "&SUB_CAT_ID=" + subCatId;
});

// Add event listener for changes in select2
select2.addEventListener('change', function() {
    // Get the selected value from select2
    var catId = select1.value;
    var subCatId = select2.value;

    window.location.href = "products-allproducts.php?CAT_ID=" + catId + "&SUB_CAT_ID=" + subCatId;
});