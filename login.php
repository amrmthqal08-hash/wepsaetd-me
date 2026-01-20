<?php
include 'db.php';
$error = ""; $success = "";
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'login';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_user = $_POST['email']; // هذا الإيميل اللي بيدخله خالك
    $input_pass = $_POST['pass'];

    if ($mode == 'signup') {
        $confirm = $_POST['confirm'];
        if ($input_pass !== $confirm) {
            $error = "كلمات المرور غير متطابقة!";
        } else {
            $hashed = password_hash($input_pass, PASSWORD_DEFAULT);
            // الحين الكود يرسل لـ username و password بالضبط مثل ما سويت في HeidiSQL
            $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $input_user, $hashed);
            if ($stmt->execute()) { 
                $success = "تم إنشاء الحساب! سجل دخولك الآن."; 
                $mode = 'login'; 
            } else { $error = "هذا المستخدم موجود مسبقاً!"; }
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->bind_param("s", $input_user);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        
        if ($admin && password_verify($input_pass, $admin['password'])) {
            $_SESSION['logged_in'] = true;
            if (isset($_POST['remember'])) { 
                setcookie(session_name(), session_id(), time() + 31536000, "/"); 
            }
            header("Location: admin.php"); exit();
        } else { $error = "خطأ في البيانات! تأكد من الإيميل والباسوورد."; }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>بوابة الوصول | أساس الإعمار</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { background: #08090a; color: white; font-family: 'Cairo', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { background: #121416; padding: 40px; border-radius: 20px; border: 1px solid #c5a059; width: 360px; text-align: center; border: 1px solid #c5a059; }
        h2 { color: #c5a059; margin-bottom: 25px; font-weight: 900; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; background: #1a1d20; border: 1px solid #333; color: white; border-radius: 10px; box-sizing: border-box; outline: none; }
        button { width: 100%; padding: 12px; background: #c5a059; border: none; border-radius: 50px; font-weight: 900; cursor: pointer; transition: 0.3s; }
        button:hover { background: #e2b86c; }
        .switch-btn { color: #888; font-size: 13px; margin-top: 20px; cursor: pointer; display: block; text-decoration: none; }
        .switch-btn b { color: #c5a059; }
        .msg { font-size: 13px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <h2><?php echo ($mode == 'login') ? "تسجيل الدخول" : "إنشاء حساب"; ?></h2>
        <?php if($error) echo "<p class='msg' style='color:#ff4d4d'>$error</p>"; ?>
        <?php if($success) echo "<p class='msg' style='color:#44ff44'>$success</p>"; ?>
        <form method="POST" autocomplete="off">
            <input type="email" name="email" placeholder="البريد الإلكتروني" required>
            <input type="password" name="pass" placeholder="كلمة المرور" required>
            <?php if ($mode == 'signup'): ?>
                <input type="password" name="confirm" placeholder="تأكيد كلمة المرور" required>
            <?php else: ?>
                <div style="text-align: right; margin-bottom: 15px; color: #888; font-size: 13px;">
                    <input type="checkbox" name="remember" id="rem" style="width:auto;"> <label for="rem">تذكرني</label>
                </div>
            <?php endif; ?>
            <button type="submit"><?php echo ($mode == 'login') ? "دخول" : "إنشاء الحساب"; ?></button>
        </form>
        <a href="?mode=<?php echo ($mode == 'login') ? 'signup' : 'login'; ?>" class="switch-btn">
            <?php echo ($mode == 'login') ? "ليس لديك حساب؟ <b>أنشئ حساباً</b>" : "لديك حساب؟ <b>سجل دخولك</b>"; ?>
        </a>
    </div>
</body>
</html>