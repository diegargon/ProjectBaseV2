<?php
/**
 *  NewsmediaUploader - formfileupload
 * 
 *  @author diego@envigo.net
 *  @package ProjectBase
 *  @subpackage NewsMediaUploadeer
 *  @copyright Copyright @ 2016 - 2019 Diego Garcia (diego@envigo.net)  
 */
!defined('IN_WEB') ? exit : true;
?>
<label><?= $LNG['L_NMU_UPLOAD_FILES'] ?><span class="text_small"><?= $LNG['L_NMU_MAX'] . $cfg['upload_max_filesize'] ?></span></label>
<div id="upload_container">
    <a id="pickfiles" href="javascript:;"><?= $LNG['L_NMU_SELECT_FILES'] ?></a>
    <a id="uploadfiles" href="javascript:;"><?= $LNG['L_NMU_UPLOAD_FILES'] ?></a>
    <a id="reloadfiles" onclick="reloadData()" href="javascript:void(0);">Reload</a>
</div>
<div id="filelist"><?= $LNG['L_NMU_E_BROWSER_UPLOAD'] ?></div>
<?php if (!empty($data['uploaded_content'])) { ?>
    <div id="uploaded_user_list"><?= $data['uploaded_content'] ?></div>
<?php } ?>
<pre id="console"></pre>
<script type="text/javascript">
    $('#photobanner').on('scroll', function () {
        let div = $(this).get(0);
        if (div.scrollTop + div.clientHeight >= div.scrollHeight) {
            //console.log($( '#photobanner a:last' ).data('id'));
            loadData($('#photobanner a:last').data('id'));
        }
    });

    function loadData(last_id) {
        //console.log( last_id );        
        var $photos = $('#photobanner');
        var $path = '/index.php?module=NewsMediaUploader&page=get_links';
        $.get($path, {last_id: last_id}, function (data) {
            $photos.append(data);
        });

    }
    function reloadData() {
        var $photos = $('#photobanner');
        var $path = '/index.php?module=NewsMediaUploader&page=get_links';
        $('#photobanner').empty();
        $.get($path, {reload: 1}, function (data) {
            $photos.append(data);
        });
    }
    var uploader = new plupload.Uploader({
        runtimes: 'html5, html4',
        browse_button: 'pickfiles', // you can pass an id...
        container: document.getElementById('upload_container'), // ... or DOM Element itself
        url: '/<?= $cfg['CON_FILE'] ?>?module=NewsMediaUploader&page=upload',
        unique_names: false,
        drop_element: "uploaded_user_list",
        autostart: true,

        filters: {
            max_file_size: '<?= $cfg['upload_max_filesize'] ?>',
            mime_types: [
                {title: "Image files", extensions: "<?= $cfg['upload_accepted_files'] ?>"}
            ]
        },

        init: {
            PostInit: function () {
                document.getElementById('filelist').innerHTML = '';
                document.getElementById('uploadfiles').onclick = function () {
                    uploader.start();
                    return false;
                };
            },
            FilesAdded: function (up, files) {
                plupload.each(files, function (file) {
                    document.getElementById('filelist').innerHTML += '<div id="' + file.id + '"><span class="file_details"><b></b>' + file.name + ' (' + plupload.formatSize(file.size) + ') </span></div>';
                });
            },
            UploadProgress: function (up, file) {
                var span_size = file.percent / 8;
                if (file.percent == 100) {
                    document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span class="file_percent" style="background-color:#4479BA;padding-left:' + span_size + '%;">' + file.percent + "%</span>";
                } else {
                    document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span class="file_percent" style=padding-left:' + span_size + '%;>' + file.percent + "%</span>";
                }
            },
            Error: function (up, err) {
                document.getElementById('console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
            },
            FileUploaded: function (up, file, object) {
                var myData;
                //console.log(object);console.log(myData); console.log(file);   
                myData = $.parseJSON(object.response);
                if (myData.error) {
                    document.getElementById('console').appendChild(document.createTextNode("\nError with " + file.name + ": " + myData.error.code + ": " + myData.error.message));
                }
                if (myData.result) {
                    var textarea = document.getElementById('editor_text');
                    textarea.value += "[localimg w=600]" + myData.result + "[/localimg]";
                }
            }
        }
    });
    uploader.init();

    function addtext(text) {
        var textarea = document.getElementById('editor_text');
        var cursor = textarea.selectionStart;
        var left_text = textarea.value.substring(0, cursor);
        var right_text = textarea.value.substring(cursor, textarea.size);
        textarea.value = left_text + text + right_text;
    }

</script>