/* 
 *  Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)
 */

window.addEventListener("load", function() { 
    $("#register").click(function(){
        $('#register').attr('disabled','disabled');
        //Email Validation
        var reg = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;

        var username = $("#username").val();
        var email = $("#email").val();
        var register = $("#register").val();
        var password = $("#password").val();
        var rpassword = $("#rpassword").val();
        if($("#tos").is(':checked')) {
            var tos = 1;
        } else {
            var tos = 0;
        }
        $('#username').css("border","1px solid black");
        $('#username').css("box-shadow","0 0 0px black");
        $('#email').css("border","1px solid black");
        $('#email').css("box-shadow","0 0 0px black");
        $('#password').css("border","1px solid black");
        $('#password').css("box-shadow","0 0 0px black");
        $('#rpassword').css("border","1px solid black");
        $('#rpassword').css("box-shadow","0 0 0px black");
        $('#tos').css("box-shadow","0 0 0px black");

        if( $('#username').length ) {
            if ( username == '' || username == null) {
                $('#username').css("border","2px solid red");
                $('#username').css("box-shadow","0 0 3px red");
                alert("Username required");
            }
        }
        if( email == '' ) {
            $('#email').css("border","2px solid red");
            $('#email').css("box-shadow","0 0 3px red");
            alert("Email required");
        } else if(email != null && reg.test(email) == false) {
                $('#email').css("border","2px solid red");
                $('#email').css("box-shadow","0 0 3px red");
                alert("Invalid email");
        } else if( password == '' || password == null) {
                $('#password').css("border","2px solid red");
                $('#password').css("box-shadow","0 0 3px red");
                alert("Password required");
        } else if( password != rpassword ){
            $('#password').css("border","2px solid red");
            $('#password').css("box-shadow","0 0 3px red");
            $('#rpassword').css("border","2px solid red");
            $('#rpassword').css("box-shadow","0 0 3px red");
            alert("Password not match");
        } else if (tos !== 1) {
            $('#tos').css("border","2px solid red");
            $('#tos').css("box-shadow","0 0 3px red");
            alert("You must accept the terms of service for register");
         } else {
            $.post("", $( "#register_form" ).serialize() ,
            function(data) {
                console.log(data); //DEBUG
                var json = $.parseJSON(data);
                if(json[0].status == 'ok') {
                    alert(json[0].msg);
                    $("form")[0].reset();
                    $(location).attr('href', json[0].url);
                } else if (json[0].status == 1) {
                    $('#email').css("border","2px solid red");
                    $('#email').css("box-shadow","0 0 3px red");
                    alert(json[0].msg);
                } else if (json[0].status == 2) {
                    $('#username').css("border","2px solid red");
                    $('#username').css("box-shadow","0 0 3px red");
                    alert(json[0].msg);
                } else if (json[0].status == 3) {
                    $('#password').css("border","2px solid red");
                    $('#password').css("box-shadow","0 0 3px red");
                    alert(json[0].msg);
                } else {
                    alert(json[0].msg);
                }
            });
        }
        $('#register').removeAttr("disabled");
        return false;
    });
});