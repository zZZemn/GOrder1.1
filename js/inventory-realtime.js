$(document).ready(function () {
  const updateInventory = (search, subcat) => {
    $.ajax({
      type: "GET",
      url:
        "../server/inventory-update.php?search=" + search + "&subcat=" + subcat,
      dataType: "HTML",
      success: function (response) {
        $("#inventory_container").html(response);
      },
    });
  };

  updateInventory("", "");

  setInterval(() => {
    var search = $("#inv-search").val();
    var sub_cat = $("#subcat-select").val();
    updateInventory(search, sub_cat);
  }, 2000);

  $("#inv-search").keyup((e) => {
    e.preventDefault();
    var search = $("#inv-search").val();
    var sub_cat = $("#subcat-select").val();
    updateInventory(search, sub_cat);
  });

  $("#category-pick").on("change", (e) => {
    $("#subcat-select option:not(:first-child)").remove();
    var cat_id = $("#category-pick").val();
    $.ajax({
      type: "GET",
      url: "../process/get-subcat.php?id=" + cat_id,
      success: function (response) {
        if ($(response).is("option")) {
          $("#subcat-select").append(response);
        }
      },
    });
  });

  $("#subcat-select").on("change", (e) => {
    var search = $("#inv-search").val();
    var sub_cat = $("#subcat-select").val();
    updateInventory(search, sub_cat);
  });

  //   dispose

  const disposeINV = (inv_id) => {
    $.ajax({
      type: "POST",
      url: "../process/dispose-product-process.php",
      data: {
        inv_id: inv_id,
      },
      success: function (response) {
        console.log(response);
        if (response === "disposed") {
          $(".product-disposed").css("opacity", 1);
          setTimeout(function () {
            $(".product-disposed").css("opacity", 0);
          }, 2000);
        } else {
          $(".product-not-disposed").css("opacity", 1);
          setTimeout(function () {
            $(".product-not-disposed").css("opacity", 0);
          }, 2000);
        }
        $("#myModal").modal("hide");
        $("#myModal").trigger("hidden.bs.modal");
      },
    });
  };

  $(document).on("click", "#dispose", (e) => {
    e.preventDefault();
    var inv_id = $(e.currentTarget).attr("data-inv_id");
    console.log(inv_id);
    console.log("he");

    var modalTitle = "Dispose " + inv_id;
    $(".modal-title").text(modalTitle);
    $("#delete-this-product").attr("data-inv_id", inv_id);
    $("#myModal").modal("show");
  });

  $(document).on("click", "#delete-this-product", () => {
    var inv_id = $("#delete-this-product").attr("data-inv_id");
    disposeINV(inv_id);
  });

  $("#myModal").on("hidden.bs.modal", () => {
    $("#delete-this-product").attr("data-product_id", "");
  });

  $("#myModal").on("click", "#close-delete-this-product", () => {
    $("#myModal").modal("hide");
    $("#myModal").trigger("hidden.bs.modal");
  });
});
