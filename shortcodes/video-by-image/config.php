<?php
defined("ABSPATH") || die("-1");

add_shortcode("video-by-placeholder", "theme_video_by_placeholder_output_html");

function theme_video_by_placeholder_output_html($atts, $content){
    ob_start();

    $shortcode_atts = shortcode_atts([
        'video-url' => '',
        'image-id' => '',
    ], $atts);

    include("template.php");

    return ob_get_clean();
}