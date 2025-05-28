<?php
/**
 * Product Filtering Functions
 * 
 * Contains all functionality related to product filtering on the shop page
 */

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Enqueue necessary scripts for product filtering
 */
function enqueue_filter_scripts() {
    if (is_shop() || is_product_category()) {
        wp_enqueue_script('product-filters', get_template_directory_uri() . '/js/product-filters.js', array('jquery'), '1.0', true);
        wp_localize_script('product-filters', 'filterAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('filter_products')
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_filter_scripts');

/**
 * Get product categories for filtering
 */
function get_product_categories_for_filter() {
    $args = array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => 0
    );
    
    return get_terms($args);
}

/**
 * Get product brands for filtering
 */
function get_product_brands_for_filter() {
    $args = array(
        'taxonomy' => 'brands',
        'hide_empty' => true,
        'parent' => 0
    );
    
    return get_terms($args);
}

/**
 * Get product price range
 */
function get_product_price_range() {
    global $wpdb;
    
    $price_range = $wpdb->get_row("
        SELECT MIN(CAST(meta_value AS DECIMAL)) as min_price, 
               MAX(CAST(meta_value AS DECIMAL)) as max_price
        FROM {$wpdb->postmeta}
        WHERE meta_key = '_price'
    ");
    
    return $price_range;
}

/**
 * AJAX handler for filtering products
 */
function handle_product_filter() {
    // Globally track if we are on the shop page 
    $GLOBALS['is_shop_layout'] = isset($_POST['is_shop']) ? true : false;
    
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 12,
        'paged' => $_POST['page'] ?? 1,
    );

    // Sorting
    switch($_POST['orderby'] ?? 'date') {
        case 'price':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
            break;
        case 'price-desc':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'title':
            $args['orderby'] = 'title';
            $args['order'] = 'ASC';
            break;
        case 'title-desc':
            $args['orderby'] = 'title';
            $args['order'] = 'DESC';
            break;
        default:
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
    }

    $tax_query = array();

    // Category filter
    if (!empty($_POST['category'])) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => $_POST['category']
        );
    }
    
    // Subcategory filter
    if (!empty($_POST['subcategory'])) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'id',
            'terms' => $_POST['subcategory']
        );
    }

    // Brands filter
    if (!empty($_POST['brand'])) {
        $tax_query[] = array(
            'taxonomy' => 'brands',
            'field' => 'id',
            'terms' => $_POST['brand']
        );
    }

    // Combine tax queries if we have any
    if (!empty($tax_query)) {
        if (count($tax_query) > 1) {
            $tax_query['relation'] = 'AND';
        }
        $args['tax_query'] = $tax_query;
    }

    // Add price filter
    if (!empty($_POST['min_price']) || !empty($_POST['max_price'])) {
        $args['meta_query'][] = array(
            'key' => '_price',
            'value' => array($_POST['min_price'] ?? 0, $_POST['max_price'] ?? 9999999),
            'type' => 'NUMERIC',
            'compare' => 'BETWEEN'
        );
    }

    // Add search filter
    if (!empty($_POST['search'])) {
        $args['s'] = sanitize_text_field($_POST['search']);
    }

    $query = new WP_Query($args);

    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            wc_get_template_part('content', 'product');
        }
    } else {
        echo '<p>No products found.</p>';
    }
    $products = ob_get_clean();
    
    $response = array(
        'products' => $products,
        'max_pages' => $query->max_num_pages,
        'found_posts' => $query->found_posts,
        'query_args' => $args // For debugging
    );
    
    wp_send_json_success($response);
    wp_die();
}
add_action('wp_ajax_filter_products', 'handle_product_filter');
add_action('wp_ajax_nopriv_filter_products', 'handle_product_filter');

/**
 * AJAX handler to get subcategories
 */
function get_subcategories_ajax() {
    // Get the parent category ID
    $parent_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    
    if ($parent_id <= 0) {
        wp_send_json_error(['message' => 'Invalid category ID']);
        return;
    }
    
    // Get subcategories
    $subcategories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => $parent_id
    ]);
    
    // Format subcategories for response
    $formatted_subcategories = [];
    foreach ($subcategories as $subcategory) {
        $formatted_subcategories[] = [
            'term_id' => $subcategory->term_id,
            'name' => $subcategory->name,
            'slug' => $subcategory->slug,
            'count' => $subcategory->count
        ];
    }
    
    wp_send_json_success([
        'subcategories' => $formatted_subcategories
    ]);
}
add_action('wp_ajax_get_subcategories', 'get_subcategories_ajax');
add_action('wp_ajax_nopriv_get_subcategories', 'get_subcategories_ajax');

/**
 * Apply URL filter parameters to WooCommerce product query
 */
function apply_url_filters_to_product_query($q, $instance) {
    // Only apply on main query for shop page
    if (!is_admin() && is_shop() && $q->is_main_query()) {
        // Create tax_query array if it doesn't exist
        $tax_query = $q->get('tax_query');
        if (!is_array($tax_query)) {
            $tax_query = array();
        }
        
        // Apply category filter
        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'id',
                'terms'    => sanitize_text_field($_GET['category']),
            );
        }
        
        // Apply subcategory filter
        if (isset($_GET['subcategory']) && !empty($_GET['subcategory'])) {
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'id',
                'terms'    => sanitize_text_field($_GET['subcategory']),
            );
        }
        
        // Apply brand filter
        if (isset($_GET['brand']) && !empty($_GET['brand'])) {
            $tax_query[] = array(
                'taxonomy' => 'brands',
                'field'    => 'id',
                'terms'    => sanitize_text_field($_GET['brand']),
            );
        }
        
        // Set tax_query if we have any taxonomies
        if (!empty($tax_query)) {
            if (count($tax_query) > 1) {
                $tax_query['relation'] = 'AND';
            }
            $q->set('tax_query', $tax_query);
        }
        
        // Apply search filter
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $q->set('s', sanitize_text_field($_GET['search']));
        }
        
        // Apply sorting
        if (isset($_GET['orderby']) && !empty($_GET['orderby'])) {
            $orderby = sanitize_text_field($_GET['orderby']);
            
            switch ($orderby) {
                case 'price':
                    $q->set('meta_key', '_price');
                    $q->set('orderby', 'meta_value_num');
                    $q->set('order', 'ASC');
                    break;
                case 'price-desc':
                    $q->set('meta_key', '_price');
                    $q->set('orderby', 'meta_value_num');
                    $q->set('order', 'DESC');
                    break;
                case 'title':
                    $q->set('orderby', 'title');
                    $q->set('order', 'ASC');
                    break;
                case 'title-desc':
                    $q->set('orderby', 'title');
                    $q->set('order', 'DESC');
                    break;
                default:
                    $q->set('orderby', 'date');
                    $q->set('order', 'DESC');
            }
        }
        
        // Apply price filter
        if ((isset($_GET['min_price']) && !empty($_GET['min_price'])) || 
            (isset($_GET['max_price']) && !empty($_GET['max_price']))) {
            
            $meta_query = $q->get('meta_query');
            if (!is_array($meta_query)) {
                $meta_query = array();
            }
            
            $min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
            $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : PHP_INT_MAX;
            
            $meta_query[] = array(
                'key'     => '_price',
                'value'   => array($min_price, $max_price),
                'type'    => 'NUMERIC',
                'compare' => 'BETWEEN',
            );
            
            $q->set('meta_query', $meta_query);
        }
    }
    
    return $q;
}
add_filter('woocommerce_product_query', 'apply_url_filters_to_product_query', 10, 2);