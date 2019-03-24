/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */

window.addEventListener("load", function () {
    $('#reset_password_chk').click(function () {
        if (this.checked) {
            $('#login_form').trigger("reset");
            $('#reset_password_chk').prop('checked', true);
        } else {
            $('#login_form').trigger("reset");
            $('#reset_password_chk').prop('checked', false);
        }
        $('#password').toggle();
        $('#label_password').toggle();
        $('#rememberme').toggle();
        $('#label_rememberme').toggle();
        $('#login').toggle();
        $('#reset_password_btn').toggle();
    });

    $("#reset_password_btn").click(function () {
        $.post("", $("#login_form").serialize(),
                function (data) {
                    console.log(data); //DEBUG
                    var json = $.parseJSON(data);
                    if (json[0].status == 1) {
                        $('#email').css("border", "2px solid red");
                        $('#email').css("box-shadow", "0 0 3px red");
                        alert(json[0].msg);
                    } else if (json['0'].status == 2) {
                        $('#login_form').trigger("reset");
                        $('#password').toggle();
                        $('#label_password').toggle();
                        $('#rememberme').toggle();
                        $('#label_rememberme').toggle();
                        $('#login').toggle();
                        $('#reset_password_btn').toggle();
                        alert(json[0].msg);
                        return true;
                    } else {
                        alert(json[0].msg);
                    }
                });
        return false;
    });

    $("#login").click(function () {
        $('#login').attr('disabled', 'disabled');
        //Email Validation        
        var reg = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;

        var email = $("#email").val();
        var password = $("#password").val();
        //reset red borders
        $('#email').css("border", "1px solid black");
        $('#email').css("box-shadow", "0 0 0px black");
        $('#password').css("border", "1px solid black");
        $('#password').css("box-shadow", "0 0 0px black");
        // Checking for blank fields. 
        if (email == '') {
            $('#email').css("border", "2px solid red");
            $('#email').css("box-shadow", "0 0 3px red");
            alert("Email es obligatorio");
        } else if (reg.test(email) == false) {
            $('#email').css("border", "2px solid red");
            $('#email').css("box-shadow", "0 0 3px red");
            alert("Email incorrecto");
        } else if (password == '') {
            $('#password').css("border", "2px solid red");
            $('#password').css("box-shadow", "0 0 3px red");
            alert("Password es obligatorio");
        } else if (password.length < 5) {
            $('#password').css("border", "2px solid red");
            $('#password').css("box-shadow", "0 0 3px red");
            alert("La contraseña es demasiado pequeña");
        } else {
            $.post("", $("#login_form").serialize(),
                    function (data) {
                        console.log(data); //DEBUG
                        var json = $.parseJSON(data);
                        if (json[0].status == 'ok') {
                            $("#login_form")[0].reset();
                            $(location).attr('href', json[0].msg);
                        } else if (json[0].status == 1) {
                            $('#email').css("border", "2px solid red");
                            $('#email').css("box-shadow", "0 0 3px red");
                            alert(json[0].msg);
                        } else if (json[0].status == 2) {
                            $('#password').css("border", "2px solid red");
                            $('#password').css("box-shadow", "0 0 3px red");
                            alert(json[0].msg);
                        } else {
                            alert(json[0].msg);
                        }
                    });
        }
        $('#login').removeAttr("disabled");
        return false;
    });
});