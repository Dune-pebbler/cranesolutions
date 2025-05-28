<?php
/**
 * WooCommerce Cart to Gravity Forms - Advanced Integration
 * 
 * Creates a custom Gravity Forms field type for WooCommerce cart items
 * and handles storing cart data with form submissions
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register the custom field with Gravity Forms
 */
add_action('gform_loaded', 'register_wc_cart_field', 5);
function register_wc_cart_field() {
    // Check if Gravity Forms is active
    if (!class_exists('GFForms')) {
        return;
    }
    
    // Include the custom field class
    class GF_Field_WC_Cart extends GF_Field {
        public $type = 'wc_cart';
        
        public function get_form_editor_field_title() {
            return esc_attr__('WC Cart', 'gravityforms');
        }
        
        public function get_form_editor_button() {
            return [
                'group' => 'advanced_fields',
                'text'  => $this->get_form_editor_field_title(),
            ];
        }
        
        public function get_field_input($form, $value = '', $entry = null) {
            // Display the cart items in the form (read-only view)
            $cart_html = $this->get_cart_html();
            return sprintf("<div class='ginput_container ginput_container_wc_cart'>%s</div>", $cart_html);
        }
        
        public function get_cart_html() {
            if (!function_exists('WC')) {
                return '<p>WooCommerce is not active.</p>';
            }
            
            $cart = WC()->cart;
            if (empty($cart->get_cart())) {
                return '<p>No products in cart.</p>';
            }
            
            $html = '<div class="wc_cart_items">';
            $html .= '<h4>Products in your cart</h4>';
            $html .= '<ul>';
            
            foreach ($cart->get_cart() as $cart_item) {
                $product = $cart_item['data'];
                $quantity = $cart_item['quantity'];
                
                $html .= '<li>';
                $html .= '<strong>' . esc_html($product->get_name()) . '</strong> × ' . $quantity;
                
                // Add variation data if it exists
                if (!empty($cart_item['variation'])) {
                    $html .= '<ul class="wc_cart_variations">';
                    foreach ($cart_item['variation'] as $attr_name => $attr_value) {
                        $taxonomy = str_replace('attribute_', '', $attr_name);
                        $term_name = get_term_by('slug', $attr_value, $taxonomy);
                        $attr_label = wc_attribute_label($taxonomy);
                        
                        $display_value = $term_name ? $term_name->name : $attr_value;
                        $html .= '<li>' . esc_html($attr_label) . ': ' . esc_html($display_value) . '</li>';
                    }
                    $html .= '</ul>';
                }
                
                $html .= '</li>';
            }
            
            $html .= '</ul>';
            $html .= '</div>';
            
            return $html;
        }
        
        public function get_value_save_entry($value, $form, $input_name, $entry_id, $entry) {
            // This field doesn't use the typical input value
            // We'll capture the cart items separately
            return '';
        }
    }
    
    // Register the field with Gravity Forms
    GF_Fields::register(new GF_Field_WC_Cart());
}

/**
 * Add custom styles for cart field display
 */
add_action('wp_head', 'wc_cart_field_styles');
function wc_cart_field_styles() {
    ?>
    <style type="text/css">
        .ginput_container_wc_cart .wc_cart_items {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
        }
        .ginput_container_wc_cart .wc_cart_items h4 {
            margin-top: 0;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .ginput_container_wc_cart .wc_cart_variations {
            margin: 5px 0 5px 20px;
            font-size: 0.9em;
            color: #666;
        }
    </style>
    <?php
}

/**
 * Save WooCommerce cart items with the form submission
 */
add_action('gform_after_submission', 'save_wc_cart_data', 10, 2);
function save_wc_cart_data($entry, $form) {
    // Check if the form contains our custom WC cart field
    $has_cart_field = false;
    foreach ($form['fields'] as $field) {
        if ($field->type == 'wc_cart') {
            $has_cart_field = true;
            break;
        }
    }
    
    if (!$has_cart_field || !function_exists('WC')) {
        return;
    }
    
    $cart = WC()->cart;
    if (empty($cart->get_cart())) {
        return;
    }
    
    // Save full cart data as JSON (complete and structured)
    $cart_data = [];
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        
        $item = [
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'sku' => $product->get_sku(),
            'quantity' => $cart_item['quantity'],
            'product_type' => $product->get_type(),
            'variations' => []
        ];
        
        if (!empty($cart_item['variation'])) {
            foreach ($cart_item['variation'] as $attr_name => $attr_value) {
                $taxonomy = str_replace('attribute_', '', $attr_name);
                $term_name = get_term_by('slug', $attr_value, $taxonomy);
                $attr_label = wc_attribute_label($taxonomy);
                
                $display_value = $term_name ? $term_name->name : $attr_value;
                $item['variations'][$attr_label] = $display_value;
            }
        }
        
        $cart_data[] = $item;
    }
    
    // Save the structured data as meta
    gform_update_meta($entry['id'], 'wc_cart_data', $cart_data);
    
    // Also save human-readable version for emails and admin views
    $text_version = '';
    foreach ($cart_data as $item) {
        $text_version .= $item['name'] . ' × ' . $item['quantity'] . "\n";
        
        if (!empty($item['variations'])) {
            foreach ($item['variations'] as $label => $value) {
                $text_version .= "  - " . $label . ": " . $value . "\n";
            }
        }
        
        $text_version .= "\n";
    }
    gform_update_meta($entry['id'], 'wc_cart_text', $text_version);
}

/**
 * Add cart data to notification emails
 */
add_filter('gform_notification', 'add_wc_cart_to_notification', 10, 3);
function add_wc_cart_to_notification($notification, $form, $entry) {
    $cart_text = gform_get_meta($entry['id'], 'wc_cart_text');
    
    if (!empty($cart_text)) {
        $notification['message'] .= "\n\nProducts in Cart:\n\n" . $cart_text;
    }
    
    return $notification;
}

/**
 * Add a meta box to the entry detail page
 */
add_action('gform_entry_detail_content_after', 'display_wc_cart_in_entry', 10, 2);
function display_wc_cart_in_entry($form, $entry) {
    $cart_data = gform_get_meta($entry['id'], 'wc_cart_data');
    
    if (empty($cart_data)) {
        return;
    }
    
    ?>
    <div class="postbox">
        <h3 class="hndle">
            <span>WooCommerce Cart Items</span>
        </h3>
        <div class="inside">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Quantity</th>
                        <th>Variations</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_data as $item): ?>
                    <tr>
                        <td><?php echo esc_html($item['name']); ?></td>
                        <td><?php echo esc_html($item['sku']); ?></td>
                        <td><?php echo esc_html($item['quantity']); ?></td>
                        <td>
                            <?php if (!empty($item['variations'])): ?>
                                <ul style="margin: 0; padding-left: 18px;">
                                <?php foreach ($item['variations'] as $label => $value): ?>
                                    <li><?php echo esc_html($label); ?>: <?php echo esc_html($value); ?></li>
                                <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}