<?php defined("ABSPATH") || die("-1"); ?>

<section class="working-at-preview" style='background-image: url("<?= wp_get_attachment_url(get_theme_option('theme_option_featured_vacancies_background')); ?>")'>
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-5 offset-lg-2">
                <div class="left-block">
                    <?= $content; ?>
                </div>
            </div>
            <div class="col-12 col-lg-3">
                <div class="right-block">
                    <h3><?= __('Uitgelichte vacatures', THEME_TD); ?></h3>

                    <?php if( $query->have_posts() ): ?>
                    <ul>
                        <?php while( $query->have_posts() ): $query->the_post(); ?>
                        <li>
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </li>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>