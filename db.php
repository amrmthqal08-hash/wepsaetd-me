<?php
// 1. منع ظهور التنبيهات لكي لا يخرب شكل التصميم للمستخدم
error_reporting(E_ERROR | E_PARSE);

// 2. ضبط الوقت قبل بدء أي جلسة
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 31536000);
    ini_set('session.cookie_lifetime', 31536000);
    session_start();
}

// 3. بيانات الاتصال الداخلي لـ Railway (الحل النهائي)
$host = "mysql.railway.internal"; 
$user = "root";
$pass = "wRvDaoyUJRmDrbfdYFnnVhIrTRycQMGY";
$db   = "railway";

// 4. الاتصال بدون رقم منفذ لأنه اتصال داخلي سريع
$conn = new mysqli($host, $user, $pass, $db);

// 5. التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// 6. ضبط الترميز لدعم اللغة العربية بشكل صحيح
$conn->set_charset("utf8mb4");

?>