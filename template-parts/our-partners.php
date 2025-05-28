<?php defined("ABSPATH") || die("-1"); ?>

<?php
$amount_of_partners = get_theme_option('theme_option_amount_of_partners');
$partner_terms = get_terms([
    'taxonomy' => 'partners',
    'hide_empty' => false,
]);
shuffle($partner_terms);

?>
<section class="our-partners">
    <div class="container">
        <h2><?= get_theme_option('theme_option_partner_titel') ?></h2>

        <div class="row">
            <div class="col-12 col-lg-10 offset-lg-1">
                <div class="partner-box owl-carousel partner-slider">
                    <?php foreach($partner_terms as $i => $term): $counter = $i+1;?>
                          <?php if( $image_url = wp_get_attachment_url(get_term_meta($term->term_id, 'seoninja_img', true))): ?>
                            <a href="<?= get_term_link($term->term_id); ?>" class="partner-logo">
                                <img src="<?= $image_url; ?>" alt="<?= $term->name; ?>"> 
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <div class="buttons">
                    <a href="<?= get_taxonomy_archive_link('partners'); ?>" class="btn">Onze partners</a>
                </div>
            </div>
        </div>
    </div>
</section>