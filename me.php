<?php
date_default_timezone_set('Europe/Warsaw');
function humanTiming ($time) {
    $time = time() - $time; // to get the time since that moment
    $periods = array (
        31536000 => 'year',
        2592000 => 'month',
        86400 => 'day',
    );
    foreach($periods AS $seconds => $name){
        $num = floor($time / $seconds);
        $time -= ($num * $seconds);
        $ret .= $num.' '.$name.(($num > 1) ? 's' : '').' ';
    }
    return trim($ret);
}

$mespecs = [
    "Year of production" => "Early 2004",
    "Full name" => "<b>GDRP PROTECTED</b>",
    "Height" => "1,9575 yards",
    "Weight" => "0x981 oz",
    "Social condition" => "null",
    "Place od residence" => "Boondocks, Silesian Voivodeship, Poland",
    "Education" => "In progress of acquiring in pain",
    "Hobby" => "cd: /var/lib/personal_life: No such file or directory",
    "Contact" => '<a href="https://t.me/mrcyjanek">Telegram</a>'
];

?>
<html>
    <head>
        <title>About me.php</title>
        <meta name="theme-color" content="#000000">
        <link rel="stylesheet" href="/oldpc.css.php">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="Czarek Nakamoto is a guy who manage OldPC, writes PHP and bash, and exist in the universe for no special reason">
        <meta name="robots" content="index, follow">
    </head>
    <div>
    <body>
        <?php include 'head.php'; ?>
        <div class="center">
            <h1>Who is Czarek Nakamoto?</h1>
            Czarek Nakamoto is one of <?= number_format(( 3802527680 + (time()*2.4)), 0, '', ' '); ?> people living on this planet, currently his age is <?= humanTiming(1075374000) ?>. You can see more about him in 'Product Specification' tab.
            <h1>Product Specification</h1>
            <table style="width:100%">
                <?php foreach ($mespecs as $key => $value) {?><tr>
                    <td style="width: 30%"><?= $key ?></td>
                    <td><?= $value ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </body>
</html>
