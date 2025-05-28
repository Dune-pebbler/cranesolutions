<?php
defined("ABSPATH") || die('-1'); 

$template_filename = basename(get_page_template()); 
$url_options = get_meta("banner-shortcut-url-options");
$url_options_object = json_decode($url_options);
?>
<style>
    .flexbox{
        display: flex;
        width: 100%;
        gap: 25px;
    }

    .flexbox .image{
        display: flex;
        align-items: center;
        flex-direction: column;
        gap: 25px;
    }
</style>
<div class="flexbox">
    <div class="image" style='width: 50%'>
        <?php if( $image_id = get_post_meta(get_the_ID(), 'banner-image-id', true)): ?>
            <img src="<?= wp_get_attachment_url($image_id); ?>" alt="" >
            <input type="hidden" name="theme-meta[banner-image-id]" class="theme-image-selector" value="<?= $image_id; ?>" />
        <?php else: ?>
            <img src="https://placehold.it/300x300" alt="" >
            <input type="hidden" name="theme-meta[banner-image-id]" class="theme-image-selector" value="" />
        <?php endif; ?>

        <div class="buttons" style='display: flex;'>
            <button type="button" class="button theme-image-selector" id="open_media_library">Selecteer afbeelding</button>
            <button type="button" class="components-button is-remove-image">verwijder afbeelding</button>
        </div>
    </div>
    
    <div class="text-container" style='width:50%'>
        <?php wp_editor(get_post_meta(get_the_ID(), 'banner-caption', true), "banner-caption"); ?>
    </div>
</div> 
<?php if( $template_filename == 'template-home.php' ): ?>
<div class="flexbox">

    <div class="form-group"  style='width:50%'>
        <label for="">Snelkoppeling content</label>
        <div class="text-container">
            <?php wp_editor(get_meta('banner-shortcut-content'), "banner-shortcut-content"); ?>
        </div>
    </div>
    <div class="form-group"  style='width:50%'>
        <h3 class='preview-titel' style='margin: 0; font-size: 20px;'>
            <?= @$url_options_object->text; ?>
        </h3>
        <small class='preview-url'><?= @$url_options_object->url; ?></small>
        
        <div class="buttons" style='display: flex;'>
            <button class="button theme-url-selector">Selecteer URL</button>
            <button class="components-button is-remove-url">Verwijder URL</button>
        </div>
        <input type="hidden" name="theme-meta[banner-shortcut-url-options]" class='url-options' value='<?= $url_options; ?>'>

    </div>
</div>
<?php endif; ?>