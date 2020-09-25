<?php
header("Content-type: text/css; charset: UTF-8");
?>
body {
    background-color: #191919;
    color: white;
    font-size: 150%;
    margin: 40px auto;
    font-family: Monospace;
}
a {
    text-decoration: none;
    color: chartreuse;
}
pre, span {
    white-space: pre-wrap;       /* Since CSS 2.1 */
    white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
    white-space: -pre-wrap;      /* Opera 4-6 */
    white-space: -o-pre-wrap;    /* Opera 7 */
    word-wrap: break-word;       /* Internet Explorer 5.5+ */
    display: inline-block;
    word-break: break-word;
}
.topmenu {
    margin: auto;
    background-color: gray;
    text-align: center;
    margin: 10px 20px;
}
.topmenuelement {
    color: white;
}
.center {
    margin: auto;
    max-width:650px;
}
.pcbr {
}

table {
    border: 1px solid white;
    width: 100%;
    background-color: white
}
th {
    background-color: gray;
}
td {
    background-color: #191919
}

blockquote {
    border: 10px solid #171717;
    background-color: #232323;
    color: white;
}

//@media screen and (min-width: 600px) {
//    .pcbr {
//       display: none;   // hide the BR tag for wider screens (i.e. disable the line break)
//    }
//}

@media only screen and (max-width: 600px) {
    .pcbr {
       display: none;   // hide the BR tag for wider screens (i.e. disable the line break)
    }
    body {
        background-color: #000000;
        max-width: 97%;
        margin: 10px 10px;
    }
    .topmenu {
        margin: auto;
    }
    .topmenuelement {
        display: block;
        width: 95%;
    }
    td {
        background-color: #000000;
    }
}
