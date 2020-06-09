<html>
    <head>
        <title>OldPC - status</title>
        <style>
        body {
            background-color: black;
            color: white;
            font-size: 150%;
            margin:40px auto;
            max-width:650px
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
        <pre><code>
<h2>Temperature</h2>
<?php echo str_replace("temp1:        "," - ",`sensors | grep " C " `); ?>
<!--<h2>Network</h2>
<table style="width:100%"><?php
foreach (explode("\n",file_get_contents('speedtest')) as $line) {
    $line = preg_replace('/\s+/', ' ', $line);
    echo "<tr>";
    foreach (explode(' ', $line) as $word) {
        echo "<td>".$word."</td>";
    }
    echo "</tr>";

}
?></table>
<h2>Uptime</h2>
<table style="width: 100%;"><?php
foreach (str_replace(" days", "d", str_replace(" hours", "h", str_replace(" minutes", "m", str_replace(" seconds", "s", str_replace("\t", "BREAK_HERE",str_replace("\t\t", "\t",explode("\n", file_get_contents('uptime')))))))) as $line) {
    $line = preg_replace('/\s+/', ' ', $line);
    echo "<tr>";
    foreach (explode('BREAK_HERE', $line) as $word) {
        echo "<td>".$word."</td>";
    }
    echo "</tr>";
}
?></table>-->
<h2>Disk</h2>
<table style="width: 100%;"><?php foreach(explode("\n",`df . -h`) as $line) {
    $line = preg_replace('/\s+/', ' ', $line);
    echo "<tr>";
    foreach (explode(' ', $line) as $word) {
        if ($word === 'on') continue;
        echo "<td>".$word."</td>";
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
        </code></pre>
    </body>
</html>
