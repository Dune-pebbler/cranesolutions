<?php
$queried_object_id = get_queried_object_id();

defined('ABSPATH') || exit;

get_header('shop');
?>

<?php if (!is_shop()):  ?>
	<section class="banner no-image">
		<div class="background-image">
			<!-- <img src="<?= get_template_directory_uri(); ?>/images/20201209_121227.jpg" alt=""> -->
		</div>
		<div class="breadcrumbs">
			<div class="container">
				<div class="row">
					<div class="col-12 col-lg-8 offset-lg-2">
						<?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="text-container">
			<div class="container">
				<div class="row">
					<div class="col-12 col-lg-6 offset-lg-2">
						<?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
							<h1><?php woocommerce_page_title(); ?></h1>
						<?php endif; ?>
						<?php do_action('woocommerce_archive_description'); ?>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if (is_shop()): ?>
	<div class="container">
		<div class="row">
			<div class="col-12">
				<?php
				if (function_exists('yoast_breadcrumb')) {
					yoast_breadcrumb('<p id="breadcrumbs">', '</p>');
				}
				?>
			</div>
		</div>
		<div class="row products-load-row">
			<span class="custom-loader"></span>

			<div class="col-12 col-lg-3 hide-when-loading">
				<?php
				$shop_page_id = wc_get_page_id('shop');
				$shop_page = get_post($shop_page_id);
				?>
				<section class="product-filters filter-aside">

					<!-- tax: product_cat -->
					<div class="filter-group">
						<div class="custom-radio">
							<input type="radio" name="category" id="cat-all" value="" checked>
							<label for="cat-all">
								<h3>Producten</h3>
							</label>
							<?php
							$categories = get_product_categories_for_filter();
							foreach ($categories as $category) {
								echo '<input type="radio" name="category" id="cat-' . esc_attr($category->term_id) . '" value="' . esc_attr($category->term_id) . '">';
								echo '<label for="cat-' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</label>';
							}
							?>
						</div>
					</div>



					<!-- tax: brands -->
					<div class="filter-group">
						<div class="custom-radio">
							<input type="radio" name="brand" id="brand-all" value="" checked class="remove-circle">
							<label for="brand-all" class="remove-circle">
								<h3>Merken</h3>
							</label>

							<?php
							$brands = get_product_brands_for_filter();
							foreach ($brands as $brand) {
								echo '<input type="radio" name="brand" id="brand-' . esc_attr($brand->term_id) . '" value="' . esc_attr($brand->term_id) . '">';
								echo '<label for="brand-' . esc_attr($brand->term_id) . '">' . esc_html($brand->name) . '</label>';
							}
							?>
						</div>
					</div>



					<div class="filter-group subcategory-filter-group">
						<div class="custom-radio" id="subcategory-container">
							<h3>Specialisaties</h3>

							<!-- <input type="radio" name="subcategory" id="subcat-all" value="" checked>
							<label for="subcat-all">Alle subcategorieën</label> -->
							<p class="no-subcategories-message">Geen Specialisaties gevonden</p>
						</div>
					</div>

					<!-- tax: capacity -->

					<div class="filter-group">
						<div class="custom-radio">
							<input type="radio" name="capacity" id="capacity-all" value="" checked class="remove-circle">
							<label for="capacity-all" class="remove-circle">
								<h3>Capaciteit</h3>
							</label>

							<?php
							$capacities = get_product_capacities_for_filter();
							foreach ($capacities as $capacity) {
								echo '<input type="radio" name="capacity" id="capacity-' . esc_attr($capacity->term_id) . '" value="' . esc_attr($capacity->term_id) . '">';
								echo '<label for="capacity-' . esc_attr($capacity->term_id) . '">' . esc_html($capacity->name) . '</label>';
							}
							?>
						</div>
					</div>


				</section>
			</div>

			<div class="col-12 col-lg-9 hide-when-loading">
			<?php endif; ?>

			<section class="product-listing">
				<?php if (is_shop()): ?>
					<div class="product-filters">
						<div id="filter-above-archive">
							<section class="shop-header">

								<h1 id="page-title"><?php echo get_the_title($shop_page_id); ?></h1>
								<?php echo apply_filters('the_content', $shop_page->post_content);  ?>

							</section>
							<div id="pills-container"></div>
							<div id="filter-button">
								Filter resultaten
							</div>
							<div class="filter-search-wrapper">
								<div class="filter-group">
									<input type="text" name="search" id="product-search" placeholder="Zoeken">
								</div>
								<div class="result-container">
									<div class="results">
										<?php
										global $wp_query;
										echo $wp_query->found_posts;
										?>
									</div>
									<span>Resultaten</span>
								</div>
								<div class="filter-group">
									<span>Sorteren op:</span>
									<select name="orderby" id="product-sort">
										<option value="date">Newest</option>
										<option value="title">Name: A to Z</option>
										<option value="title-desc">Name: Z to A</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
				<?php
				if (woocommerce_product_loop()) {
				?>
					<div class="product-archive">
						<div class="container">
							<div class="row">
							<?php
							if (wc_get_loop_prop('total')) {
								while (have_posts()) {
									the_post();
									do_action('woocommerce_shop_loop');
									wc_get_template_part('content', 'product');
								}
							}

							woocommerce_product_loop_end();
							do_action('woocommerce_after_shop_loop');
						} else {
							echo '<div class="product-archive">';
							echo '<div class="row">';
							echo '<p class="no-products">Geen producten gevonden</p>';
							echo '</div>';
							echo '</div>';
						}
							?>
							</div>
						</div>
					</div>
			</section>
			</div>
		</div>

		<?php if (is_shop()): ?>
	</div>
	</div>
<?php endif; ?>

<?php
global $product;
$product_categories = $product ? implode(',', $product->get_category_ids()) : '9999';
?>

<?= do_shortcode("[related-posts title='Gerelateerde projecten' subtitle='' post_type='projects' per-row='3' product_categories='$product_categories']"); ?>
<?= do_shortcode("[faq-items category_ids='$product_categories' ]"); ?>

<?php get_footer('shop'); ?>