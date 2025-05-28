<?php
defined("ABSPATH") || die("-1");

add_shortcode("related-posts", "theme_related_posts_output_html");

function theme_related_posts_output_html($atts, $content)
{
    ob_start();

    $shortcode_atts = shortcode_atts([
        'post_type' => 'post',
        'title' => is_single() ? "Andere artikelen" : 'Gerelateerde info',
        'subtitle' => 'Lees over wat ons bezig houdt',
        'archive-url' => '',
        'archive-title' => '',
        'per-row' => 2,
        'post_ids' => [],
        'orderby' => 'rand',
        'order' => 'ASC',
        'category_ids' => '',
        'sectors' => '',
        'product_categories' => ''
    ], $atts);

    $args = [
        'post_type' => $shortcode_atts['post_type'],
        'posts_per_page' =>  $shortcode_atts['per-row'],
        'post__in' => $shortcode_atts['post_ids'],
        'page__in' => $shortcode_atts['post_ids'],
        'category__in' => $shortcode_atts['category_ids'],
        'orderby' => $shortcode_atts['orderby'],
        'order'    => $shortcode_atts['order'],
        'post__not_in'   => array(get_the_ID()),
    ];

    $args += $shortcode_atts['sectors'] ? [
        'tax_query' => [
            [
                'taxonomy' => 'sectors',
                'field' => 'term_id', 
                'terms' => explode(',', $shortcode_atts['sectors']),
                'operator' => 'IN'
            ],
        ],
    ] : [];

    $args += $shortcode_atts['product_categories'] ? [
        'tax_query' => [
            [
                'taxonomy' => 'product_cat',
                'terms' => explode(',', $shortcode_atts['product_categories']),
                'include_children' => false,
            ],
        ],
    ] : [];

    $query = new WP_Query($args);

    include("template.php");

    return ob_get_clean();
}
