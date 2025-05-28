<?php 
defined("ABSPATH") || die("-1");
/** 
 *  Template name: Template Nieuwsoverzicht
 */

$args = [
    'post_type' => 'post',
    'posts_per_page' => 12,
    'paged' => max(1, get_query_var('paged')),
];

if( isset($_GET['search']) ) {
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
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="news is-news-archive">
    <div class="container">
        <div class="titel-container">
            <h2> <?= __('Wat vind je interessant?'); ?> </h2>
            <!-- <form method="GET">
                <input type="text" name="search" value="<?= @$_GET['search']; ?>" placeholder="zoeken in nieuws">
            </form> -->
        </div>
        <?php if( $query->have_posts() ): ?>
        <div class="row">
            <?php 
            while( $query->have_posts() ): $query->the_post(); 
                get_template_part("template-parts/news", "item");
            endwhile; wp_reset_postdata(); 
            ?>
        </div>
        <?= theme_pagination($query); ?>
        <?php else: ?>
            <p><?= __('Helaas hebben we geen nieuwsberichten kunnen vinden met', THEME_TD); ?> '<b><?= $_GET['search']; ?></b>'</p>
        <?php endif; ?>
    </div>
</section>


<?php get_footer(); ?>