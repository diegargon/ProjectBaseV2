/* 
 *  Copyright @ 2016  - 2019 Diego Garcia
 */

window.addEventListener("load", function () {
    $("#newsFormSubmit").click(function () {
        $('#newsFormSubmit').attr('disabled', 'disabled');
        $.post("", $("#form_news").serialize() + '&submitForm=1',
                function (data) {
                    console.log(data); //DEBUG
                    var json = $.parseJSON(data);
                    if (json[0].status === 'ok') {
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