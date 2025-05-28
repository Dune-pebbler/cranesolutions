<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $product->get_image_id() ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);
$attachment_ids = $product->get_gallery_image_ids();
?>
<div class="product-image-slider owl-carousel product-images">
    <div class="slide">
        <?php if ( $product->get_image_id() ): ?>
            <a href="<?= wp_get_attachment_url($post_thumbnail_id); ?>" data-fancybox="image"> 
                <img src="<?= wp_get_attachment_url($post_thumbnail_id); ?>" alt="<?= get_the_title($post_thumbnail_id); ?>" loading="lazy" />
            </a>
        <?php else: ?>
            <div class="woocommerce-product-gallery__image--placeholder">
                <?= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) ); ?>
            </div>
        <?php endif; ?>
    </div>
   
    <?php
		if ( $attachment_ids && $product->get_image_id() ):
            foreach ( $attachment_ids as $attachment_id ):
                ?>
                <div class="slide">
                    <a href="<?= wp_get_attachment_url($attachment_id); ?>" data-fancybox="image"> 
                        <img src="<?= wp_get_attachment_url($attachment_id); ?>" alt="<?= get_the_title($attachment_id); ?>" loading="lazy" />
                    </a>
                </div>
                <?php
            endforeach;
        endif;
    ?>
</div>

<div class="product-thumbnail-slider owl-carousel">
        
<div class="slide">
        <?php if ( $product->get_image_id() ): ?>
        <img src="<?= wp_get_attachment_url($post_thumbnail_id); ?>" alt="<?= get_the_title($post_thumbnail_id); ?>" loading="lazy" />
        <?php else: ?>
            <div class="woocommerce-product-gallery__image--placeholder">
                <?= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) ); ?>
            </div>
        <?php endif; ?>
    </div>
   
    <?php
		if ( $attachment_ids && $product->get_image_id() ):
            foreach ( $attachment_ids as $attachment_id ):
                ?>
                <div class="slide">
                    <img src="<?= wp_get_attachment_url($attachment_id); ?>" alt="<?= get_the_title($attachment_id); ?>" loading="lazy" />
                </div>
                <?php
            endforeach;
        endif;
    ?>
</div>