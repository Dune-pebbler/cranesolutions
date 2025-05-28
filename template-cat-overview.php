<?php

/**
 * Template name: Template Product categorie overzicht
 */

defined('ABSPATH') || exit;

get_header('shop');

function get_top_level_product_categories()
{
    $args = [
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => 0,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ];

    $categories = get_terms($args);
    
    if (is_wp_error($categories)) {
        return [];
    }

    return array_values(array_filter($categories, function ($category) {
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
    }));
}
function get_product_brands()
{
    $args = [
        'taxonomy'   => 'product_brand',
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ];

    $brands = get_terms($args);
    
    if (is_wp_error($brands)) {
        return [];
    }

    return array_values(array_filter($brands, function ($brand) {
        $products = get_posts(array(
            'post_type' => 'product',
            'numberposts' => 1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_brand',
                    'field' => 'term_id',
                    'terms' => $brand->term_id,
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
    }));
}

$categories = get_top_level_product_categories();
$brands = get_product_brands();
$shop_page_url = get_permalink(wc_get_page_id('shop'));
?>
<div class="cat-overview">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-10 col-lg-8 mb-4">
                <h1 class="cat-overview__title"><?php echo get_the_title(); ?></h1>
                <div class="category-intro-text">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
        <?php if (!empty($categories)): ?>
            <div class="row">
                <?php foreach ($categories as $category):
                    $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                    $image = wp_get_attachment_image_src($thumbnail_id, 'large');
                    $image_url = $image ? $image[0] : wc_placeholder_img_src('large');
                    $category_link = home_url('/product-filter/?category=' . $category->term_id . '&orderby=date');
                ?>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <a href="<?php echo esc_url($category_link); ?>" class="card-link">
                            <div class="product-category-box">
                                <div class="category-image">
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($category->name); ?>">
                                </div>
                                <span class="category-button btn arrow">
                                    <?php echo esc_html($category->name); ?>
                                </span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12 text-center">
                    <p>Geen categorieën gevonden.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<div class="brand-overview ">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-10 col-lg-8 mb-4">
                <h1 class="cat-overview__title">Onze Merken</h1>
                <div class="category-intro-text">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
        <?php if (!empty($brands)): ?>
            <div class="row">
                <?php foreach ($brands as $brand):
                    // Get the brand thumbnail image
                    $thumbnail_id = get_term_meta($brand->term_id, 'thumbnail_id', true);
                    // If no thumbnail_id, check for custom field 'seoninja_img'
                    if (!$thumbnail_id) {
                        $custom_img_id = get_term_meta($brand->term_id, 'seoninja_img', true);
                        if ($custom_img_id) {
                            $thumbnail_id = $custom_img_id;
                        }
                    }
                    $image = wp_get_attachment_image_src($thumbnail_id, 'large');
                    $image_url = $image ? $image[0] : wc_placeholder_img_src('large');
                    $brand_link = home_url('/product-filter/?brand=' . $brand->term_id . '&orderby=date');
                ?>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <a href="<?php echo esc_url($brand_link); ?>" class="card-link">
                            <div class="product-category-box">
                                <div class="category-image">
                                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($brand->name); ?>">
                                </div>
                                <span class="category-button btn arrow">
                                    <?php echo esc_html($brand->name); ?>
                                </span>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php get_footer('shop'); ?>