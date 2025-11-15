<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
session_unset();  // menghapus semua session
session_destroy(); // menghancurkan session

header("Location: ../index.php");
exit;
?>