<?php
$featured_review_ids = get_theme_option('theme_option_featured_reviews_ids', [1515, 1514, 1513]);
$review_query = get_review_query();
get_header(); ?>

<section class="banner no-image">
    <div class="background-image">
        <img src="<?= get_template_directory_uri(); ?>/images/20201209_121227.jpg" alt="">
    </div>
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-8 offset-lg-2">
                    <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="text-container">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-6 offset-lg-2">
                    <p style='margin-bottom: 0;'>Vacatures</p>
                    <h1><?php the_title(); ?></h1>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="page-content">
    <div class="backdrop">
        <div class="top right">
            <?= get_svg('images/arrows-up-backdrop.svg'); ?>
        </div>
        <div class="bottom left">
            <?= get_svg("images/page-content-backdrop.svg"); ?>
        </div>
    </div>
    <section class="text-field">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-8 offset-lg-2">
                    <div class="text-container">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if ($user_object = get_team_object_by_auteur()) : ?>
        <section class="contact-us">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-3 offset-lg-1">
                        <div class="image">
                            <?php if (has_post_thumbnail($user_object->ID)) : ?>
                                <?= get_the_post_thumbnail($user_object->ID, 'full', []); ?>

                            <?php else : ?>
                                <img src="https://secure.gravatar.com/avatar/6ac40747f1f2a118d21980a197c931a9?&d=mm&r=g" width="100%" loading="lazy" alt="<?= $user_object->post_title; ?> profiel foto">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col12- col-lg-5 offset-lg-1">
                        <div class="text-container">
                            <!-- <h2><?= __('Op zoek naar een speciale hijsoplossing?', THEME_TD); ?></h2> -->
                            <p><?= __('Neem contact op met', THEME_TD); ?> <a href="<?= get_the_permalink($user_object->ID); ?>"><?= $user_object->post_title; ?></a> </p>
                            <div class="buttons">
                                <?php if ($phone_number = get_meta('seoninja_employee_telephone', $user_object->ID)) : ?>
                                    <a href="tel:<?= $phone_number; ?>" class="btn is-alternative"><?= str_replace("+31", "0", $phone_number); ?> <?= get_svg("images/arrow-right.svg"); ?></a>
                                <?php endif; ?>

                                <!-- <?php if ($email = get_meta('seoninja_employee_email', get_the_ID())) : ?>
                            <a href="mailto:<?= $email; ?>" class="btn is-alternative"><?= $email; ?> <?= get_svg("images/arrow-right.svg"); ?></a>
                        <?php endif; ?> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="contact-form" style='padding: 25px 0'>
        <div class="container">
            <div class="col-12 col-lg-8 offset-lg-2">
                <div class="form">
                    <?= do_shortcode('[gravityform id="4" title="true" ajax="false"]'); ?>
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
                        <?php if ($review_query->have_posts()) : ?>
                            <div class="reviews">
                                <div class="review-slider owl-carousel owl-theme">
                                    <?php while ($review_query->have_posts()) : $review_query->the_post(); ?>
                                        <?php get_template_part('template-parts/review', 'item'); ?>
                                    <?php endwhile;
                                    wp_reset_postdata(); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if ($content = get_meta('text-block-content')) : ?>
        <section class="text-field">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-8 offset-lg-2">
                        <div class="text-container">
                            <?= $content; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <div class="buttons">
        <a href="/werken-bij-cranesolutions" class="btn is-back-btn">
            <?= __('Terug naar vacatureoverzicht', THEME_TD); ?>
            <?= get_svg("images/arrow-right.svg"); ?>
        </a>
    </div>
</section>



<?= do_shortcode("[related-posts per-row='3' subtitle='' title='Interviews met collega&#39;s' orderby='date' order='DESC' category_ids='20']"); ?>



<?php get_footer(); ?>