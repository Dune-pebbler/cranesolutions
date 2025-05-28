<?php
/**
 * Replace product_brand taxonomy with capacity taxonomy
 * Add to your theme's functions.php or create a custom plugin
 */

// Unregister the default product_brand taxonomy
// function unregister_product_brand_taxonomy() {
//     if (taxonomy_exists('product_brand')) {
//         unregister_taxonomy('product_brand');
//     }
// }
// add_action('init', 'unregister_product_brand_taxonomy', 9);

// Register the new capacity taxonomy
function register_capacity_taxonomy() {
    $labels = array(
        'name'              => _x('Capaciteiten', 'taxonomy general name', 'textdomain'),
        'singular_name'     => _x('Capaciteit', 'taxonomy singular name', 'textdomain'),
        'search_items'      => __('Zoek Capaciteiten', 'textdomain'),
        'all_items'         => __('Alle Capaciteiten', 'textdomain'),
        'parent_item'       => __('Hoofd Capaciteit', 'textdomain'),
        'parent_item_colon' => __('Hoofd Capaciteit:', 'textdomain'),
        'edit_item'         => __('Bewerk Capaciteit', 'textdomain'),
        'update_item'       => __('Update Capaciteit', 'textdomain'),
        'add_new_item'      => __('Voeg Nieuwe Capaciteit Toe', 'textdomain'),
        'new_item_name'     => __('Nieuwe Capaciteit Naam', 'textdomain'),
        'menu_name'         => __('Capaciteit', 'textdomain'),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'capaciteit'),
        'show_in_rest'      => true, // Enable Gutenberg editor support
    );

    register_taxonomy('capacity', array('product'), $args);
}
add_action('init', 'register_capacity_taxonomy', 10);