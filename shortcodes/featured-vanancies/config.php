<?php
defined("ABSPATH") || die("-1");

add_shortcode("featured-vacancies", "theme_featured_vacancies_output_html");

function theme_featured_vacancies_output_html($atts, $content){
    ob_start();

    $shortcode_atts = shortcode_atts([
        'title' => 'Uitgelichte vacatures',
        'subtitle' => 'Lees over wat ons bezig houdt',
        'post_type' => 'jobpostings',
    ], $atts);
    
    $args = [
        'post_type' => $shortcode_atts['post_type'],
        'posts_per_page' => 3,
        'post__in' => get_theme_option('theme_option_featured_vacancies_ids', []),
    ];

    $content = get_option("featured_vacancies_block_content");

    $query = new WP_Query($args);

    include("template.php");

    return ob_get_clean();
}