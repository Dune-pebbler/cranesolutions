<?php $amount = 4; ?>
<div class="form-group">
    <label for="">Snelle menu titel</label>

    <input type="text" name='theme-meta[quickmenu-titel]' value='<?= get_meta('quickmenu-titel'); ?>' style="width: 100%;">
</div>
<div class="flexbox" style='background-color: #f2f2f2; padding: 25px; gap: 5px'>
    <?php for( $i = 0; $i < $amount; $i++): ?>
        <?php 
        $image_id = get_meta("quickmenu-icon-{$i}"); 
        $url_options = get_meta("url-options-{$i}");
        $url_options_object = json_decode($url_options);
        ?>
        
        <div class="image" style='width: 25%; background-color: #fff; padding: 25px; align-items: flex-start'>
            <?php if ($image_id ): ?>
                <img src="<?= wp_get_attachment_url($image_id); ?>" alt="" width="150" >
            <?php else: ?>
                <img src="https://placehold.it/300x300" alt="" width="150">
            <?php endif; ?>

            <div class="buttons" style='display: flex;'>
                <button type="button" class="button theme-image-selector" id="open_media_library">Selecteer icoon</button>
                <button type="button" class="components-button is-remove-image">Verwijder icoon</button>
            </div>
            
            <h3 class='preview-titel' style='margin: 0; font-size: 20px;'>
                <?= @$url_options_object->text; ?>
            </h3>
            <small class='preview-url'><?= @$url_options_object->url; ?></small>
            
            <div class="buttons" style='display: flex;'>
                <button class="button theme-url-selector">Selecteer URL</button>
                <button class="components-button is-remove-url">Verwijder URL</button>
            </div>
            <input type="hidden" name="theme-meta[quickmenu-icon-<?= $i; ?>]" class="theme-image-selector" value="<?= @$image_id; ?>" />
            <input type="hidden" name="theme-meta[url-options-<?= $i; ?>]" class='url-options' value='<?= $url_options; ?>'>
        </div>
    <?php endfor; ?>
</div>