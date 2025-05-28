<?php 
defined("ABSPATH") || die("-1");

global $wp_query;

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
                    <h1><?= post_type_archive_title(); ?></h1>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="news is-news-archive">
    <div class="container">
        <div class="titel-container">
            <h2> <?= __('Wat vind je interessant?'); ?> </h2>
            <form method="GET">
                <input type="text" name="s" value="<?= @$_GET['s']; ?>" placeholder="zoeken">
            </form>
        </div>
        <?php
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

        $the_query = new WP_Query( $args );

        // The Loop
        if($the_query->have_posts()):?> 
        <div class="row"> 
            <?php while ( $the_query->have_posts() ) {
                $the_query->the_post();
                get_template_part("template-parts/vacature", "item"); 
            } 
            wp_reset_postdata(); ?> 
        </div> 
        <?= theme_pagination($wp_query); ?>
        <?php else: ?>
            <p><?= __('Helaas hebben we geen nieuwsberichten kunnen vinden met', THEME_TD); ?> '<b><?= @$_GET['s']; ?></b>'</p>
        <?php endif; ?>
    </div>
</section>


<?php get_footer(); ?>