<?php
defined("ABSPATH") || die("-1");

add_shortcode("latest-posts", "theme_latest_posts_output_html");

function theme_latest_posts_output_html($atts, $content){
    ob_start();

    $shortcode_atts = shortcode_atts([
        'post_type' => 'post',
        'title' => 'Laatste hoogtepunten',
        'subtitle' => 'Lees over wat ons bezig houdt',
        'archive-url' => '',
        'archive-title' => '',
        'per-row' => 3
    ], $atts);

    $args = [
        'post_type' => $shortcode_atts['post_type'],
        'posts_per_page' =>  $shortcode_atts['per-row'],
    ];
    $query = new WP_Query($args);

    include("template.php");

    return ob_get_clean();
}