<?php
defined('ABSPATH') || die('-1');

register_theme_option_page('Header', [
    'callback' => function(){
        include('template.php');
    },
    'position' => 10,
]);