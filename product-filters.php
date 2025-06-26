<?php

/**
 * Product Filtering Functions
 * * Contains all functionality related to product filtering on the shop page
 */


defined('ABSPATH') || exit;

add_action('wp_enqueue_scripts', 'enqueue_filter_scripts');
add_action('wp_ajax_filter_products', 'handle_product_filter');
add_action('wp_ajax_nopriv_filter_products', 'handle_product_filter');
add_action('wp_ajax_get_subcategories', 'get_subcategories_ajax');
add_action('wp_ajax_nopriv_get_subcategories', 'get_subcategories_ajax');
add_action('wp_ajax_get_filtered_brands', 'get_filtered_brands_ajax');
add_action('wp_ajax_nopriv_get_filtered_brands', 'get_filtered_brands_ajax');
add_action('wp_ajax_get_filtered_capacities', 'get_filtered_capacities_ajax');
add_action('wp_ajax_nopriv_get_filtered_capacities', 'get_filtered_capacities_ajax');
add_filter('woocommerce_product_query', 'apply_url_filters_to_product_query', 10, 2);


function enqueue_filter_scripts()
{
    if (is_shop() || is_product_category()) {
        wp_enqueue_script('product-filters', get_template_directory_uri() . '/js/product-filters.js', array('jquery'), filemtime(get_template_directory() . '/js/product-filters.js'), true);
        wp_localize_script('product-filters', 'filterAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('filter_products')
        ));
    }
}


function get_product_categories_for_filter()
{
    $args = array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'parent' => 0
    );

    $categories = get_terms($args);

    return array_filter($categories, function ($category) {
        $products = get_posts(array(
            'post_type' => 'product',
            'numberposts' => 1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $category->term_id,
                    'include_children' => false
                )
            ),
            'meta_query' => array(
                array(
                    'key' => '_stock_status',
                    'value' => 'instock',
                    'compare' => '='
                )
            )
        ));

        return !empty($products);
    });
}
function get_product_brands_for_filter()
{
    $args = array(
        'taxonomy' => 'product_brand',
        'hide_empty' => false,
        'parent' => 0
    );

    $selected_category = isset($_GET['category']) ? intval($_GET['category']) : 0;

    $brands = get_terms($args);

    if (!$selected_category) {
        return array_filter($brands, function ($brand) {
            $products = get_posts(array(
                'post_type' => 'product',
                'numberposts' => 1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_brand',
                        'field' => 'term_id',
                        'terms' => $brand->term_id
                    )
                )
            ));
            return !empty($products);
        });
    }

    return array_filter($brands, function ($brand) use ($selected_category) {
        $products = get_posts(array(
            'post_type' => 'product',
            'numberposts' => 1,
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $selected_category
                ),
                array(
                    'taxonomy' => 'product_brand',
                    'field' => 'term_id',
                    'terms' => $brand->term_id
                )
            )
        ));
        return !empty($products);
    });
}

function get_product_capacities_for_filter()
{
    $args = array(
        'taxonomy' => 'capacity',
        'hide_empty' => false,
        'parent' => 0
    );

    $selected_category = isset($_GET['category']) ? intval($_GET['category']) : 0;

    $capacities = get_terms($args);

    // Sort capacities numerically
    usort($capacities, function($a, $b) {
        return intval($a->name) - intval($b->name);
    });

    if (!$selected_category) {
        return array_filter($capacities, function ($capacity) {
            $products = get_posts(array(
                'post_type' => 'product',
                'numberposts' => 1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'capacity',
                        'field' => 'term_id',
                        'terms' => $capacity->term_id
                    )
                )
            ));
            return !empty($products);
        });
    }

    return array_filter($capacities, function ($capacity) use ($selected_category) {
        $products = get_posts(array(
            'post_type' => 'product',
            'numberposts' => 1,
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $selected_category
                ),
                array(
                    'taxonomy' => 'capacity',
                    'field' => 'term_id',
                    'terms' => $capacity->term_id
                )
            )
        ));
        return !empty($products);
    });
}

function handle_product_filter()
{
    // Globally track if we are on the shop page 
    $GLOBALS['is_shop_layout'] = isset($_POST['is_shop']) ? true : false;

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 12,
        'paged' => $_POST['page'] ?? 1,
        'meta_query' => array(
            array(
                'key' => '_stock_status',
                'value' => 'instock',
                'compare' => '='
            )
        )
    );

    switch ($_POST['orderby'] ?? 'date') {
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
            'taxonomy' => 'product_brand',
            'field' => 'id',
            'terms' => $_POST['brand']
        );
    }
    
    // Capacity filter
    if (!empty($_POST['capacity'])) {
        $tax_query[] = array(
            'taxonomy' => 'capacity',
            'field' => 'id',
            'terms' => $_POST['capacity']
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
        echo '<p class="no-products">Geen producten gevonden</p>';
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
function get_subcategories_ajax()
{
    $parent_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

    if ($parent_id <= 0) {
        wp_send_json_error(['message' => 'Invalid category ID']);
        return;
    }

    $subcategories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'parent' => $parent_id
    ]);

    $formatted_subcategories = [];
    foreach ($subcategories as $subcategory) {
        $products_in_subcat = get_posts(array(
            'post_type' => 'product',
            'numberposts' => 1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => $subcategory->term_id,
                    'include_children' => false
                )
            )
        ));
        if (!empty($products_in_subcat)) {
            $formatted_subcategories[] = [
                'term_id' => $subcategory->term_id,
                'name' => $subcategory->name,
                'slug' => $subcategory->slug,
                'count' => $subcategory->count
            ];
        }
    }

    wp_send_json_success([
        'subcategories' => $formatted_subcategories
    ]);
}
function get_filtered_brands_ajax()
{
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

    $brands = get_terms([
        'taxonomy' => 'product_brand',
        'hide_empty' => false,
        'parent' => 0
    ]);

    if ($category_id <= 0) {
        $filtered_brands = array_filter($brands, function ($brand) {
            $products = get_posts(array(
                'post_type' => 'product',
                'numberposts' => 1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_brand',
                        'field' => 'term_id',
                        'terms' => $brand->term_id
                    )
                )
            ));
            return !empty($products);
        });
    } else {
        $filtered_brands = array_filter($brands, function ($brand) use ($category_id) {
            $products = get_posts(array(
                'post_type' => 'product',
                'numberposts' => 1,
                'tax_query' => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $category_id
                    ),
                    array(
                        'taxonomy' => 'product_brand',
                        'field' => 'term_id',
                        'terms' => $brand->term_id
                    )
                ),
                'meta_query' => array(
                    array(
                        'key' => '_stock_status',
                        'value' => 'instock',
                        'compare' => '='
                    )
                )
            ));
            return !empty($products);
        });
    }

    $formatted_brands = array_map(function ($brand) {
        return [
            'term_id' => $brand->term_id,
            'name' => $brand->name,
            'slug' => $brand->slug,
            'count' => $brand->count
        ];
    }, array_values($filtered_brands));

    wp_send_json_success([
        'brands' => $formatted_brands
    ]);
}

function get_filtered_capacities_ajax()
{
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

    $capacities = get_terms([
        'taxonomy' => 'capacity',
        'hide_empty' => false,
        'parent' => 0
    ]);
    
    // Sort capacities numerically
    usort($capacities, function($a, $b) {
        return intval($a->name) - intval($b->name);
    });

    if ($category_id <= 0) {
        $filtered_capacities = array_filter($capacities, function ($capacity) {
            $products = get_posts(array(
                'post_type' => 'product',
                'numberposts' => 1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'capacity',
                        'field' => 'term_id',
                        'terms' => $capacity->term_id
                    )
                )
            ));
            return !empty($products);
        });
    } else {
        $filtered_capacities = array_filter($capacities, function ($capacity) use ($category_id) {
            $products = get_posts(array(
                'post_type' => 'product',
                'numberposts' => 1,
                'tax_query' => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $category_id
                    ),
                    array(
                        'taxonomy' => 'capacity',
                        'field' => 'term_id',
                        'terms' => $capacity->term_id
                    )
                ),
                'meta_query' => array(
                    array(
                        'key' => '_stock_status',
                        'value' => 'instock',
                        'compare' => '='
                    )
                )
            ));
            return !empty($products);
        });
    }

    $formatted_capacities = array_map(function ($capacity) {
        return [
            'term_id' => $capacity->term_id,
            'name' => $capacity->name,
            'slug' => $capacity->slug,
            'count' => $capacity->count
        ];
    }, array_values($filtered_capacities));

    wp_send_json_success([
        'capacities' => $formatted_capacities
    ]);
}

function apply_url_filters_to_product_query($q, $instance)
{
    // Only apply on main query for shop page
    if (!is_admin() && is_shop() && $q->is_main_query()) {

        $tax_query = $q->get('tax_query');
        if (!is_array($tax_query)) {
            $tax_query = array();
        }

        if (isset($_GET['category']) && !empty($_GET['category'])) {
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'id',
                'terms'    => sanitize_text_field($_GET['category']),
            );
        }
        if (isset($_GET['subcategory']) && !empty($_GET['subcategory'])) {
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'id',
                'terms'    => sanitize_text_field($_GET['subcategory']),
            );
        }

        if (isset($_GET['brand']) && !empty($_GET['brand'])) {
            $tax_query[] = array(
                'taxonomy' => 'product_brand',
                'field'    => 'id',
                'terms'    => sanitize_text_field($_GET['brand']),
            );
        }
        
        // Add capacity filter to URL handling
        if (isset($_GET['capacity']) && !empty($_GET['capacity'])) {
            $tax_query[] = array(
                'taxonomy' => 'capacity',
                'field'    => 'id',
                'terms'    => sanitize_text_field($_GET['capacity']),
            );
        }

        if (!empty($tax_query)) {
            if (count($tax_query) > 1) {
                $tax_query['relation'] = 'AND';
            }
            $q->set('tax_query', $tax_query);
        }

        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $q->set('s', sanitize_text_field($_GET['search']));
        }

        if (isset($_GET['orderby']) && !empty($_GET['orderby'])) {
            $orderby = sanitize_text_field($_GET['orderby']);

            switch ($orderby) {
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

        $meta_query = $q->get('meta_query');
        if (!is_array($meta_query)) {
            $meta_query = array();
        }

        $q->set('meta_query', $meta_query);
    }

    return $q;
}