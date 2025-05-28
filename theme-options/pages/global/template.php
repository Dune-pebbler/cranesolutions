<?php
global $theme_options; 
$vacancies_args = [
    'post_type' => 'jobpostings',
    'posts_per_page' => -1,
];
$review_args = [
    'post_type' => 'reviews',
    'posts_per_page' => -1,
    'post_status' => 'publish'
];
$featured_vacancies_ids = get_theme_option('theme_option_featured_vacancies_ids', []);
$featured_review_ids = get_theme_option('theme_option_featured_reviews_ids', []);
$vacancy_query = new WP_Query($vacancies_args);
$review_query = new WP_Query($review_args);
?>

<section class="theme-options">
    <div class="theme-options-sidebar">
        <ul>
            <li>
                <a href="/wp-admin/admin.php?page=theme_settings_page">
                    <img src="<?= get_template_directory_uri(); ?>/images/logo.svg" alt="">
                </a>
            </li>
            <?php foreach($theme_options['pages'] as $page_name => $page_arguments): ?>
                <li>
                    <a href="<?= admin_url("admin.php?page=" . sanitize_title("theme-options-" . $page_name)); ?>"><?= $page_name; ?></a>
                </li>
            <?php endforeach; ?>

            <li style='display: flex; align-items: center; padding: 15px; margin-left: auto;'>
                <div class="buttons">
                    <button type="submit" class='button button-primary' onclick="jQuery('.theme-options-content form').submit();">Opslaan</button>
                </div>
            </li>
        </ul>
    </div>
    <div class="theme-options-content">        
        <form action="<?= admin_url('admin.php?page=theme-options-globaal'); ?>" method="POST">
            <div class="theme-option-box">
                <div class="header">
                    <h2>Uitgelichte vacatures</h2>
                </div>
                <div class="body">
                    <div class="form-item one-third-size">
                        <label for="">Uitgelichte vacatures achtergrond</label>
                        <div class="image">
                            <?php $background_image_id = get_theme_option('theme_option_featured_vacancies_background'); ?>
                            <?php if ($background_image_id ): ?>
                                <img src="<?= wp_get_attachment_url($background_image_id); ?>" alt="" >
                            <?php else: ?>
                                <img src="https://placehold.it/300x300" alt="" height="200">
                            <?php endif; ?>
            
                            <div class="buttons" style='display: flex;'>
                                <button type="button" class="button theme-image-selector" id="open_media_library">Selecteer afbeelding</button>
                                <button type="button" class="components-button is-remove-image">Verwijder afbeelding</button>
                            </div>
                            
                            <input type="hidden" name="theme_option_featured_vacancies_background" class="theme-image-selector" value="<?= @$background_image_id; ?>" />
                        </div>

                        <div class="form-group">
                            <label for="">Uitgelichte vacatures</label>
                            <select name="theme_option_featured_vacancies_ids[]" multiple class='select2-js'>
                                <?php while( $vacancy_query->have_posts() ): $vacancy_query->the_post(); ?>
                                    <option value="<?php the_ID(); ?>" <?= in_array(get_the_ID(), $featured_vacancies_ids) ? "selected" : ""; ?>><?php the_title(); ?></option>
                                <?php endwhile; wp_reset_postdata(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-item two-third-size">
                        <label for="">Uitgelichte vacatures Content</label>
                        <?php wp_editor(get_theme_option('theme_option_featured_vacancies_block_content'), "theme_option_featured_vacancies_block_content"); ?>
                    </div>

                    <div class="clearfix"></div>
                    
                </div>
            </div>
        
            <div class="theme-option-box">
                <div class="header">
                    <h2>USP's blok</h2>
                </div>
                <div class="body">
                    <div class="form-item one-third-size">
                        <label for="">USP Achtergrond</label>
                        <div class="image">
                            <?php $background_image_id = get_theme_option('theme_option_usps_background_image'); ?>
                            <?php if ($background_image_id ): ?>
                                <img src="<?= wp_get_attachment_url($background_image_id); ?>">
                            <?php else: ?>
                                <img src="https://placehold.it/300x300" alt="" height="200">
                            <?php endif; ?>
            
                            <div class="buttons" style='display: flex;'>
                                <button type="button" class="button theme-image-selector" id="open_media_library">Selecteer afbeelding</button>
                                <button type="button" class="components-button is-remove-image">Verwijder afbeelding</button>
                            </div>
                            
                            <input type="hidden" name="theme_option_usps_background_image" class="theme-image-selector" value="<?= @$background_image_id; ?>" />
                        </div>
                                
                        <div class="form-group">
                            <label for="">USP Reviews</label>
                            <select name="theme_option_featured_reviews_ids[]" multiple class='select2-js'>
                                <?php while( $review_query->have_posts() ): $review_query->the_post(); ?>
                                    <option value="<?php the_ID(); ?>" <?= in_array(get_the_ID(), (array)$featured_review_ids) ? "selected" : ""; ?>><?php the_title(); ?></option>
                                <?php endwhile; wp_reset_postdata(); ?>
                            </select>
                        </div>
                    </div>
                            
                    <div class="form-item two-third-size">
                        <div class="form-group">
                            <label for="">USP Content</label>
                            <?php wp_editor(get_theme_option('theme_option_usps_block_content'), "theme_option_usps_block_content"); ?>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="buttons">
                <button type="submit" class='button button-primary'>Opslaan</button>
            </div>

            <?php wp_nonce_field('global_theme_nonce_field', 'global_theme_nonce_field'); ?>
        </form>
    </div>
</section>