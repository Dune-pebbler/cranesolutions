<?php
/**
 * Template name: Template Contact
 */
get_header();
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

    <?php if( $image_id = get_meta('content-block-image-id')): ?>
    <section class="impressions">
        <div class="container-fluid">
            <div class="image">
                <?=  wp_get_attachment_image($image_id, 'full', []); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</section>

<section class="text-field">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8 offset-lg-2">
                <div class="text-container">
                    <h2><?= __('Adres', THEME_TD); ?></h2>
                </div>
               <div class="row">
                <div class="col-12 col-lg-6">
                    <p>
                        <?php if($address = get_option('seoninja_company_address')): ?>
                            <?= $address; ?><br />
                        <?php endif; ?>
                        <?php if($zipcode = get_option('seoninja_company_postal_code')): ?>
                            <?= $zipcode; ?> <?= get_option("seoninja_company_city"); ?><br />
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-12 col-lg-6">
                    <?= get_meta('content-block-content'); ?>
                </div>
               </div>
            </div>
        </div>
    </div>
</section>
<?= do_shortcode("[featured-vacancies]"); ?>
<?php get_template_part("template-parts/our", 'partners'); ?>

<?php get_footer(); ?>