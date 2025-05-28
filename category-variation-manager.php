<?php 
add_action('admin_footer', 'category_eigenschappen_handler');
function category_eigenschappen_handler() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Watch for category changes
        function handleCategoryChange() {
            var selectedCategories = [];
            $('.categorychecklist input:checked').each(function() {
                selectedCategories.push($(this).val());
            });
            
            // Make AJAX call to get eigenschappen for category
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_category_eigenschappen',
                    categories: selectedCategories,
                    nonce: '<?php echo wp_create_nonce("get_category_eigenschappen"); ?>'
                },
                success: function(response) {
                    if (response.success && response.data.eigenschappen) {
                        // Clear existing eigenschappen if none are set yet
                        if ($('.woocommerce_attribute').length === 0) {
                            $('.product_attributes').empty();
                        }
                        
                        // Add each eigenschap
                        response.data.eigenschappen.forEach(function(eigenschap) {
                            // Only add if it doesn't already exist
                            if ($('input[value="' + eigenschap.name + '"]').length === 0) {
                                addNewAttribute(eigenschap);
                            }
                        });
                    }
                }
            });
        }

        // Add new attribute to the product
        function addNewAttribute(eigenschap) {
            // Trigger the "Add New" button click
            $('.add_attribute').click();
            
            // Wait for the new attribute row to be added
            setTimeout(function() {
                var $lastRow = $('.product_attributes .woocommerce_attribute:last');
                
                // Set the name
                $lastRow.find('input[name^="attribute_names"]').val(eigenschap.name);
                
                // Set the values
                $lastRow.find('textarea[name^="attribute_values"]').val(eigenschap.values);
                
                // Check the "Used for variations" if needed
                if (eigenschap.variation) {
                    $lastRow.find('input[name^="attribute_variation"]').prop('checked', true);
                }
                
                // Check the "Visible on product page" if needed
                $lastRow.find('input[name^="attribute_visibility"]').prop('checked', true);
            }, 100);
        }

        // Bind to category checkbox changes
        $('.categorychecklist input[type="checkbox"]').on('change', handleCategoryChange);
    });
    </script>
    <?php
}

// Add AJAX handler for eigenschappen
add_action('wp_ajax_get_category_eigenschappen', 'handle_get_category_eigenschappen');
function handle_get_category_eigenschappen() {
    check_ajax_referer('get_category_eigenschappen', 'nonce');
    
    $categories = $_POST['categories'];
    if (!is_array($categories)) {
        $categories = [$categories];
    }
    
    // Define default eigenschappen for each category
    $category_eigenschappen = [
        'stahl-kettingtakels' => [
            [
                'name' => 'Capaciteit (kg)',
                'values' => '250|500|1000|2000|3200|5000',
                'variation' => true
            ],
            [
                'name' => 'Snelheid (m/min)',
                'values' => '4/1|8/2|12/3',
                'variation' => true
            ],
            [
                'name' => 'ISO',
                'values' => 'M4|M5|M6',
                'variation' => true
            ],
            [
                'name' => 'Inschakelduur (%)',
                'values' => '40|60',
                'variation' => true
            ],
            [
                'name' => 'Schakelingen (per uur)',
                'values' => '180|240|300',
                'variation' => true
            ]
        ],
        // Add more categories as needed
    ];
    
    // Get eigenschappen for selected category
    $eigenschappen = [];
    foreach ($categories as $category_id) {
        $term = get_term($category_id, 'product_cat');
        if ($term && isset($category_eigenschappen[$term->slug])) {
            $eigenschappen = array_merge($eigenschappen, $category_eigenschappen[$term->slug]);
        }
    }
    
    wp_send_json_success(['eigenschappen' => $eigenschappen]);
}
?>