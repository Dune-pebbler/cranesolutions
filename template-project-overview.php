<?php

/**
 *  Template name: Template Projectoverzicht
 */
$project_query = get_filterd_sector_projects_query();
$get_children_query = theme_get_current_page_children();
get_header('header-1'); ?>

<section class="banner no-image">
    <div class="background-image"></div>
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
                <div class="col-12 col-lg-8 offset-lg-2">
                    <h1><?php the_title(); ?></h1>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="page-content">
    <div class="backdrop">
        <?= get_svg("images/page-content-backdrop.svg"); ?>
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


    <?php if ($get_children_query->have_posts()) : ?>
        <section class="news is-news-archive">
            <div class="container">
                <!-- <div class="titel-container">
                <h2> <?= __('Bekijk onze vacatures', THEME_TD); ?> </h2>
            </div> -->
                <div class="row">
                    <?php
                    while ($get_children_query->have_posts()) : $get_children_query->the_post();
                        get_template_part("template-parts/child", "item");
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</section>
<section class="news is-project-archive">

    <div class="container">
    <div class="filters">
            <ul>
                <li class='is-active'><button class='filter-btn' data-reset>Alles</button></li>
                <?php foreach (get_terms(['taxonomy' => 'sectors', 'hide_empty' => true]) as $term) : ?>
                    <li><button class='filter-btn' data-filter="<?= $term->slug; ?>"><?= $term->name; ?></button></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div id="loader" style="display:none">
          <svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
            <path fill="#F49600" d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50">
              <animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="360 50 50" repeatCount="indefinite" />
            </path>
          </svg>
        </div>
        <div class="row" id="project-container">
            <?php while ($project_query->have_posts()) : $project_query->the_post(); ?>
                <?php get_template_part('template-parts/project', 'item'); ?>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>
        <?= theme_pagination($project_query); ?>
    </div>
</section>
<?= do_shortcode("[featured-vacancies]"); ?>
<?php get_template_part("template-parts/our", 'partners'); ?>
<?php get_footer(); ?>