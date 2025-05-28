<?php 
global $wp_query; 
get_header(); ?>

<section class="banner no-image">
    <div class="background-image">
        <!-- <img src="<?= get_template_directory_uri(); ?>/images/20201209_121227.jpg" alt=""> -->
    </div>
    <div class="breadcrumbs">
        <div class="container">
            <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
        </div>
    </div>
    <div class="text-container">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-6 offset-lg-2">
                    <p style='margin-bottom: 0;'><?= __('Uw zoekopdracht', THEME_TD); ?>: </p>
                    <h1><?php the_search_query(); ?></h1>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="news">
    <div class="container">
     <?php if( have_posts() ): ?>
        <div class="row">
            <?php 
            while( have_posts() ): the_post(); 
                get_template_part("template-parts/news", "item");
            endwhile; wp_reset_postdata(); 
            ?>
        </div>
        <?= theme_pagination($wp_query); ?>
    <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>