<?php
defined("ABSPATH") || die("-1");

add_shortcode("faq-items", "theme_faq_item_output_html");

function theme_faq_item_output_html($atts, $content)
{
    ob_start();

    $shortcode_atts = shortcode_atts([
        'post_type' => 'faqs',
        'question' => '',
        'orderby' => 'date',
        'category_ids' => '',
        'title' => 'FAQ'
    ], $atts);

    $args = [
        'post_type' => $shortcode_atts['post_type'],
        'orderby' => $shortcode_atts['orderby'],
    ];

    $args += $shortcode_atts['category_ids'] ? [
        'tax_query' => [
            [
                'taxonomy' => 'product_cat',
                'terms' => explode(',', $shortcode_atts['category_ids']),
                'include_children' => false,
            ],
        ],
    ] : [];

    $query = new WP_Query($args);

    include("template.php");

    return ob_get_clean();
}
