<?php
defined("ABSPATH") || die("-1"); 

add_shortcode("quick-menu-item", "theme_quick_menu_item_output_html");

function theme_quick_menu_item_output_html($atts, $content){
    ob_start();

    $shortcode_atts = shortcode_atts([
        'image-id'  => false,
        'image-url' => false,
        'title'     => false,
        'button'     => false,
        'url' => '#!',
    ], $atts);

    include("template.php");

    return ob_get_clean();
}