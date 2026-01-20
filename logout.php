<?php
/**
 * نظام تسجيل الخروج - مؤسسة أساس الإعمار
 * وظيفة الملف: إنهاء الجلسة وحماية لوحة التحكم
 */

// 1. بدء التعامل مع الجلسة الحالية
session_start();

// 2. تفريغ جميع متغيرات الجلسة (مثل الحالة logged_in)
$_SESSION = array();

// 3. إذا كنت تستخدم ملفات تعريف الارتباط (Cookies) للجلسة، فمن الأفضل مسحها أيضاً
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. تدمير الجلسة نهائياً من السيرفر
session_destroy();

// 5. التوجيه الفوري لصفحة تسجيل الدخول
header("Location: login.php");
exit();
?>