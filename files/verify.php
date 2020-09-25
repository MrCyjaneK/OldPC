<?php
session_name('oldpc_files');
session_start();
if (strtoupper($_SESSION['captcha_text']) === strtoupper($_GET['captcha'])) {
    $_SESSION['captcha_text'] = hash('sha512', microtime(true));
    $_SESSION['current_tries'] = 0;
}
header("Location: /files/".$_GET['drive'].$_GET['p']);
?>
