<?php
$term = get_queried_object();
$project_query = get_project_query([
    'tax_query' => [
        [
            'terms' => $term->name,
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

<?php if ($term && !empty($term->description)) : ?>
    <section class="page-content">
        <div class="backdrop">
            <?= get_svg("images/page-content-backdrop.svg"); ?>
        </div>
        <section class="text-field">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-8 offset-lg-2">
                        <div class="text-container">
                            <?= '<p>' . $term->description . '</p>' ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </section>
<?php endif; ?>

<?php if ($project_query->have_posts()) : ?>
    <section class="news is-news-archive">
        <div class="container">
            <div class="titel-container">
                <h2> <?= __('Projecten voor'); ?> <?php single_term_title(); ?></h2>
                <!-- <form method="GET">
                <input type="text" name="search" value="<?= @$_GET['search']; ?>" placeholder="zoeken in <?php single_term_title(); ?>">
            </form> -->
            </div>
            <div class="row">
                <?php
                while ($project_query->have_posts()) : $project_query->the_post();
                    get_template_part("template-parts/project", "item");
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
            <?= theme_pagination($project_query); ?>
        </div>
    </section>
<?php endif; ?>

<?= do_shortcode("[related-posts per-row='3' subtitle='' title='Nieuws over $term->name' orderby='date' sectors='$term->term_id' order='DESC']"); ?>

<?php get_footer(); ?>