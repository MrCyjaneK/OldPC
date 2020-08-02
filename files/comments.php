<?php
if (!$inc) die('no');
$datadir = "/opt/shared_files/.oldpcdata";
date_default_timezone_set('Europe/Warsaw');
// Config is overwritten by config.php placeced anywhere in parent directory.
$config = [
    "suicide" => false, // Set this to cleanly exit script.
    "max_level" => "/opt/shared_files", // Don't go deeper than that
    "allow_comments" => false, // Don't allow comments by default
];
?>
<?php
$c_path = [];
foreach (explode('/',$path) as $dir) {
    $c_path[] = $dir;
    $p = implode('/',$c_path);
    if (substr($p, 0,strlen($config['max_level'])) == $config['max_level']) {
        if (is_dir($p)) {
            if (file_exists($p.'/.dirconf') && is_file($p.'/.dirconf')) {
                foreach (explode("\n", file_get_contents($p.'/.dirconf') )as $cline) {
                    $carr = explode("=", $cline);
                    if (isset($carr[1])) {
                        $config[$carr[0]] = $carr[1];
                    }
                }
            }
        }
    }
}
if ($config['allow_comments'] == true) {
?>
<div id="comments">
    <?php
    $filename = $datadir.'/'.preg_replace('{(.)\1+}','$1',preg_replace("/[^a-zA-Z0-9]+/", "", $_GET['p']))."-comments.json";
    if (file_exists($filename)) {
        foreach (json_decode(file_get_contents($filename), true) as $comment) {
            if ($comment["approved"] == false) {
                $pfx = "<b>[!!!]</b> ";
                $pfxlong = "<b>This comment was not approved! You can read it's content, but I'm not responsible for what has been written in it.</b><br />";
            } else {
                $pfx = "";
                $pfxlong = "";
            }
            echo "<details><summary>$pfx".htmlspecialchars($comment["name"])." @ <i>".htmlspecialchars(date("Y/m/d H:i:s", $comment['time']))."</i></summary>$pfxlong".str_replace("\n","<br />",htmlspecialchars($comment["comment"]))."</details>";
        }
    }
?>
</div>
<details><summary>Write a comment</summary>
<div id="write">
    <form method="post" action="post_comment.php">
        <table st>
            <tr><td width="100">Name</td><td><input type="text" name="name" style="width: 400px;" /></td></tr>
            <tr><td>Comment</td><td><textarea name="comment" rows="10" cols="40" style="width: 400px; height: 100px;"></textarea></td></tr>
            <tr><td></td><td><img src="/files/captcha.php?session_name=oldpc_files" alt="Captcha"/></td></tr>
            <tr><td>Captcha:</td><td><input type="text" name="captcha" /></td></tr>
            <tr><td></td><td align="right"><input type="submit" name="submit" value="Submit" /></td></tr>
        </table>
        <input type="hidden" name="p" value="<?php echo htmlspecialchars($_GET['p']); ?>" />
        <input type="hidden" name="drive" value="<?php echo htmlspecialchars($drive); ?>" />
    </form>
</div>
</details>
<?php } ?>
