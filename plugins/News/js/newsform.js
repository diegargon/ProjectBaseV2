/**
 *  News - News form js script
 *
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage News
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net) 
 */

var formHasChanged = false;
var submitted = false;

$(document).on('change', 'input', function (e) {
    formHasChanged = true;
});

$(document).ready(function () {
    window.onbeforeunload = function (e) {
        if (formHasChanged && !submitted) {
            var message = "You have not saved your changes.", e = e || window.event;
            if (e) {
                e.returnValue = message;
            }
            return message;
        }
    };
});

window.addEventListener("load", function () {
    $("#newsFormSubmit").click(function () {
        $('#newsFormSubmit').attr('disabled', 'disabled');
        $.post("", $("#form_news").serialize() + '&submitForm=1',
                function (data) {
                    console.log(data); //DEBUG
                    var json = $.parseJSON(data);
                    if (json[0].status === 'ok') {
                        submitted = true;
                        alert(json[0].msg);
                        $("form")[0].reset();
                        $(location).attr('href', json[0].url);
                    } else {
                        alert(json[0].msg);
                    }
                });

        $('#newsFormSubmit').removeAttr("disabled");
        return false;
    });
});