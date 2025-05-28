<?php
defined("ABSPATH") || die("-1");

register_theme_metabox("Hero/Banner - Afbeelding bij binnenkomst", [
    'post_type' => 'page',
     'render-condition' => function(){
        $template_filename = basename(get_page_template());
        
        return $template_filename != 'template-werken-bij.php';
    },
    'render-callback' => function(){
        include "template.php";
    },
    'save-callback' => function($post_id){
        if( isset($_POST['banner-shortcut-content'] ) ){
            update_post_meta($post_id, 'banner-shortcut-content', $_POST['banner-shortcut-content']);
        }
    }
]);