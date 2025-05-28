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

<?php if ($product && $product->is_type('variable')) :
    $args = array(
        'post_type'     => 'product_variation',
        'post_status'   => 'publish',
        'numberposts'   => -1,
        'orderby'       => 'menu_order',
        'order'         => 'asc',
        'post_parent'   => $product->get_id()
    );

    $variations = get_posts($args);

    if (!empty($variations)) :
        // Get the first variation to extract all possible attributes
        $first_variation = $variations[0];
        $specs = $first_variation->post_excerpt;

        // Get all attribute names from the first variation
        $attribute_names = array();
        foreach (explode(', ', $specs) as $spec) {
            $parts = explode(': ', $spec);
            if (count($parts) == 2) {
                $attribute_names[$parts[0]] = $parts[0];
            }
        }
?>
        <div class="product-variations">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h3 class="product_variations__title">Alle varianten</h3>
                        <div class="variations-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <?php foreach ($attribute_names as $name) : ?>
                                            <th><?php echo $name; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($variations as $variation) :
                                        $specs = $variation->post_excerpt;

                                        $specs_array = array();
                                        foreach (explode(', ', $specs) as $spec) {
                                            $parts = explode(': ', $spec);
                                            if (count($parts) == 2) {
                                                $specs_array[$parts[0]] = $parts[1];
                                            }
                                        }
                                    ?>
                                        <tr>
                                            <td>#<?php echo $variation->ID; ?></td>
                                            <?php foreach ($attribute_names as $name) : ?>
                                                <td><?php echo isset($specs_array[$name]) ? $specs_array[$name] : ''; ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
<?php endif;
endif; ?>


<section class="page-content">
    <div class="backdrop">
        <?= get_svg("images/page-content-backdrop.svg"); ?>
    </div>
    <section class="text-field">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-8 offset-lg-2">
                    <div class="text-container">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</section>

<?php $product_categories = implode(',', $product->get_category_ids()); ?>
<?= do_shortcode("[related-posts title='Gerelateerde producten' subtitle='' post_type='product' per-row='3' product_categories='$product_categories']"); ?>
<?= do_shortcode("[related-posts title='Gerelateerde projecten' subtitle='' post_type='projects' per-row='3' product_categories='$product_categories']"); ?>
<?php get_template_part('template-parts/our', 'partners'); ?>

<?php do_action('woocommerce_after_single_product'); ?>
<style>
    .product-variations {
        margin: 2rem 0;
        overflow-x: auto;
    }

    .product-variation__title {
        color: black
    }

    .variations-table table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }

    .variations-table th {
        background-color: #2B4292;
        /* Dark blue header */
        color: white;
        padding: 12px 16px;
        text-align: left;
        font-weight: 500;
    }

    .variations-table td {
        padding: 12px 16px;
        text-align: left;
    }

    .variations-table tbody tr:nth-child(even) {
        background-color: #f8f8f8;
        /* Light gray for even rows */
    }

    .variations-table tbody tr:nth-child(odd) {
        background-color: white;
    }

    /* Quantity controls and add button */
    .quantity-control {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .quantity-input {
        width: 60px;
        padding: 4px 8px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .add-to-quote {
        background-color: #F5A524;
        /* Orange button */
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
    }

    .quantity-button {
        background: white;
        border: 1px solid #ddd;
        padding: 4px 8px;
        border-radius: 4px;
        cursor: pointer;
    }
</style>