<?php
session_name('oldpc_files');
session_start();
header('Content-Type: text/plain');

//print_r($_POST);
//Array
//(
//    [name] => Czarek Nakamoto
//    [comment] => First comment
//    [captcha] => ZJKWVRTV
//    [submit] => Submit
//    [p] => /
//    [drive] => all
//)

if (strlen($_POST['name']) < 5 ) die('Your name is too short, 5 is the minimum');
if (strlen($_POST['name']) > 32) die('Your name is too long, 32 is the maximum');
if (strlen($_POST['comment']) < 5 ) die('Your comment is too short, 5 is the minimum');
if (strlen($_POST['comment']) > 2*1024) die('Your comment is too long, 2048 is the maximum');


if (strtoupper($_SESSION['captcha_text']) === strtoupper($_POST['captcha'])) {
    include 'drive_config.php';
    //$drives = [
    //    ':D' => '/opt/shared_files',
    //    ':3' => '/opt/shared_files/.drives/d2',
    //    'all' => 'all'
    //];
    $exists = false;
    foreach ($drives as $drive) {
        if (file_exists($drive.$_POST['p'])) {
            $exists = true;
        }
    }
    if (!$exists) {
        die('Oups? It seems like requested path doesn`t exist');
    }
    $datadir = "/opt/shared_files/.oldpcdata/";
    $filename = $datadir.preg_replace('{(.)\1+}','$1',preg_replace("/[^a-zA-Z0-9]+/", "", $_POST['p']))."-comments.json";
    if (file_exists($filename)) {
        $cdata = json_decode(file_get_contents($filename),true);
    } else {
        $cdata = [];
    }
    $cdata[] = [
        "name" => $_POST["name"],
        "comment" => $_POST["comment"],
        "approved" => false,
        "time" => time()
    ];
    file_put_contents($filename, json_encode($cdata, JSON_PRETTY_PRINT));
} else {
    die('Incorrect captcha');
}
//$_SESSION['captcha_text'] = hash('sha512', microtime(true));
header("Location: /files/index.php?p=".$_POST['p']."&drive=".$_POST['drive']);
?>
