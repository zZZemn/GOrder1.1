$(document).ready(function () {
    $('#region').on('change', function () {
        var regionID = $(this).val();

        $.ajax({
            url: 'ajax-url/get-provinces.php',
            type: 'POST',
            data: { regionID: regionID },
            success: function (data) {
                var provinces = JSON.parse(data);

                $('#province').empty();

                $('#province').append($('<option>', {
                    value: "",
                    text: ""
                }));

                for (var i = 0; i < provinces.length; i++) {
                    $('#province').append($('<option>', {
                        value: provinces[i].provinceID,
                        text: provinces[i].province
                    }));
                }
            }
        });
    });

    $('#province').on('change', function () {
        var provinceID = $(this).val();

        $.ajax({
            url: 'ajax-url/get-municipalities.php',
            type: 'POST',
            data: { provinceID: provinceID },
            success: function (data) {
                var municipalities = JSON.parse(data);

                $('#municipality').empty();

                $('#municipality').append($('<option>', {
                    value: "",
                    text: ""
                }));

                for (var i = 0; i < municipalities.length; i++) {
                    $('#municipality').append($('<option>', {
                        value: municipalities[i].municipalityID,
                        text: municipalities[i].municipality
                    }));
                }
            }
        });
    })

    $('#municipality').on('change', function () {
        var municipalityID = $(this).val();

        $.ajax({
            url: 'ajax-url/get-barangay.php',
            type: 'POST',
            data: { municipalityID: municipalityID },
            success: function (data) {
                var barangays = JSON.parse(data);

                $('#barangay').empty();

                $('#barangay').append($('<option>', {
                    value: "",
                    text: ""
                }));

                for (var i = 0; i < barangays.length; i++) {
                    $('#barangay').append($('<option>', {
                        value: barangays[i].barangayID,
                        text: barangays[i].barangay
                    }));
                }
            }
        });
    })

    $('#sign-up-form').on('submit', function (event) {
        event.preventDefault();
        var form = $(this);

        var first_name = $('#first_name').val();
        var last_name = $('#last_name').val();
        var mi = $('#mi').val();
        var suffix = $('#suffix').val();

        var birthday = $('#birthday').val();
        var sex = $('#sex').val();
        var contact = $('#contact').val();
        var email = $('#email').val();

        var unit = $('#unit').val();
        var region = $('#region').val();
        var province = $('#province').val();
        var municipality = $('#municipality').val();
        var barangay = $('#barangay').val();

        var username = $('#username').val();
        var password = $('#password').val();

        var birthDate = new Date(birthday);
        var timeDiff = Date.now() - birthDate.getTime();
        var age = Math.floor(timeDiff / (1000 * 60 * 60 * 24 * 365.25));

        if (age >= 16) {
            if (contact.length === 10) {
                if (region !== null && province !== "" && municipality !== "" && barangay !== "") {
                    if (username.length > 6) {
                        var containsSpecialChars = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(username);
                        var containsOnlyNumbers = /^\d+$/.test(username);

                        if (!containsSpecialChars && !containsOnlyNumbers) {
                            var containsDigit = /\d/.test(password);
                            var containsLetter = /[a-zA-Z]/.test(password);
                            var containsSpecialSymbol = /[!@]/.test(password);
                            if (containsDigit && containsLetter && containsSpecialSymbol && password.length >= 8) {
                                $.ajax({
                                    url: 'ajax-url/check-existence.php',
                                    data: {
                                        username: username,
                                        email: email
                                    },
                                    type: 'POST',
                                    success: function (response) {
                                        if (response === 'exists') {
                                            console.log('dhsakjdh');
                                            $('.username-email-exists').css('opacity', 1).css('pointer-events', 'auto');
                                            setTimeout(function () {
                                                $('.username-email-exists').css('opacity', 0).css('pointer-events', 'none');
                                            }, 2000);
                                        } else {
                                            form.off('submit').submit();
                                        }
                                    }
                                });
                            } else {
                                $('.password-format').css('opacity', 1).css('pointer-events', 'auto');
                                setTimeout(function () {
                                    $('.password-format').css('opacity', 0).css('pointer-events', 'none');
                                }, 2000);
                            }
                        } else {
                            $('.username_min_char').css('opacity', 1).css('pointer-events', 'auto');
                            setTimeout(function () {
                                $('.username_min_char').css('opacity', 0).css('pointer-events', 'none');
                            }, 2000);
                        }
                    } else {
                        $('.username_min_char').css('opacity', 1).css('pointer-events', 'auto');
                        setTimeout(function () {
                            $('.username_min_char').css('opacity', 0).css('pointer-events', 'none');
                        }, 2000);
                    }
                } else {
                    $('.set-up-add').css('opacity', 1).css('pointer-events', 'auto');
                    setTimeout(function () {
                        $('.set-up-add').css('opacity', 0).css('pointer-events', 'none');
                    }, 2000);
                }
            } else {
                $('.contact_no_min').css('opacity', 1).css('pointer-events', 'auto');
                setTimeout(function () {
                    $('.contact_no_min').css('opacity', 0).css('pointer-events', 'none');
                }, 2000);
            }
        } else {
            $('.age_min').css('opacity', 1).css('pointer-events', 'auto');
            setTimeout(function () {
                $('.age_min').css('opacity', 0).css('pointer-events', 'none');
            }, 2000);
        }
    });



});
