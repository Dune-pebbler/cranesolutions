<?php
defined("ABSPATH") || die("-1");

add_shortcode("button", "theme_button_output_html");

function theme_button_output_html($atts, $content){
    ob_start();

    $shortcode_atts = shortcode_atts([
        'url' => false,
        'title' => false,
        'target' => false,
    ], $atts);

    include("template.php");

    return ob_get_clean();
}