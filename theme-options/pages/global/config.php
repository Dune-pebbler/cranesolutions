<?php
defined('ABSPATH') || die('-1');

register_theme_option_page('Globaal', [
    'callback' => function(){
        include('template.php');
    },
    'position' => 10,
]);

add_action('admin_init', function(){
    if( !isset($_REQUEST['global_theme_nonce_field']) ) return;
    if( !wp_verify_nonce($_REQUEST['global_theme_nonce_field'], 'global_theme_nonce_field')) return;

    foreach($_REQUEST as $key => $value){
        if( strpos($key, "theme_option_") === false) continue;

        update_theme_option($key, is_string($value) ? stripslashes($value) : $value);
    }

    wp_safe_redirect($_REQUEST['_wp_http_referer']);
    exit;
});