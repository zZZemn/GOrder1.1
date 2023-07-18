$(document).ready(function () {
    var cust_type = $('#cust_filter').val();
    var search = $('#search_cust').val();

    const users_cust = (cust_type, search) => {
        $.ajax({
            type: "POST",
            url: "../server/customer-update.php",
            data: {
                cust_type: cust_type,
                search: search
            },
            success: function (response) {
                $('#customer-container').html(response);
            }
        });
    }

    const changeStatus = (cust_id, new_stats) => {
        var cust_type = $('#cust_filter').val();
        var search = $('#search_cust').val();
        $.ajax({
            type: "POST",
            url: "../process/users-customer-change-status-process.php",
            data: {
                cust_id: cust_id,
                new_stats: new_stats
            },
            success: function (response) {
                console.log(response);
                if (response == 'alert-act' || response == 'alert-deact') {
                    users_cust(cust_type, search);
                    $('.' + response).css('opacity', 1);
                    setTimeout(function () {
                        $('.' + response).css('opacity', 0);
                    }, 2000);
                }
            }
        });
    }

    const updateAddress = (region, province, municipality, barangay) => {
        $.ajax({
            type: "POST",
            url: "../ajax-url/get-provinces.php",
            data: {
                regionID: region
            },
            success: function (response) {
                var responseData = JSON.parse(response);
                var selectElement = $('#province');
                selectElement.empty();
                $.each(responseData, function (index, item) {
                    var option = $('<option></option>');

                    option.val(item.provinceID);
                    option.text(item.province);
                    selectElement.append(option);
                });

                $('#province').val(province);
                $.ajax({
                    type: "POST",
                    url: "../ajax-url/get-municipalities.php",
                    data: { provinceID: province },
                    success: function (response) {
                        var responseData = JSON.parse(response);
                        var selectElement = $('#municipality');
                        selectElement.empty();
                        $.each(responseData, function (index, item) {
                            var option = $('<option></option>');

                            option.val(item.municipalityID);
                            option.text(item.municipality);
                            selectElement.append(option);
                        });

                        $('#municipality').val(municipality);

                        $.ajax({
                            type: "POST",
                            url: "../ajax-url/get-barangay.php",
                            data: { municipalityID: municipality },
                            success: function (response) {
                                var responseData = JSON.parse(response);
                                var selectElement = $('#barangay');
                                selectElement.empty();
                                $.each(responseData, function (index, item) {
                                    var option = $('<option></option>');

                                    option.val(item.barangayID);
                                    option.text(item.barangay);
                                    selectElement.append(option);
                                });
                                $('#barangay').val(barangay);
                            }
                        });
                    }
                });

            }
        });
    }

    const getCustDetails = (cust_id) => {
        $.ajax({
            type: "POST",
            url: "../process/customer-get-details.php",
            data: { cust_id: cust_id },
            success: function (response) {
                var data = JSON.parse(response);
                console.log(data);
                //../img/userprofile/E.png
                $('#cust_id_hidden').val(data.cust_id);
                $('#edit-cust-title').text('Edit Customer ' + data.cust_id);
                $('#cust-photo').attr('src', '../img/userprofile/' + data.picture);
                $('#fname').val(data.first_name);
                $('#lname').val(data.last_name);
                $('#mi').val(data.middle_initial);
                $('#suffix').val(data.suffix);
                $('#sex').val(data.sex);
                $('#birthday').val(data.bday);
                $('#discount-type').val(data.discount_type);
                $('#username').val(data.username)
                $('#contact-no').val(data.contact_no);
                $('#email').val(data.email);
                //set address
                $('#unit').val(data.unit_st);
                $('#region').val(data.address.region_id);

                var region = data.address.region_id;
                var province = data.address.province_id;
                var municipality = data.address.municipality_id;
                var barangay = data.address.barangay_id;
                updateAddress(region, province, municipality, barangay);

                $('#frm-edit-cust').css('display', 'flex');
            }
        });
    }

    const onChangeRegion = (region_id) => {
        $('#province').empty();
        $('#municipality').empty();
        $('#barangay').empty();

        $.ajax({
            type: "POST",
            url: "../ajax-url/get-provinces.php",
            data: { regionID: region_id },
            success: function (response) {
                var responseData = JSON.parse(response);
                var selectElement = $('#province');
                selectElement.empty();
                selectElement.append('<option></option>');
                $.each(responseData, function (index, item) {
                    var option = $('<option></option>');

                    option.val(item.provinceID);
                    option.text(item.province);
                    selectElement.append(option);
                });
            }
        });
    }

    const onChangeProvince = (province_id) => {
        $('#municipality').empty();
        $('#barangay').empty();

        $.ajax({
            type: "POST",
            url: "../ajax-url/get-municipalities.php",
            data: { provinceID: province_id },
            success: function (response) {
                var responseData = JSON.parse(response);
                var selectElement = $('#municipality');
                selectElement.empty();
                selectElement.append('<option></option>');
                $.each(responseData, function (index, item) {
                    var option = $('<option></option>');

                    option.val(item.municipalityID);
                    option.text(item.municipality);
                    selectElement.append(option);
                });
            }
        });
    }

    const onChangeMunicipality = (municipality_id) => {
        $('#barangay').empty();

        $.ajax({
            type: "POST",
            url: "../ajax-url/get-barangay.php",
            data: { municipalityID: municipality_id },
            success: function (response) {
                var responseData = JSON.parse(response);
                var selectElement = $('#barangay');
                selectElement.empty();
                selectElement.append('<option></option>');
                $.each(responseData, function (index, item) {
                    var option = $('<option></option>');

                    option.val(item.barangayID);
                    option.text(item.barangay);
                    selectElement.append(option);
                });
            }
        });
    }

    const validateInputs = (fname, lname, barangay, unit, cust_id) => {
        if (fname.trim() === '') {
            $('.enter-fname').css('opacity', 1);
            setTimeout(function () {
                $('.enter-fname').css('opacity', 0);
            }, 2000);
            return false;
        }

        if (lname.trim() === '') {
            $('.enter-lname').css('opacity', 1);
            setTimeout(function () {
                $('.enter-lname').css('opacity', 0);
            }, 2000);
            return false;
        }

        if (barangay.trim() === '') {
            $('.enter-bgy').css('opacity', 1);
            setTimeout(function () {
                $('.enter-bgy').css('opacity', 0);
            }, 2000);
            return false;
        }

        if (unit.trim() === '') {
            $('.enter-unit').css('opacity', 1);
            setTimeout(function () {
                $('.enter-unit').css('opacity', 0);
            }, 2000);
            return false;
        }

        if (cust_id.trim() === '') {
            return false;
        }

        return true;
    }

    const closeEditFRM = () => {
        $('#frm-edit-cust').css('display', 'none');
    }

    $('#cust_filter').on('change', () => {
        $('#search_cust').val('');
        var cust_type = $('#cust_filter').val();
        users_cust(cust_type, '');
    })

    $('#search_cust').keyup((e) => {
        $('#cust_filter').val('');
        var search = $('#search_cust').val();
        users_cust('', search);
    });

    $(document).on('click', '#change-status', (e) => {
        e.preventDefault();

        var cust_id = $(e.currentTarget).attr('data-cust_id');
        var new_stats = $(e.currentTarget).attr('data-new_status');

        changeStatus(cust_id, new_stats);
    })

    //editing cust
    $(document).on('click', '#edit-customer', (e) => {
        e.preventDefault();
        var cust_id = $(e.currentTarget).attr('data-cust_id');
        getCustDetails(cust_id);
    })

    $('#close-frm-edit-cust').click((e) => {
        e.preventDefault();
        closeEditFRM();
    })

    $('#region').change(() => {
        var region = $('#region').val();
        onChangeRegion(region);
    })

    $('#province').change(() => {
        var province = $('#province').val();
        onChangeProvince(province);
    })

    $('#municipality').change(() => {
        var municipality = $('#municipality').val();
        onChangeMunicipality(municipality);
    })

    $('#btn-submit').click((e) => {
        e.preventDefault();
        var cust_type = $('#cust_filter').val();
        var search = $('#search_cust').val();

        // Get the values from the input fields
        var cust_id = $('#cust_id_hidden').val();
        var fname = $('#fname').val();
        var lname = $('#lname').val();
        var mi = $('#mi').val();
        var suffix = $('#suffix').val();
        var sex = $('#sex').val();
        var birthday = $('#birthday').val();
        var discountType = $('#discount-type').val();
        var barangay = $('#barangay').val();
        var unit = $('#unit').val();
        var username = $('#username').val();
        var email = $('#email').val();
        var contactNo = $('#contact-no').val();

        if (validateInputs(fname, lname, barangay, unit, cust_id)) {
            $.ajax({
                type: "POST",
                url: "../process/edit-cust-process.php",
                data: {
                    cust_id: cust_id,
                    fname: fname,
                    lname: lname,
                    mi: mi,
                    suffix: suffix,
                    sex: sex,
                    birthday: birthday,
                    discountType: discountType,
                    barangay: barangay,
                    unit: unit,
                    username: username,
                    email: email,
                    contactNo: contactNo
                },
                success: function (response) {
                    if (response === 'edited') {
                        users_cust(cust_type, search);
                        $('.alert-edited').css('opacity', 1);
                        setTimeout(function () {
                            $('.alert-edited').css('opacity', 0);
                        }, 2000);
                        closeEditFRM();
                    } else {
                        users_cust(cust_type, search);
                        $('.alert-not-edited').css('opacity', 1);
                        setTimeout(function () {
                            $('.alert-not-edited').css('opacity', 0);
                        }, 2000);
                        closeEditFRM();
                    }
                }
            });

        }
    });

    users_cust(cust_type, search);
});