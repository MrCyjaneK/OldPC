<?php
//if (!isset($argv[1])) {
//    die('Usage: "php updatestatus.php"');
//}
// Colors
define('OK_BEGIN', '<span style="color: green">');
define('OK_END'  , '</span>');

define('FAIL_BEGIN', '<b><span style="color: red">');
define('FAIL_END'  , '</span></b>');

checkStatus('mrcyjanek.net connection (local)' , "http://oldpc/ok.txt");
checkStatus('mrcyjanek.net connection (remote - OpenVPN)', "http://10.8.0.2/ok.txt");
checkStatus('mrcyjanek.net connection (remote - domain)', "http://mrcyjanek.net/ok.txt");
checkStatus('mrcyjanek.net connection (remote - domain ssl)', "https://mrcyjanek.net/ok.txt");
checkStatus('php-fpm', "http://oldpc/ok.php");
checkStatus('Files', "http://oldpc/files/raw-:D/.ok.txt");
checkStatus('Gitea', "http://oldpc/git/mrcyjanek/OldPC/raw/branch/master/ok.txt");
checkStatus('Jenkins', "http://oldpc/ci/job/meta/lastSuccessfulBuild/artifact/status.txt");
checkStatus('W(allet) API - Bitcoin', "http://oldpc/wapi/ping.php?cur=BTC");
checkStatus('W(allet) API - Litecoin', "http://oldpc/wapi/ping.php?cur=LTC");
checkStatus('W(allet) API - Dogecoin Wow!', "http://oldpc/wapi/ping.php?cur=DOGE");
function checkStatus($name, $url) {
    if (substr(@file_get_contents($url), 0, 2) === 'OK') {
        echo "[".OK_BEGIN." OK ".OK_END."]";
    } else {
        echo "[".FAIL_BEGIN."FAIL".FAIL_END."]";
    }
    echo " $name<br />";
}
