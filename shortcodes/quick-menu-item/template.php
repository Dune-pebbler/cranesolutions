<?php defined("ABSPATH") || die("-1"); ?>
<div class="col-12 col-lg-3">
    <a href="<?= $shortcode_atts['url']; ?>">
        <div class="icon">
            <img src="<?= $shortcode_atts['image-url']; ?>" alt="" loading="lazy">
        </div>

        <?php if( $shortcode_atts['title']): ?>
        <p><?= $shortcode_atts['title']; ?></p>
        <?php endif; ?>

        <button> <span><?= get_svg('images/arrow-right.svg'); ?></span> <?= $shortcode_atts['button']; ?></button>
    </a>
</div>