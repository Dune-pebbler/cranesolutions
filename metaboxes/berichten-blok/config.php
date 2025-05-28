<?php
defined("ABSPATH") || die("-1");

register_theme_metabox("Berichten blok", [
    'post_type' => 'page',
    'render-condition' => function(){
        $template_filename = basename(get_page_template());
        
        return $template_filename == 'template-werken-bij.php';
    },
    'render-callback' => function(){
        include "template.php";
    },
    'save-callback' => function($post_id){
          if( isset($_POST['message-block-content'] ) ){
            update_post_meta($post_id, 'message-block-content', $_POST['message-block-content']);
        }
    }
]);