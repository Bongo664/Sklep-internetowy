<?php
$db_host = "localhost";
$db_user = "root";
$db_password = "";
$db_name = "sklep";

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);

if (!$conn) {
    die("Brak Połączenia: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>
