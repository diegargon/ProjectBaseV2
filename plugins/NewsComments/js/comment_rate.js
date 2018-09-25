/* 
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
window.addEventListener("load", function () {
    $(".btnCommentRate").on('click', function () {
        var rate_rid = $(this).closest("form").find("input[name='rate_rid']").val();
        var rate_lid = $(this).closest("form").find("input[name='rate_lid']").val();
        $("#form_comment_rate\\[" + rate_rid + "\\] :button").attr("disabled", "disabled");

        if (rate_rid === null || rate_lid === null) {
            alert("Internal error, please reload:" + rate_rid + ":" + rate_lid);
        } else {
            vote = $(this).val();
            $.post("", $("#form_comment_rate\\[" + rate_rid + "\\]").serialize() + '&comment_rate=' + vote,
                    function (data) {
                        console.log(data);
                        var json = $.parseJSON(data);
                        var i;
                        for (i = 1; i <= vote; i++) {
                            $("#form_comment_rate\\[" + rate_rid + "\\] :button[name='" + i + "']").attr('class', 'btnCommentRate btnRate vFull');
                        }
                        alert(json[0].msg);
                    });
        }
    });
});
