<?php
require_once('functions.php');
$currentPage = basename($_SERVER['PHP_SELF']);
$cust = $p_content['customization'];
$contact = $p_content['contact'];
$hero = $p_content['hero'];
$about = $p_content['about'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $metaDescription ?? $cust['meta_description']; ?>">
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.svg">
    <title><?php echo $pageTitle ?? $cust['site_title']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap min css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.css">
    <link rel="stylesheet" href="assets/css/plugins/swiper.css">
    <link rel="stylesheet" href="assets/css/plugins/odometer.css">
    <link rel="stylesheet" href="assets/css/vendor/animate.min.css">
    <link rel="stylesheet" href="assets/css/vendor/bootstrap.min.css">
    <!-- custom css -->
    <link rel="stylesheet" href="assets/css/style.css">
    <?php if (isset($extraStyles)) echo $extraStyles; ?>
    <style>
        :root {
            --color-primary: <?php echo $cust['primary_color']; ?> !important;
            --font-primary: <?php echo $cust['font_family'] ?? "'Plus Jakarta Sans', sans-serif"; ?> !important;
        }
        
        body {
            background-color: <?php echo $cust['background_color'] ?? '#ffffff'; ?> !important;
        }

        .tmp-banner-one-area {
            background-image: none !important;
            background-color: transparent !important;
        }

        body, p, h1, h2, h3, h4, h5, h6, a, span, div, li, ul, button, input, textarea {
            font-family: var(--font-primary) !important;
        }
        /* Global Header Spacing */
        .tmp-header-area-start {
            padding-top: 10px;
            padding-bottom: 10px;
        }

        /* Large Desktop Adjustments (XXL) */
        @media only screen and (min-width: 1400px) {
            .container { max-width: 1320px; }
        }

        /* Desktop & Laptop (LG to XL) */
        @media only screen and (max-width: 1199px) {
            .tmp-banner-one-area h2.title {
                font-size: 38px !important;
            }
        }

        /* Tablet & Small Laptop (MD to LG) */
        @media only screen and (max-width: 991px) {
            .tmp-header-area-start {
                padding-top: 15px !important;
                padding-bottom: 10px !important;
            }
            
            .logo h4.title {
                font-size: 18px !important;
                letter-spacing: 0 !important;
            }
            .logo i {
                font-size: 24px !important;
            }
            
            .tmp-banner-one-area h2.title {
                font-size: 32px !important;
            }
            .banner-big-text-1 {
                font-size: 60px !important;
            }
            
            .tmp-section-gapTop { padding-top: 80px !important; }
            .tmp-section-gap { padding-top: 60px !important; padding-bottom: 60px !important; }
            .tmp-section-gapBottom { padding-bottom: 60px !important; }
        }
        
        /* Mobile Landscape & Large Phones (SM to MD) */
        @media only screen and (max-width: 767px) {
            .tmp-banner-one-area h2.title {
                font-size: 28px !important;
            }
            .banner-big-text-1 {
                font-size: 40px !important;
            }
            .inner p.disc {
                font-size: 14px !important;
            }
        }
        
        /* Small Mobile Screens (XS) */
        @media only screen and (max-width: 480px) {
            .logo h4.title {
                font-size: 14px !important;
            }
            .logo i {
                font-size: 20px !important;
            }
            .logo {
                padding-left: 10px !important;
            }
            .tmp-header-right {
                padding-right: 10px !important;
            }
            
            h2.title, .section-head h2 {
                font-size: 26px !important;
            }
            .banner-big-text-1 {
                font-size: 30px !important;
                top: 15% !important;
            }
        }

        /* Active Page State */
        .tmp-mainmenu li.active > a,
        .tmp-mainmenu li.active > a i {
            color: var(--color-primary) !important;
        }

        /* Red Hover Effect for Navigation */
        .tmp-mainmenu li a:hover,
        .tmp-mainmenu li.has-dropdown:hover > a,
        .tmp-mainmenu li.has-dropdown:hover > a i,
        .submenu li a:hover {
            color: var(--color-primary) !important;
        }

        /* Mobile Menu Red Hover */
        ul.tmp-mainmenu li a:hover {
            color: var(--color-primary) !important;
        }
    </style>
</head>

<body class="tmp-white-version">

    <!-- tpm-header-area start -->
    <header class="tmp-header-area-start header-one header--sticky header--transparent">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="header-content">
                        <div class="logo" style="display: flex; align-items: center; padding-left: 20px;">
                            <a href="index" style="display: flex; align-items: center; gap: 12px; white-space: nowrap;">
                                <i class="fa-solid fa-graduation-cap" style="font-size: 28px; color: var(--color-primary);"></i>
                                <h4 class="title" style="margin: 0; font-weight: 800; font-size: 22px; color: var(--color-heading); line-height: 1;"><?php echo $cust['logo_text']; ?></h4>
                            </a>
                        </div>
                        <nav class="tmp-mainmenu-nav d-none d-xl-block">
                            <ul class="tmp-mainmenu">
                                <li class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">
                                    <a href="index">Home</a>
                                </li>
                                <li class="<?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>">
                                    <a href="about">About</a>
                                </li>
                                <li class="<?php echo ($currentPage == 'research.php') ? 'active' : ''; ?>">
                                    <a href="research">Research</a>
                                </li>
                                <li class="<?php echo ($currentPage == 'publications.php') ? 'active' : ''; ?>">
                                    <a href="publications">Publications</a>
                                </li>
                                <li class="<?php echo ($currentPage == 'experience.php') ? 'active' : ''; ?>">
                                    <a href="experience">Experience</a>
                                </li>
                                 <li class="<?php echo ($currentPage == 'teaching.php') ? 'active' : ''; ?>">
                                    <a href="teaching">Teaching</a>
                                </li>
                                 <li class="<?php echo ($currentPage == 'talks.php') ? 'active' : ''; ?>"><a href="talks">Talks</a></li>
                                <li class="<?php echo ($currentPage == 'skills.php') ? 'active' : ''; ?>"><a href="skills">Skills</a></li>
                                <li class="<?php echo ($currentPage == 'contact.php') ? 'active' : ''; ?>">
                                    <a href="contact">Contact</a>
                                </li>
                            </ul>
                        </nav>
                        <div class="tmp-header-right" style="padding-right: 20px;">

                            <div class="actions-area">
                                <div class="tmp-side-collups-area d-none d-xl-block">
                                    <button class="tmp-menu-bars tmp_button_active"><i class="fa-solid fa-bars-staggered"></i></button>
                                </div>
                                <div class="tmp-side-collups-area d-block d-xl-none">
                                    <button class="tmp-menu-bars humberger_menu_active"><i class="fa-solid fa-bars-staggered"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- tpm-header-area end -->

    <!-- Sidebar & Mobile Menu -->
    <div class="d-none d-xl-block">
        <div class="tmp-sidebar-area tmp_side_bar">
            <div class="inner">
                <div class="top-area">
                    <a href="index.php" class="logo">
                        <h4 class="title" style="margin: 0; font-weight: 800; color: var(--color-primary); letter-spacing: 1px;"><?php echo $cust['logo_text']; ?></h4>
                    </a>
                    <div class="close-icon-area">
                        <button class="tmp-round-action-btn close_side_menu_active">
                            <i class=" fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
                <div class="content-wrapper">
                    <div class="image-area-feature">
                        <a href="index.php">
                            <img src="<?php echo $cust['sidebar_image'] ?? $hero['image']; ?>" alt="<?php echo $cust['logo_text'] ?? 'Profile'; ?>">
                        </a>
                    </div>
                    <h5 class="title mt--30"><?php echo $cust['sidebar_title'] ?? implode(', ', array_slice($hero['designations'], 0, 3)); ?></h5>
                    <p class="disc"><?php echo $cust['sidebar_description'] ?? strip_tags($hero['description']); ?></p>
                    <div class="short-contact-area">
                        <div class="single-contact">
                            <i class="fa-solid fa-phone"></i>
                            <div class="information tmp-link-animation">
                                <span>Call Now</span>
                                <a href="tel:<?php echo str_replace(' ', '', $cust['sidebar_phone'] ?? $contact['phones'][0] ?? ''); ?>" class="number"><?php echo $cust['sidebar_phone'] ?? $contact['phones'][0] ?? ''; ?></a>
                            </div>
                        </div>
                        <div class="single-contact">
                            <i class="fa-solid fa-envelope"></i>
                            <div class="information tmp-link-animation">
                                <span>Mail Us</span>
                                <a href="mailto:<?php echo $cust['sidebar_email'] ?? $contact['email']; ?>" class="number"><?php echo $cust['sidebar_email'] ?? $contact['email']; ?></a>
                            </div>
                        </div>
                        <div class="single-contact">
                            <i class="fa-solid fa-gear"></i>
                            <div class="information tmp-link-animation">
                                <span>Expertise</span>
                                <a href="skills.php" class="number">Technical Skills</a>
                            </div>
                        </div>
                        <div class="single-contact">
                            <i class="fa-solid fa-location-crosshairs"></i>
                            <div class="information tmp-link-animation">
                                <span>Address</span>
                                <span class="number"><?php echo $cust['sidebar_address'] ?? $contact['mailing_address'] ?? ''; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="social-wrapper mt--20">
                        <span class="subtitle">find with me</span>
                        <div class="social-link">
                            <?php foreach($contact['social'] as $soc): ?>
                                <a href="<?php echo $soc['link']; ?>"><i class="<?php echo $soc['icon']; ?>"></i></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a class="overlay_close_side_menu close_side_menu_active" href="javascript:void(0);"></a>
    </div>

    <div class="d-block d-xl-none">
        <div class="tmp-popup-mobile-menu">
            <div class="inner">
                <div class="header-top">
                    <div class="logo">
                        <a href="index.php" style="display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-graduation-cap" style="font-size: 28px; color: var(--color-primary);"></i>
                            <h4 class="title" style="margin: 0; font-weight: 800; font-size: 20px; color: var(--color-heading);"><?php echo $cust['logo_text']; ?></h4>
                        </a>
                    </div>
                    <div class="close-menu">
                        <button class="close-button tmp-round-action-btn">
                            <i class=" fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
                <ul class="tmp-mainmenu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About</a></li>
                    <li class="has-dropdown">
                        <a href="javascript:void(0)">Research <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li><a href="research.php#intro">Introduction</a></li>
                            <li><a href="research.php#areas">Research Areas</a></li>
                            <li><a href="research.php#thesis">M.Phil & Ph.D</a></li>
                            <li><a href="research.php#patents">Patents</a></li>
                            <li><a href="research.php#shreetech">Shreetech / ISRD</a></li>
                        </ul>
                    </li>
                    <li class="has-dropdown">
                        <a href="javascript:void(0)">Publications <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li><a href="publications.php#journals">Journals</a></li>
                            <li><a href="publications.php#intl-conferences">Intl Conferences</a></li>
                            <li><a href="publications.php#nat-conferences">National Conferences</a></li>
                            <li><a href="publications.php#patents">Patents</a></li>
                            <li><a href="publications.php#books">Books</a></li>
                        </ul>
                    </li>
                    <li class="has-dropdown">
                        <a href="javascript:void(0)">Experience <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li><a href="experience.php#journey">Professional Journey</a></li>
                            <li><a href="experience.php#examinership">Examinership</a></li>
                        </ul>
                    </li>
                    <li class="has-dropdown">
                        <a href="javascript:void(0)">Teaching <i class="fa-solid fa-chevron-down"></i></a>
                        <ul class="submenu">
                            <li><a href="teaching.php#subjects">Subjects</a></li>
                            <li><a href="teaching.php#courses">Courses</a></li>
                            <li><a href="teaching.php#mentoring">Mentoring</a></li>
                        </ul>
                    </li>
                    <li><a href="talks.php">Talks</a></li>
                    <li><a href="skills.php">Skills</a></li>
                    <li><a href="contact.php">Contact</a></li>
                </ul>
                <div class="social-wrapper mt--40">
                    <span class="subtitle">find with me</span>
                    <div class="social-link">
                        <?php foreach($contact['social'] as $soc): ?>
                            <a href="<?php echo $soc['link']; ?>"><i class="<?php echo $soc['icon']; ?>"></i></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
