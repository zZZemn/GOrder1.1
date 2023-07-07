$(document).ready(function () {
    function empUpdate() {
        var search = $('#search_emp').val();
        var filter = $('#emp_filter').val();
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("emp-container").innerHTML =
                    this.responseText;
            }
        };
        var url = "../server/emp-update.php?filter=" + filter + "&search=" + search;
        console.log(url);
        xhttp.open("GET", url, true);
        xhttp.send();
    }

    window.onload = empUpdate();

    $('#emp_filter').change(function () {
        setTimeout(empUpdate, 500);
    })

    $('#search_emp').on('input', function () {
        setTimeout(empUpdate, 500);
    });

    $('#new_emloyee').click(function (event) {
        event.preventDefault();
        $('.add-emp-form').css('opacity', 1);
        $('.add-emp-form').css('pointer-events', 'auto');
    })

    $('#close-add-emp-form').click(function (event) {
        event.preventDefault();
        $('.add-emp-form').css('opacity', 0);
        $('.add-emp-form').css('pointer-events', 'none');
        $('.add-emp-form input[type="text"], .add-emp-form input[type="password"], .add-emp-form input[type="email"], .add-emp-form input[type="number"], .add-emp-form input[type="date"], .add-emp-form select').val('');
    })

    $('#btn-save-employee').click(function (event) {
        event.preventDefault();

        var f_name = $('#f_name').val();
        var l_name = $('#l_name').val();
        var mi = $('#mi').val();
        var suffix = $('#suffix').val();
        var sex = $('#sex').val();
        var birthday = $('#birthday').val();
        var emp_type = $('#emp_type').val();
        var email = $('#email').val();
        var contact_no = $('#contact_no').val();
        var address = $('#address').val();
        var username = $('#username').val();
        var password = $('#password').val();

        if (f_name !== '' && l_name !== '' && sex !== '' && birthday !== '' && emp_type !== '' && email !== '' && contact_no !== '' && username !== '' && password !== '') {
            if (emailIsValid(email)) {
                if (username.length >= 8) {
                    $.ajax({
                        url: '../ajax-url/insert-emp.php',
                        method: 'POST',
                        data: {
                            f_name: f_name,
                            l_name: l_name,
                            mi: mi,
                            suffix: suffix,
                            sex: sex,
                            birthday: birthday,
                            emp_type: emp_type,
                            email: email,
                            contact_no: contact_no,
                            address: address,
                            username: username,
                            password: password
                        },
                        success: function (response) {
                            if (response === 'inserted') {
                                $('.add-emp-form').css('opacity', 0);
                                $('.add-emp-form').css('pointer-events', 'none');
                                $('.add-emp-form input[type="text"], .add-emp-form input[type="password"], .add-emp-form input[type="email"], .add-emp-form input[type="number"], .add-emp-form input[type="date"], .add-emp-form select').val('');
                                empUpdate();
                                $('.acc_created').css('opacity', 1);
                                setTimeout(function () {
                                    $('.acc_created').css('opacity', 0);
                                }, 2000);
                            } else {
                                $('.acc_created_unsuccessful').css('opacity', 1);
                                setTimeout(function () {
                                    $('.acc_created_unsuccessful').css('opacity', 0);
                                }, 1000);
                                console.log(response);
                            }
                        },
                        error: function (error) {
                            // Handle error response
                            console.log(error);
                        }
                    });
                } else {
                    $('.invalid_username').css('opacity', 1);
                    setTimeout(function () {
                        $('.invalid_username').css('opacity', 0);
                    }, 1000);
                }
            } else {
                $('.invalid_email').css('opacity', 1);
                setTimeout(function () {
                    $('.invalid_email').css('opacity', 0);
                }, 1000);
            }
        } else {
            $('.input_empty').css('opacity', 1);
            setTimeout(function () {
                $('.input_empty').css('opacity', 0);
            }, 1000);
        }


    })

    function emailIsValid(email) {
        // Regular expression for email validation
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Test the email against the regex
        return emailRegex.test(email);
    }

})