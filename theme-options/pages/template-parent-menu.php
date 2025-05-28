<?php
global $theme_options; 
?>

<section class="theme-options">
    <div class="theme-options-sidebar">
        <ul>
            <?php foreach($theme_options['pages'] as $page_name => $page_arguments): ?>
                <li>
                    <a href="<?= admin_url("admin.php?page=" . sanitize_title("theme-options-" . $page_name)); ?>"><?= $page_name; ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="theme-options-content">
        <h2>Thema opties</h2>
        <p>Dit zijn de thema opties voor <b><?= site_url(); ?></b></p>
    </div>
</section>