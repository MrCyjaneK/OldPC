<?php
error_reporting(1);
ini_set("display_errors", 1);
ini_set('display_startup_errors',1);
ini_set("html_errors", 1);
error_reporting(E_ALL | E_STRICT | E_NOTICE);





$inc = true;

// legacy links support
if (isset($_GET['p'])) {
    include 'index.php';
    die();
}

//nginx:
//location /files/ {
//    try_files /donotexistplz /files/index.php?ngp=$uri;
//}

session_name('oldpc_files');
session_start();

// Check if user have made download requests or decided to view a file
// This is antispam.
if (empty($_SESSION['current_tries']) && empty($_SESSION['v'])) {
    $_SESSION['current_tries'] = 9999;
    $_SESSION['v'] = true;
}

$expl = explode('/', $_GET['ngp']);
unset($expl[1]);

if (substr($expl[2],0,4) == 'raw-') {
    $_GET['raw'] = true;
    $expl[2] = substr($expl[2],4);
}
$_GET['drive'] = $expl[2];
unset($expl[2]);
$_GET['p'] = implode("/",$expl);
//var_dump($_GET);
// Allowed filenames "Ściema<jakaś>" -> "?ciema?jaka??"
$regex = "/[^a-zA-Z0-9_\- \.\/()]+/";

// Default drive, shouldn't be all (Why?)
$default = ':D';
// Include drive's config
//
//$drives = [
//    ':D' => '/opt/shared_files',
//    ':3' => '/opt/shared_files/.drives/d2',
//    'all' => 'all'
//];
//$drive_color = [
//    ':D'  => '00ff00',
//    ':3'  => '00ffff',
//    'all' => 'ff0000'
//];
include 'drive_config.php';


if (isset($_GET['drive'])) {
    $_COOKIE['drive'] = $_GET['drive'];
}
if (isset($_COOKIE['drive']) && isset($drives[$_COOKIE['drive']])) {
    $drive = $_COOKIE['drive'];
} else {
    $drive = 'all'; //$default;
}

define('FM_ROOT_PATH', $drives[$drive]);
//set_time_limit(10);
//ignore_user_abort(true);

// Don't show hidden files (.*)
$showhidden = 0;

// Access control, allow everything from localhost and
// rate-limit connections from outside
if ((substr($_SERVER['REMOTE_ADDR'],0,8) == "192.168.")) {
    $showhidden = 1;
    define('MAX_SIZE', 1024 * 1024 * 1024 * 1024); // 1TB
    define('MAX_FILES_PER_CAPTCHA', -1); // Unlimited
} else {
    define('MAX_SIZE', 2048 * 1024 * 1024); // 2GB
    define('MAX_FILES_PER_CAPTCHA', 10); // 10 is ok.
}


define('SHOWHIDDEN', $showhidden);

$BEGIN = 'http://';
$path = $_GET['p'];

if (!file_exists(FM_ROOT_PATH."$path")) {
    //TODO: This should be static file in www directory... I think.
    $path = FM_ROOT_PATH.'/.404/readme.txt';
} else {
    $path = FM_ROOT_PATH."$path";
}

// Send raw file
include './send_raw.php';
include './functions.php';
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
            padding-bottom: 300px;
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
        Drive: <b><?php echo $drive ?></b> | Switch: <?php
$i = 0;
foreach ($drives as $d => $dr) {
    if ($i != 0) echo " | ";
    $i++;
    ?><a style="color: #<?php echo $drive_color[$d]; ?>" href="/files/<?= $d ?><?= $_GET['p'] ?>"><?php echo htmlspecialchars($d); ?></a> (<?php echo driveinfo($dr); ?>)<?php
}
?>
        <hr />
        <?php
if ($drive != 'all') {
    if (is_dir($path)) {
        $ign = [];
        foreach (['index.txt','readme.txt','note.txt','notes.txt','changelog.txt'] as $tocheck) {
            if (file_exists($path."/$tocheck")) {
                ?><details open><summary><?php echo "~~~~~~~~~~~~~ $tocheck\n"; ?></summary><code><pre><?php echo htmlspecialchars(file_get_contents($path.'/'.$tocheck)); ?></pre></code></details><?php
                $ign[] = $tocheck;
            }
        }
        foreach (scandirSorted($path) as $key => $dir) {
            if (in_array($dir,$ign)) continue;
            if (@is_dir($path.'/'.$dir)) {
                $dird = $dir.'/';
            } else {
                $dird = $dir;
            }
            ?><a style="color: #<?php echo $drive_color[$drive]; ?>;width:100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; float: left;" href="/files/<?= $drive ?><?= remove_dot_segments($_GET['p']."/$dir"); ?>"><?php echo htmlspecialchars(substr(convert_filesize(@folderSize($path.'/'.$dir)).'__________',0,10).'|'.preg_replace($regex, "?", $dird)); ?></a>
<?php
        }
    } else {
        // We are file lol
        if (filesize($path) > MAX_SIZE) {
            echo "I'm sorry, but ".htmlspecialchars(basename($path))." is too big, and cannot be displayed. It's size is ".convert_filesize(filesize($path))." and the limit is ".convert_filesize(MAX_SIZE).'.';
        } else if ($_SESSION['current_tries'] >= MAX_FILES_PER_CAPTCHA && MAX_FILES_PER_CAPTCHA != -1 && filesize($path) > 1024*1024*15) {
            ?><img src="/files/captcha.php?session_name=oldpc_files" alt="Captcha"/>
    <p>We all hate them, but I want only humans to be able to read my files, please type everything you se above in box below.</p>
    <form action="/files/verify.php" method="get">
        <input type="text" name="captcha" />
        <input type="hidden" name="p" value="<?php echo htmlspecialchars($_GET['p']); ?>" />
        <input type="hidden" name="drive" value="<?php echo htmlspecialchars($drive); ?>" />
        <input type="submit">
    </form>
<?php
        } else {
            $_SESSION['current_tries'] += 0.1;
            echo "<h2>".basename($path)."</h2>";
            ?><a style="color: #<?php echo $drive_color[$drive]; ?>;width:100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; float: left;" href="/files/<?= $drive ?><?= remove_dot_segments($_GET['p']."/.."); ?>">close</a><?php
            if (strtolower(substr($path,-4)) === '.txt' ||
                strtolower(substr($path,-3)) === '.sh'  ||
                strtolower(substr($path,-3)) === '.js'  ||
                strtolower(substr($path,-4)) === '.css' ||
                strtolower(substr($path,-4)) === '.php' ||
                strtolower(substr($path,-5)) === '.html'||
                filesize($path) < 1024*1024*15) { //15mb
                echo '<pre><code>'.htmlspecialchars(file_get_contents($path))."</code></pre><br />";
            }
            if (in_array(strtolower(substr($path,-4)),['.mp4','.mkv','.avi','webm']) && $showhidden === 1) {
                ?><video controls width="100%" src="/files/raw-<?= $drive ?><?= $_GET['p'] ?>" ></video><?php
            }
            if (in_array(strtolower(substr($path,-4)),['.png','.gif','.jpg','jpeg'])) {
                ?><a href="/files/raw-<?= $drive ?><?= $_GET['p'] ?>"><img width="100%" src="/files/raw-<?= $drive ?><?= $_GET['p'] ?>" /></a><?php
            }
            ?>
    <a style="color: #<?php echo $drive_color[$drive]; ?>;" href="/files/raw-<?= $drive ?><?= $_GET['p'] ?>">Download</a><?php
        }
    }
} else {
    // All drives
    $c = 0;
    $ign = [];
    foreach ($drives as $d => $p) {
        if ($d == 'all') continue;
        $path = $p.$_GET['p'];
        if(@!file_exists($path)) continue;
        if (is_dir($path)) {
            foreach (['index.txt','readme.txt','note.txt','notes.txt','changelog.txt'] as $tocheck) {
                if (file_exists($path."/$tocheck")) {
                    ?>
                    <details open>
                        <summary><?php echo "~~~~~~~~~~~~~ $tocheck\n"; ?></summary>
                        <code><pre><?php echo htmlspecialchars(file_get_contents($path.'/'.$tocheck)); ?></pre></code>
                    </details>
                    <?php
                    $ign[] = $tocheck;
                }
            }
            foreach (scandirSorted($path, $c) as $key => $dir) {
                if (in_array($dir,$ign)) continue;
                if (@is_dir($path.'/'.$dir)) {
                    $drivetouse = 'all';
                    $dird = $dir.'/';
                } else {
                    $dird = $dir;
                    $drivetouse = $d;
                }
                $ign[] = $dir;
                ?>
                <a style="color: #<?php echo $drive_color[$d]; ?>;width:100%; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; float: left;" href="/files/<?= $drivetouse ?><?= remove_dot_segments($_GET['p']."/$dir"); ?>"><?php echo htmlspecialchars(substr(convert_filesize(@folderSize($path.'/'.$dir)).'__________',0,10).'|'.preg_replace($regex, "?", $dird)); ?></a>
                <?php
            }
            $c++;
        }
    }
}
//    include 'comments.php';
?>
    </body>
</html>