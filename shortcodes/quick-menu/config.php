<?php
defined("ABSPATH") || die("-1");

add_shortcode("quick-menu", "theme_quick_menu_output_html");

function theme_quick_menu_output_html($atts, $content){
    ob_start();

    $shortcode_atts = shortcode_atts([
        'image-id'  => false,
        'title'     => false,
    ], $atts);

    include("template.php");

    return ob_get_clean();
}