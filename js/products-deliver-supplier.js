// Get references to the select elements
var supplier = document.getElementById('supplier-select');
var sort_by_del_price = document.getElementById('by-filtering');

// Add event listener for changes in select1
supplier.addEventListener('change', function() {
    // Get the selected value from select1
    var supplierValue = supplier.value;
    var sort_by = sort_by_del_price.value;

    window.location.href = "products-deliver.php?supplier=" + supplierValue + "&sort_by=" + sort_by;
});

// Add event listener for changes in select2
sort_by_del_price.addEventListener('change', function() {
    // Get the selected value from select2
    var supplierValue = supplier.value;
    var sort_by = sort_by_del_price.value;

    window.location.href = "products-deliver.php?supplier=" + supplierValue + "&sort_by=" + sort_by;
});