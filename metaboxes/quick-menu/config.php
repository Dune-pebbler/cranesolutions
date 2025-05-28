<?php
defined("ABSPATH") || die("-1");

register_theme_metabox("Snelle menu - 4 snelle links", [
    'post_type' => 'page',
    'render-condition' => function(){
        $template_filename = basename(get_page_template());
        
        return $template_filename == 'template-home.php';
    },
    'render-callback' => function(){
        include "template.php";
    },
    'save-callback' => function($post_id){
        if( !isset($_POST['quickmenu'] ) ){ return; }
    }
]);