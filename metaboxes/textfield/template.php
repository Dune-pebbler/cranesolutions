<?php
$image_id = get_meta("content-block-image-id"); 
?>
<div class="flexbox">
    <div class="text-container">
        <?php wp_editor(get_meta('text-block-content'), "text-block-content"); ?>
    </div>
</div> 