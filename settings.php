<?php
session_start(); // تفعيل الجلسة أولاً
include 'db.php';

// 1. حماية الصفحة (القفل الأمني)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$message = "";

// 2. معالجة البيانات عند الضغط على الزر
if (isset($_POST['submit_update'])) {
    
    $email = $_POST['email'];
    $new_pass = $_POST['new_password'];
    $admin_id = $_SESSION['admin_id']; // جلب الـ ID من الجلسة لضمان تحديث الحساب الصحيح
    
    // تشفير الباسورد الجديد
    $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);
    
    // تحديث البيانات باستخدام الاستعلامات المحمية (Prepare)
    // التحديث الآن يتم بناءً على ID المستخدم المسجل دخوله حالياً
    $sql = "UPDATE admins SET email = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $email, $hashed_password, $admin_id);
    
    if ($stmt->execute()) {
        $message = "✅ تم تحديث بياناتك بنجاح! سيتم تسجيل خروجك للأمان...";
        
        // تدمير الجلسة لإجبار المستخدم على الدخول بالبيانات الجديدة
        session_destroy();
        header("refresh:3;url=login.php"); 
    } else {
        $message = "❌ حدث خطأ أثناء التحديث: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>⚙️ إعدادات الحساب</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; background: #08090a; color: white; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: #121416; padding: 35px; border-radius: 20px; border: 1px solid #c5a059; width: 340px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        h3 { color: #c5a059; margin-bottom: 25px; font-weight: 900; }
        input { width: 100%; padding: 12px; margin: 10px 0; background: #1a1d20; border: 1px solid #333; color: white; border-radius: 10px; box-sizing: border-box; outline: none; }
        input:focus { border-color: #c5a059; }
        button { width: 100%; padding: 12px; background: #c5a059; color: black; border: none; border-radius: 50px; cursor: pointer; font-size: 16px; font-weight: bold; transition: 0.3s; }
        button:hover { background: #e2b86c; transform: scale(1.02); }
        .status { margin-bottom: 20px; font-weight: bold; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <h3>تحديث الحساب ⚙️</h3>
        
        <?php if($message) echo "<p class='status' style='color:#25d366;'>$message</p>"; ?>
        
        <form method="POST">
            <input type="email" name="email" placeholder="البريد الإلكتروني الجديد" required>
            <input type="password" name="new_password" placeholder="كلمة المرور الجديدة" required>
            <button type="submit" name="submit_update">حفظ التغييرات</button>
        </form>
        
        <p style="margin-top: 20px;"><a href="admin.php" style="color: #888; text-decoration: none; font-size: 13px;">العودة للوحة التحكم</a></p>
    </div>
</body>
</html>