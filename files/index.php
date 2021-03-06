<?php
error_reporting(1);
ini_set("display_errors", 1);
ini_set('display_startup_errors',1);
ini_set("html_errors", 1);
error_reporting(E_ALL | E_STRICT | E_NOTICE);
$inc = true;
include './functions.php';

// legacy links support
//if (isset($_GET['p'])) {
//    include 'index.php';
//    die();
//}

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
$regex = "/[^a-zA-Z0-9_\- \.\/()~]+/";

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
    $drive = $default_drive; //$default;
}

define('FM_ROOT_PATH', $drives[$drive]);
//set_time_limit(10);
//ignore_user_abort(true);

$config = [
    "suicide" => false, // Set this to cleanly exit script.
    "max_level" => "/opt/shared_files", // Don't go deeper than that. If you want to go deeper check r/im14andthisisdeep
    "allow_comments" => false, // Don't allow comments by default
    "dontshow" => false // Set this to lock access to directory, should be a string
];
// Don't show hidden files (.*)
$showhidden = 0;

// Access control, allow everything from localhost and
// rate-limit connections from outside
if ((substr($_SERVER['REMOTE_ADDR'],0,8) == "192.168.")) {
    $showhidden = 1;
    define('MAX_SIZE', 1024 * 1024 * 1024 * 1024); // 1TB
    define('MAX_FILES_PER_CAPTCHA', -1); // Unlimited
} else if (substr($_SERVER['REMOTE_ADDR'],0,5) == "127.0") {
    define('MAX_SIZE', 1024 * 1024 * 1024 * 5); // 5GB
    define('MAX_FILES_PER_CAPTCHA', -1); // Unlimited
} else {
    define('MAX_SIZE', 2048 * 1024 * 1024); // 2GB
    define('MAX_FILES_PER_CAPTCHA', -1); // 10 is ok.
}


define('SHOWHIDDEN', $showhidden);

$BEGIN = 'http://';
$path = $_GET['p'];

foreach (explode('/',$path) as $dir) {
    $c_path[] = $dir;
    $p = implode('/',$c_path);
    if (substr($p, 0,strlen($config['max_level'])) == $config['max_level']) {
        if (is_dir($p)) {
            if (file_exists($p.'/.dirconf') && is_file($p.'/.dirconf')) {
                foreach (explode("\n", file_get_contents($p.'/.dirconf') ) as $cline) {
                    $carr = explode("=", $cline);
                    if (isset($carr[1])) {
                        $config[$carr[0]] = $carr[1];
                    }
                }
            }
        }
    }
}

if (!file_exists(FM_ROOT_PATH."$path")) {
    //TODO: This should be static file in www directory... I think.
    http_response_code(404);
    $drive = $default_drive;
    //define('FM_ROOT_PATH', $drives[$drive]);
    $path = $drives[$default_drive].'/.404/readme.txt';
} else {
    $path = FM_ROOT_PATH."$path";
}

$visual_path = substr($path,strlen($drives[$default_drive]));


// Generate $description
$description = 'OldPC file manager';
if ($drive != 'all') {
    if (is_dir($path)) {
        $description = basename($path)." is a directory located on $drive";
    } else {
        // We are file lol
        $vp = dirname($visual_path);
        $description = basename($path)."is a file located in ".$vp." on $drive";
        if ((strtolower(substr($path,-4)) === '.txt' ||
            strtolower(substr($path,-3)) === '.sh'  ||
            strtolower(substr($path,-3)) === '.js'  ||
            strtolower(substr($path,-4)) === '.css' ||
            strtolower(substr($path,-4)) === '.php' ||
            strtolower(substr($path,-5)) === '.html'||
            filesize($path) < 1024*1024*1) && filesize($path) < 1024*1024*1) { //15mb
            $description = basename($path)." is a text file or a script located in $vp on $drive";
        }
        if (in_array(strtolower(substr($path,-4)),['.mp4','.mkv','.avi','webm']) && $showhidden === 1) {
            $description = basename($path)." is a video file located in $vp on $drive";
        }
        if (in_array(strtolower(substr($path,-4)),['.png','.gif','.jpg','jpeg'])) {
             $description = basename($path)." is an image file located in $vp on $drive";
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
            foreach (scandirSorted($path, $c) as $key => $dir) {
                if ($config['dontshow']) continue;
                if (in_array($dir,$ign)) continue;
                if (@is_dir($path.'/'.$dir)) {
                    $drivetouse = 'all';
                    $dird = $dir.'/';
                } else {
                    $dird = $dir;
                    $drivetouse = $d;
                }
                $ign[] = $dir;
            }
            $c++;
        }
    }
    $description = "There are $c files in $visual_path";
}
// Send raw file
include './send_raw.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Files - <?= $drive ?> - <?= $visual_path ?></title>
        <title>mrcyjanek.net - The PC on my attic.</title>
        <meta name="theme-color" content="#000000">
        <link rel="stylesheet" href="/oldpc.css.php">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="<?= htmlspecialchars($description) ?>">
        <meta name="robots" content="index, follow">
        <style>
        body {
            max-width:95%;
            padding-bottom: 300px;
        }
        </style>
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
            if ($config['dontshow']) continue;
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
            if ((strtolower(substr($path,-4)) === '.txt' ||
                strtolower(substr($path,-3)) === '.sh'  ||
                strtolower(substr($path,-3)) === '.js'  ||
                strtolower(substr($path,-4)) === '.css' ||
                strtolower(substr($path,-4)) === '.php' ||
                strtolower(substr($path,-5)) === '.html'||
                filesize($path) < 1024*1024*1) && filesize($path) < 1024*1024*2 &&
                strtolower(substr($path,-3)) != ".md") { //15mb
                echo '<pre><code>'.htmlspecialchars(file_get_contents($path))."</code></pre><br />";
            }
            if (strtolower(substr($path,-3)) == ".md") {
                include "Parsedown.php";
                $Parsedown = new Parsedown();
                $Parsedown->setSafeMode(true);
                echo $Parsedown->text(file_get_contents($path));
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
                if ($config['dontshow']) continue;
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
    if ($config['dontshow']) {
        echo "You can't access this directory because of .dirconf rules.<br />";
        echo htmlspecialchars($config['dontshow']);
    };
?>
    </body>
</html>
