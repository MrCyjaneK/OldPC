<?php
session_name('oldpc_files');
session_start();
if (strtoupper($_SESSION['captcha_text']) === strtoupper($_GET['captcha'])) {
    $_SESSION['current_tries'] = 0;
}
header("Location: /files/index.php?p=".$_GET['p']);
?>
