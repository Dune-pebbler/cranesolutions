    <section class="video-placeholder">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-10 offset-lg-1">
                    <a href="<?= $shortcode_atts['video-url']; ?>" data-fancybox>
                        <?php if( $image_url = wp_get_attachment_url($shortcode_atts['image-id'])): ?>
                            <img src="<?= $image_url ?>" alt="" loading="lazy">
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </section>