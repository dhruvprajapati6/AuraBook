<?php
session_start();
session_unset();
session_destroy(); // Saara session data delete
header("Location: index.php"); // Home page par bhej rahe hain
exit();
?>