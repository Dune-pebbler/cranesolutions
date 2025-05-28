<?php
defined("ABSPATH") || die("-1");

register_theme_metabox("Tekst block", [
    'post_type' => ['page', 'jobpostings'],
    'render-condition' => function(){
        $template_filename = basename(get_page_template());

        return $template_filename == 'template-werken-bij.php' || get_post_type() == 'jobpostings';
    },
    'render-callback' => function(){
        include "template.php";
    },
    'save-callback' => function($post_id){
          if( isset($_POST['text-block-content'] ) ){
            update_post_meta($post_id, 'text-block-content', $_POST['text-block-content']);
        }
    }
]);