<?php 
include 'db.php'; 
session_start();

// 1. LANGUAGE SETTING
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$current_lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'gu'; // Defaulted to Gujarati/English

$words = [
    'en' => [
        'site_title' => 'AuraBook',
        'site_subtitle' => 'Affordable Books for Rural Students',
        'welcome' => 'Welcome',
        'my_orders' => 'My Orders',
        'my_sales' => 'My Sold Books',
        'my_profile' => 'My Profile',
        'track_returns' => 'Track Returns',
        'logout' => 'Logout',
        'login' => 'Login / Register',
        'admin_login' => 'Admin Login',
        'sell_book' => '+ Sell Book',
        'hero_title' => 'Find & Sell Used & New Books in Your Village',
        'hero_subtitle' => 'Connecting students across 10+ nearby villages for affordable education.',
        'old_books_btn' => 'Browse Used Books',
        'new_books_btn' => 'Buy New Books',
        'donets_books_btn' => 'Buy Donate Books',
        'search_placeholder' => 'Search by Book Title, Author or Subject...',
        'search_btn' => 'Search',
        'buy_title' => 'How to Buy Books?',
        'buy_sub' => 'Get your required books delivered directly to your village in 3 easy steps.',
        'buy_step1_t' => 'Search & Select',
        'buy_step1_d' => 'Browse new or used books available in your or nearby villages.',
        'buy_step2_t' => 'Place Order',
        'buy_step2_d' => 'Review details, select Cash on Delivery, and confirm your village address.',
        'buy_step3_t' => 'Village Delivery',
        'buy_step3_d' => 'Our delivery team hands over the book right at your doorstep.',
        'sell_title' => 'How to Sell Used Books?',
        'sell_sub' => 'Earn cash by selling books you no longer need to other students.',
        'sell_step1_t' => 'Click "Sell Book"',
        'sell_step1_d' => 'Fill a quick 1-minute form with basic details about your book.',
        'sell_step2_t' => 'Upload Photos & Price',
        'sell_step2_d' => 'Take clear photos of cover pages and set your selling price.',
        'sell_step3_t' => 'Get Paid on Pickup',
        'sell_step3_d' => 'Once ordered, our agent collects the book from your home and pays you.',
        'sell_now_btn' => '+ Upload Your Book Now',
        'video_title' => 'How GaonCart Works?',
        'video_sub' => 'Watch this short guide to learn how to buy and sell easily.',
        'live_visitors' => 'Live Platform Activity',
        'total_visitors' => 'TOTAL VISITORS',
        'today_visitors' => "TODAY'S VISITORS",
        'online_now' => 'ONLINE NOW',
        'testimonials_title' => 'What Our Student Community Says',
        'faq_title' => 'Frequently Asked Questions (FAQ)',
        'need_help' => 'Need Any Assistance?',
        'help_desc' => 'Contact our dedicated support line for orders, sales, or delivery queries.',
        'call_us' => 'Call Helpline',
        'newsletter_title' => 'Stay Updated with New Arrivals',
        'newsletter_sub' => 'Subscribe to get SMS/WhatsApp alerts when new stock arrives.',
        'subscribe_btn' => 'Subscribe',
        'footer_about' => 'KitabBazar is an initiative aimed at empowering rural education by making books accessible and affordable to every student.',
        'quick_links' => 'Quick Links',
        'villages' => 'Serviced Villages',
        'all_rights' => 'All Rights Reserved.'
    ],
    'gu' => [
        'site_title' => 'ઓરાબુક',
        'site_subtitle' => 'ગામડાના વિદ્યાર્થીઓ માટે અતિ સસ્તી ચોપડીઓ',
        'welcome' => 'સુપ્રભાત/નમસ્તે',
        'my_orders' => 'મારા ઓર્ડર્સ',
        'my_sales' => 'મારી વેચેલી ચોપડીઓ',
        'my_profile' => 'મારી પ્રોફાઇલ',
        'track_returns' => 'રીટર્ન ટ્રેક કરો',
        'logout' => 'લોગઆઉટ',
        'login' => 'લોગિન / રજીસ્ટર',
        'admin_login' => 'એડમિન લોગિન',
        'sell_book' => '+ ચોપડી વેચો',
        'hero_title' => 'તમારા ગામમાં જ નવી અને જૂની ચોપડીઓ શોધો અને વેચો',
        'hero_subtitle' => 'આજુબાજુના ૧૦ થી વધુ ગામડાઓના વિદ્યાર્થીઓને એકસાથે જોડતું પ્લેટફોર્મ.',
        'old_books_btn' => 'જૂની ચોપડીઓ જુઓ (Old Books)',
        'new_books_btn' => 'નવી ચોપડીઓ ખરીદો (New Books)',
       'donets_books_btn' =>'ચોપડીઓ ખરીદો (Donate Books)',
        'search_placeholder' => 'ચોપડીનું નામ, લેખક અથવા વિષય શોધો...',
        'search_btn' => 'શોધો',
        'buy_title' => 'ચોપડી કેવી રીતે ખરીદવી?',
        'buy_sub' => 'સરળ ૩ સ્ટેપમાં તમારી પસંદગીની ચોપડી સીધી તમારા ગામમાં મંગવો.',
        'buy_step1_t' => '૧. ચોપડી શોધો',
        'buy_step1_d' => 'જૂની અથવા નવી ચોપડીઓમાંથી તમારી જરૂરિયાત મુજબ પસંદ કરો.',
        'buy_step2_t' => '૨. ઓર્ડર કન્ફર્મ કરો',
        'buy_step2_d' => 'વિગતો ચકાસો, કેશ ઓન ડિલિવરી પસંદ કરો અને સરનામું ભરો.',
        'buy_step3_t' => '૩. ગામમાં ડિલિવરી મેળવો',
        'buy_step3_d' => 'અમારી ટીમ ચોપડી સીધી તમારા ઘર સુધી પહોંચાડશે.',
        'sell_title' => 'જૂની ચોપડી કેવી રીતે વેચવી?',
        'sell_sub' => 'ઘર બેઠા તમારી નકામી ચોપડીઓ વેચીને રોકડ રકમ કમાઓ.',
        'sell_step1_t' => '૧. ચોપડી અપલોડ કરો',
        'sell_step1_d' => 'વેચો બટન પર ક્લિક કરીને ચોપડીની વિગત ભરો.',
        'sell_step2_t' => '૨. ફોટો અને કિંમત ઉમેરો',
        'sell_step2_d' => 'ચોપડીનો ચોખ્ખો ફોટો પાડો અને તમારી કિંમત નક્કી કરો.',
        'sell_step3_t' => '૩. ઘરેથી પિકઅપ અને પેમેન્ટ',
        'sell_step3_d' => 'ઓર્ડર આવતા જ ડિલિવરી બોય તમારા ઘરેથી બુક લેશે અને પૈસા આપશે.',
        'sell_now_btn' => '+ અત્યારે જ બુક અપલોડ કરો',
        'video_title' => 'ગામકાટ્ કેવી રીતે કામ કરે છે?',
        'video_sub' => 'આ ૨ મિનિટના વિડીયોમાં ખરીદવાની અને વેચવાની સરળ રીત જુઓ.',
        'live_visitors' => 'લાઈવ પ્લેટફોર્મ આંકડા',
        'total_visitors' => 'કુલ મુલાકાતીઓ',
        'today_visitors' => 'આજના મુલાકાતીઓ',
        'online_now' => 'ઓનલાઈન એક્ટિવ',
        'testimonials_title' => 'વિદ્યાર્થીઓનો અનુભવ',
        'faq_title' => 'વારંવાર પૂછાતા પ્રશ્નો (FAQ)',
        'need_help' => 'કોઈ મદદ કે પ્રશ્ન છે?',
        'help_desc' => 'ઓર્ડર અથવા ચોપડી વેચવામાં સહાય માટે અમારી હેલ્પલાઇન પર વાત કરો.',
        'call_us' => 'કોલ કરો',
        'newsletter_title' => 'નવા સ્ટોકની માહિતી મેળવો',
        'newsletter_sub' => 'તમારા ગામમાં નવી બુક્સ આવતા જ મેસેજ મેળવવા માટે સબ્સ્ક્રાઇબ કરો.',
        'subscribe_btn' => 'સબ્સ્ક્રાઇબ',
        'footer_about' => 'કિતાબબજાર ગામડાના વિદ્યાર્થીઓને સસ્તી ચોપડીઓ સરળતાથી મળી રહે તે માટે કાર્યરત છે.',
        'quick_links' => 'ઝડપી લિંક્સ',
        'villages' => 'સેવા આપતા ગામો',
        'all_rights' => 'સર્વાધિકાર સુરક્ષિત.'
    ]
];

$l = $words[$current_lang];

// 2. VISITOR COUNTER LOGIC
$user_ip = $_SERVER['REMOTE_ADDR'];
$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');

try {
    $v_stmt = $pdo->prepare("
        INSERT INTO site_visitors (ip_address, visit_date, last_activity) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE last_activity = ?
    ");
    $v_stmt->execute([$user_ip, $today, $now, $now]);

    $total_visitors = $pdo->query("SELECT COUNT(*) FROM site_visitors")->fetchColumn();

    $today_stmt = $pdo->prepare("SELECT COUNT(*) FROM site_visitors WHERE visit_date = ?");
    $today_stmt->execute([$today]);
    $today_visitors = $today_stmt->fetchColumn();

    $online_time = date('Y-m-d H:i:s', strtotime('-5 minutes'));
    $online_stmt = $pdo->prepare("SELECT COUNT(DISTINCT ip_address) FROM site_visitors WHERE last_activity >= ?");
    $online_stmt->execute([$online_time]);
    $online_now = $online_stmt->fetchColumn();
} catch (Exception $e) {
    $total_visitors = 1420;
    $today_visitors = 88;
    $online_now = 6;
}

$user_initial = '';
if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
    $user_initial = strtoupper(substr(trim($_SESSION['user_name']), 0, 1));
} elseif (isset($_SESSION['user_id'])) {
    $user_initial = 'U';
}
?>
<!DOCTYPE html>
<html lang="<?= $current_lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $l['site_title'] ?> - <?= $l['site_subtitle'] ?></title>
    
        <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Noto+Sans+Gujarati:wght@400;600;700&display=swap" rel="stylesheet">
    
        <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            400: '#38bdf8',
                            500: '#0284c7',
                            600: '#0369a1',
                            900: '#0c4a6e',
                        },
                        amberGold: '#f59e0b',
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --kb-green: #0d5c3a;
            --kb-green-light: #198754;
            --kb-yellow: #ffc107;
            --kb-dark: #0a251c;
            --kb-bg: #f4f7f5;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Noto Sans Gujarati', sans-serif;
            background-color: var(--kb-bg);
            color: #2b2b2b;
            padding-bottom: 70px; /* Space for mobile bottom bar */
        }

        /* Glassmorphism Navbar */
        .custom-navbar {
            background: rgba(13, 92, 58, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .hero-banner {
            background: linear-gradient(135deg, #093723 0%, #136f45 50%, #198754 100%);
            border-radius: 28px;
            color: white;
            padding: 60px 24px;
            position: relative;
            overflow: hidden;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .search-box-wrap {
            max-width: 650px;
            margin: -30px auto 30px auto;
            position: relative;
            z-index: 10;
        }

        .step-card {
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 20px;
            background: #ffffff;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .step-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 30px rgba(13, 92, 58, 0.12) !important;
        }

        .step-number {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--kb-green-light), var(--kb-green));
            color: white;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.3rem;
            margin-bottom: 18px;
            box-shadow: 0 6px 12px rgba(25, 135, 84, 0.25);
        }

        .category-pill {
            background: #ffffff;
            border: 1.5px solid #e0e0e0;
            border-radius: 50px;
            padding: 8px 18px;
            color: #333;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .category-pill:hover {
            background: var(--kb-green);
            color: white;
            border-color: var(--kb-green);
        }

        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            border: 4px solid #ffffff;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .user-avatar-btn {
            width: 42px; height: 42px; border-radius: 50%; background: #ffffff;
            color: var(--kb-green); font-weight: 800; font-size: 18px; display: flex;
            align-items: center; justify-content: center; border: 2px solid #ffffff;
            cursor: pointer; text-decoration: none; box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .visitor-box {
            background: linear-gradient(145deg, #09261e, #114b38);
            border-radius: 24px;
            color: #ffffff;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .visitor-card {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 18px;
            padding: 18px 22px;
        }

        .help-card {
            background: linear-gradient(135deg, #114b38 0%, #0a2f23 100%);
            border-radius: 24px;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .main-footer {
            background: var(--kb-dark);
            color: #a3b8b0;
            border-top-left-radius: 30px;
            border-top-right-radius: 30px;
        }

        .main-footer a { color: #d1d5db; text-decoration: none; transition: color 0.2s; }
        .main-footer a:hover { color: var(--kb-yellow); }

        /* Mobile Bottom Nav */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            z-index: 1000;
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }

        .mobile-bottom-nav a {
            color: #666;
            text-decoration: none;
            font-size: 0.75rem;
            text-align: center;
            font-weight: 600;
        }

        .mobile-bottom-nav a.active, .mobile-bottom-nav a:hover {
            color: var(--kb-green-light);
        }

        .mobile-bottom-nav i {
            font-size: 1.2rem;
            display: block;
            margin-bottom: 2px;
        }

        @media (min-width: 992px) {
            .mobile-bottom-nav { display: none; }
            body { padding-bottom: 0; }
        }

        /* Developed By Section Styling */
        .developed-by-wrapper {
            background: linear-gradient(135deg, #070707 0%, #050505 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .dev-profile-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .dev-profile-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .dev-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ffc107; /* Warning yellow border */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        .dev-name {
            font-size: 1.15rem;
        }

        @media (max-width: 576px) {
            .dev-avatar {
                width: 65px;
                height: 65px;
            }
            .dev-name {
                font-size: 1rem;
            }
        }


          /* Glassmorphism Card Effect */
        .glass-card {
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .glass-card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card-hover:hover {
            transform: translateY(-6px);
            background: rgba(30, 41, 59, 0.75);
            border-color: rgba(56, 189, 248, 0.3);
            box-shadow: 0 12px 40px -10px rgba(14, 165, 233, 0.25);
        }

        /* Neon Glow Utility */
        .neon-glow-cyan {
            box-shadow: 0 0 25px -5px rgba(56, 189, 248, 0.4);
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, #0bb3fc 0%, #3d50f7 50%, #9d44f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gradient-gold {
            background: linear-gradient(135deg, #fef08a 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* SVG Countdown Circle Ring Styling */
        .progress-ring__circle {
            transition: stroke-dashoffset 1s linear;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }

        /* Floating background elements animation */
        @keyframes float {
            0% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-18px) rotate(5deg); }
            100% { transform: translateY(0px) rotate(0deg); }
        }

        .animate-float-slow {
            animation: float 8s ease-in-out infinite;
        }

        .animate-float-delayed {
            animation: float 10s ease-in-out 2s infinite;
        }

        /* Smooth Screen Fade Transitions */
        .fade-out-screen {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.8s ease-in-out, visibility 0.8s ease-in-out;
        }

        .fade-in-screen {
            opacity: 1;
            visibility: visible;
            transition: opacity 0.8s ease-in-out;
        }
 
    </style>
</head>
<body>

 


<!-- TOP NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark custom-navbar sticky-top">
    <div class="container d-flex justify-content-between align-items-center">
       
                  <a class="navbar-brand fw-bold fs-3" href="index.php">  <i class="fa-solid fa-book "></i>&nbsp;&nbsp;<?= $l['site_title'] ?></a>
            <span class="navbar-text text-white d-none d-sm-inline-block"><?= $l['site_subtitle'] ?></span>

       
        <div class="d-none d-md-block text-white-50 small">
            <i class="fa-solid fa-location-dot text-warning me-1"></i> 10+ Villages Connected
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Language Switcher -->
            <div class="btn-group bg-black bg-opacity-25 p-1 rounded-pill">
                <a href="?lang=gu" class="btn btn-sm rounded-pill px-3 fw-bold <?= $current_lang == 'gu' ? 'btn-warning text-dark' : 'text-white' ?>">ગુજરાતી</a>
                <a href="?lang=en" class="btn btn-sm rounded-pill px-3 fw-bold <?= $current_lang == 'en' ? 'btn-warning text-dark' : 'text-white' ?>">English</a>
            </div>

            <!-- Admin Login -->
            <a href="admin/login.php" class="btn btn-outline-light btn-sm rounded-pill px-2 px-sm-3 fw-bold text-nowrap">
                <i class="fa-solid fa-user-shield me-sm-1"></i><span class="d-none d-sm-inline"><?= $l['admin_login'] ?></span><span class="d-sm-none">Admin</span>
            </a>

            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="sell.php" class="btn btn-warning text-dark fw-bold btn-sm rounded-pill px-3 d-none d-sm-inline-block">
                    <?= $l['sell_book'] ?>
                </a>
            <?php else: ?>
                <a href="sell.php" class="btn btn-warning text-dark fw-bold btn-sm rounded-pill px-3 d-none d-sm-inline-block">
                    <?= $l['sell_book'] ?>
                </a>
            <?php endif; ?>

            <!-- User Menu -->
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="dropdown">
                    <a href="#" class="user-avatar-btn" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <?= $user_initial ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2" aria-labelledby="userMenu">
                        <li class="px-3 py-2 border-bottom bg-light rounded-top-4">
                            <span class="text-muted small d-block"><?= $l['welcome'] ?>,</span>
                            <strong class="text-dark"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Student') ?></strong>
                        </li>
                        <li><a class="dropdown-item py-2" href="my_orders.php"><i class="fa-solid fa-box-open me-2 text-success"></i><?= $l['my_orders'] ?></a></li>
                        <li><a class="dropdown-item py-2" href="my_sales.php"><i class="fa-solid fa-store me-2 text-primary"></i><?= $l['my_sales'] ?></a></li>
                        <li><a class="dropdown-item py-2" href="profile.php"><i class="fa-solid fa-user me-2 text-primary"></i><?= $l['my_profile'] ?></a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger fw-bold" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i><?= $l['logout'] ?></a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-bold">
                    <i class="fa-regular fa-circle-user me-1"></i> <?= $l['login'] ?>
                </a>

               
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container my-4">



       <!-- Main Hero Presentation Container -->
        <div class="w-full max-w-6xl mx-auto my-auto py-8">
            
            <!-- Central Badge & Title -->
            <div class="text-center max-w-3xl mx-auto mb-10">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-sky-500/10 border border-sky-500/30 text-black font-semibold tracking-wide uppercase mb-4">
                    <i class="fa-solid fa-sparkles text-amber-400"></i> BCA Semester 5 Project Presentation
                </span>
                <h1 class="text-3xl md:text-5xl font-extrabold text-black tracking-tight leading-tight mb-4">
                    Welcome to <span class="gradient-text">AuraBook Hub Platform</span>
                </h1>
                <p class="text-slate-300 text-black md:text-base leading-relaxed">
                    A comprehensive digital ecosystem for exchanging old books, purchasing new ones, donating to underprivileged students, and reading/downloading free e-books instantly.
                </p>
            </div>

            <!-- 3 Glowing Feature Highlight Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Card 1: Idea / Inspiration -->
                <div class="glass-card glass-card-hover rounded-2xl p-6 relative overflow-hidden">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 text-xl mb-4">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 flex items-center justify-between">
                        The Inspiration <span class="text-xs font-normal text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">💡 Idea</span>
                    </h3>
                    <p class="text-slate-300 text-xs md:text-sm leading-relaxed">
                        Many students struggle with the high cost of new academic textbooks, while thousands of read books sit idle in homes. We created Book Hub to recycle knowledge and make books freely circular.
                    </p>
                </div>

                <!-- Card 2: Mission & Purpose -->
                <div class="glass-card glass-card-hover rounded-2xl p-6 relative overflow-hidden">
                    <div class="w-12 h-12 rounded-xl bg-sky-500/20 border border-sky-500/30 flex items-center justify-center text-sky-400 text-xl mb-4">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 flex items-center justify-between">
                        Our Mission <span class="text-xs font-normal text-sky-400 bg-sky-500/10 px-2 py-0.5 rounded border border-sky-500/20">🎯 Purpose</span>
                    </h3>
                    <p class="text-slate-300 text-xs md:text-sm leading-relaxed">
                        To build a 100% accessible educational gateway where students can exchange used books seamlessly, buy low-cost second-hand books, or access our built-in PDF Reader for immediate study.
                    </p>
                </div>

                <!-- Card 3: Key Features -->
                <div class="glass-card glass-card-hover rounded-2xl p-6 relative overflow-hidden">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-xl mb-4">
                        <i class="fa-solid fa-rocket"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-2 flex items-center justify-between">
                        Key Features <span class="text-xs font-normal text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">🚀 Facilities</span>
                    </h3>
                    <ul class="text-slate-300 text-xs md:text-sm space-y-2">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i> 1-to-1 Book Exchange System</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i> Buy & Sell New/Used Textbooks</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i> Book Donation for Needy Students</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i> Integrated E-PDF Reader & Downloads</li>
                    </ul>
                </div>

            </div>

        </div>
        

    <!-- HERO BANNER -->
    <div class="hero-banner text-center shadow-lg mb-4">
        <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill mb-3 text-uppercase">
            <i class="fa-solid fa-graduation-cap me-1"></i> Village Student Empowerment
        </span>
        <h1 class="fw-extrabold display-5 mb-3"><?= $l['hero_title'] ?></h1>
        <p class="fs-5 mb-4 text-white-50 max-w-700 mx-auto"><?= $l['hero_subtitle'] ?></p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="used_books.php" class="btn btn-light btn-lg text-success fw-bold px-4 py-3 rounded-4 shadow-sm">
                <i class="fa-solid fa-book-open-reader me-2"></i> <?= $l['old_books_btn'] ?>
            </a>
            <a href="new_books.php" class="btn btn-warning btn-lg text-dark fw-bold px-4 py-3 rounded-4 shadow-sm">
                <i class="fa-solid fa-book me-2"></i> <?= $l['new_books_btn'] ?>
            </a>
           <a href="browse_books.php" class="btn btn-info btn-lg text-dark fw-bold px-4 py-3 rounded-4 shadow-sm">
         <i class="fa-solid fa-hand-holding-heart"></i> <?= $l['donets_books_btn'] ?>
            </a>
            
        </div>
    </div>

    <!-- SEARCH BAR -->
    <div class="search-box-wrap">
        <form action="used_books.php" method="GET" class="card p-2 shadow-lg rounded-pill border-0 d-flex flex-row align-items-center">
            <i class="fa-solid fa-magnifying-glass text-muted ms-3 fs-5"></i>
            <input type="text" name="search" class="form-control border-0 shadow-none bg-transparent px-3" placeholder="<?= $l['search_placeholder'] ?>">
            <button type="submit" class="btn btn-success rounded-pill px-4 py-2 fw-bold me-1">
                <?= $l['search_btn'] ?>
            </button>
        </form>
    </div>
<?php $classes = [
    "Standard 1"  => "Class 01st (ધોરણ ૦૧)",
    "Standard 2"  => "Class 02nd (ધોરણ ૦૨)",
    "Standard 3"  => "Class 03rd (ધોરણ ૦૩)",
    "Standard 4"  => "Class 04th (ધોરણ ૦૪)",
    "Standard 5"  => "Class 05th (ધોરણ ૦૫)",
    "Standard 6"  => "Class 06th (ધોરણ ૦૬)",
    "Standard 7"  => "Class 07th (ધોરણ ૦૭)",
    "Standard 8"  => "Class 08th (ધોરણ ૦૮)",
    "Standard 9"  => "Class 09th (ધોરણ ૦૯)",
    "Standard 10" => "Class 10th (ધોરણ ૧૦)",
    "Standard 12" => "Class 12th (ધોરણ ૧૨)"
];
?>

<div style="position: relative; display: inline-block;" onmouseover="this.querySelector('.menu').style.display='block'" onmouseout="this.querySelector('.menu').style.display='none'">
    
    <!-- Main Button -->
    <a href="#" class="category-pill">
        <i class="fa-solid fa-school text-success"></i> 
        <?= $_GET['cat'] ?? 'Choose Class (ધોરણ)' ?> ▼
    </a>

    <!-- Dropdown List -->
    <div class="menu" style="display: none; position: absolute; background: #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.1); border-radius: 8px; padding: 5px; z-index: 100; min-width: 200px;">
        <?php foreach ($classes as $cat => $name): ?>
            <a href="used_books.php?cat=<?= urlencode($cat) ?>" class="category-pill" style="display: block; margin: 3px 0; text-align: left;">
                <i class="fa-solid fa-school text-success"></i> <?= $name ?>
            </a>
        <?php endforeach; ?>
        
    </div>

        </div>
        <a href="used_books.php?cat=College" class="category-pill"><i class="fa-solid fa-building-columns text-warning"></i> College / ITI</a>
        <a href="used_books.php?cat=Competitive" class="category-pill"><i class="fa-solid fa-award text-danger"></i> GPSC / Talati Exam</a>
        <a href="used_books.php?cat=General" class="category-pill"><i class="fa-solid fa-book text-info"></i> Story / Novels</a>
        <a href="pdf_library.php"  class="category-pill"> <i class="fa-solid fa-file-pdf"></i>PDF Book Read / Downlod </a>
    </div>
  
    

    <!-- HOW TO BUY BOOKS (STEPS) -->
    <div class="mb-5">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-success"><i class="fa-solid fa-cart-shopping me-2"></i> <?= $l['buy_title'] ?></h3>
            <p class="text-muted"><?= $l['buy_sub'] ?></p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 p-4 shadow-sm step-card text-center">
                    <div class="step-number mx-auto">1</div>
                    <h5 class="fw-bold"><?= $l['buy_step1_t'] ?></h5>
                    <p class="text-muted small mb-0"><?= $l['buy_step1_d'] ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 shadow-sm step-card text-center">
                    <div class="step-number mx-auto">2</div>
                    <h5 class="fw-bold"><?= $l['buy_step2_t'] ?></h5>
                    <p class="text-muted small mb-0"><?= $l['buy_step2_d'] ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 shadow-sm step-card text-center">
                    <div class="step-number mx-auto">3</div>
                    <h5 class="fw-bold"><?= $l['buy_step3_t'] ?></h5>
                    <p class="text-muted small mb-0"><?= $l['buy_step3_d'] ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- HOW TO SELL BOOKS (STEPS) -->
    <div class="mb-5 bg-white p-4 p-md-5 rounded-4 shadow-sm border">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark"><i class="fa-solid fa-sack-dollar me-2 text-warning"></i> <?= $l['sell_title'] ?></h3>
            <p class="text-muted"><?= $l['sell_sub'] ?></p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 p-4 shadow-sm step-card text-center bg-light">
                    <div class="step-number bg-dark text-white mx-auto">1</div>
                    <h5 class="fw-bold"><?= $l['sell_step1_t'] ?></h5>
                    <p class="text-muted small mb-0"><?= $l['sell_step1_d'] ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 shadow-sm step-card text-center bg-light">
                    <div class="step-number bg-dark text-white mx-auto">2</div>
                    <h5 class="fw-bold"><?= $l['sell_step2_t'] ?></h5>
                    <p class="text-muted small mb-0"><?= $l['sell_step2_d'] ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 shadow-sm step-card text-center bg-light">
                    <div class="step-number bg-dark text-white mx-auto">3</div>
                    <h5 class="fw-bold"><?= $l['sell_step3_t'] ?></h5>
                    <p class="text-muted small mb-0"><?= $l['sell_step3_d'] ?></p>
                </div>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="sell.php" class="btn btn-success btn-lg fw-bold px-5 py-3 rounded-pill shadow">
                <?= $l['sell_now_btn'] ?>
            </a>
        </div>
    </div>

    <!-- DEMO / GUIDE VIDEO SECTION -->
    <div class="mb-5">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark"><i class="fa-solid fa-circle-play me-2 text-danger"></i> <?= $l['video_title'] ?></h3>
            <p class="text-muted"><?= $l['video_sub'] ?></p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="video-container">
<iframe width="560" height="315" src="https://www.youtube.com/embed/YxJJkA-EvaE?si=g3b0A8yl2dbCh_0_" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>            </div>
        </div>
    </div>

    <!-- LIVE VISITORS UI -->
    <div class="visitor-box p-4 p-md-5 mb-5">
        <div class="text-center mb-4">
            <span class="badge bg-danger text-uppercase px-3 py-2 me-2 fw-bold align-middle rounded-pill">
                <i class="fa-solid fa-circle text-white me-1" style="font-size: 8px;"></i> LIVE
            </span>
            <h3 class="d-inline-block fw-bold text-white mb-0 align-middle"><?= $l['live_visitors'] ?></h3>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="visitor-card d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-uppercase text-light fw-bold d-block mb-1 opacity-75" style="font-size: 11px;"><?= $l['total_visitors'] ?></small>
                        <div class="fs-2 fw-extrabold text-warning"><?= number_format($total_visitors) ?></div>
                    </div>
                    <div class="fs-1 text-warning opacity-50"><i class="fa-solid fa-earth-asia"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="visitor-card d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-uppercase text-light fw-bold d-block mb-1 opacity-75" style="font-size: 11px;"><?= $l['today_visitors'] ?></small>
                        <div class="fs-2 fw-extrabold text-info"><?= number_format($today_visitors) ?></div>
                    </div>
                    <div class="fs-1 text-info opacity-50"><i class="fa-solid fa-chart-line"></i></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="visitor-card d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-uppercase text-light fw-bold d-block mb-1 opacity-75" style="font-size: 11px;"><?= $l['online_now'] ?></small>
                        <div class="fs-2 fw-extrabold text-success" style="color: #00ff87 !important;"><?= number_format($online_now) ?></div>
                    </div>
                    <div class="fs-1 text-success opacity-50"><i class="fa-solid fa-users"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- STUDENT TESTIMONIALS -->
    <div class="mb-5">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark"><i class="fa-solid fa-comments text-success me-2"></i> <?= $l['testimonials_title'] ?></h3>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card p-4 rounded-4 border-0 shadow-sm h-100">
                    <div class="text-warning mb-2"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    <p class="text-muted small">"મેં ધોરણ ૧૨ ની ચોપડીઓ અડધી કિંમતે મેળવી. ગામડા સુધી ડિલિવરી મળી ગઈ, ખૂબ જ ઉપયોગી પ્લેટફોર્મ છે."</p>
                    <div class="fw-bold text-dark mt-auto">- રમેશ ચૌધરી</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 rounded-4 border-0 shadow-sm h-100">
                    <div class="text-warning mb-2"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    <p class="text-muted small">"My old college books were lying useless. I uploaded them here and got cash delivered home!"</p>
                    <div class="fw-bold text-dark mt-auto">- Vikas Patel</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 rounded-4 border-0 shadow-sm h-100">
                    <div class="text-warning mb-2"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                    <p class="text-muted small">"હેલ્પલાઇન સપોર્ટ પણ ખૂબ સારો છે. ઓર્ડર ટ્રેકિંગ સરળ છે."</p>
                    <div class="fw-bold text-dark mt-auto">- ભાવિન જોશી </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ SECTION -->
    <div class="mb-5 bg-white p-4 p-md-5 rounded-4 shadow-sm border">
        <h3 class="fw-bold text-dark text-center mb-4"><i class=" fa-solid fa-circle-question text-primary me-2  "></i> <?= $l['faq_title'] ?></h3>
        <div class="accordion accordion-flush" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        ડિલિવરી ચાર્જ કેટલો છે? (What is the Delivery Charge?)
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">
                        આજુબાજુના નિયુક્ત ગામો માટે ખૂબ જ વ્યાજબી (માત્ર ₹૨૦) ડિલિવરી ચાર્જ રાખવામાં આવ્યો છે.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        પૈસા કેવી રીતે ચૂકવવા? (How to Pay?)
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted small">
                        તમે કેશ ઓન ડિલિવરી (Cash on Delivery) દ્વારા ચોપડી હાથમાં મળે ત્યારે જ પૈસા આપી શકો છો.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- NEWSLETTER / WHATSAPP ALERTS -->
    <div class="card bg-success text-white p-4 p-md-5 rounded-4 border-0 mb-5 text-center shadow">
        <h3 class="fw-bold mb-2"><i class="fa-regular fa-bell me-2"></i> <?= $l['newsletter_title'] ?></h3>
        <p class="text-white-50 max-w-600 mx-auto small mb-4"><?= $l['newsletter_sub'] ?></p>
        <form class="d-flex justify-content-center gap-2 max-w-500 mx-auto">
            <input type="tel" class="form-control rounded-pill border-0 px-3" placeholder="Enter Mobile Number">
            <button class="btn btn-warning rounded-pill px-4 fw-bold text-dark"><?= $l['subscribe_btn'] ?></button>
        </form>
    </div>

    <!-- HELPLINE CARD -->
    <div class="help-card p-4 p-md-5 text-center mb-5">
        <h3 class="fw-bold mb-2"><i class="fa-solid fa-headset me-2 text-warning"></i> <?= $l['need_help'] ?></h3>
        <p class="text-light small mb-4"><?= $l['help_desc'] ?></p>
        <a href="tel:7856432898" class="btn btn-warning btn-lg px-5 py-3 fw-extrabold text-dark rounded-pill shadow-lg">
            <i class="fa-solid fa-phone me-2"></i> <?= $l['call_us'] ?>: 8320799285
        </a>
    </div>

</div>

    <!-- DEVELOPED BY SECTION (Footer ke Upar) -->
    <section class="developed-by-wrapper mb-5 p-4 p-md-5 rounded-4 text-center">
        <h3 class="fw-bold text-white mb-4"><i class="fa-solid fa-code text-warning me-2"></i> Developed By</h3>
        <div class="row justify-content-center g-4">
            
            <!-- Developer 1 -->
            <div class="col-6 col-md-3">
                <div class="dev-profile-card p-3 rounded-4 h-100">
                    <img src="Devloped\dhruv.png" alt="Developer 1" class="dev-avatar mb-3">
                        <h4 class="dev-name text-white fw-bold mb-1">Prajapati &nbsp; Dhruv</h4>
                        <p class="dev-edu text-warning small mb-1 fw-semibold">BCA - Semester 5</p>
                        <p class="dev-role text-info small mb-0">Role: Frontend & UI Developer</p>
                </div>
            </div>

            <!-- Developer 2 -->
            <div class="col-6 col-md-3">
                <div class="dev-profile-card p-3 rounded-4 h-100">
                    <img src="Devloped\marvan.jpeg" alt="Developer 2" class="dev-avatar mb-3">
                    <h4 class="dev-name text-white fw-bold mb-1">Parbdiya &nbsp; Marvan</h4>
                     <p class="dev-edu text-warning small mb-1 fw-semibold">BCA - Semester 5</p>
                     <p class="dev-role text-info small mb-0">Role: Backend & Logic Developer</p>
                </div>
            </div>

        </div>
    </section>

</div> <!-- End of main container -->




<!-- FOOTER -->
<footer class="main-footer pt-5 pb-4">
    <div class="container">
        <div class="row g-4 mb-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold text-white mb-3"><i class =" fa-solid fa-book"></i>&nbsp;&nbsp;AuraBook</h5>
                <p class="small text-white-50"><?= $l['footer_about'] ?></p>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-white mb-3"><?= $l['quick_links'] ?></h6>
                <ul class="list-unstyled small d-flex flex-column gap-2">
                    <li><a href="index.php"><i class="fa-solid fa-chevron-right me-1 text-success small"></i> Home</a></li>
                    <li><a href="used_books.php"><i class="fa-solid fa-chevron-right me-1 text-success small"></i> Used Books Store</a></li>
                    <li><a href="new_books.php"><i class="fa-solid fa-chevron-right me-1 text-success small"></i> New Books Store</a></li>
                    <li><a href="browse_books.php"><i class="fa-solid fa-chevron-right me-1 text-success small"></i>donetsbook</a></li>
                   <li><a href="sell.php"><i class="fa-solid fa-chevron-right me-1 text-success small"></i> Sell Your Book</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold text-white mb-3"><?= $l['villages'] ?></h6>
                <ul class="list-unstyled small d-flex flex-column gap-1 text-white-50">
                    <li>Gola</li><li>Jalotra</li><li>Talepura</li><li>Pimpli</li><li>Karnala</li><li>Moteta</li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold text-white mb-3">Support & Help</h6>
                <p class="small mb-2"><i class="fa-solid fa-phone text-warning me-2"></i> +91 8320799285</p>
                <p class="small mb-2"><i class="fa-solid fa-envelope text-warning me-2"></i> support@gaoncart.com</p>
                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width:36px; height:36px;"><i class="fa-brands fa-whatsapp"></i></a>
                    <a href="#" class="btn btn-outline-light btn-sm rounded-circle" style="width:36px; height:36px;"><i class="fa-brands fa-facebook"></i></a>
                </div>
            </div>
        </div>
        <hr class="border-secondary opacity-25">
        <div class="text-center small text-white-50">&copy; <?= date('Y') ?> AuraBook. <?= $l['all_rights'] ?></div>
    </div>
</footer>

<!-- MOBILE BOTTOM NAVIGATION -->
<div class="mobile-bottom-nav">
    <a href="index.php" class="active">
        <i class="fa-solid fa-house"></i> Home
    </a>
    <a href="used_books.php">
        <i class="fa-solid fa-book"></i> Used Books
    </a>
    <a href="login.php?redirect=sell.php">
        <i class="fa-solid fa-circle-plus text-success" style="font-size:1.5rem;"></i> Sell
    </a>
    <a href="new_books.php">
        <i class="fa-solid fa-book"></i> New Books
                    </a>
    <a href="<?= isset($_SESSION['user_id']) ? 'profile.php' : 'login.php' ?>">
        <i class="fa-solid fa-user"></i> Account
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>