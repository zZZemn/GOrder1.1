$(document).ready(function () {
    var region = $('#select-region').val();
    var province = $('#select-province').val();
    var municipality = $('#select-municipality').val();

    function loadXMLDoc(region, province, municipality) {
      $.ajax({
        url: "../server/address-update.php",
        method: "POST",
        data: {
            region: region,
            province: province,
            municipality: 'MUNI_64954378'
        },
        success: function (response) {
          $("#address_container").html(response);
          console.log(response);
        },
        error: function (xhr, status, error) {
          console.error("Error occurred:", error);
        }
      });
    }

    loadXMLDoc(region, province, municipality);
  });