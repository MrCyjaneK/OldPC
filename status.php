<html>
    <head>
        <title>OldPC - status</title>
        <meta name="theme-color" content="#000000">
        <link rel="stylesheet" href="/oldpc.css.php">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="OldPC is an Laptop on my attic, maintained by Czarek Nakamoto. Here you can see how is it doing.">
        <meta name="robots" content="index, follow">
    </head>
    <body>
        <?php include 'head.php'; ?>
<div class="center">
<h2>Infrastructure</h2>
<?php echo file_get_contents('status.txt'); ?>
<h2>Temperature</h2>
<?php echo str_replace("temp1:        "," - ",`sensors | grep " C " `); ?>
<h2>Network</h2><?php
/**
 * @package    vnstat-very-simple-php-frontend
 * @author     Iranian Patriot <sunchi at bioid dot ir>
 * @copyright  2018 The Iranian Patriot Group
 * @license    https://www.gnu.org/licenses/gpl-3.0.en.html
 * @version    Release: 1
 * @note       Modded by Czarek Nakamoto
 */
$command_remove_all_png = "rm *aaa.png";
$output = shell_exec("$command_remove_all_png");
$vnstati_cmd = "/usr/bin/vnstati";
$iface = "eno1";
$date = date("Y_m_d_h_i_sa");
$extra = 'style="width:100%"';
$image_sum_file_name = strval($date)."_sum_aaa.png";
$image_day_file_name = strval($date)."_day_aaa.png";
$image_month_file_name = strval($date)."_month_aaa.png";
$image_hour_file_name = strval($date)."_hour_aaa.png";
$image_top10_file_name = strval($date)."_top10_aaa.png";
$command_sum = "$vnstati_cmd -s -i $iface -o $image_sum_file_name";
$output = shell_exec("$command_sum");
echo "<details open><summary>~~~~~~~~~~~~~ Summary </summary><img $extra src='$image_sum_file_name'/></details>";
$command_top10 = "$vnstati_cmd  -t -i $iface -o $image_top10_file_name";
$output = shell_exec("$command_top10");
echo "<details><summary>~~~~~~~~~~~~~ Top </summary><img $extra src='$image_top10_file_name'/></details>";
$command_hour = "$vnstati_cmd -h -i $iface -o $image_hour_file_name";
$output = shell_exec("$command_hour");
echo "<details><summary>~~~~~~~~~~~~~ Hourly </summary><img $extra src='$image_hour_file_name'/></details>";
$command_day = "$vnstati_cmd  -d -i $iface -o $image_day_file_name";
$output = shell_exec("$command_day");
echo "<details><summary>~~~~~~~~~~~~~ Daily </summary><img $extra src='$image_day_file_name'/></details>";
$command_month = "$vnstati_cmd  -m -i $iface -o $image_month_file_name";
$output = shell_exec("$command_month");
echo "<details><summary>~~~~~~~~~~~~~ Monthly </summary><img $extra src='$image_month_file_name'/></details>";
?><h2>Uptime</h2>
<table style="width: 100%;"><?php
foreach (str_replace(" days", "d", str_replace(" hours", "h", str_replace(" minutes", "m", str_replace(" seconds", "s", str_replace("\t", "BREAK_HERE",str_replace("\t\t", "\t",explode("\n", file_get_contents("uptime")))))))) as $line) {
    $line = preg_replace('/\s+/', ' ', $line);
    echo "<tr>";
    foreach (explode('BREAK_HERE', $line) as $word) {
        echo "<td>".$word."</td>";
    }
    echo "</tr>";
}
?></table>
<h2>Disk</h2>
<table style="width: 100%;"><?php foreach(explode("\n",`df / /opt/shared_files/ -h`) as $line) {
    $line = preg_replace('/\s+/', ' ', $line);
    echo "<tr>";
    $i = 0;
    foreach (explode(' ', $line) as $word) {
        if ($i === 5) continue; // ignore 'mounted'
        if ($word === 'on') continue;
        echo "<td>".$word."</td>";
        $i++;
    }
    echo "</tr>";

} ?></table>
<h2>Ram</h2>
<table style="width:100%;"><?php foreach (explode("\n",`free -h`) as $line) {
    $line = preg_replace('/\s+/', ' ', $line);
    echo "<tr>";
    foreach (explode(' ', $line) as $word) {
        if ($word === 'on') continue;
        echo "<td>".$word."</td>";
    }
    echo "</tr>";
} ?></table>
        <hr />
        <a href="/cgp/">Click here</a> for more graphs
        </div>
    </body>
</html>
