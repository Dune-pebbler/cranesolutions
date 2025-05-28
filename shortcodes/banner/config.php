<?php
defined("ABSPATH") || die("-1");

add_shortcode("banner", "theme_banner_output_html");

function theme_banner_output_html($atts, $content){
    ob_start();

    $shortcode_atts = shortcode_atts([
        'image-id' => false,
        'title' => false,
        'subtitle' => false,
        'classes' => '',
        'alignment' => '',
    ], $atts); 


    if( empty($content) ) {
        $content = "<h1>{$shortcode_atts['title']}</h1>";
        
    }

    include("template.php");

    return ob_get_clean();
}