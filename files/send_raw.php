<?php
if (!$inc) die('no');
if (isset($_GET['raw']) && filesize($path) < MAX_SIZE && file_exists($path)) {
    set_time_limit(0);
    //TODO: Allow small files to be send without captcha.
    if ($_SESSION['current_tries'] >= MAX_FILES_PER_CAPTCHA && MAX_FILES_PER_CAPTCHA != -1) {
        var_dump($_SESSION);
        header("Location: /files/".$drive.$_GET['p']);
        die();
    }
    $_SESSION['current_tries'] += 1;
    switch (strtolower(substr($path,-4))) {
        case '.mp4':
            $type = 'video/mp4';
            break;
        case 'webm':
            $type = 'video/webm';
            break;
        default:  
            $type = 'application/octet-stream';
            header('Content-Disposition: attachment; filename="' . basename($path) . '"');
    }
    $file = $path;
    $fp = @fopen($file, 'rb');
    $size = filesize($file); // File size
    $length = $size;         // Content length
    $start = 0;              // Start byte
    $end = $size - 1;        // End byte
    header('Content-type: '.$type);
    header("Accept-Ranges: bytes");
    if (isset($_SERVER['HTTP_RANGE'])) {
        $c_start = $start;
        $c_end = $end; 

        list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
        if (strpos($range, ',') !== false) {
            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes $start-$end/$size");
            exit;
        }
        if ($range == '-') {
            $c_start = $size - substr($range, 1);
        } else {
            $range = explode('-', $range);
            $c_start = $range[0];
            $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
        }
        $c_end = ($c_end > $end) ? $end : $c_end;
        if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
            header('HTTP/1.1 416 Requested Range Not Satisfiable');
            header("Content-Range: bytes $start-$end/$size");
            exit;
        }
        $start = $c_start;
        $end = $c_end;
        $length = $end - $start + 1;
        fseek($fp, $start);
        header('HTTP/1.1 206 Partial Content');
    }
    header("Content-Range: bytes $start-$end/$size");
    header("Content-Length: " . $length);
    $buffer = 1024 * 8;
    while (!feof($fp) && ($p = ftell($fp)) <= $end) {
        if ($p + $buffer > $end) {
            $buffer = $end - $p + 1;
        }
        set_time_limit(0);
        if ($fp === false || $fp === true) exit;
        echo fread($fp, $buffer);
        flush();
    }

    fclose($fp);
    exit();
}

