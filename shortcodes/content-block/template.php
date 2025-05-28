<?php defined("ABSPATH") || die("-1"); ?>

<section class="content-block">
    <!-- <div class="background-image">
        <img src="https://crane.dune-pebbler.nl/wp-content/themes/crane-theme/images/drawing.png">
    </div> -->

    <div class="container">
        <div class="row">
            <?php if( $shortcode_atts['image-position'] == 'left' ): ?>
            <div class="col-12 col-lg-4 offset-lg-2">
                <?php if( $image_url = wp_get_attachment_url($shortcode_atts['image-id'])): ?>
                    <div class="image">
                        <img src="<?= $image_url ?>" alt="" loading="lazy">
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-12 col-lg-4 offset-lg-1">
                <div class="text-container">
                   <?= do_shortcode($content); ?>
                </div>
            </div>
            <?php else: ?>
                <div class="col-12 col-lg-6">
                    <div class="text-container">
                        <?= do_shortcode($content); ?>
                    </div>
                </div>
                <div class="col-12 col-lg-5 offset-lg-1">
                    <?php if( $image_url = wp_get_attachment_url($shortcode_atts['image-id'])): ?>
                        <div class="image">
                            <img src="<?= $image_url ?>" alt="" loading="lazy">
                        </div>
                    <?php endif; ?>
                </div>

            <?php endif; ?>
        </div>
    </div>
</section> 