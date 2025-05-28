<?php
$queried_object_id = get_queried_object_id();

defined('ABSPATH') || exit;

get_header('shop');
/**
 * Display category cards for top-level WooCommerce product categories
 */

$categories = get_top_level_product_categories();

if (empty($categories)) {
    echo '<p>No product categories found.</p>';
    return;
}

echo '<div class="category-cards-container">';
echo '<div class="row">';

foreach ($categories as $category) {
    // Get category thumbnail
    $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
    $image = wp_get_attachment_image_src($thumbnail_id, 'medium');
    $image_url = $image ? $image[0] : wc_placeholder_img_src('medium');

    // Get category link
    $category_link = get_term_link($category, 'product_cat');

    // Output card HTML
?>
    <div class="col-12 col-md-4 col-lg-3 mb-4">
        <div class="category-card">
            <a href="<?php echo esc_url($category_link); ?>">
                <div class="category-image">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($category->name); ?>">
                </div>
                <div class="category-info">
                    <h3><?php echo esc_html($category->name); ?></h3>
                    <?php if (!empty($category->description)): ?>
                        <div class="category-description">
                            <?php echo wp_kses_post(wp_trim_words($category->description, 10)); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </a>
        </div>
    </div>
<?php
}

echo '</div>'; // End row
echo '</div>'; // End container
get_footer('shop'); ?>