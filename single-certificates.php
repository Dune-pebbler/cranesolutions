<?php 
$team_user = get_team_object_by_auteur(); 

get_header('header-1'); ?>

<section class="banner no-image">
    <div class="background-image"></div>
    <div class="breadcrumbs">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-8 offset-lg-2">
                    <?php if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs(); ?>
                </div>  
            </div>
        </div>
    </div>
    <div class="text-container">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-8 offset-lg-2">
                    <div class="wrapped">
                        <?php if( has_post_thumbnail() ): ?>
                        <div class="image">
                            <?=  get_the_post_thumbnail(get_the_ID(), 'full', []); ?>
                        </div>
                        <?php endif; ?>
                        <div class="titel">
                        
                            <div class="subtitle">
                                <p><?= __('Certificaat', THEME_TD); ?></p>
        
                                <?php if( $tags = get_the_tags() ): ?>
                                    <?php foreach($tags as $tag): ?>
                                        <div class="tag"><?= $tag->name; ?></div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                                    
                            <h1><?php the_title(); ?></h1>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</section>

<section class="page-content">
    <div class="backdrop">
        <?= get_svg("images/page-content-backdrop.svg"); ?>
    </div>

     <section class="text-field">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-8 offset-lg-2">
                    <div class="text-container">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    

    <div class="buttons">
        <a href="<?= get_post_type_archive_link(get_post_type()); ?>" class="btn is-back-btn">
            <?= __('Terug naar certificaten overzicht', THEME_TD); ?>
            <?= get_svg("images/arrow-right.svg"); ?>
        </a>
    </div>
</section>
<?php if( $user_object = get_team_object_by_auteur() ): ?>
<section class="contact-us">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-3 offset-lg-1">
                <div class="image">
                    <?php if( has_post_thumbnail($user_object->ID) ): ?>
                        <?=  get_the_post_thumbnail($user_object->ID, 'full', []); ?>
                    <?php else: ?>
                        <img src="https://secure.gravatar.com/avatar/6ac40747f1f2a118d21980a197c931a9?&d=mm&r=g" width="100%" loading="lazy" alt="<?= $user_object->post_title; ?> profiel foto">
                    <?php endif; ?>
                </div>
            </div>
            <div class="col12- col-lg-5 offset-lg-1">
                <div class="text-container">
                    <!-- <h2><?= __('Op zoek naar een speciale hijsoplossing?', THEME_TD); ?></h2> -->
                    <p><?= __('Neem contact op met', THEME_TD); ?> <a href="<?= get_the_permalink($user_object->ID); ?>"><?= $user_object->post_title; ?></a> </p>
                    <div class="buttons">
                        <?php if( $phone_number = get_meta('seoninja_employee_telephone', $user_object->ID) ): ?>
                            <a href="tel:<?= $phone_number; ?>" class="btn is-alternative"><?= str_replace( "+31", "0", $phone_number); ?> <?= get_svg("images/arrow-right.svg"); ?></a>
                        <?php endif; ?>

                        <!-- <?php if( $email = get_meta('seoninja_employee_email', get_the_ID()) ): ?>
                            <a href="mailto:<?= $email; ?>" class="btn is-alternative"><?= $email; ?> <?= get_svg("images/arrow-right.svg"); ?></a>
                        <?php endif; ?> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
<?= do_shortcode('[related-posts]'); ?>
<?php get_template_part('template-parts/our', 'partners'); ?>

<?php get_footer(); ?>
