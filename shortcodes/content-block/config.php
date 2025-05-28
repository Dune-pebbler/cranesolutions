<?php
defined("ABSPATH") || die("-1");

add_shortcode("content-block", "theme_content_block_output_html");

function theme_content_block_output_html($atts, $content){
    ob_start();

    $shortcode_atts = shortcode_atts([
        'image-id' => false,
        'background-image-id' => false,
        'image-position' => 'left'
    ], $atts);

    include("template.php");

    return ob_get_clean();
} 