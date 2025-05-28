<?php 
$term = get_queried_object();
$project_query = get_project_query([
    'post_type' => 'post',
    'tax_query' => [
        [
            'terms' => [$term->name],
            'taxonomy' => $term->taxonomy,
            'field' => 'name'
        ]
    ]
]);

get_header(); ?>

<section class="banner no-image">
    <div class="background-image">
        <!-- <img src="<?= get_template_directory_uri(); ?>/images/20201209_121227.jpg" alt=""> -->
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
                    <h1><?php single_term_title(); ?></h1>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="news is-news-archive">
    <div class="container">
        <div class="titel-container">
            <h2> <?= __('Artikelen voor'); ?> <?php single_term_title(); ?></h2>
            <!-- <form method="GET">
                <input type="text" name="search" value="<?= @$_GET['search']; ?>" placeholder="zoeken in <?php single_term_title(); ?>">
            </form> -->
        </div>
        <?php if( $project_query->have_posts() ): ?>
        <div class="row">
            <?php 
            while( $project_query->have_posts() ): $project_query->the_post(); 
                get_template_part("template-parts/news", "item");
            endwhile; wp_reset_postdata(); 
            ?>
        </div>
        <?= theme_pagination($project_query); ?>
        <?php endif; ?>
    </div>
</section>
<?php get_footer(); ?>