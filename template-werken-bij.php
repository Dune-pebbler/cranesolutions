<?php

/**
 *  Template name: Template Werken bij
 */
$args = array(
    'post_type' => 'jobpostings',
    'post_status' => 'publish',
    'meta_query' => array(
        array(
            'key' => 'seoninja_vacature_valid_through',
            'value' => date('Y-m-d'), // Today's date
            'compare' => '>=',
            'type' => 'DATE' // Important: Make sure to compare as dates
        )
    )
);

if (isset($_GET['search'])) {
    $args['s'] = wp_kses_data($_GET['search']);
}

$query = new WP_Query($args);

get_header('header-1'); ?>
<section class="banner no-image">
    <div class="background-image"></div>
    <div class="breadcrumbs">
        <div class="container">
            <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
        </div>
    </div>
    <div class="text-container">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-8 offset-lg-2">
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
        <div class="backdrop">
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

    <?php /*
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
 */ ?>

    <section class="news is-news-archive">
        <div class="container">
            <div class="titel-container">
                <h2> <?= __('Wil je werken bij CraneSolutions?', THEME_TD); ?> </h2>
            </div>
            <?php if ($query->have_posts()) : ?>
                <div class="row">
                    <?php
                    while ($query->have_posts()) : $query->the_post();
                        get_template_part("template-parts/vacature", "item");
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
                <?= theme_pagination($query); ?>
            <?php else : ?>
                <p><?= __('Helaas hebben we geen vacaturees kunnen vinden met', THEME_TD); ?> '<b><?= $_GET['search']; ?></b>'</p>
            <?php endif; ?>
        </div>
    </section>
    <?php if ($content = get_meta('message-block-content')) : ?>
        <section class="open-soli">
            <div class="backdrop">
                <?= get_svg("images/page-content-backdrop.svg"); ?>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-8 offset-lg-2">
                        <div class="text-container">
                            <?= do_shortcode($content); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
    <?php if ($content = get_meta('text-block-content')) : ?>
        <section class="text-field">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-8 offset-lg-2">
                        <div class="text-container">
                            <?= do_shortcode($content); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>
</section>

<?= do_shortcode("[related-posts per-row='3' title='Interviews met collega&#39;s' orderby='date' order='DESC' category_ids='20']"); ?>



<?php get_footer(); ?>