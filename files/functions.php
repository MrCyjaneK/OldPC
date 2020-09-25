<?php
if (!$inc) die('no');
function remove_dot_segments($path) {
    //str_replace('//','/',$path);
    $path = explode('/', $path);
    $stack = array();
    foreach ($path as $seg) {
        if ($seg == '..') {
            // Ignore this segment, remove last segment from stack
            array_pop($stack);
            continue;
        }

        if ($seg == '.') {
            // Ignore this segment
            continue;
        }

        $stack[] = $seg;
    }

    return implode('/', $stack);
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
    if ($path == 'all') return 'all drives';
    return dcf(disk_total_space($path) - disk_free_space($path)).' of '.dcf(disk_total_space($path));
}
function scandirSorted($path,$c = 0) {
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
    if ($c === 0 && $_GET['p'] != '' && $_GET['p'] != "/" && $_GET['p'] != '/.') {
        array_unshift($sortedData, '..');
    }
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
