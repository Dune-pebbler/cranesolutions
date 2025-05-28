<?php 
$args = [
    'post_type' => 'post',
    'posts_per_page' => 12,
    'paged' => max(1, get_query_var('paged')),
    'author' => get_meta('seoninja_employee_user'),
];

if( isset($_GET['search']) ) {
    $args['s'] = wp_kses_data($_GET['search']);
}

$query = new WP_Query($args);

get_header(); ?>
<section class="contact-us">
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-3 offset-lg-1">
                <div class="image">
                    <?php if( has_post_thumbnail() ): ?>
                        <?=  get_the_post_thumbnail(get_the_ID(), 'full', []); ?>
                    <?php else: ?>
                        <img src="https://secure.gravatar.com/avatar/6ac40747f1f2a118d21980a197c931a9?&d=mm&r=g" width="100%" loading="lazy" alt="<?php the_title(); ?> profiel foto">
                    <?php endif; ?>
                </div>
            </div>
            <div class="col12- col-lg-5 offset-lg-1">
                <div class="text-container">
                    <!-- <h2><?= __('Op zoek naar een speciale hijsoplossing?', THEME_TD); ?></h2> -->
                    <p><?= __('Neem contact op met', THEME_TD); ?> <a href="<?= get_the_permalink(get_the_ID()); ?>"><?php the_title(); ?></a> </p>
                    <div class="buttons">
                        <?php if( $phone_number = get_meta('seoninja_employee_telephone', get_the_ID()) ): ?>
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

<section class="news is-news-archive">
    <div class="container">
        <div class="titel-container">
            <h2> <?= __('Artikelen van'); ?> <?php the_title(); ?></h2>
            <!-- <form method="GET">
                <input type="text" name="search" value="<?= @$_GET['search']; ?>" placeholder="zoeken in artikelen">
            </form> -->
        </div>
        <?php if( $query->have_posts() ): ?>
        <div class="row">
            <?php 
            while( $query->have_posts() ): $query->the_post(); 
                get_template_part("template-parts/news", "item");
            endwhile; wp_reset_postdata(); 
            ?>
        </div>
        <?= theme_pagination($query); ?>
        <?php else: ?>
            <p><?= __('Helaas hebben we geen nieuwsberichten kunnen vinden met', THEME_TD); ?> '<b><?= $_GET['search']; ?></b>'</p>
        <?php endif; ?>
    </div>
</section>
<?php get_footer(); ?>