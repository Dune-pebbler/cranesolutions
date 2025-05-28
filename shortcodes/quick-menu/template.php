<?php defined("ABSPATH") || die("-1"); ?>
<section class="quick-actions">
    <div class="container">
        <?php if( $shortcode_atts['title'] ): ?>
            <h2><?= $shortcode_atts['title']; ?></h2>
        <?php endif; ?>

        <div class="row">
            <?= do_shortcode($content); ?>
        </div>
    </div>
</section>
