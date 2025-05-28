<?php defined("ABSPATH") || die("-1"); ?>
<div class="review-item">
    <blockquote>
        <p>“<?= wp_strip_all_tags(get_the_content()); ?>”</p>
        <cite>
            <?php if( has_post_thumbnail() ): ?>
                <img src="<?= the_post_thumbnail_url(get_the_ID(), 'full'); ?>" loading="lazy">
            <?php endif; ?>
            <p>
                <?php the_title(); ?>
                <?php if( $function_title = get_meta('functie') ): ?>
                    <span><?= $function_title; ?></span>
                <?php endif; ?>
            </p>
        </cite>
    </blockquote>
</div>