/**
 *  NewsmediaUploader - editor.js script
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage NewsMediaUploadeer
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */

function get_highlighted() {
    var txtarea = document.getElementById('editor_text');
    var start = txtarea.selectionStart;
    var finish = txtarea.selectionEnd;
    var sel = txtarea.value.substring(start, finish);

    return sel;
}

function new_tagged_text(tag) {
    var txtarea = document.getElementById('editor_text');
    var sel_start = txtarea.selectionStart;
    var sel_finish = txtarea.selectionEnd;
    var left_text = txtarea.value.substring(0, sel_start);
    var right_text = txtarea.value.substring(sel_finish, txtarea.size);

    return left_text + tag + right_text;
}

$("#btnEditorSave").click(function () {
    var text = $("#editor_text").val();
    var btnEditorSave = 1;
    $text = $.trim(text);
    if (text.length > 0) {
        $.post("", {editor_text: text, btnEditorSave: btnEditorSave},
                function (data) {
                    console.log(data);
                    var json = $.parseJSON(data);
                    //if (json[0].status  0) {
                    alert(json[0].msg);
                    return true;
                    // }
                });
    }
    return false;
});

window.addEventListener("load", function () {
    $(".btnEditor").on('click', function () {
        var tag = $(this).val();
        var tag_ary = tag.split(/\$1/);
        var preTag = tag_ary[0];
        var postTag = tag_ary[1];
        var selection = get_highlighted();
        var new_text = new_tagged_text(preTag + selection + postTag);
        $("#editor_text").val(new_text);
    });

    $('#btnEditorPreview').click(function () {
        $.post("", $("#editor_text").serialize() + '&editor_preview=1',
                function (data) {
                    $("#editor_preview").html(data);
                });
    });
    $('#btnEditorHiddePreview').click(function () {
        $("#editor_preview").html("");
    });
});