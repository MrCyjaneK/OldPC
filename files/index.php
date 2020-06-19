<?php
session_name('oldpc_files');
session_start();
if (empty($_SESSION['current_tries']) && empty($_SESSION['v'])) {
    $_SESSION['current_tries'] = 9999;
    $_SESSION['v'] = true;
}
$regex = "/[^a-zA-Z0-9_\- \.\/()]+/";
//define('FM_ROOT_PATH','/opt/shared_files');
$default = ':D';
$drives = [
    ':D' => '/opt/shared_files',
    ':3' => '/opt/shared_files/.drives/d2'
];
if (isset($_GET['drive'])) {
    $_COOKIE['drive'] = $_GET['drive'];
}
if (isset($_COOKIE['drive']) && isset($drives[$_COOKIE['drive']])) {
    $drive = $_COOKIE['drive'];
} else {
    $drive = $default;
}
define('FM_ROOT_PATH', $drives[$drive]);
set_time_limit(0);
ignore_user_abort(true);
$showhidden = 0;
if ((substr($_SERVER['REMOTE_ADDR'],0,8) == "192.168.")) {
    $showhidden = 1;
    define('MAX_SIZE', 1024 * 1024 * 1024 * 1024); // 1TB
    define('MAX_FILES_PER_CAPTCHA', -1); // Unlimited
} else {
    define('MAX_SIZE', 100 * 1024 * 1024); // 100mb
    define('MAX_FILES_PER_CAPTCHA', 10); // 10 is ok.
}
define('SHOWHIDDEN', $showhidden);
$BEGIN = 'http://';
$path = $_GET['p'];
if (!file_exists(FM_ROOT_PATH."$path")) {
    $path = FM_ROOT_PATH.'/.404/readme.txt';
} else {
    $path = FM_ROOT_PATH."$path";
}
try {
    //$path = str_replace('//','/',$path);
    $path = realpath($path);
    if (empty($path)) {
        var_dump($path);
        die();
        //$_GET['p'] = '/.404';
        //$path = realpath(FM_ROOT_PATH.'/.404');
        //header("Location: ".$_SERVER["SCRIPT_NAME"]."?p=/.404/");
        die();
    }
//    if (substr($path, strlen(FM_ROOT_PATH)) != $_GET['p'] &&
//        '/' != $_GET['p']) {
//        header("Location: ".$_SERVER["SCRIPT_NAME"]."?p=".substr($path, strlen(FM_ROOT_PATH)));
//        die($path);
//    }
} catch (Exception $e) {
    header("Location: ".$_SERVER["SCRIPT_NAME"]."?p=/");
    die();
}
if ($_GET['raw'] && filesize($path) < MAX_SIZE) {
    if ($_SESSION['current_tries'] >= MAX_FILES_PER_CAPTCHA && MAX_FILES_PER_CAPTCHA != -1) {
        header("Location: ".$_SERVER["SCRIPT_NAME"]."?p=/".substr($path, strlen(FM_ROOT_PATH)));
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
        echo fread($fp, $buffer);
        flush();
    }

    fclose($fp);
    exit();
//    header('Content-Description: File Transfer');
//    header('Content-Type: application/octet-stream');
//    header('Content-Disposition: attachment; filename="' . basename($path) . '"');
//    header('Content-Transfer-Encoding: binary');
//    header('Connection: Keep-Alive');
//    header('Expires: 0');
//    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//    header('Pragma: public');
//    header('Content-Length: ' . filesize($path));
//    ob_end_clean();
//    readfile($path);
//    exit;
}
function convert_filesize($bytes, $decimals = 2){
    $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
}
function dcf($bytes) {
    return convert_filesize($bytes,0);
}
function driveinfo($path) {
    return dcf(disk_total_space($path) - disk_free_space($path)).' of '.dcf(disk_total_space($path));
}
function scandirSorted($path) {
    $sortedData = array();
    foreach(scandir($path) as $file) {
        if ($file === '.' || $file === '..') continue;
        if (substr($file,0,1) === '.' && SHOWHIDDEN == 0) continue;
        if(@is_file($path . $file)) {
            // Add entry at the end of the array
            array_push($sortedData, $file);
        } else {
            // Add entry at the begin of the array
            array_unshift($sortedData, $file);
        }
    }
    array_unshift($sortedData, '..');
    return $sortedData;
}
function folderSize ($dir) {
	return filesize($dir);
	//return explode("/", shell_exec("du -s \"$dir\""))[0];
	if (is_file($dir)) return filesize($dir);
	if (substr($dir, -3,2) === "/.") return 4096;
    $size = 0;

    foreach (scandir($dir) as $key => $each) {
    	if ($key === 0 || $key === 1) continue;
        $size += folderSize($dir.'/'.$each);
    }

    return $size;
}
?>

<html>
    <head>
        <title>OldPC - File Manager</title>
        <style>
        body {
            background-color: black;
            color: white;
            font-size: 150%;
            margin:20px 20px;
            font-family: Monospace;
            /* max-width:900px */
        }
        a {
            text-decoration: none;
            color: chartreuse;
        }
        pre {
            white-space: pre-wrap;       /* Since CSS 2.1 */
            white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
            white-space: -pre-wrap;      /* Opera 4-6 */
            white-space: -o-pre-wrap;    /* Opera 7 */
            word-wrap: break-word;       /* Internet Explorer 5.5+ */
        }
        </style>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <code><pre>Drive: <b><?php echo $drive ?></b> | Switch: <?php
$i = 0;
foreach ($drives as $d => $dr) {
    if ($i != 0) echo " | ";
    $i++;
    ?><a href="<?php echo $_SERVER["SCRIPT_NAME"]."?drive=".urlencode($d)."&p=".urlencode($_GET['p']).""; ?>"><?php echo htmlspecialchars($d); ?></a> (<?php echo driveinfo($dr); ?>)<?php
}
?></pre></code>
        <hr />
        <?php
if (is_dir($path)) {
    $ign = [];
    foreach (['index.txt','readme.txt','note.txt','notes.txt','changelog.txt'] as $tocheck) {
        if (file_exists($path."/$tocheck")) {
            ?><details><summary><?php echo "~~~~~~~~~~~~~ $tocheck\n"; ?></summary><p><?php echo htmlspecialchars(file_get_contents($path.'/'.$tocheck)); ?></p></details><?php
            $ign[] = $tocheck;
        }
    }
    foreach (scandirSorted($path) as $key => $dir) {
        if (in_array($dir,$ign)) continue;
        try {
            if (!@is_dir($path.'/'.$dir) && $key != 0 && false) {
                ?><a style="float: right;" href="<?php echo $_SERVER["SCRIPT_NAME"]."?raw=true&drive=".urlencode($drive)."&p=".urlencode($_GET['p']."/$dir").""; ?>">Download</a><?php
            } else {
                ?><p style="float: right;"></p><?php
            }
        } catch (Exception $e) {
        }
        if (@is_dir($path.'/'.$dir)) {
            $dird = $dir.'/';
        } else {
            $dird = $dir;
        }
        ?><a style="width:100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; float: left;" href="<?php echo $_SERVER["SCRIPT_NAME"]."?drive=".urlencode($drive)."&p=".urlencode($_GET['p']."/$dir"); ?>"><?php echo htmlspecialchars(substr(convert_filesize(@folderSize($path.'/'.$dir)).'__________',0,10).'|'.preg_replace($regex, "?", $dird)); ?></a>
<?php
    }
} else {
    // We are file lol
    if (filesize($path) > MAX_SIZE) {
        echo "I'm sorry, but ".htmlspecialchars(basename($path))." is too big, and cannot be displayed. It's size is ".convert_filesize(filesize($path))." and the limit is ".convert_filesize(MAX_SIZE).'.';
    } else if ($_SESSION['current_tries'] >= MAX_FILES_PER_CAPTCHA && MAX_FILES_PER_CAPTCHA != -1) {
        ?><img src="/files/captcha.php?session_name=oldpc_files" alt="Captcha"/>
<p>We all hate them, but I want only humans to be able to read my files</p>
<form action="/files/verify.php" method="get">
    <input type="text" name="captcha" />
    <input type="hidden" name="p" value="<?php echo htmlspecialchars($_GET['p']); ?>"/>
    <input type="submit">
</form>
<?php
    } else {
        $_SESSION['current_tries'] += 0.1;
        if (strtolower(substr($path,-4)) === '.txt' ||
            strtolower(substr($path,-3)) === '.sh'  ||
            strtolower(substr($path,-3)) === '.js'  ||
            strtolower(substr($path,-4)) === '.css' ||
            strtolower(substr($path,-4)) === '.php' ||
            strtolower(substr($path,-5)) === '.html') {
            echo "<h2>".basename($path)."</h2>";
            echo htmlspecialchars(file_get_contents($path))."\n";
        }
        if (in_array(strtolower(substr($path,-4)),['.png','.gif','.jpg','jpeg'])) {
            ?><a href="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]."?p=".$_GET['p']."&raw=true"); ?>"><img width="100%" src="<?php echo htmlspecialchars($BEGIN.$_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"]."?p=".$_GET['p']."&raw=true"); ?>" /></a><?php
        }
        if (in_array(strtolower(substr($path,-4)),['.mp4','.mkv','.avi','webm']) && $showhidden === 1) {
            ?><video controls width="100%" src="<?php echo htmlspecialchars($_SERVER["SCRIPT_NAME"]."?p=".$_GET['p']."&raw=true");?>" ></video><?php
        }
        ?><a href="<?php echo $_SERVER["SCRIPT_NAME"]."?p=".urlencode($_GET['p'])."&raw=true"; ?>">Download</a><?php
    }
}
?>
    </body>
</html>
