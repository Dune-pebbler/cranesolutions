<?php
$image_id = get_meta("content-block-image-id"); 
?>
<div class="flexbox">
    <div class="text-container" style='width:50%'>
        <label for="">Content naast adres</label>
        <?php wp_editor(get_meta('content-block-content'), "content-block-content"); ?>
    </div>
    
    <div class="image" style='width: 50%'>
        <label for="">Afbeelding onder formulier</label>

        <?php if ($image_id ): ?>
            <img src="<?= wp_get_attachment_url($image_id); ?>" alt="" height="400" >
        <?php else: ?>
            <img src="https://placehold.it/300x300" alt="" height="400">
        <?php endif; ?>

        <div class="buttons" style='display: flex;'>
            <button type="button" class="button theme-image-selector" id="open_media_library">Selecteer afbeelding</button>
            <button type="button" class="components-button is-remove-image">Verwijder afbeelding</button>
        </div>
        
        <input type="hidden" name="theme-meta[content-block-image-id]" class="theme-image-selector" value="<?= @$image_id; ?>" />
    </div>
</div>  