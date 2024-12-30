<?php
session_start();
session_destroy();
header("Location: ../html/Login.htm");
exit();
?>
