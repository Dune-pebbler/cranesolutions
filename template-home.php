<?php
defined("ABSPATH") || die("-1");

/*
 * Template name: Template Homepagina
 */

$featured_review_ids = get_theme_option('theme_option_featured_reviews_ids', [1515, 1514, 1513]);
$review_query = get_review_query();
 
get_header();
?> 


<section class="banner">
    <?php if( $image = wp_get_attachment_image(get_meta('banner-image-id'), 'full', [])): ?>
    <div class="background-image">
        <?=  $image; ?>
    </div>
    <?php endif; ?>
    
    <div class="text-container">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-6">
                   <?= get_meta('banner-caption'); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if( $content = get_meta('banner-shortcut-content')): ?>
<section class="quick-start">
    <div class="container">
        <div class="quick-start-item">
            <div class="content">
                <?= $content; ?>
            </div>
            
            <?php if( $url = get_meta('banner-shortcut-url-options')): $url_options_object = json_decode($url); ?>
            <a href="<?= $url_options_object->url; ?>" class='btn'><?= $url_options_object->text; ?> <?= get_svg('images/arrow-right.svg'); ?></a>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="quick-actions">
    <div class="container">
        <?php if( $titel = get_meta('quickmenu-titel')): ?>
            <h2><?= $titel; ?></h2>
        <?php endif; ?>

        <div class="row">
            <?php for( $i = 0; $i < 4; $i++): ?>
                <?php 
                $image_id = get_meta("quickmenu-icon-{$i}"); 
                $url_options = get_meta("url-options-{$i}");
                $url_options_object = json_decode($url_options);
                ?>

                <div class="col-12 col-sm-6 col-xxl-3">
                    <a href="<?= @$url_options_object->url; ?>" target="<?= $url_options_object->isBlanked ? "_blank" : ""; ?>">
                        <div class="icon">
                            <?=  wp_get_attachment_image($image_id, 'full', []); ?>
                        </div>

                        <?php if( isset($url_options_object->text) ): ?>
                        <p><?= $url_options_object->text; ?></p>
                        <?php endif; ?>

                        <button aria-label="Ontdek meer"> <span><?= get_svg('images/arrow-right.svg'); ?></span> Ontdek meer</button>
                    </a>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</section>

<section class="introduction">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8 offset-lg-2">
                <?php the_content(); ?>
            </div>
        </div>
    </div>
</section>

<section class="usps" style="background-image: url('<?= wp_get_attachment_url(get_theme_option('theme_option_usps_background_image')); ?>');">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-10 offset-lg-1">
                <div class="usps-box">
                    <div class="usps">
                    <?= wpautop(get_theme_option('theme_option_usps_block_content')); ?>
                    </div>
                    <?php if($review_query->have_posts()): ?>
                    <div class="reviews">
                        <div class="review-slider owl-carousel owl-theme">
                            <?php while($review_query->have_posts()): $review_query->the_post(); ?>
                                <?php get_template_part('template-parts/review', 'item'); ?>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="content-block">
    <div class="background-image">
        <img src="/wp-content/themes/crane-theme/images/drawing.png">
    </div>

    <div class="container">
        <div class="row">
            <?php if( 'right' == 'left' ): ?>
            <div class="col-12 col-lg-4 offset-lg-2">
                <?php if( $image = wp_get_attachment_image(get_meta("content-block-image-id"), 'full', [])): ?>
                    <div class="image">
                        <?= $image; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-12 col-lg-4 offset-lg-1">
                <div class="text-container">
                   <?= get_meta('content-block-content'); ?>
                </div>
            </div>
            <?php else: ?>
                <div class="col-12 col-lg-6">
                    <div class="text-container">
                         <?= get_meta('content-block-content'); ?>
                    </div>
                </div>
                <div class="col-12 col-lg-5 offset-lg-1">
                    <?php if( $image = wp_get_attachment_image(get_meta("content-block-image-id"), 'full', [])): ?>
                        <div class="image">
                           <?= $image; ?>
                        </div>
                    <?php endif; ?>
                </div>

            <?php endif; ?>
        </div>
    </div>
</section> 

<?= do_shortcode("[latest-posts]"); ?>
<?= do_shortcode("[featured-vacancies]"); ?>
<?php get_template_part("template-parts/our", 'partners'); ?>

<?php get_footer(); ?>