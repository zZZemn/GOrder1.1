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
        console.log(response);
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
    console.log('clicked');
    var input_bgy = $("#txt-add-barangay").val();
    $.ajax({
      type: "POST",
      url: "../process/add-barangay.php",
      data: {
        bgy: input_bgy,
      },
      success: function (response) {
        if (response === "updated") {
          $("#txt-add-barangay").val('');
          var region = $("#select-region").val();
          var province = $("#select-province").val();
          var municipality = $("#select-municipality").val();
          loadXMLDoc(region, province, municipality);
        } else {
          console.log(response);
        }
      },
    });
  });
});
