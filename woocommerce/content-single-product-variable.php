<?php

/**
 * The template for displaying variable product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product-variable.php.
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

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form(); // WPCS: XSS ok.
    return;
}
?>
<?php
$args = array(
    'post_type'     => 'product_variation',
    'post_status'   => 'publish',
    'numberposts'   => -1,
    'orderby'       => 'menu_order',
    'order'         => 'asc',
    'post_parent'   => $product->get_id()
);

$variations = get_posts($args);
?>


<div class="product-overview">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <?php
                if (function_exists('yoast_breadcrumb')) {
                    yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
                }
                ?>
            </div>
            <!-- Product Images Column -->
            <div class="col-12 col-md-6">
                <div class="product-images">
                    <!-- Main Image -->
                    <div class="main-image">
                        <?php if (! function_exists('wc_get_gallery_image_html')) {
                            return;
                        }

                        global $product;

                        $columns           = apply_filters('woocommerce_product_thumbnails_columns', 4);
                        $post_thumbnail_id = $product->get_image_id();
                        $wrapper_classes   = apply_filters(
                            'woocommerce_single_product_image_gallery_classes',
                            array(
                                'woocommerce-product-gallery',
                                'woocommerce-product-gallery--' . ($product->get_image_id() ? 'with-images' : 'without-images'),
                                'woocommerce-product-gallery--columns-' . absint($columns),
                                'images',
                            )
                        );
                        $attachment_ids = $product->get_gallery_image_ids();
                        ?>
                        <div class="product-image-slider owl-carousel product-images">
                            <div class="slide">
                                <?php if ($product->get_image_id()): ?>
                                    <a href="<?= wp_get_attachment_url($post_thumbnail_id); ?>" data-fancybox="image">
                                        <img src="<?= wp_get_attachment_url($post_thumbnail_id); ?>" alt="<?= get_the_title($post_thumbnail_id); ?>" loading="lazy" />
                                    </a>
                                <?php else: ?>
                                    <div class="woocommerce-product-gallery__image--placeholder">
                                        <?= sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src('woocommerce_single')), esc_html__('Awaiting product image', 'woocommerce')); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php
                            if ($attachment_ids && $product->get_image_id()):
                                foreach ($attachment_ids as $attachment_id):
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
                                <?php if ($product->get_image_id()): ?>
                                    <img src="<?= wp_get_attachment_url($post_thumbnail_id); ?>" alt="<?= get_the_title($post_thumbnail_id); ?>" loading="lazy" />
                                <?php else: ?>
                                    <div class="woocommerce-product-gallery__image--placeholder">
                                        <?= sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src('woocommerce_single')), esc_html__('Awaiting product image', 'woocommerce')); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php
                            if ($attachment_ids && $product->get_image_id()):
                                foreach ($attachment_ids as $attachment_id):
                            ?>
                                    <div class="slide">
                                        <img src="<?= wp_get_attachment_url($attachment_id); ?>" alt="<?= get_the_title($attachment_id); ?>" loading="lazy" />
                                    </div>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </div>
                    </div>
                    <!-- Product Gallery -->

                </div>
            </div>

            <!-- Product Info Column -->
            <div class="col-12 col-md-6">
                <div class="product-info">
                    <h1 class="product-title"><?php the_title(); ?></h1>
                    <div class="product-category">
                        <?php
                        $categories = get_the_terms($product->get_id(), 'product_cat');
                        if ($categories && !is_wp_error($categories)) {
                            $category_names = array();
                            foreach ($categories as $category) {
                                $category_names[] = $category->name;
                            }
                            echo implode(', ', $category_names);
                        }
                        ?>
                    </div>
                    <div class="short-description">
                        <p class="about-product"><strong>Over dit product</strong></p>
                        <?php echo apply_filters('the_excerpt', get_the_excerpt()); ?>
                    </div>

                    <div class="button-container">
                        <a href="#var-table" class="btn-round">Bekijk alle varianten</a>
                        <a href="#order-form" class="btn-round-outline">Advies aanvragen</a>
                    </div>
                    <!-- Downloads Section -->
                    <?php
                    $has_downloads = $product->get_downloads();
                    $documents = wc_get_product_documents($product->get_id());
                    $has_documents = !empty($documents) && is_array($documents);

                    if ($has_downloads || $has_documents) :
                    ?>
                        <div class="downloads">
                            <p class="mb-2"><strong>Downloads</strong></p>
                            <ul>
                                <?php if ($has_downloads) : ?>
                                    <?php foreach ($product->get_downloads() as $download) : ?>
                                        <li>
                                            <a href="<?php echo esc_url($download['file']); ?>">
                                                <?php echo esc_html($download['name']); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                                <?php if ($has_documents) : ?>
                                    <?php foreach ($documents as $document) : ?>
                                        <?php
                                        $file_type = pathinfo($document['url'], PATHINFO_EXTENSION);
                                        $file_type = strtoupper($file_type);
                                        ?>
                                        <li>
                                            <a href="<?php echo esc_url($document['url']); ?>" target="_blank">
                                                <?php echo esc_html($document['title']); ?>
                                                <?php if ($file_type) : ?>
                                                    <span class="document-type">(<?php echo esc_html($file_type); ?>)</span>
                                                <?php endif; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="quicklinks">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="links-container">
                    <a href="#var-table">Alle varianten</a>
                    <a href="#product-description">Product informatie</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="product-variations" id="var-table">

    <?php if (!empty($variations)) :
        $first_variation = $variations[0];
        $specs = $first_variation->post_excerpt;


        $attribute_names = array();
        foreach (explode(', ', $specs) as $spec) {
            $parts = explode(': ', $spec);
            if (count($parts) == 2) {
                $attribute_names[$parts[0]] = $parts[0];
            }
        }
    ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h3 class="product_variations__title">Alle varianten</h3>
                    <div class="variation-selection-section">

                        <?php
                        // Custom variation dropdown implementation - only the selectors
                        global $product;

                        if ($product->is_type('variable')) {
                            $variation_attributes = $product->get_variation_attributes();
                            $default_attributes = $product->get_default_attributes();

                            // Start form but we won't process it - use div instead of table
                            echo '<div class="variations-only">';
                            echo '<div class="variation-selectors">';

                            foreach ($variation_attributes as $attribute_name => $options) {
                                // Get the display label - ensure this matches the table column headers
                                $attribute_label = wc_attribute_label($attribute_name);

                                echo '<div class="variation-selector-item">';
                                // Display attribute name
                                echo '<div class="selector-label">';
                                echo '<label for="' . esc_attr(sanitize_title($attribute_name)) . '">' . $attribute_label . '</label>';
                                echo '</div>';

                                // Display attribute options dropdown
                                echo '<div class="selector-value">';

                                $selected = isset($default_attributes[sanitize_title($attribute_name)]) ? $default_attributes[sanitize_title($attribute_name)] : '';

                                // Display dropdown with placeholder
                                wc_dropdown_variation_attribute_options(array(
                                    'options'       => $options,
                                    'attribute'     => sanitize_title($attribute_name),
                                    'product'       => $product,
                                    'selected'      => $selected,
                                    'show_option_none' => sprintf(__('Kies een optie', 'woocommerce'), $attribute_label)
                                ));

                                echo '</div>';

                                echo '</div>';
                            }
                            echo '<button type="button" class="btn reset-btn reset-filters-btn">Wis filters</button>';

                            echo '</div>'; // Close variation-selectors

                            // Add button to reset filters

                            // Show variation description without quantity/add to cart
                            echo '<div class="woocommerce-variation single_variation"></div>';

                            echo '</div>'; // Close variations-only
                        }
                        ?>

                    </div>
                    <div class="variations-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Artikelnr.</th>
                                    <?php foreach ($attribute_names as $name) : ?>
                                        <th><?php echo $name; ?></th>
                                    <?php endforeach; ?>
                                    <th>Toevoegen aan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($variations as $variation) :
                                    $specs = $variation->post_excerpt;
                                    $variation_id = $variation->ID;

                                    // Get the variation's SKU
                                    $variation_obj = wc_get_product($variation_id);
                                    $variation_sku = $variation_obj->get_sku();

                                    $specs_array = array();
                                    foreach (explode(', ', $specs) as $spec) {
                                        $parts = explode(': ', $spec);
                                        if (count($parts) == 2) {
                                            $specs_array[$parts[0]] = $parts[1];
                                        }
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo !empty($variation_sku) ? $variation_sku : '-'; ?></td>
                                        <?php foreach ($attribute_names as $name) : ?>
                                            <td><?php echo isset($specs_array[$name]) ? $specs_array[$name] : ''; ?></td>
                                        <?php endforeach; ?>
                                        <td>
                                            <div class="add-to-cart-actions">
                                                <div class="quantity-control">
                                                    <button class="quantity-button minus">−</button>
                                                    <input type="number" min="1" value="1" class="quantity-input" id="quantity-<?php echo $variation_id; ?>">
                                                    <button class="quantity-button plus">+</button>

                                                </div>

                                                <a href="<?php echo esc_url(add_query_arg(array(
                                                                'add-to-cart' => $product->get_id(),
                                                                'variation_id' => $variation_id,
                                                                'quantity' => 1
                                                            ), get_permalink())); ?>"
                                                    data-variation-id="<?php echo $variation_id; ?>"
                                                    class="add-to-cart-button button">
                                                    <img src="/images/arrow-right">
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="order-form" id="order-form">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-6">
                <h2>Wil je informatie over <?php echo get_the_title(); ?>?</h2>
                <?php echo do_shortcode('[gravityform id="2" title="false"]'); ?>
            </div>
        </div>
    </div>
</div>

<div class="related-products">
    <?php $product_categories = implode(',', $product->get_category_ids()); ?>
    <?= do_shortcode("[related-posts title='Gerelateerde producten' subtitle='' post_type='product' per-row='3' product_categories='$product_categories']"); ?>
    <?= do_shortcode("[related-posts title='Gerelateerde projecten' subtitle='' post_type='projects' per-row='3' product_categories='$product_categories']"); ?>
</div>

<section class="page-content">
    <div class="backdrop">
        <?= get_svg("images/page-content-backdrop.svg"); ?>
    </div>
    <section class="text-field" id="product-description">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-8 offset-lg-2">
                    <div class="text-container description-table">
                        <h1 class="page-content__title">Product informatie</h1>
                        <div class="brands">
                            <?php
                            $brands = get_the_terms($product->get_id(), 'product_brand');
                            if ($brands && !is_wp_error($brands)) : ?>
                                <div class="product-brands">
                                    <?php foreach ($brands as $brand) :
                                        $brand_image_id = get_term_meta($brand->term_id, 'thumbnail_id', true);
                                        $brand_description = $brand->description; 
                                    ?>
                                        <div class="brand-container">
                                            <?php if ($brand_image_id) :
                                                $image = wp_get_attachment_image($brand_image_id, 'full', false, ['class' => 'brand-image']);
                                                if ($image) : ?>
                                                    <div class="brand-image-container">
                                                        <?= $image; ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <div class="brand-item brand-text">
                                                    <a href="<?= get_term_link($brand); ?>"><?= esc_html($brand->name); ?></a>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (!empty($brand_description)) : ?>
                                                <div class="brand-description">
                                                    <?= wpautop($brand_description); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>

<?php get_template_part('template-parts/our', 'partners'); ?>

<?php do_action('woocommerce_after_single_product'); ?>