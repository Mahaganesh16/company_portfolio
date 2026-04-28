<?php
require_once('functions.php');
$hero = $p_content['hero'];
$cust = $p_content['customization'];

$pageTitle = $cust['site_title'];
$metaDescription = $cust['meta_description'];
include('header.php');
?>

    <!-- tmp banner area start -->
    <div class="tmp-banner-one-area">
        <div class="container">
            <div class="banner-one-main-wrapper">
                <div class="row align-items-center">
                    <div class="col-lg-6 order-lg-2">
                        <div class="banner-right-content">
                            <img class="tmp-scroll-trigger tmp-zoom-in animation-order-1" src="<?php echo $hero['image']; ?>" alt="<?php echo $hero['name']; ?>" style="border-radius: 250px 250px 0 0; border: 5px solid rgba(255,255,255,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.1); object-fit: cover;">
                            <h2 class="banner-big-text-1 up-down">Professor</h2>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <div class="inner">
                            <span class="sub-title tmp-scroll-trigger tmp-fade-in animation-order-1"><?php echo $hero['sub_title']; ?></span>
                            <h4 class="title tmp-scroll-trigger tmp-fade-in animation-order-2 mt--5" style="font-size: 38px; line-height: 1.2;"><?php echo $hero['title_prefix']; ?>
                               <?php echo $hero['name']; ?> <br>
                                <span class="header-caption">
                                    <span class="cd-headline clip is-full-width">
                                        <span class="cd-words-wrapper">
                                            <?php foreach($hero['designations'] as $index => $designation): ?>
                                                <b class="<?php echo $index === 0 ? 'is-visible' : 'is-hidden'; ?> theme-gradient">  <?php echo $designation; ?></b>
                                            <?php endforeach; ?>
                                        </span>
                                </span>
                                </span>
                            </h4>
                            <p class="disc tmp-scroll-trigger tmp-fade-in animation-order-3"><?php echo nl2br($hero['description']); ?></p>
                            <div class="button-area-banner-one tmp-scroll-trigger tmp-fade-in animation-order-4">
                                <a class="tmp-btn hover-icon-reverse radius-round" href="about">
                                    <span class="icon-reverse-wrapper">
                                        <span class="btn-text">View Portfolio</span>
                                    <span class="btn-icon"><i class=" fa-solid fa-arrow-right"></i></span>
                                    <span class="btn-icon"><i class=" fa-solid fa-arrow-right"></i></span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- tmp banner area end -->

<?php include('footer.php'); ?>
