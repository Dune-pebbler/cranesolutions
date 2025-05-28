<?php
$team_user = get_team_object_by_auteur();

get_header(); ?>

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
                    <div class="subtitle">
                        <?php if (get_post_type() == 'post') : ?>
                            <p><?= __('Blog', THEME_TD); ?></p>
                        <?php else : ?>
                            <p><?= ucfirst(get_post_type()); ?></p>
                        <?php endif; ?>

                        <?php if ($tags = get_the_category()) : ?>
                            <?php
                            $tags = array_filter($tags, function ($term) {
                                return $term->term_id != 1;
                            });
                            $tag_ids = wp_list_pluck($tags, 'term_id');
                            $tag_links = array_map(function ($tag_id) {
                                return get_term_link($tag_id);
                            }, $tag_ids);
                            ?>
                            <?php foreach ($tags as $index => $tag) : ?>
                                <a href="<?= $tag_links[$index] ?>" class="tag"><?= $tag->name; ?></a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <h1><?php the_title(); ?></h1>

                    <?php if ($team_user) : ?>
                        <!-- <div class="author"> 
                        <p>
                            <?= __('Auteur', THEME_TD); ?>
                            <a href="<?= get_the_permalink($team_user->ID); ?>" class="author-information">
                                <?php if (has_post_thumbnail($team_user->ID)) : ?>
                                    <img src="<?= get_the_post_thumbnail_url($team_user->ID, 'full') ?>" width="100%" alt="<?= get_the_title($team_user->ID); ?> profiel foto"  loading="lazy">
                                <?php else : ?>
                                    <img src="https://secure.gravatar.com/avatar/6ac40747f1f2a118d21980a197c931a9?&d=mm&r=g" width="100%" loading="lazy" alt="<?php the_title(); ?> profiel foto">
                                <?php endif; ?>
                                <?php the_author(); ?>
                            </a>
                        </p>
                    </div> -->
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="page-content">
    <div class="backdrop">
        <?= get_svg("images/page-content-backdrop.svg"); ?>
    </div>
    <?php if (has_post_thumbnail()) : ?>
        <section class="featured-image">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-10 offset-lg-1">
                        <div class="image">
                            <?= get_the_post_thumbnail(get_the_ID(), 'full', []); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

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
            <?= sprintf(__('Terug naar %soverzicht', THEME_TD), get_post_type() == 'post' ? 'nieuws' : get_post_type()); ?>
            <?= get_svg("images/arrow-right.svg"); ?>
        </a>
    </div>
</section>
<?php if ($user_object = get_team_object_by_auteur()) : ?>
    <section class="contact-us">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-3 offset-lg-1">
                    <div class="image">
                        <?php if (has_post_thumbnail($user_object->ID)) : ?>
                            <?= get_the_post_thumbnail($user_object->ID, 'full', []); ?>
                        <?php else : ?>
                            <img src="https://secure.gravatar.com/avatar/6ac40747f1f2a118d21980a197c931a9?&d=mm&r=g" width="100%" loading="lazy" alt="<?= $user_object->post_title; ?> profiel foto">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col12- col-lg-5 offset-lg-1">
                    <div class="text-container">
                        <h2><?= __('Heb je vragen naar aanleiding van dit artikel?', THEME_TD); ?></h2>
                        <p><?= __('Neem contact op met', THEME_TD); ?> <a href="<?= get_the_permalink($user_object->ID); ?>"><?= $user_object->post_title; ?></a> </p>
                        <div class="buttons">
                            <?php if ($phone_number = get_meta('seoninja_employee_telephone', $user_object->ID)) : ?>
                                <a href="tel:<?= $phone_number; ?>" class="btn is-alternative"><?= str_replace("+31", "0", $phone_number); ?> <?= get_svg("images/arrow-right.svg"); ?></a>
                            <?php elseif ($company_number = get_option('seoninja_company_telephone_number')) : ?>
                                <a href="tel:<?= $company_number; ?>" class="btn is-alternative"><?= str_replace("+31", "0", $company_number); ?> <?= get_svg("images/arrow-right.svg"); ?></a>
                            <?php endif; ?>
                            <?php if ($contact_page_url = get_permalink(get_page_by_path('contact'))) : ?>
                                <a href="<?= esc_url($contact_page_url); ?>" class="btn is-alternative">Neem contact op<?= get_svg("images/arrow-right.svg"); ?></a>
                            <?php endif; ?>

                            <!-- <?php if ($email = get_meta('seoninja_employee_email', get_the_ID())) : ?>
                            <a href="mailto:<?= $email; ?>" class="btn is-alternative"><?= $email; ?> <?= get_svg("images/arrow-right.svg"); ?></a>
                        <?php endif; ?> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php
?>

<?php
if ($current_post_categories = get_the_category()) {
    $category_ids = array();
    foreach ($current_post_categories as $category) {
        //filter out the ghost category
        if ($category->term_id != 1) {
            $category_ids[] = $category->term_id;
        }
    }
    $category_ids = implode(',', $category_ids); //convert array to comma-separated string for the WP query
}
?>

<?= do_shortcode("[related-posts per-row='3' category_ids='$category_ids' orderby='date' order='DESC']"); ?>


<?php get_footer(); ?>