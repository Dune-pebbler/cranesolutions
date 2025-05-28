<?php
/**
 * Template name: Template FAQ
 */
$args = [
    'post_type' => 'faqs',
    'posts_per_page' => -1, 
];
$query = new WP_Query($args);
get_header(); 
?>

<?= do_shortcode("[banner classes='no-image' title='".get_the_title()."' image-id='".get_post_thumbnail_id()."']"); ?>

<section class="page-content">
    <div class="backdrop">
        <div class="top right">
            <?= get_svg('images/arrows-up-backdrop.svg'); ?>
        </div>
    </div>

    <?php if( $query->have_posts() ): ?>
    <section class="faq-categories">
        <div class="container">
            <div class="row">
                <?php while( $query->have_posts() ): $query->the_post(); ?>
                <div class="col-12">
                    <div class="faq-category">
                        <h2><?php the_title(); ?> <span class='is-toggle'><img src="<?= get_template_directory_uri(); ?>/images/arrow-right.svg" alt=""></span></h2>
                        <ul>
                            <li>
                                <?php the_content(); ?>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</section>
<?php get_template_part('template-parts/our', 'partners'); ?>
<?php get_footer(); ?>