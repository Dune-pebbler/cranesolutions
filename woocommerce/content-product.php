<?php

/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}

// Get product data
$regular_price = $product->is_type('variable') 
    ? $product->get_variation_regular_price() 
    : $product->get_regular_price();

$sale_price = $product->get_price();
$gallery_images = $product->get_gallery_image_ids();

// Handle product thumbnail
$thumbnail_id = get_post_thumbnail_id(get_the_ID());
$thumbnail_data = wp_get_attachment_image_src($thumbnail_id, 'full');

$svg_html = '';
if ($thumbnail_data && strtolower(pathinfo($thumbnail_data[0], PATHINFO_EXTENSION)) === 'svg') {
    $svg_content = '';
    
    $upload_dir = wp_upload_dir();
    $relative_path = str_replace($upload_dir['baseurl'], '', $thumbnail_data[0]);
    $local_file_path = $upload_dir['basedir'] . $relative_path;
    
    if (file_exists($local_file_path)) {
        $svg_content = file_get_contents($local_file_path);
    } else {
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'ignore_errors' => true
            ]
        ]);
        
        $svg_content = @file_get_contents($thumbnail_data[0], false, $context);
    }
    
    if ($svg_content && !empty(trim($svg_content))) {
        $svg_html = '<div class="icon">' . $svg_content . '</div>';
    } else {
        $svg_html = '<div class="icon">' . get_the_post_thumbnail(get_the_ID(), 'full', ['class' => 'custom-thumbnail-class']) . '</div>';
    }
} else {
    $svg_html = '<div class="icon">' . get_the_post_thumbnail(get_the_ID(), 'full', ['class' => 'custom-thumbnail-class']) . '</div>';
}

if (!isset($GLOBALS['is_shop_layout'])) {
    $GLOBALS['is_shop_layout'] = is_shop();
}

$product_column_class = $GLOBALS['is_shop_layout']
    ? 'col-12 col-sm-6 col-lg-4'  // 3 columns for shop
    : 'col-12 col-sm-6 col-lg-3'; // 4 columns for other pages
?>

<article
    id="product-<?php echo get_the_ID(); ?>"
    class="category product <?php echo esc_attr($product_column_class); ?>">
    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
        <?php echo $svg_html; ?>

        <p><?php the_title(); ?></p>

        <button> <span><?php echo get_svg('images/arrow-right.svg'); ?></span> Ontdek meer</button>
    </a>
</article>