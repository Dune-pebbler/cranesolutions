<?php
defined("ABSPATH") || die("-1");

add_shortcode("message", "theme_message_output_html");

function theme_message_output_html($atts, $content){
    ob_start();

    $shortcode_atts = shortcode_atts([], $atts);

    include("template.php");

    return ob_get_clean();
}