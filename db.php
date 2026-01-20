<?php
// منع ظهور التحذيرات للمستخدم لكي لا يخرب شكل التصميم
error_reporting(E_ERROR | E_PARSE);

// ضبط الوقت قبل بدء أي جلسة
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 31536000);
    ini_set('session.cookie_lifetime', 31536000);
    session_start();
}

$conn = new mysqli("localhost", "root", "", "basic_const");
if ($conn->connect_error) {
    die("فشل الاتصال");
}
$conn->set_charset("utf8mb4");
?>