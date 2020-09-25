<?php
date_default_timezone_set('Europe/Warsaw');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>OldPC Premium</title>
        <meta name="theme-color" content="#000000">
        <link rel="stylesheet" href="/oldpc.css.php">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="description" content="Get access to OldPC Premium">
        <meta name="robots" content="index, follow">
    </head>
    <body>
        <?php include 'head.php'; ?>
        <h1>OldPC Premium</h1>
        <p>So you like the <a href="/">OldPC</a>, don't you? That's cool! You can get your own user account set up on it by contacting <a href="/me.php">Czarek Nakamoto</a>, you will get access to a lot of things, including prehosted <a href="/files/:D/OldPC Stuff/software.txt">software</a>, and <a href="/files/:D/OldPC Stuff/OldPC Premium.txt">other things</a>.</p>
        <hr />
        <h1>Calculator</h1>
        <label for="ram">RAM</label>
        <select id="ram" name="ram" onChange="updatePrice();">
             <option value="256" selected>256 MB</option>
             <option value="512">512 MB</option>
             <option value="1024">1 GB</option>
             <option value="2048">2 GB</option>
             <option value="4096">4 GB</option>
        </select>
        <br />
        <label for="ssd">SSD Storage</label>
        <select id="ssd" name="ssd" onChange="updatePrice();">
             <option value="5" selected>5 GB</option>
             <option value="10">10 GB</option>
             <option value="25">25 GB</option>
             <option value="50">50 GB</option>
             <option value="100">100 GB</option>
        </select>
        <br />
        <label for="hdd">HDD Storage</label>
        <select id="hdd" name="hdd" onChange="updatePrice();">
             <option value="0" >0 GB</option>
             <option value="10" selected>10 GB</option>
             <option value="25">25 GB</option>
             <option value="50">50 GB</option>
             <option value="100">100 GB</option>
             <option value="250">250 GB</option>
             <option value="500">500 GB</option>
        </select>
        <br />
        <input type="checkbox" id="n1" name="n1" onChange="updatePrice();">
        <label for="n1" selected> Fun</label>
        <br />
        <input type="checkbox" id="n2" name="n2" onChange="updatePrice();">
        <label for="n2" selected> Random reboots</label>
        <br />
        <input type="checkbox" id="n3" name="n3" onChange="updatePrice();">
        <label for="n3" selected> Long motd</label>
        <p>Price: <b id="price_m"></b>$ monthly</p> <i>This price is not final, please contact <a href="/me.php">Me</a> to get real price, you may get discount when you tell me what you will be using OldPC for c:</i><br/>
        <i>Note also that one-time payment is not required in some cases</i>
    </body>
    <script>
    function updatePrice() {
        var ram = Number(document.getElementById('ram').value);
        var ssd = Number(document.getElementById('ssd').value);
        var hdd = Number(document.getElementById('hdd').value);
        // Set the prices
        var ram_price = 0.0013;
        var ssd_price = 0.05;
        var hdd_price = 0.04;
        // Do the math
        var price_m = Number(
            (ram*ram_price) +
            (ssd*ssd_price) +
            (hdd*hdd_price)
        );
        document.getElementById('price_m').innerText = price_m.toFixed(2);
    }
    // Call the function to not leave blank places
    updatePrice();
    </script>
</html>
