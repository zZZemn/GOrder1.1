$(document).ready(function () {
  var region = $("#select-region").val();
  var province = $("#select-province").val();
  var municipality = $("#select-municipality").val();

  function loadXMLDoc(region, province, municipality) {
    $.ajax({
      url: "../server/address-update.php",
      method: "POST",
      data: {
        region: region,
        province: province,
        municipality: municipality,
      },
      success: function (response) {
        $("#address_container").html(response);
      },
      error: function (xhr, status, error) {
        console.error("Error occurred:", error);
      },
    });
  }

  loadXMLDoc(region, province, municipality);

  $("#select-region").change(() => {
    $("#select-province").val("");
    $("#select-municipality").val("");

    var region = $("#select-region").val();

    $.ajax({
      type: "POST",
      url: "../ajax-url/get-provinces.php",
      data: {
        regionID: region,
      },
      success: function (response) {
        var provinceData = JSON.parse(response);
        var selectElement = $("#select-province");
        selectElement.empty();
        selectElement.append("<option value='' disabled selected></option>");
        $.each(provinceData, function (index, item) {
          var option = $("<option></option>");
          option.val(item.provinceID);
          option.text(item.province);
          selectElement.append(option);
        });
        var region = $("#select-region").val();
        var province = $("#select-province").val();
        var municipality = $("#select-municipality").val();
        loadXMLDoc(region, province, municipality);
      },
    });
  });

  $("#select-province").change(() => {
    $("#select-municipality").val("");

    var province = $("#select-province").val();

    $.ajax({
      type: "POST",
      url: "../ajax-url/get-municipalities.php",
      data: {
        provinceID: province,
      },
      success: function (response) {
        var municipalityData = JSON.parse(response);
        var selectElement = $("#select-municipality");
        selectElement.empty();
        selectElement.append("<option value='' disabled selected></option>");
        $.each(municipalityData, function (index, item) {
          var option = $("<option></option>");
          option.val(item.municipalityID);
          option.text(item.municipality);
          selectElement.append(option);
        });
        var region = $("#select-region").val();
        var province = $("#select-province").val();
        var municipality = $("#select-municipality").val();
        loadXMLDoc(region, province, municipality);
      },
    });
  });

  $("#select-municipality").change(() => {
    var region = $("#select-region").val();
    var province = $("#select-province").val();
    var municipality = $("#select-municipality").val();
    loadXMLDoc(region, province, municipality);
  });

  $("#btn-add-region").click((e) => {
    e.preventDefault();
    var input_bgy = $("#txt-add-barangay").val();
    $.ajax({
      type: "POST",
      url: "../process/add-barangay.php",
      data: {
        bgy: input_bgy,
      },
      success: function (response) {
        if (response === "updated") {
          $("#txt-add-barangay").val("");
          $(".alert-add-address").css("opacity", "1");
          setTimeout(() => {
            $(".alert-add-address").css("opacity", "0");
          }, 2000);
          var region = $("#select-region").val();
          var province = $("#select-province").val();
          var municipality = $("#select-municipality").val();
          loadXMLDoc(region, province, municipality);
        }
      },
    });
  });

  //

  const editBGYClose = () => {
    $("#df").val("");
    $("#bgy-id").val("");
    $("#barangay-name").val("");

    $(".edit-bgy-container").css("display", "none");
  };

  $(document).on("click", "#edit-bgy", (e) => {
    e.preventDefault();
    var bgy_id = $(e.currentTarget).data("bgy_id");
    var bgy = $(e.currentTarget).data("bgy");
    var df = $(e.currentTarget).data("df");

    $("#df").val(df);
    $("#bgy-id").val(bgy_id);
    $("#barangay-name").text(bgy);

    $(".edit-bgy-container").css("display", "flex");
  });

  $("#close-edit-bgy-container").click((e) => {
    e.preventDefault();
    editBGYClose();
  });

  $("#save-df").click((e) => {
    e.preventDefault();
    var new_df = $("#df").val();
    var bgy_id = $("#bgy-id").val();

    $.ajax({
      type: "POST",
      url: "../process/edit-bgy-df.php",
      data: {
        new_df: new_df,
        bgy_id: bgy_id,
      },
      success: function (response) {
        if (response === "success") {
          $(".alert-df-edit-success").css("opacity", "1");
          setTimeout(() => {
            $(".alert-df-edit-success").css("opacity", "0");
          }, 2000);
          var region = $("#select-region").val();
          var province = $("#select-province").val();
          var municipality = $("#select-municipality").val();
          loadXMLDoc(region, province, municipality);
          editBGYClose();
        } else {
          $(".alert-df-edit-not-success").css("opacity", "1");
          setTimeout(() => {
            $(".alert-df-edit-not-success").css("opacity", "0");
          }, 2000);
          editBGYClose();
        }
      },
    });
  });

  $(document).on("click", "#disable-bgy", (e) => {
    e.preventDefault();
    var bgy_id = $(e.currentTarget).data("bgy_id");
    $.ajax({
      type: "POST",
      url: "../process/edit-bgy-df.php",
      data: {
        bgy_id: bgy_id,
        delete_this: true,
      },
      success: function (response) {
        if (response === "success") {
          $(".alert-add-disabled").css("opacity", "1");
          setTimeout(() => {
            $(".alert-add-disabled").css("opacity", "0");
          }, 2000);
          var region = $("#select-region").val();
          var province = $("#select-province").val();
          var municipality = $("#select-municipality").val();
          loadXMLDoc(region, province, municipality);
        }
      },
    });
  });
});
