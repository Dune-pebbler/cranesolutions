<?php
/*
 * Template name: Template partners
 */
$partner_terms = get_terms([
    'taxonomy' => 'partners',
    'hide_empty' => false,
]);
shuffle($partner_terms);

get_header('header-1'); ?>

<section class="banner no-image">
    <div class="background-image"></div>
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
                <div class="col-12 col-lg-8 offset-lg-2">
                    <h1><?php the_title(); ?></h1>
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="news is-news-archive">
    <div class="container">
        <div class="titel-container">
            <h2> <?= __('Onze partners'); ?> </h2>
            <!-- <form method="GET">
                <input type="text" name="search" value="<?= @$_GET['search']; ?>" placeholder="zoeken in nieuws">
            </form> -->
        </div>
        <?php if( !empty( $partner_terms ) ): ?>
        <div class="row">
            <?php foreach( $partner_terms as $term ): ?>
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="<?= get_term_link($term->term_id); ?>" class="news-item is-partner-item">
                        <div class="image">
                            <?php if( $image_url = get_term_meta($term->term_id, 'seoninja_img', true) ): ?>
                                <?=  wp_get_attachment_image($image_url, 'full'); ?>
                            <?php endif; ?>
                        </div>
    
                        <div class="content">
                            <h3><?= $term->name; ?></h3>
                            
                            <div class="footer">
                                <button><span><?= get_svg("images/arrow-right.svg"); ?></span> <?= __('Lees meer', THEME_TD); ?></button>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>


<?php get_footer(); ?>