<?php defined("ABSPATH") || die("-1"); ?>
<div class="col-12 col-md-6 col-xl-4">
    <a href="<?php the_permalink(); ?>" class="news-item">
        <div class="image" style> 
            <?php if( has_post_thumbnail() ): ?>
                <?php $isProduct = (get_post_type() === 'product'); ?>
                <?= get_the_post_thumbnail(get_the_ID(), 'full', $isProduct ? ['style' => 'object-fit:contain;'] : []); ?>
            <?php endif; ?>

            <?php if( $tags = get_the_category() ): ?>
                    <?php 
                    $tags = array_filter($tags, function($term) { return $term->term_id != 1; }); 
                    $tags = wp_list_pluck($tags, 'name');  
                    ?>
                    <div class="tag"><?= implode(', ', $tags); ?></div>
            <?php endif; ?>
        </div>

        <div class="content">
            <h3><?php the_title(); ?></h3>
            
            <?php the_excerpt(); ?>
            
            <div class="footer">
                <button aria-label="Lees meer"><span><?= get_svg("images/arrow-right.svg"); ?></span> <?= __('Lees meer', THEME_TD); ?></button>
                <span><?= date_i18n("j F Y", get_the_date("U")); ?></span>
            </div>
        </div>
    </a>
</div>