<?php
session_start();
session_destroy();
echo("Logged out");
echo("<script>document.location.href=\"/index.html\";</script>");
?>