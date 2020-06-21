/* 
 *  Copyright @ 2016 - 2020 Diego Garcia (diego@envigo.net)
 */

window.addEventListener("load", function() {
    $("#profile").click(function(){
        // $('#profile').attr('disabled', 'disabled');
        //Email Validation
        var reg = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i;
        var username = $("#username").val();
        var email = $("#email").val();
        var profile = $("#profile").val();
        var cur_password = $("#cur_password").val();
        var new_password = $("#new_password").val();
        var r_password = $("#r_password").val();
        $('#username').css("border", "1px solid black");
        $('#username').css("box-shadow", "0 0 0px black");
        $('#email').css("border", "1px solid black");
        $('#email').css("box-shadow", "0 0 0px black");
        $('#cur_password').css("border", "1px solid black");
        $('#cur_password').css("box-shadow", "0 0 0px black");
        $('#r_password').css("border", "1px solid black");
        $('#r_password').css("box-shadow", "0 0 0px black");
        $('#new_password').css("border", "1px solid black");
        $('#new_password').css("box-shadow", "0 0 0px black");
        $('.avatar').css("border", "1px solid black");
        if (email == '') {
            $('#email').css("border", "2px solid red");
            $('#email').css("box-shadow", "0 0 3px red");
            alert("Email is required");
        } else if(email != null && reg.test(email) == false) {
            $('#email').css("border","2px solid red");
            $('#email').css("box-shadow","0 0 3px red");
            alert("Invalid email");
        } else if( cur_password == null) {
            $('#cur_password').css("border","2px solid red");
            $('#cur_password').css("box-shadow","0 0 3px red");
            alert("Password required");
        } else if( cur_password.length < 5){
            $('#cur_password').css("border","2px solid red");
            $('#cur_password').css("box-shadow","0 0 3px red");
            alert("Password too small");
        } else if( new_password != null && new_password > 1 ){
            if( new_password != r_password ){
                $('#new_password').css("border","2px solid red");
                $('#new_password').css("box-shadow","0 0 3px red");
                $('#r_password').css("border","2px solid red");
                $('#r_password').css("box-shadow","0 0 3px red");
                alert("Passwords not match");
            }
        } else {
            $.post("", $("#profile_form").serialize() + '&profile=1',
                function(data) {
                    console.log(data); //DEBUG
                    var json = $.parseJSON(data);
                    if (json[0].status == 'ok') {
                        alert(json[0].msg);
                        $("form")[0].reset();
                        $(location).attr('href', json[0].url);
                    } else if (json[0].status == 1) {
                        $('#cur_password').css("border", "2px solid red");
                        $('#cur_password').css("box-shadow", "0 0 3px red");
                        alert(json[0].msg);
                    } else if (json[0].status == 2) {
                        $('#cur_password').css("border", "2px solid red");
                        $('#cur_password').css("box-shadow", "0 0 3px red");
                        alert(json[0].msg);
                    } else if (json[0].status == 3) {
                        $('#new_password').css("border", "2px solid red");
                        $('#new_password').css("box-shadow", "0 0 3px red");
                        $('#r_password').css("border", "2px solid red");
                        $('#r_password').css("box-shadow", "0 0 3px red");
                        alert(json[0].msg);
                    } else if (json[0].status == 4) {
                        $('#username').css("border", "2px solid red");
                        $('#username').css("box-shadow", "0 0 3px red");
                        alert(json[0].msg);
                    } else if (json[0].status == 5) {
                        $('#email').css("border", "2px solid red");
                        $('#email').css("box-shadow", "0 0 3px red");
                        alert(json[0].msg);
                    } else if (json[0].status == 6) {
                        $('.avatar').css("border", "2px solid red");
                        $('.avatar').css("box-shadow", "0 0 3px red");
                        alert(json[0].msg);
                    } else {
                        alert(json[0].msg);
                    }
                }
            );
        }
        $('#profile').removeAttr("disabled");
        return false;
    });
});
