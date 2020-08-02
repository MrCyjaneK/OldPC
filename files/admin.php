<html>
    <head>
        <title>OldPC</title>
        <style>
        body {
            background-color: black;
            color: white;
        }
        a {
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
<?php
session_name('oldpc_files');
session_start();
if (empty($_SESSION['current_tries']) && empty($_SESSION['v'])) {
    $_SESSION['current_tries'] = 9999;
    $_SESSION['v'] = true;
}
$datadir = "/opt/shared_files/.oldpcdata";
date_default_timezone_set('Europe/Warsaw');
$files = scandir($datadir);
foreach ($files as $key => $value) {
    if ($value == "." || $value == "..") {
        unset($files[$key]);
    }
}
?>
<table>
    <tr>
        <th>Name</th>
        <th>Comment</th>
        <th>Action</th>
    </tr>
<?php
foreach ($files as $file) {
    $name = $datadir.'/'.$file;
    $data = json_decode(file_get_contents($name),1);
    foreach ($data as $comment) {
        if ($comment['approved']) continue;
        ?>
        <tr>
            <td><?php echo htmlspecialchars($comment['name']);    ?></td>
            <td><?php echo htmlspecialchars($comment['comment']); ?></td>
            <td>
                <a href="/">delete</a>
                <a href="/">approve</a>
            </td>
        </tr>
        <?php
    }
}
?>
    </body>
</html>
