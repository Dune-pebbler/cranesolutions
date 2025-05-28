<?php
/**
 * WooCommerce Product Document Upload Metabox
 * Include this file in your theme's functions.php using require_once
 */

// Add the metabox to product pages
function wc_product_documents_add_metabox() {
    add_meta_box(
        'wc_product_documents',
        'Product Documenten',
        'wc_product_documents_metabox_callback',
        'product',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'wc_product_documents_add_metabox');

// Metabox content
function wc_product_documents_metabox_callback($post) {
    // Add nonce for security
    wp_nonce_field('wc_product_documents_save', 'wc_product_documents_nonce');
    
    // Get existing documents if any
    $documents = get_post_meta($post->ID, '_product_documents', true);
    
    ?>
    <div class="product-documents-container">
        <p>Upload product documenten (handleidingen, specificaties, etc.)</p>
        
        <div class="document-list">
            <?php if (!empty($documents) && is_array($documents)) : ?>
                <?php foreach ($documents as $index => $document) : ?>
                    <div class="document-item">
                        <input type="hidden" name="product_documents[<?php echo $index; ?>][id]" value="<?php echo esc_attr($document['id']); ?>">
                        <input type="hidden" name="product_documents[<?php echo $index; ?>][url]" value="<?php echo esc_attr($document['url']); ?>">
                        <input type="hidden" name="product_documents[<?php echo $index; ?>][title]" value="<?php echo esc_attr($document['title']); ?>">
                        <span class="document-title"><?php echo esc_html($document['title']); ?></span>
                        <a href="<?php echo esc_url($document['url']); ?>" target="_blank">Bekijken</a>
                        <a href="#" class="remove-document">Verwijderen</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="document-upload-buttons">
            <button type="button" class="button upload-document-button">Document toevoegen</button>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Document upload button
        $('.upload-document-button').on('click', function(e) {
            e.preventDefault();
            
            var documentUploader = wp.media({
                title: 'Selecteer of upload een product document',
                button: {
                    text: 'Gebruik dit document'
                },
                multiple: false
            });
            
            documentUploader.on('select', function() {
                var attachment = documentUploader.state().get('selection').first().toJSON();
                var index = $('.document-item').length;
                
                var documentHtml = '<div class="document-item">' +
                    '<input type="hidden" name="product_documents[' + index + '][id]" value="' + attachment.id + '">' +
                    '<input type="hidden" name="product_documents[' + index + '][url]" value="' + attachment.url + '">' +
                    '<input type="hidden" name="product_documents[' + index + '][title]" value="' + attachment.title + '">' +
                    '<span class="document-title">' + attachment.title + '</span>' +
                    '<a href="' + attachment.url + '" target="_blank">Bekijken</a>' +
                    '<a href="#" class="remove-document">Verwijderen</a>' +
                    '</div>';
                
                $('.document-list').append(documentHtml);
            });
            
            documentUploader.open();
        });
        
        // Remove document button
        $(document).on('click', '.remove-document', function(e) {
            e.preventDefault();
            $(this).parent('.document-item').remove();
        });
    });
    </script>
    
    <style>
    .document-item {
        margin-bottom: 10px;
        padding: 8px;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
    .document-title {
        font-weight: bold;
        margin-right: 10px;
    }
    .document-item a {
        margin-left: 5px;
    }
    .document-upload-buttons {
        margin-top: 15px;
    }
    </style>
    <?php
}

// Save metabox data
function wc_product_documents_save_metabox($post_id) {
    // Check if nonce is set
    if (!isset($_POST['wc_product_documents_nonce'])) {
        return;
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['wc_product_documents_nonce'], 'wc_product_documents_save')) {
        return;
    }
    
    // Check if autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save documents data
    if (isset($_POST['product_documents'])) {
        update_post_meta($post_id, '_product_documents', $_POST['product_documents']);
    } else {
        delete_post_meta($post_id, '_product_documents');
    }
}
add_action('save_post_product', 'wc_product_documents_save_metabox');

// Helper function to get product documents - can be used in your templates
function wc_get_product_documents($product_id = null) {
    if (!$product_id) {
        global $product;
        if (isset($product) && is_object($product)) {
            $product_id = $product->get_id();
        }
    }
    
    if ($product_id) {
        return get_post_meta($product_id, '_product_documents', true);
    }
    
    return false;
}