<?php
defined("ABSPATH") || die("-1");

# DEFINES
define('THEME_PATH', get_template_directory());
define('THEME_URL', get_template_directory_uri());
define('THEME_TD', sanitize_title(get_bloginfo("title")));


# REQUIRES
// include("blocks/init.php");
require_once("capacity-taxonomy.php");
require_once("download-metabox.php");
require_once("theme-options/init.php");
require_once("shortcodes/shortcodes.php");
require_once("metaboxes/metaboxes.php");
require_once("category-variation-manager.php");
require_once("product-filters.php");

# ACTIONS
add_action('admin_enqueue_scripts', 'ds_admin_theme_style');
add_action('login_enqueue_scripts', 'ds_admin_theme_style');
add_action('wp_enqueue_scripts', 'theme_enqueue_scripts');
add_action('wp_enqueue_scripts', 'theme_enqueue_styles');
add_action('enqueue_block_editor_assets', 'theme_enqueue_gluten_styles');
add_action('enqueue_block_editor_assets', 'theme_enqueue_gluten_scripts');

add_action('wp_ajax_filter_projects', 'filter_projects');
add_action('wp_ajax_nopriv_filter_projects', 'filter_projects');

add_action('gform_after_submission', 'leeg_winkelwagen_na_formulier', 10, 2);
add_action('init', function() {
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=UTF-8');
    }
});


# FILTERS
add_filter('wp_page_menu_args', 'home_page_menu_args');
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10);
add_filter('image_send_to_editor', 'remove_thumbnail_dimensions', 10);
add_filter('the_content', 'remove_thumbnail_dimensions', 10);
add_filter('the_content', 'add_image_responsive_class');
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');
add_filter('excerpt_length', 'custom_excerpt_length');

add_filter( 'gform_pre_render_5', 'populate_cart_in_gravity_form' );
add_filter( 'gform_pre_validation_5', 'populate_cart_in_gravity_form' );
add_filter( 'gform_pre_submission_filter_5', 'populate_cart_in_gravity_form' );
add_filter( 'gform_admin_pre_render_5', 'populate_cart_in_gravity_form' );

# THEME SUPPORTS
add_theme_support('menus');
add_theme_support('post-thumbnails'); // array for post-thumbnail support on certain post-types.
add_theme_support('woocommerce'); // array for post-thumbnail support on certain post-types.

# IMAGE SIZES
add_image_size('default-thumbnail', 128, 128, true); // true: hard crop or empty if soft crop

set_post_thumbnail_size(128, 128, true);

# FUNCTIONS
register_nav_menus([
  'top-nav' => __('Top Menu', THEME_TD),
  'primary' => __('Primary Menu', THEME_TD),
  'footer-1' => __('Footer 1 Menu', THEME_TD),
  'footer-2' => __('Footer 2 Menu', THEME_TD),
  'footer-3' => __('Footer 3 Menu', THEME_TD),
  'footer-4' => __('Footer 4 Menu', THEME_TD),
  'footer-5' => __('Footer 5 Menu', THEME_TD),
]);
function custom_excerpt_length($length)
{
  return 20;
}
function theme_enqueue_gluten_styles()
{
  wp_enqueue_style('bootstrap.min.js', get_template_directory_uri() . "/stylesheets/bootstrap.min.css");
  wp_enqueue_script('admin.shortcode.extensions.js', get_template_directory_uri() . "/js/admin.shortcode.extensions.js", ['jquery'], filemtime(get_template_directory() . "/js/main.js"), true);
}

function theme_enqueue_styles()
{
  wp_enqueue_style('fontawesome.all.min.js', get_template_directory_uri() . "/assets/fontawesome/css/all.min.css");
  wp_enqueue_style('theme-jquery.fancybox.min.css', get_template_directory_uri() . "/assets/fancybox/jquery.fancybox.min.css");
  wp_enqueue_style('owl.carousel.min.css', get_template_directory_uri() . "/assets/owlcarousel/owl.carousel.min.css");
  wp_enqueue_style('owl.carousel.default.theme.min.css', get_template_directory_uri() . "/assets/owlcarousel/owl.theme.default.min.css");
  wp_enqueue_style('bootstrap.min.js', get_template_directory_uri() . "/stylesheets/bootstrap.min.css");
  wp_enqueue_style('theme.font.css', "https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap");
  wp_enqueue_style('theme.style.css', get_template_directory_uri() . "/stylesheets/style.css", [],  filemtime(get_template_directory() . "/stylesheets/style.css"));
}

function theme_enqueue_scripts()
{
  wp_enqueue_script('owl.carousel.min.js', get_template_directory_uri() . "/assets/owlcarousel/owl.carousel.min.js", ['jquery'],  '1.0.0', true);
  wp_enqueue_script('jquery.fancybox.min.js', get_template_directory_uri() . "/assets/fancybox/jquery.fancybox.min.js", ['jquery'],  '1.0.0', true);
  wp_enqueue_script('in-view.min.js', get_template_directory_uri() . "/js/in-view.js", ['jquery'], '1.0.0', true);
  wp_enqueue_script('theme.main.js', get_template_directory_uri() . "/js/main.js", ['jquery'], filemtime(get_template_directory() . "/js/main.js"), true);
  wp_enqueue_script('dropdown-filter.js', get_template_directory_uri() . "/js/dropdown-filter.js", ['jquery'], filemtime(get_template_directory() . "/js/dropdown-filter.js"), true);

  wp_localize_script('theme.main.js', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}

function get_meta($name, $id = false, $is_single_meta = true)
{
  $id = !$id ? get_the_ID() : $id;

  return get_post_meta($id, $name, $is_single_meta);
}

function leeg_winkelwagen_na_formulier($entry, $form) {
    // Form ID 5 is het juiste formulier
    if ($form['id'] == 5 && class_exists('WC_Cart') && function_exists('WC')) {
        if (WC()->cart) {
            WC()->cart->empty_cart();
        }
    }
}

function populate_cart_in_gravity_form( $form ) {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return $form;
    }

    foreach ( $form['fields'] as &$field ) {
        if ( strpos( $field->cssClass, 'populate-cart' ) === false ) {
            continue;
        }

        if ( is_admin() ) {
            $field->defaultValue = 'Cart contents will be populated here.';
            continue;
        }

        $cart_items = WC()->cart->get_cart();

        if ( empty( $cart_items ) ) {
            $field->defaultValue = 'The cart is empty.';
            continue;
        }

        $cart_contents = "Products in Cart:\n\n";

        foreach ( $cart_items as $cart_item_key => $cart_item ) {
            $product = $cart_item['data'];
            $product_name = $product->get_name();
            $quantity = $cart_item['quantity'];
            $price = wc_price( $product->get_price() );
            $line_total = wc_price( $cart_item['line_total'] );

            $cart_contents .= "Product: $product_name\n";
            $cart_contents .= "Quantity: $quantity\n";
            $cart_contents .= "Price: $price\n";
            $cart_contents .= "Total: $line_total\n\n";
        }

        $cart_contents .= "----------------------------------\n";
        $cart_contents .= "Cart Total: " . WC()->cart->get_cart_total() . "\n";

        $field->defaultValue = $cart_contents;
    }

    return $form;
}


function theme_enqueue_gluten_scripts()
{
  wp_enqueue_script(
    'custom-block-script', // Unieke handle voor de script
    get_template_directory_uri() . '/js/admin.global.js', // Pad naar je aangepaste blokstijl-bestand
    array('wp-blocks', 'wp-dom-ready', 'wp-edit-post'), // Lijst van afhankelijkheden
    '1.0', // Versienummer
    true // Laad het script in de footer
  );
}

function home_page_menu_args($args)
{
  $args['show_home'] = true;
  return $args;
}

function remove_thumbnail_dimensions($html)
{
  $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
  return $html;
}

function remove_width_attribute($html)
{
  $html = preg_replace('/(width|height)="\d*"\s/', "", $html);
  return $html;
}

function add_image_responsive_class($content)
{
  global $post;
  $pattern = "/<img(.*?)class=\"(.*?)\"(.*?)>/i";
  $replacement = '<img$1class="$2 img-responsive"$3>';
  $content = preg_replace($pattern, $replacement, $content);
  return $content;
}

function ds_admin_theme_style()
{
  if (!current_user_can('manage_options')) {
    echo '<style>.update-nag, .updated, .error, .is-dismissible { display: none; }</style>';
  }
}

// Method 1: Filter.
function my_acf_google_map_api($api)
{
  $api['key'] = '';
  return $api;
}

function register_theme_metabox($name, $arguments = [])
{
  if (!isset($arguments['render-condition'])) $arguments['render-condition'] = function () {
    return true;
  };

  // add meta box 
  add_action(
    'add_meta_boxes',
    function () use ($name, $arguments) {
      if (!$arguments['render-condition']()) return;

      add_meta_box(
        sanitize_title($name), // Unieke ID voor de metabox
        $name, // Titel van de metabox
        isset($arguments['render-callback']) ? $arguments['render-callback'] : false, // Callback functie om de inhoud van de metabox weer te geven
        isset($arguments['post_type']) ? $arguments['post_type'] : 'post' // Scherm waarop de metabox moet verschijnen (bijv. 'post', 'page', 'custom_post_type')
      );
    }
  );

  add_action('save_post', function ($post_id) use ($arguments) {
    // default save function
    if (isset($_POST['theme-meta']))
      foreach ($_POST['theme-meta'] as $key => $value) {
        update_post_meta($post_id, $key, $value);
      }

    // we check if want to have a save callback
    if (!isset($arguments['save-callback'])) return;

    // we need to deny the function if it doesn't exist.
    if (is_string($arguments['save-callback'])) if (!function_exists($arguments['save-callback'])) return;

    $arguments['save-callback']($post_id);
  });
}

function theme_pagination($query)
{
  global $wp_rewrite;
  $pagination = '';

  if ($query->max_num_pages <= 1) {
    return;
  }

  $pagination .= '<div class="pagination">';
  $base_url = get_pagenum_link(1) . '%_%';
  // $format = $wp_rewrite->using_permalinks() ? '&paged=%#%' : '?paged=%#%';
  $args = array(
    'base' => $base_url,
    'format' => '?paged=%#%',
    'current' => max(1, get_query_var('paged')),
    'total' => $query->max_num_pages,
    'prev_text' => __('&laquo; Previous'),
    'next_text' => __('Next &raquo;'),
  );
  $pagination .= paginate_links($args);
  $pagination .= '</div>';

  return $pagination;
}

function get_team_object_by_auteur($author_id = false)
{
  global $post;

  $author_id = !$author_id ? $post->post_author : $author_id;

  $query = new WP_Query([
    'post_type' => 'employees',
    'posts_per_page' => -1,
    'meta_query' => [
      [
        'key' => 'seoninja_employee_user',
        'value' => $author_id,
        'compare' => '=='
      ]
    ]
  ]);

  return get_post_type() == 'jobpostings' ? get_post(1447) : $query->post;
}

function get_project_query($custom_args = [])
{
  $args = [
    'post_type' => 'projects',
    'posts_per_page' => -1,
  ];

  $args = array_merge($args, $custom_args);

  if (isset($_GET['search']) && !empty($_GET['search'])) {
    $args['s'] = wp_kses_data($_GET['search']);
  }

  $query = new WP_Query($args);

  return $query;
}

function get_review_query($custom_args = [])
{
  $args = [
    'post_type' => 'reviews',
    'posts_per_page' => -1,
  ];

  $args = array_merge($args, $custom_args);

  $query = new WP_Query($args);

  return $query;
}

function get_taxonomy_archive_link($taxonomy)
{
  $tax = get_taxonomy($taxonomy);
  return get_bloginfo('url') . '/' . $tax->rewrite['slug'];
}

function theme_get_current_page_children($id = false)
{
  global $post;
  $args = [
    'post_type'      => 'page',
    'posts_per_page' => -1,
    'post_parent'    => !$id ? $post->ID : $id,
    'order'          => 'ASC',
    'orderby'        => 'menu_order',
    'post__not_in'   => [get_the_ID()]
  ];


  $query = new WP_Query($args);

  return $query;
}

function get_filterd_sector_projects_query($sector = '')
{
  $args = array(
    'post_type' => 'projects',
    'posts_per_page' => -1,
  );
  // If a sector is provided, add taxonomy query
  if (!empty($sector)) {
    $args['tax_query'] = array(
      array(
        'taxonomy' => 'sectors',
        'field'    => 'slug',
        'terms'    => $sector,
      ),
    );
  }
  return new WP_Query($args);
}
function filter_projects()
{
  $project_query = get_filterd_sector_projects_query($_POST['sector']);
  ob_start();
  while ($project_query->have_posts()) : $project_query->the_post();
    get_template_part('template-parts/project', 'item');
  endwhile;
  wp_reset_postdata();
  $response = ob_get_clean();
  echo $response;
  exit;
}

# Random code
// add editor the privilege to edit theme
// get the the role object
$role_object = get_role('editor');
// add $cap capability to this role object
$role_object->add_cap('edit_theme_options');

if (function_exists('acf_add_options_sub_page')) {
  acf_add_options_page();
  acf_add_options_sub_page('Footer');
  acf_add_options_sub_page('Header');
  acf_add_options_sub_page('Globale Opties');
  acf_add_options_sub_page('Socials');
  //     acf_add_options_sub_page( 'Side Menu' );
}
