/* 
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
window.addEventListener("load", function () {
    $(".btnRate").on('click', function () {
        var rate_rid = $(this).closest("form").find("input[name='rate_rid']").val();
        var rate_lid = $(this).closest("form").find("input[name='rate_lid']").val();
        var rate_section = $(this).closest("form").find("input[name='rate_section']").val();
        $("#form_" + rate_section + "\\[" + rate_rid + "\\] :button").attr("disabled", "disabled");

        if (rate_rid === null || rate_lid === null || rate_section === null) {
            alert("Internal error, please reload:" + rate_rid + ":" + rate_lid + ":" + rate_section);
        } else {
            vote = $(this).val();
            $.post("", $("#form_" + rate_section + "\\[" + rate_rid + "\\]").serialize() + '&rate=' + vote,
                    function (data) {
                        //console.log(data);
                        var json = $.parseJSON(data);
                        var i;
                        for (i = 1; i <= vote; i++) {
                            $("#form_" + rate_section + "\\[" + rate_rid + "\\] :button[name='" + i + "']").attr('class', 'btnRate vFull');
                        }
                        alert(json[0].msg);
                    });
        }
    });
});
