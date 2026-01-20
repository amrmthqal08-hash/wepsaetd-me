<?php
session_start();
require_once 'db.php'; 

// 1. حماية الصفحة
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 2. تسجيل الزوار
$u_ip = $_SERVER['REMOTE_ADDR'];
$conn->query("INSERT IGNORE INTO visitors (ip_address) VALUES ('$u_ip')");

// 3. معالجة الحذف
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); 
    $res = $conn->query("SELECT image_path FROM projects WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        $file_to_delete = "uploads/projects/" . $row['image_path'];
        if (file_exists($file_to_delete)) { unlink($file_to_delete); }
    }
    $conn->query("DELETE FROM projects WHERE id = $id");
    header("Location: admin.php?msg=deleted#manage");
    exit();
}

// 4. معالجة الرفع (تم التعديل لإضافة الاسم والوصف)
$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['img'])) {
    $dir = "uploads/projects/"; 
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    
    // استلام النصوص من الفورم
    $p_title = $conn->real_escape_string($_POST['title']);
    $p_desc = $conn->real_escape_string($_POST['desc']);

    $file_ext = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
    $allowed = array('jpg', 'jpeg', 'png', 'gif'); 

    if (in_array($file_ext, $allowed)) {
        $name = time() . "_" . $_FILES['img']['name'];
        $path = $dir . $name;
        $img_data = file_get_contents($_FILES['img']['tmp_name']);
        $src = imagecreatefromstring($img_data);
        
        if ($src) {
            imagejpeg($src, $path, 85); 
            imagedestroy($src);
            
            // السطر الذهبي: حفظ الصورة مع الاسم والوصف
            $sql = "INSERT INTO projects (image_path, title, description) VALUES ('$name', '$p_title', '$p_desc')";
            if($conn->query($sql)) {
                $msg = "<div class='alert success'>✅ تم رفع المشروع ($p_title) بنجاح</div>";
            }
        }
    } else {
        $msg = "<div class='alert' style='color:red; border:1px solid red;'>❌ خطأ: مسموح بالصور فقط!</div>";
    }
}

// جلب البيانات
$v_count = $conn->query("SELECT COUNT(*) as t FROM visitors")->fetch_assoc()['t'];
$projects_res = $conn->query("SELECT * FROM projects ORDER BY id DESC");
$p_count = $projects_res->num_rows;
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم | أساس الإعمار</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --gold: #c5a059; --dark: #08090a; --card: #121416; --red: #ff4d4d; }
        body { background: var(--dark); color: white; font-family: 'Cairo', sans-serif; margin: 0; display: flex; scroll-behavior: smooth; }
        .sidebar { width: 260px; background: #000; height: 100vh; padding: 30px 20px; border-left: 1px solid var(--gold); position: fixed; right: 0; }
        .sidebar h2 { color: var(--gold); text-align: center; font-weight: 900; border-bottom: 2px solid var(--gold); padding-bottom: 10px; }
        .nav-link { display: flex; align-items: center; gap: 10px; color: white; text-decoration: none; padding: 15px; border-radius: 8px; margin-bottom: 10px; }
        .nav-link:hover, .nav-link.active { background: var(--gold); color: black; }
        .main { flex: 1; margin-right: 260px; padding: 40px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .box { background: var(--card); padding: 25px; border-radius: 15px; border-right: 5px solid var(--gold); }
        .box p { font-size: 35px; font-weight: 900; margin: 10px 0; }
        
        /* استايل الفورم الجديد */
        .upload-area { background: var(--card); padding: 30px; border-radius: 20px; border: 2px dashed #333; }
        .input-group { margin-bottom: 15px; text-align: right; }
        .input-group label { display: block; margin-bottom: 5px; color: var(--gold); font-weight: bold; }
        .input-group input, .input-group textarea { 
            width: 100%; padding: 12px; background: #08090a; border: 1px solid #333; 
            border-radius: 8px; color: white; font-family: 'Cairo';
        }
        .input-group input:focus { border-color: var(--gold); outline: none; }

        button { background: var(--gold); color: black; border: none; padding: 15px 40px; border-radius: 50px; cursor: pointer; font-weight: 900; width: 100%; font-size: 1.1rem; }
        .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px; }
        .img-item { position: relative; border-radius: 10px; overflow: hidden; border: 1px solid #333; background: var(--card); }
        .img-item img { width: 100%; height: 150px; object-fit: cover; }
        .img-info { padding: 10px; font-size: 0.8rem; }
        .img-info h4 { color: var(--gold); margin: 0; }
        .delete-btn { position: absolute; top: 5px; left: 5px; background: var(--red); color: white; border-radius: 5px; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; text-decoration: none; }
        .alert { padding: 15px; border-radius: 10px; margin-bottom: 20px; background: rgba(197, 160, 89, 0.2); color: var(--gold); border: 1px solid var(--gold); }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>أساس الإعمار</h2>
    <nav style="margin-top:20px;">
        <a href="admin.php" class="nav-link active"><i class="fas fa-chart-line"></i> الإحصائيات</a>
        <a href="projects.php" target="_blank" class="nav-link"><i class="fas fa-images"></i> عرض المشاريع</a>
        <a href="index.php" target="_blank" class="nav-link"><i class="fas fa-external-link-alt"></i> الموقع</a>
        <a href="logout.php" class="nav-link" style="color:var(--red)"><i class="fas fa-sign-out-alt"></i> خروج</a>
    </nav>
</div>

<div class="main">
    <h1>لوحة التحكم الهندسية</h1>
    <div class="stats">
        <div class="box"><h3>الزوار</h3><p><?php echo number_format($v_count); ?></p></div>
        <div class="box"><h3>إجمالي المشاريع</h3><p><?php echo $p_count; ?></p></div>
    </div>

    <div class="upload-area">
        <h2 style="margin-top:0;">إضافة مشروع جديد</h2>
        <?php echo $msg; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label>اسم المشروع</label>
                <input type="text" name="title" placeholder="مثلاً: فيلا سكنية - صنعاء" required>
            </div>
            <div class="input-group">
                <label>وصف المشروع</label>
                <textarea name="desc" rows="3" placeholder="تكلم عن جودة العمل أو تفاصيل التنفيذ..." required></textarea>
            </div>
            <div class="input-group">
                <label>صورة المشروع</label>
                <input type="file" name="img" accept="image/*" required>
            </div>
            <button type="submit"><i class="fas fa-cloud-upload-alt"></i> اعتماد وحفظ المشروع</button>
        </form>
    </div>

    <div class="manage-images" id="manage" style="margin-top:40px;">
        <h2><i class="fas fa-edit"></i> المشاريع الحالية</h2>
        <div class="image-grid">
            <?php while($row = $projects_res->fetch_assoc()): ?>
                <div class="img-item">
                    <img src="uploads/projects/<?php echo $row['image_path']; ?>">
                    <div class="img-info">
                        <h4><?php echo $row['title']; ?></h4>
                        <p><?php echo substr($row['description'], 0, 50); ?>...</p>
                    </div>
                    <a href="admin.php?delete=<?php echo $row['id']; ?>#manage" class="delete-btn" onclick="return confirm('هل أنت متأكد من حذف هذا المشروع؟')">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
</body>
</html>