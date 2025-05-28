<?php defined("ABSPATH") || die("-1"); ?>
<?php if ($query->have_posts()) : ?>
    <section class="news">
        <div class="container">
            <div class="titel-container" style='justify-content: center; text-align: center'>
                <h2>
                    <?= $shortcode_atts['title']; ?>
                    <?php if ($shortcode_atts['subtitle']) : ?>
                        <small>
                            <?= $shortcode_atts['subtitle']; ?>
                        </small>
                    <?php endif; ?>
                </h2>
                <?php if ($shortcode_atts['archive-url']) : ?>
                    <a href="<?= $shortcode_atts['archive-url']; ?>" class="btn"><?= $shortcode_atts['archive-title']; ?> <?= get_svg('images/arrow-right.svg'); ?></a>
                <?php endif; ?>
            </div>

            <div class="row" style='justify-content: center'>
                <?php
                while ($query->have_posts()) : $query->the_post();
                    get_template_part("template-parts/news", "item");
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>
<?php endif; ?>