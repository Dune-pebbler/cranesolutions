<?php
defined("ABSPATH") || die("-1");

add_shortcode("text-block", "theme_text_block_output_html");

function theme_text_block_output_html($atts, $content){
    ob_start();

    $shortcode_atts = shortcode_atts([
        'mode' => 'introduction'
    ], $atts);

    include("template.php");

    return ob_get_clean();
}