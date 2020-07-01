<?php
date_default_timezone_set('Europe/Warsaw');
//$file = 'phooo'.time().rand(0,10000).'.png';
//shell_exec("rm ./phooo*.png");
$file = "/opt/shared_files/Pictures/OldPC_selfies/".date('Y/m/d/H.i.', time()).substr(date('s',time()),0,1).".jpg";
if (file_exists($file)) {
    header('Content-Type: image/jpeg');
    readfile($file);
} else {
    shell_exec("mkdir -p $file && rm -rf $file");
    $txt = date('Y/m/d H-i-s', time());
    $cmd = "ffmpeg -i /dev/video0 -frames:v 1 -vf \"drawtext=text='$txt':x=10:y=H-th-10:fontfile=font.ttf:fontsize=12:fontcolor=white\" $file 2>&1";

    shell_exec($cmd);
    header('Content-Type: image/jpeg');
    readfile($file);
    // unlink($file);
    die();
    //ffmpeg -i ffmpeg -frames:v 1 -i /dev/video0 pho.png
}
