<html>
    <head>
        <title>OldPC</title>
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
<img id="selfie" src="/selfie.php" width="100%"></img>
<h1>What is OldPC?</h1>
OldPC is 'Hewlett-Packard HP Pavilion 17 Notebook PC' connected over ethernet cable to the <a href="https://en.wikipedia.org/wiki/IP_over_Avian_Carriers">IPoAC</a> router that gives us connection to the internet that is almost twice as fast as standard dial-up internet!<br />
In addition to that, it has an non-water cooling system with "Fan Always On" techonogy (that is impossible to disable in Basic Input/Output System) which keep us on about 40 degrees! It's CPU is 'AMD A8-4500M APU with Radeon(tm) HD Graphics', also, if you choose to use OldPC as your hosting provider, you will get:<br />
&nbsp;&nbsp;&nbsp;&nbsp;> Random reboots<br />
&nbsp;&nbsp;&nbsp;&nbsp;> Unstable and slow network connection<br />
&nbsp;&nbsp;&nbsp;&nbsp;> Closed ports<br />
&nbsp;&nbsp;&nbsp;&nbsp;> 1600 <strike>600</strike> GB of shared HDD storage<br />
&nbsp;&nbsp;&nbsp;&nbsp;> 7 <strike>8</strike> GB of RAM<br />
&nbsp;&nbsp;&nbsp;&nbsp;> Access to camera, on which you can see the router, which is on my attic<br />
&nbsp;&nbsp;&nbsp;&nbsp;> KVM based virtualization, with os of your choice<br />
&nbsp;&nbsp;&nbsp;&nbsp;> Security provided by AMD Platform Security Processor<br />
&nbsp;&nbsp;&nbsp;&nbsp;> No warranty of any kind.<br />
<hr />
        No, this is not a joke. Feel free to grab OldPC source from git and setup your own server.
    <script>
    var nextid = 0;
    function prefetchNext() {
        nextid++;
        url = "/selfie.php?id="+nextid;
        var req = new XMLHttpRequest();
        req.open('GET', url, true);
        req.responseType = 'blob';
        image = document.getElementById('selfie');
        req.onload = function() {
            if (this.status === 200) {
                var imageBlob = this.response;
                img = URL.createObjectURL(imageBlob); // IE10+
                // Video is now downloaded
                // and we can set it as source on the video element
                image.src = img
            }
        }
        req.onerror = function() {
            //alert("An error occured, refreshing page");
            window.location.href = "/";
        }
        req.send();
    }
    setInterval(() => {
        prefetchNext();
    }, 10 * 1000)
    </script>
    </body>
</html>
