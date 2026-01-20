<?php
$host = "mysql.railway.internal";
$user = "root";
$pass = "wRvDaoyUJRmDrbfdYFnnVhIrTRycQMGY";
$db   = "railway";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    // هذا السطر سيخبرك بالضبط ما هي المشكلة لو فشل
    die("Connection failed: " . $conn->connect_error);
}
?>