<?php
global $theme_options; 

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
                    <button type="submit" class='button' onclick="jQuery('.theme-options-content form').submit();">Opslaan</button>
                </div>
            </li>
        </ul>
    </div>
    <div class="theme-options-content">        
        <form action="<?= admin_url('admin.php?page=theme-options-globaal'); ?>" method="POST">
            <div class="theme-option-box">
                <div class="header">
                    <h2>Footer shortcuts</h2>
                </div>
                <div class="body">
                    <div class="form-item">
                        <label for="">Content</label>
                        <?php wp_editor(get_theme_option('theme_option_footer_block_content'), "theme_option_footer_block_content"); ?>
                    </div>
                    <?php for( $i = 0; $i < 3; $i++ ): ?>
                        <?php 
                        $url_options = get_theme_option("theme_option_footer_url_options_{$i}");
                        $url_options_object = json_decode($url_options);
                        ?>
                        <div class="form-item one-third-size">
                            <label for="">Buttons</label>
                            <h3 class='preview-titel' style='margin: 0; font-size: 20px;'>
                                <?= @$url_options_object->text; ?>
                            </h3>
                            <small class='preview-url'><?= @$url_options_object->url; ?></small>
                            <div class="buttons" style='display: flex;'>
                                <button class="button theme-url-selector">Selecteer URL</button>
                                <button class="components-button is-remove-url">Verwijder URL</button>
                            </div>
                            <input type="hidden" name="theme_option_footer_url_options_<?= $i; ?>" class='url-options' value='<?= $url_options; ?>'>
                        </div>
                    <?php endfor; ?>
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="buttons">
                <button type="submit" class='button'>Opslaan</button>
            </div>

            <?php wp_nonce_field('global_theme_nonce_field', 'global_theme_nonce_field'); ?>
        </form>
    </div>
</section>