$(document).ready(function () {
  var search = $("#search-input").val();
  var subcat = $("#select2").val();
  var cat = $("#select1").val();

  function allproductsUpdate(search, subcat, cat) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("products-container").innerHTML =
          this.responseText;
      }
    };
    var url = `../server/all-products-update.php?search=${search}&cat=${cat}&sub_cat=${subcat}`;
    xhttp.open("GET", url, true);
    xhttp.send();
  }

  window.onload = allproductsUpdate(search, subcat, cat);

  $("#search-input").on("input", function () {
    var search = $(this).val();
    allproductsUpdate(search, subcat, cat);
  });

  $("#select1").change(function () {
    $("#search-input").val("");
    var cat = $(this).val();

    $.ajax({
      type: "POST",
      url: "../ajax-url/get-sub-cat.php",
      data: {
        cat: cat,
      },
      success: function (response) {
        console.log(response);
        var responseData = JSON.parse(response);
        var selectElement = $("#select2");
        selectElement.empty();
        selectElement.append("<option value='all'>All</option>");
        $.each(responseData, function (index, item) {
          var option = $("<option></option>");

          option.val(item.sub_cat_id);
          option.text(item.sub_cat);
          selectElement.append(option);
        });
      },
    });

    allproductsUpdate(search, subcat, cat);
  });

  $("#select2").change(function () {
    $("#search-input").val("");
    var subcat = $(this).val();
    allproductsUpdate(search, subcat, cat);
  });

  $(document).on("click", ".delete-product", function () {
    var product_id = $(this).data("product_id");

    $.ajax({
      url: "../ajax-url/get-product-details-delete.php",
      method: "GET",
      data: { product_id: product_id },
      success: function (response) {
        var data = JSON.parse(response);
        var productName = data[0];
        var productId = data[1];

        var modalTitle = "Delete " + productName;
        $(".modal-title").text(modalTitle);
        $("#delete-this-product").attr("data-product_id", productId);

        console.log(response);
        $("#myModal").modal("show");
      },
      error: function (xhr, status, error) {
        console.log(error);
      },
    });
  });

  let deleteProduct = (product_id) => {
    $.ajax({
      url: "../process/delete-product-process.php",
      method: "GET",
      data: { product_id: product_id },
      success: function (response) {
        if (response === "ok") {
          $(".product-deleted").css("opacity", 1);
          setTimeout(function () {
            $(".product-deleted").css("opacity", 0);
          }, 2000);
          $("#search-input").val("");
          allproductsUpdate(search, subcat, cat);
        } else {
          $(".deletion-unsuccessful").css("opacity", 1);
          setTimeout(function () {
            $(".deletion-unsuccessful").css("opacity", 0);
          }, 2000);
          $("#search-input").val("");
          allproductsUpdate(search, subcat, cat);
        }
        $("#myModal").modal("hide");
        $("#myModal").trigger("hidden.bs.modal");
      },
      error: function (xhr, status, error) {
        console.log(error);
      },
    });
  };

  $(document).on("click", "#delete-this-product", () => {
    var productId = $("#delete-this-product").attr("data-product_id");
    deleteProduct(productId);
  });

  $("#myModal").on("hidden.bs.modal", () => {
    $("#delete-this-product").attr("data-product_id", "");
  });

  $("#myModal").on("click", "#close-delete-this-product", () => {
    $("#myModal").modal("hide");
    $("#myModal").trigger("hidden.bs.modal");
  });
});
