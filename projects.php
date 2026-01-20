<?php
// 1. الاتصال الموحد بقاعدة البيانات
require_once 'db.php'; 

// 2. جلب الصور مع العنوان والوصف من الأحدث إلى الأقدم
$result = $conn->query("SELECT image_path, title, description FROM projects ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>معرض المشاريع | أساس الإعمار</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root { --gold: #c5a059; --dark: #08090a; --card-bg: #121416; --white: #ffffff; }
        body { background-color: var(--dark); color: var(--white); font-family: 'Cairo', sans-serif; margin: 0; overflow-x: hidden; scroll-behavior: smooth; }
        
        header { 
            height: 45vh; display: flex; flex-direction: column; align-items: center; justify-content: center; 
            background: linear-gradient(rgba(8, 9, 10, 0.85), rgba(8, 9, 10, 0.85)), url('images/1.jpg') no-repeat center center; 
            background-size: cover; border-bottom: 2px solid var(--gold); text-align: center; padding: 0 20px; 
        }
        
        header h1 { font-size: clamp(1.8rem, 6vw, 3rem); color: var(--gold); font-weight: 900; margin: 0; }
        
        .back-home { 
            color: white; margin-top: 25px; text-decoration: none; border: 1px solid var(--gold); 
            padding: 10px 30px; border-radius: 50px; transition: 0.3s; font-weight: 700;
        }
        .back-home:hover { background: var(--gold); color: black; }

        .gallery-grid { display: grid; grid-template-columns: repeat(1, 1fr); gap: 20px; padding: 20px 15px; }
        @media (min-width: 768px) { 
            .gallery-grid { grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; padding: 40px 5%; } 
        }
        
        .project-card { 
            background: var(--card-bg); border-radius: 15px; overflow: hidden; 
            border: 1px solid rgba(255, 255, 255, 0.05); transition: 0.4s ease;
            display: flex; flex-direction: column;
        }
        
        @media (min-width: 768px) { .project-card:hover { transform: translateY(-8px); border-color: var(--gold); } }
        
        .img-container { width: 100%; height: 250px; cursor: pointer; overflow: hidden; }
        .project-card img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .project-card:hover img { transform: scale(1.1); }

        /* منطقة تفاصيل المشروع تحت الصورة */
        .project-info { padding: 20px; border-top: 1px solid rgba(197, 160, 89, 0.2); }
        .project-info h3 { color: var(--gold); margin: 0 0 10px 0; font-size: 1.3rem; font-weight: 900; }
        .project-info p { color: #ccc; font-size: 0.95rem; line-height: 1.6; margin: 0; }

        .fullscreen-viewer { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.98); display: none; justify-content: center; align-items: center; z-index: 99999; }
        .fullscreen-viewer img { max-width: 95%; max-height: 85%; object-fit: contain; border: 2px solid var(--gold); }
        .close-btn { position: absolute; top: 30px; left: 30px; background: var(--gold); color: #000; border: none; padding: 12px 30px; border-radius: 50px; cursor: pointer; font-weight: 900; }

        footer { background: #050505; padding: 40px 20px; text-align: center; border-top: 1px solid rgba(197, 160, 89, 0.2); }
        .social-icons { display: flex; justify-content: center; gap: 15px; margin-top: 20px; }
        .social-btn {
            width: 45px; height: 45px; background: #111; border: 1px solid rgba(197, 160, 89, 0.3);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            color: var(--gold); text-decoration: none; font-size: 1.2rem; transition: 0.3s;
        }
        .social-btn:hover { transform: translateY(-5px); border-color: var(--gold); }
        .wa:hover { color: #25d366; } .fb:hover { color: #1877f2; }

        .admin-lock { position: fixed; bottom: 25px; right: 25px; width: 45px; height: 45px; background: rgba(197, 160, 89, 0.1); color: var(--gold); border: 1px solid var(--gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; z-index: 1000; }
    </style>
</head>
<body>

<header>
    <h1>معرض إنجازاتنا</h1>
    <p style="color: #888; margin-top: 10px;">شاهد جودة التنفيذ في مشاريع أساس الإعمار</p>
    <a href="index.php" class="back-home"><i class="fas fa-home"></i> العودة للرئيسية</a>
</header>

<div class="gallery-grid">
<?php
if($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()):
        $imagePath = "uploads/projects/" . $row['image_path'];
?>
    <div class="project-card" data-aos="fade-up">
        <div class="img-container" onclick="goFull('<?php echo $imagePath; ?>')">
            <img src="<?php echo $imagePath; ?>" loading="lazy" alt="<?php echo htmlspecialchars($row['title']); ?>">
        </div>
        <div class="project-info">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
        </div>
    </div>
<?php endwhile; } else { ?>
    <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
        <i class="fas fa-images" style="font-size: 3rem; color: #333;"></i>
        <p style="margin-top: 15px; color: #666;">لا توجد مشاريع معروضة حالياً</p>
    </div>
<?php } ?>
</div>

<div class="fullscreen-viewer" id="fullViewer">
    <button class="close-btn" onclick="exitFull()">إغلاق</button>
    <img id="fullViewImg">
</div>

<footer>
    <p style="color: var(--gold); margin-bottom: 10px;">تواصل معنا عبر منصاتنا الرسمية</p>
    <div class="social-icons">
        <a href="https://wa.me/967773174081" target="_blank" class="social-btn wa"><i class="fab fa-whatsapp"></i></a>
        <a href="https://www.facebook.com/nshwan.al.awady" target="_blank" class="social-btn fb"><i class="fab fa-facebook-f"></i></a>
    </div>
    <p style="color: #444; font-size: 0.8rem; margin-top: 25px;">© 2026 أساس الإعمار | تطوير م/ عمر العوضي</p>
</footer>

<a href="admin.php" class="admin-lock"><i class="fas fa-lock"></i></a>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true });
    const viewer = document.getElementById('fullViewer'), vImg = document.getElementById('fullViewImg');
    function goFull(src) { vImg.src = src; viewer.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
    function exitFull() { viewer.style.display = 'none'; document.body.style.overflow = 'auto'; }
</script>
</body>
</html>