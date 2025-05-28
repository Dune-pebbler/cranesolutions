<?php
defined("ABSPATH") || die("-1");
?>
<section class="banner <?= $shortcode_atts['classes']; ?>">
    <?php if( $image = wp_get_attachment_image($shortcode_atts['image-id'], 'full', [])): ?>
    <div class="background-image">
       <?= $image; ?>
    </div>
    <?php endif; ?>
    
    <?php if( $content ): ?>
    <div class="text-container">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-6 <?= $shortcode_atts['alignment'] == 'smaller' ? 'offset-lg-2' : ''; ?>">
                   <?= $content; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>