<?php
defined('ABSPATH') || die('-1');

// globals
global $theme_options;

define('THEME_OPTIONS_DIR', dirname(__FILE__));
define('THEME_OPTIONS_URL', get_template_directory_uri() . '/theme-options');

// we need to load this before
setup_theme_options();

// includes
include dirname(__FILE__) . '/pages/header/config.php';
include dirname(__FILE__) . '/pages/global/config.php';
include dirname(__FILE__) . '/pages/footer/config.php';

// hooks
add_action('admin_enqueue_scripts', 'theme_options_enqueue_scripts');
add_action('admin_menu', function() {
    add_menu_page(
        'Thema Opties', // Paginatitel
        'Thema Opties', // Menu titel
        'manage_options', // Capaciteit die nodig is om toegang te krijgen tot de pagina
        'theme_settings_page', // Unieke ID voor de pagina
        function(){
            include("pages/template-parent-menu.php");
        },
        'dashicons-table-col-after', // Pictogram voor het menupunt,
        10,
    );
});

// we create our functions here
function theme_options_enqueue_scripts(){
    wp_enqueue_style('admin-css', get_template_directory_uri() . '/stylesheets/admin.css');
    wp_enqueue_style('select2--css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
    wp_enqueue_script('select2--js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js');
    wp_enqueue_script(
        'custom-block-script', // Unieke handle voor de script
        get_template_directory_uri() . '/js/admin.global.js', // Pad naar je aangepaste blokstijl-bestand
        array( 'wp-blocks', 'wp-dom-ready', 'wp-edit-post' ), // Lijst van afhankelijkheden
        '1.0', // Versienummer
        true // Laad het script in de footer
    );
}

function register_theme_option_page( $name, $arguments ){
    global $theme_options;

    $theme_options['pages'][$name] = $arguments;
} 

function theme_option_parent_template(){

}

function init_theme_options(){

}

function init_theme_options_menu(){
    global $theme_options;
    
    // load pages
    foreach($theme_options['pages'] as $name => $arguments){
        add_action('admin_menu', function () use ($name, $arguments){
            add_submenu_page( 'theme_settings_page', $name, $name, 'manage_options', sanitize_title("theme-options-" . $name), $arguments['callback'], isset($arguments['position']) ? $arguments['position'] : 10 );
        }, 90);
    }
}

function setup_theme_options(){
    global $theme_options;

    // we want to configure our theme_options
    $theme_options = [
        'pages' => [],
    ];

    add_action('admin_init', 'init_theme_options');
    add_action('admin_menu', 'init_theme_options_menu');
}

function get_theme_option($name, $default_value = false) {
    $option_name = defined("ICL_LANGUAGE_CODE") ? $name . "_" . ICL_LANGUAGE_CODE : $name;

    return get_option($option_name, $default_value);
}

function update_theme_option($name, $value){
    $option_name = defined("ICL_LANGUAGE_CODE") ? $name . "_" . ICL_LANGUAGE_CODE : $name;

    update_option($option_name, $value);

    return get_option($option_name, $value);

}