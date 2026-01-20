<?php
require_once 'db.php'; // استدعاء الخزان الرئيسي

// 1. تسجيل الزيارة الحقيقية
$u_ip = $_SERVER['REMOTE_ADDR'];
$stmt_visitor = $conn->prepare("INSERT IGNORE INTO visitors (ip_address) VALUES (?)");
$stmt_visitor->bind_param("s", $u_ip);
$stmt_visitor->execute();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>ASAS| OMAR - للمقاولات والهندسة</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&family=Montserrat:wght@900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root {
            --gold: #c5a059;
            --dark: #08090a;
            --white: #ffffff;
            --whatsapp: #25d366;
            --instagram: #e1306c;
            --facebook: #1877f2;
            --snapchat: #fffc00;
        }

        html, body { overflow-x: hidden; width: 100%; margin: 0; padding: 0; scroll-behavior: smooth; background-color: var(--dark); color: var(--white); }
        * { box-sizing: border-box; font-family: 'Cairo', sans-serif; }

        /* Navbar */
        nav { position: fixed; top: 0; width: 100%; padding: 20px 5%; display: flex; justify-content: space-between; align-items: center; z-index: 2000; transition: 0.4s; }
        nav.scrolled { background: rgba(8, 9, 10, 0.95); backdrop-filter: blur(10px); padding: 12px 5%; border-bottom: 1px solid rgba(197, 160, 89, 0.2); }
        .logo { font-family: 'Montserrat', sans-serif; font-size: 1.5rem; font-weight: 900; color: var(--white); text-decoration: none; }
        .logo span { color: var(--gold); }
        .nav-links { display: flex; gap: 25px; list-style: none; }
        .nav-links a { color: white; text-decoration: none; cursor: pointer; transition: 0.3s; }
        .nav-links a:hover { color: var(--gold); }
        .menu-btn { display: none; font-size: 1.8rem; color: var(--gold); cursor: pointer; z-index: 2501; }

        @media (max-width: 768px) {
            .menu-btn { display: block; }
            .nav-links { position: fixed; top: 0; right: -100%; width: 100%; height: 100vh; background: rgba(8, 9, 10, 0.98); flex-direction: column; justify-content: center; align-items: center; transition: 0.5s; }
            .nav-links.active { right: 0; }
        }

        /* Hero */
        .hero { height: 100vh; width: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(rgba(8, 9, 10, 0.6), rgba(8, 9, 10, 0.6)), url('images/back.jpg'); background-size: cover; background-position: center; }
        .hero h1 { font-size: clamp(2rem, 8vw, 4rem); text-align: center; line-height: 1.4; padding: 0 5%; font-weight: 900; text-shadow: 2px 2px 15px rgba(0, 0, 0, 0.7); }

        /* Content Sections */
        .content-section { padding: 80px 5%; display: flex; align-items: center; gap: 40px; flex-wrap: wrap; }
        .content-section:nth-child(even) { flex-direction: row-reverse; background: rgba(255, 255, 255, 0.02); }
        .text-box { flex: 1; min-width: 300px; }
        .img-box { flex: 1; min-width: 300px; border-radius: 20px; overflow: hidden; border: 1px solid var(--gold); height: 380px; }
        .img-box img { width: 100%; height: 100%; object-fit: cover; }
        .section-title { color: var(--gold); font-size: 2.2rem; margin-bottom: 20px; font-weight: 900; }
        .section-desc { line-height: 2.1; color: #d1d1d1; font-size: 1.15rem; text-align: justify; }

        /* Contact Section */
        .contact-sec { padding: 100px 5%; text-align: center; }
        .phone-link { display: inline-block; font-size: 2.2rem; color: var(--gold); text-decoration: none; margin-bottom: 40px; font-weight: 900; transition: 0.3s; }
        .phone-link:hover { text-shadow: 0 0 15px var(--gold); }
        .social-row { display: flex; justify-content: center; gap: 35px; flex-wrap: wrap; }
        .social-row a { font-size: 3.5rem; transition: 0.5s; color: rgba(255, 255, 255, 0.2); }
        .wa:hover { color: var(--whatsapp); filter: drop-shadow(0 0 20px var(--whatsapp)); }
        .fb:hover { color: var(--facebook); filter: drop-shadow(0 0 20px var(--facebook)); }

        footer { padding: 40px; text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.1); background: #050505; color: #888; }
        footer span { color: var(--gold); font-weight: bold; }
    </style>
</head>

<body>

    <nav id="navbar">
        <a href="#" class="logo">العواضي <span>إعمار </span></a>
        <ul class="nav-links" id="navLinks">
            <li><a onclick="scrollToSection('home')">الرئيسية</a></li>
            <li><a onclick="scrollToSection('about')">خبرتنا</a></li>
            <li><a href="projects.php">المشاريع</a></li>
            <li><a onclick="scrollToSection('contact')">اتصل بنا</a></li>
        </ul>
        <div class="menu-btn" id="menuBtn" onclick="toggleMenu()">
            <i class="fas fa-bars"></i>
        </div>
    </nav>

    <header class="hero" id="home">
        <h1 data-aos="zoom-in">إتقان هندسي يجمع بين <br><span style="color:var(--gold)">القوة والفخامة</span></h1>
    </header>

    <section id="about" class="content-section">
        <div class="text-box" data-aos="fade-left">
            <h2 class="section-title">لماذا تختارنا؟</h2>
            <p class="section-desc">
                نحن في مؤسسة العواضي إعمار لا نقدم مجرد خدمات بناء، بل نقدم حلولاً هندسية متكاملة. ندمج بين الخبرة الميدانية العميقة في فنون المقاولات وبين أحدث برمجيات التصميم ثنائي وثلاثي الأبعاد، لنرسم لك ملامح حلمك قبل وضع اللبنة الأولى، مع ضمان أعلى معايير الجودة والاستدامة.
            </p>
        </div>
        <div class="img-box" data-aos="zoom-in">
            <img src="images/12.jpeg" loading="lazy" alt="مشروع هندسي">
        </div>
    </section>

    <section class="content-section">
        <div class="img-box" data-aos="zoom-in">
            <img src="images/15.jpg" loading="lazy" alt="دقة التنفيذ">
        </div>
        <div class="text-box" data-aos="fade-right">
            <h2 class="section-title">فلسفة البناء الحديث</h2>
            <p class="section-desc">
                تتجلى قوتنا في دقة التنفيذ وعظمة الإنجاز. نؤمن أن الأساسات المتينة هي سر البقاء، لذا نلتزم بتنفيذ أدق المواصفات الهندسية "عضم وتشطيب" بأيدي أمهر الكوادر. في كل زاوية من مشاريعنا، ستجد لمسة الفخامة التي تليق بك، حيث نحوّل المساحات الصامتة إلى تحف معمارية تنبض بالحياة، ملتزمين بالجدول الزمني وأمان الهيكل الإنشائي.
            </p>
        </div>
    </section>

    <div style="text-align: center; padding: 60px 0 100px;">
        <a href="projects.php" style="padding: 18px 50px; border: 2px solid var(--gold); color: var(--gold); text-decoration: none; border-radius: 50px; font-weight: 900; transition: 0.3s; font-size: 1.1rem;">
            تصفح معرض المشاريع <i class="fas fa-arrow-left" style="margin-right: 12px;"></i>
        </a>
    </div>

    <section id="contact" class="contact-sec">
        <h2 style="color:var(--gold); margin-bottom: 10px;">نحن بانتظار تواصلك</h2>
        <a href="tel:773174081" class="phone-link">773174081</a>
        <div class="social-row">
            <a href="https://wa.me/967773174081" class="wa" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i></a>
            <a href="https://www.facebook.com/nshwan.al.awady?mibextid=rS40aB7S9Ucbxw6v" class="fb" target="_blank" rel="noopener"><i class="fab fa-facebook-f"></i></a>
        </div>
    </section>

    <footer>
        &copy; 2026 ابو احمد للمقاولات | تطوير <span>OMAR AL-AWADY</span>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 1000, once: true });

        function toggleMenu() {
            const nav = document.getElementById('navLinks');
            const btn = document.getElementById('menuBtn');
            const isOpened = nav.classList.toggle('active');
            btn.innerHTML = isOpened ? '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        }

        function scrollToSection(id) {
            const el = document.getElementById(id);
            if (el) {
                window.scrollTo({ top: el.offsetTop - 70, behavior: 'smooth' });
                document.getElementById('navLinks').classList.remove('active');
            }
        }

        window.onscroll = () => {
            const nav = document.getElementById('navbar');
            if (window.scrollY > 50) { nav.classList.add('scrolled'); }
            else { nav.classList.remove('scrolled'); }
        };
    </script>
</body>
</html>