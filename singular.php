<?php 
global $post;
$get_children_query = theme_get_current_page_children();

if( $post->post_parent > 0 ){
    $get_children_query = theme_get_current_page_children($post->post_parent);
}

get_header('header-1'); 
?>

<?= do_shortcode("[banner alignment='smaller' classes='no-image' title='".get_the_title()."' image-id='".get_post_thumbnail_id()."']"); ?>

<section class="page-content">
    <div class="backdrop">
        <div class="top right">
            <?= get_svg('images/arrows-up-backdrop.svg'); ?>
        </div>
    </div>
    <section class="text-field">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-8 offset-lg-2">
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </section>


    <?php if( $get_children_query->have_posts() ): ?>
    <section class="news is-news-archive">
        <div class="container">
            <!-- <div class="titel-container">
                <h2> <?= __('Bekijk onze vacatures', THEME_TD); ?> </h2>
            </div> -->
            <div class="row">
                <?php 
                while( $get_children_query->have_posts() ): $get_children_query->the_post(); 
                    get_template_part("template-parts/child", "item");
                endwhile; wp_reset_postdata(); 
                ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</section>
<?php get_footer(); ?>