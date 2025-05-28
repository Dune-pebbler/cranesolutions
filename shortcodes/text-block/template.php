<?php defined("ABSPATH") || die("-1"); ?>
<?php if( $shortcode_atts['mode'] == 'normal' ): ?>
 <section class="text-field">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-8 offset-lg-2">
                    <div class="text-container">
                        <?= do_shortcode($content); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php else: ?>
<section class="introduction">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8 offset-lg-2">
                <?= do_shortcode($content); ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>