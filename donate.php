<?php
date_default_timezone_set('Europe/Warsaw');
?>
<html>
    <head>
        <title>Donate to OldPC</title>
        <style>
        body {
            background-color: black;
            color: white;
            font-size: 150%;
            margin:40px auto;
            max-width:650px;
            font-family: Monospace;
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
    <body>
        <?php include 'head.php'; ?>
        <?php
        $price_onetime = 0;
        foreach ($_GET as $key => $value) {
            if ($value < 0) {
                $_GET[$key] = 0;
            }
        }
        $price_monthly = 0;
        $opts = [
            "storage" => [
                "label" => "Addinational HDD Storage in GB (for more than 128GB contact me)",
                "price_onetime" => 0.05,
                "price_monthly" => 0.02,
                "note" => "HDD storage, purchased alone without ram will be usable as pure-static web server."
            ],
            "ram" => [
                "label" => "Addinational RAM Memory in MB (for more than 8GB contact me)",
                "price_onetime" => 0.0050,
                "price_monthly" => 0.0010,
                "note" => "If you want to run PHP web server, 64mb can handle it. If you want to run KVM machine, I recommend 512mb"
            ]
        ];
        foreach ($opts as $key => $value) {
            $price_onetime = $price_onetime + ($value["price_onetime"] * $_GET["$key"]);
            $price_monthly = $price_monthly + ($value["price_monthly"] * $_GET["$key"]);
        } ?>
        <h1>Why should I donate?</h1>
        When you donate to OldPC, you will get many benefits, like for example<br />
        &nbsp;&nbsp;&nbsp;&nbsp;> Shell/VNC access to virtual machine running under KVM<br />
        &nbsp;&nbsp;&nbsp;&nbsp;> Awesome subdomain *.oldpc.mrcyjanek.net<br />
        &nbsp;&nbsp;&nbsp;&nbsp;> Privileged PeerTube account.<br />
        &nbsp;&nbsp;&nbsp;&nbsp;> Dedicated support, when needed. (Which is often)<br />
        &nbsp;&nbsp;&nbsp;&nbsp;> HDD Storage (public and private)<br />
        &nbsp;&nbsp;&nbsp;&nbsp;> No warranty of any kind<br />
        <br />
        &nbsp;&nbsp;&nbsp;&nbsp;> btc: <i>bc1q8r4z2u4rjw8gw2p5kv7cw2uep5d2s5wxnj0zph</i><br />
        &nbsp;&nbsp;&nbsp;&nbsp;> doge: <i>DFMtLwbefVSWQJaCER2KwKt5aG2MN3azzz</i>
        <hr />
        <h1>OldPC Premium</h1>

        <?php if ($price_onetime != 0 || $price_monthly != 0) { ?>
            To get machine like you have specified below, you will need to donate:<br />
            <?php if ($price_onetime != 0) { ?>
                one-time donation: <?php echo number_format($price_onetime, 2,'.',' '); ?> USD <br />
            <?php } ?>
            <?php if ($price_monthly != 0) { ?>
                monthly donation: <?php echo number_format($price_monthly, 2,'.',' '); ?> USD<br />
            <?php } ?>
            Numbers visible above are just suggestions c:, no matter of donation size you will get some bonus.
            <hr />
        <?php } ?>
        <form method="GET">
            <?php foreach ($opts as $key => $value) { ?>
                <label for="<?= $key ?>">
                    &nbsp;&nbsp;&nbsp;&nbsp;> <?= $value['label']; ?><br />
                    &nbsp;&nbsp;&nbsp;&nbsp;> <?= $value['note']; ?>
                </label><br>
                <center><input type="text" id="<?= $key ?>" name="<?= $key ?>" value="<?= round($_GET[$key]) ?>"></center><br>
            <?php } ?><br />
            <b>Keep in mind that you are not allowed to host anything illegal, including copyrighted content, you are also not allowed to seed torrents, use it for scam, cryptocurrency mining or phishing on OldPC. Doing anything considered as illegal will lead to termination of your account.</b><br /><br />
            <input type="submit" value="Accept and submit">
            <?php
            if ($price_onetime != 0) {
                ?>
                Minimal one-time donation: <?= number_format($price_onetime, 2,'.',' ') ?> USD <br />
                <?php
            }
            ?>
            <?php
            if ($price_monthly != 0) {
                ?>
                Minimal monthly donation: <?= number_format($price_monthly, 2,'.',' ') ?> USD
                <?php
            }
            ?>
        </form>
    </body>
</html>
