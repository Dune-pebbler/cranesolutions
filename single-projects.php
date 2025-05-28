<?php
$team_user = get_team_object_by_auteur();
$projects = get_project_query();
$project_position = array_search($post, $projects->posts);
$previous_project_position = ($project_position > 0) ? $project_position - 1 : -1;
$next_project_position = ($project_position < count($projects->posts) - 1) ? $project_position + 1 : false;
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
                    <div class="subtitle">
                        <?php if (get_post_type() == 'post') : ?>
                            <p><?= __('Blog', THEME_TD); ?></p>
                        <?php else : ?>
                            <p><?= ucfirst(get_post_type()); ?></p>
                        <?php endif; ?>

                        <?php if ($tags = wp_get_post_terms(get_the_ID(), 'sectors')) : ?>
                            <?php
                            $tags = array_filter($tags, function ($term) {
                                return $term->term_id != 1;
                            });
                            $tag_ids = wp_list_pluck($tags, 'term_id');
                            $tag_links = array_map(function ($tag_id) {
                                return get_term_link($tag_id, 'sectors');
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
                        <!-- <h2><?= __('Op zoek naar een speciale hijsoplossing?', THEME_TD); ?></h2> -->
                        <p><?= __('Neem contact op met', THEME_TD); ?> <a href="<?= get_the_permalink($user_object->ID); ?>"><?= $user_object->post_title; ?></a> </p>
                        <div class="buttons">
                            <?php if ($phone_number = get_meta('seoninja_employee_telephone', $user_object->ID)) : ?>
                                <a href="tel:<?= $phone_number; ?>" class="btn is-alternative"><?= str_replace("+31", "0", $phone_number); ?> <?= get_svg("images/arrow-right.svg"); ?></a>
                            <?php endif; ?>

                            <a href="/contact" class="btn is-alternative">Neem contact op</a>
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
<section class="project-navigation">
    <div class="container">
        <div class="centered row">
            <div class="previous block col-12 offset-sm-1 col-sm-3">
                <?php if ($previous_project_position >= 0) : $id = $projects->posts[$previous_project_position]->ID; ?>
                    <a href="<?php echo get_the_permalink($id); ?>"><i class="fal fa-arrow-left"></i> <?php echo get_the_title($id); ?></a>
                <?php endif; ?>
            </div>

            <div class="back-to block col-12 col-sm-4">
                <a href="/projecten" class='btn'> Naar projectoverzicht</a>
            </div>
            <?php if ($next_project_position) : $id = $projects->posts[$next_project_position]->ID; ?>
                <div class="next block col-12 col-sm-3">
                    <a href="<?php echo get_the_permalink($id); ?>"> <?php echo get_the_title($id); ?> <i class="fal fa-arrow-right"></i></a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php
if ($current_post_terms = wp_get_post_terms(get_the_ID(), 'sectors')) {
    $sector_ids = array_map(function ($term) {
        return $term->term_id;
    }, $current_post_terms);
    $term_ids = implode(',', $sector_ids);

    $sector_names = array_map(function ($term) {
        return $term->name;
    }, $current_post_terms);
    $term_names = implode(',', $sector_names);
}
?>

<?= do_shortcode("[related-posts per-row='3' orderby='date' order='DESC' sectors='$term_ids' post_type='projects' title='Gerelateerde projecten']"); ?>
<?php get_template_part('template-parts/our', 'partners'); ?>
<?= do_shortcode("[related-posts per-row='3' subtitle='' title='Nieuws over $term_names' orderby='date' sectors='$term_ids' order='DESC']"); ?>

<?php get_footer(); ?>