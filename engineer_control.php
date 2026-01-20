<?php
session_start(); // تفعيل الجلسة
require_once 'db.php';

// 1. حماية الصفحة (القفل الأمني)
// لن يسمح بالدخول إلا لمن سجل دخوله رسمياً
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 2. معالجة الأوامر (تصفير كلمة المرور أو فك الحظر)
if (isset($_GET['action'])) {
    $admin_id = intval($_GET['id']);
    
    if ($_GET['action'] == 'reset') {
        $new_pass = password_hash("123456", PASSWORD_DEFAULT);
        $conn->query("UPDATE admins SET password = '$new_pass' WHERE id = $admin_id");
        $msg = "تم إعادة تعيين كلمة المرور لـ 123456";
    }
    
    if ($_GET['action'] == 'activate') {
        $conn->query("UPDATE admins SET status = 1 WHERE id = $admin_id");
        $msg = "تم فك الحظر عن الحساب بنجاح";
    }
}

// 3. جلب قائمة المستخدمين
$result = $conn->query("SELECT id, username, email, status FROM admins");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم الهندسية | م/ عمر</title>
    <style>
        body { background: #08090a; color: white; font-family: 'Cairo', sans-serif; padding: 50px; }
        .master-table { width: 100%; border-collapse: collapse; background: #111; border: 1px solid #c5a059; }
        .master-table th, .master-table td { padding: 15px; border: 1px solid #222; text-align: center; }
        .master-table th { background: #c5a059; color: black; }
        .btn { padding: 8px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 0.8rem; }
        .btn-reset { background: #ff9800; color: white; }
        .btn-status { background: #25d366; color: white; }
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 0.7rem; }
        .active { background: rgba(37, 211, 102, 0.2); color: #25d366; }
        .blocked { background: rgba(244, 67, 54, 0.2); color: #f44336; }
    </style>
</head>
<body>

    <h2 style="color: #c5a059;">مرحباً م/ عمر - لوحة التحكم العليا</h2>
    <?php if(isset($msg)) echo "<p style='color: #25d366;'>$msg</p>"; ?>

    <table class="master-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>اسم المستخدم (خالك)</th>
                <th>البريد الإلكتروني</th>
                <th>الحالة</th>
                <th>الإجراءات السريعة</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $row['id']; ?></td>
                <td style="font-weight: bold; color: #c5a059;"><?php echo $row['username']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td>
                    <span class="status-badge <?php echo ($row['status'] == 1) ? 'active' : 'blocked'; ?>">
                        <?php echo ($row['status'] == 1) ? 'نشط' : 'محظور'; ?>
                    </span>
                </td>
                <td>
                    <a href="?action=reset&id=<?php echo $row['id']; ?>" class="btn btn-reset" onclick="return confirm('هل تريد تصفير الباسورد لـ 123456؟')">تصفير الباسورد</a>
                    <?php if($row['status'] == 0): ?>
                        <a href="?action=activate&id=<?php echo $row['id']; ?>" class="btn btn-status">فك الحظر</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <p style="margin-top: 30px; font-size: 0.8rem; color: #555;">* هذه الصفحة سرية ولا تظهر للعامة.</p>

</body>
</html>