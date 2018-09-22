<?php

/*
 *  Copyright @ 2016 - 2018 Diego Garcia
 */
!defined('IN_WEB') ? exit : true;

global $cfg, $db;


if ((!$user = $sm->getSessionUser())) {
    if ($cfg['NMU_ALLOW_ANON']) {
        $user['uid'] = 0;
    } else {
        die('{"jsonrpc" : "2.0", "error" : {"code": 105, "message": "' . $LNG['L_NMU_W_DISABLE'] . '"}, "id" : "id"}');
        exit();
    }
}

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// 5 minutes execution time
@set_time_limit(5 * 60);
// Settings
$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
$targetDir = $cfg['NMU_UPLOAD_DIR'];
$cleanupTargetDir = true; // Remove old files
$maxFileAge = 5 * 3600; // Temp file age in seconds
$thumbsDir = $targetDir . DIRECTORY_SEPARATOR . "thumbs";
$mobileDir = $targetDir . DIRECTORY_SEPARATOR . "mobile";
$desktopDir = $targetDir . DIRECTORY_SEPARATOR . "desktop";
// Create target dir
if (!file_exists($targetDir)) {
    @mkdir($targetDir);
}
if (!file_exists($mobileDir)) {
    @mkdir($mobileDir);
}
if (!file_exists($thumbsDir)) {
    @mkdir($thumbsDir);
}
if (!file_exists($desktopDir)) {
    @mkdir($desktopDir);
}
// Get a file name
// FIXME: $_FILES filename empty uploading  files greater than 8mb 
if (isset($_REQUEST["name"])) {
    $fileName = $_REQUEST["name"];
} elseif (!empty($_FILES)) {
    $fileName = $_FILES["file"]["name"];
} else {
    $fileName = uniqid("file_");
}
$fileName = S_VAR_FILENAME($fileName, 256, 1);
$fileName = preg_replace("/\s+/", "_", $fileName); //spaces to _
if (empty($fileName)) {
    die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "' . $LNG['L_NMU_E_FILENAME'] . '"}, "id" : "id"}');
    exit();
}

$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

if (file_exists($filePath)) {
    die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "' . $LNG['L_NMU_E_ALREADY_EXISTS'] . '"}, "id" : "id"}');
    exit();
}
// Chunking might be enabled
$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
// Remove old temp files
if ($cleanupTargetDir) {
    if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "' . $LNG['L_NMU_E_OPENTMP'] . '"}, "id" : "id"}');
    }
    while (($file = readdir($dir)) !== false) {
        $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
        // If temp file is current file proceed to the next
        if ($tmpfilePath == "{$filePath}.part") {
            continue;
        }
        // Remove temp file if it is older than the max age and is not the current file
        if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
            @unlink($tmpfilePath);
        }
    }
    closedir($dir);
}
// Open temp file
if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "' . $LNG['L_NMU_E_OPENSTREAM'] . '"}, "id" : "id"}');
}

if (!empty($_FILES)) {
    if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "' . $LNG['L_NMU_E'] . '"}, "id" : "id"}');
    }
    // Read binary input stream and append it to temp file
    if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "' . $LNG['L_NMU_E_OPEN_INPUT_STREAM'] . '"}, "id" : "id"}');
    }
} else {
    if (!$in = @fopen("php://input", "rb")) {
        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "' . $LNG['L_NMU_E_OPEN_INPUT_STREAM'] . '"}, "id" : "id"}');
    }
}

while ($buff = fread($in, 4096)) {
    fwrite($out, $buff);
}

@fclose($out);
@fclose($in);

// Check if file has been uploaded
if (!$chunks || $chunk == $chunks - 1) {
    // Strip the temp .part suffix off 
    rename("{$filePath}.part", $filePath);
}

$filePathSelector = $cfg['NMU_UPLOAD_DIR'] . "[S]" . $fileName;
$insert_ary = array(
    "plugin" => "news_img_upload",
    "source_id" => $user['uid'],
    "type" => "image",
    "link" => $filePathSelector,
);
$db->insert("links", $insert_ary);

if ((getLib("ImageLib", "0.1")) && ( $cfg['NMU_CREATE_IMG_THUMBS'] || $cfg['NMU_CREATE_IMG_MOBILE'] )) {
    $imglib = new ImageLib;
    if ($cfg['NMU_CREATE_IMG_THUMBS']) {
        $thumb_filePath = $thumbsDir . DIRECTORY_SEPARATOR . $fileName; // str_replace(".", "-thumb.", $filePath);
        $imglib->do_thumb($filePath, $thumb_filePath, $cfg['NMU_THUMBS_WIDTH']);
    }
    if ($cfg['NMU_CREATE_IMG_MOBILE']) {
        $mobile_filePath = $mobileDir . DIRECTORY_SEPARATOR . $fileName;
        $imglib->do_thumb($filePath, $mobile_filePath, $cfg['NMU_MOBILE_WIDTH']);
    }
    if ($cfg['NMU_CREATE_IMG_DESKTOP']) {
        $desktop_filePath = $desktopDir . DIRECTORY_SEPARATOR . $fileName;
        $imglib->do_thumb($filePath, $desktop_filePath, $cfg['NMU_DESKTOP_WIDTH']);
    }
}
die('{"jsonrpc" : "2.0", "result" : "' . $filePathSelector . '", "id" : "id"}');
