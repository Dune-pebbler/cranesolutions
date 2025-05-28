<?php
defined("ABSPATH") || die("-1");

register_theme_metabox("Content block - Afbeelding rechts / tekst links", [
    'post_type' => 'page',
    'render-condition' => function(){
        $template_filename = basename(get_page_template());
        
        return $template_filename != 'template-contact.php';
    },
    'render-callback' => function(){
        include "template.php";
    },
    'save-callback' => function($post_id){
          if( isset($_POST['content-block-content'] ) ){
            update_post_meta($post_id, 'content-block-content', $_POST['content-block-content']);
        }
    }
]);