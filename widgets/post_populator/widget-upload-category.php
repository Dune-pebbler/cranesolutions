<?php
#ACTIONS 
add_action('wp_dashboard_setup', 'custom_dashboard_widget');

function custom_dashboard_widget()
{
    wp_add_dashboard_widget(
        'custom_dashboard_widget',
        'Category creator',
        'custom_dashboard_widget_content'
    );
} 
function custom_dashboard_widget_content()
{
    //do when submitted
    if (isset($_POST['submit_csv'])) {
        $form_fields = [
            'csv_file' => $_FILES['csv_file']['tmp_name'],
            'taxonomy_name' => $_POST['taxonomy_name'],
            'category_name' => $_POST['category_name'],
            'category_description' => $_POST['category_description']
        ];
        // set & check the required fields
        if (
            empty($form_fields['csv_file']) ||
            empty($form_fields['taxonomy_name']) ||
            empty($form_fields['category_name'])
        ) {
            echo "<p>Please fill in all required fields.</p>";
            return; // Stop execution if required fields are empty
        }
        // If all required fields are filled in, proceed with CSV upload
        handle_csv_upload($form_fields);
    }
?>
    <!-- form that displays as widget -->
    <form method="post" enctype="multipart/form-data" class="widget-form-container">

        <div class="grouper">
            <label for="csv_file" class="widget-label">Upload CSV File: *</label>
            <input type="file" id="csv_file" name="csv_file" required>
        </div>

        <h3><strong>Waar moeten de categorien worden geplaatst?</strong></h3>
        <div class="grouper flex-row">
            <label for="taxonomy_name" class="widget-label">Naam taxonomy: * </label>
            <input type="text" id="taxonomy_name" name="taxonomy_name" required>
        </div>

        <div class="grouper">
            <h3><strong>Velden</strong></h3>
            <p>Vul de naam in van de kolom header in het CSV bestand </p>
        </div>

        <div class="grouper flex-row">
            <label class="widget-label"><strong>Veld</strong></label>
            <p><strong>CSV header naam</strong></p>
        </div>

        <div class="grouper flex-row">
            <label for="category_name" class="widget-label">Naam: *</label>
            <input type="text" id="category_name" name="category_name" placeholder="categorie_naam" required>
        </div>

        <div class="grouper flex-row">
            <label for="category_description" class="widget-label">Beschrijving</label>
            <input type="text" id="category_description" name="category_description" placeholder="categorie_beschrijving">
        </div>

        <input class="upload-button" type="submit" name="submit_csv" value="Upload">

    </form>
<?php
}
function handle_csv_upload($form_fields)
{
    $csv_file = $form_fields['csv_file'];
    $taxonomy_name = $form_fields['taxonomy_name'];

    //open the svg in readmode and attempt to handle it
    if (($handle = fopen($csv_file, "r")) !== FALSE) {

        // get data from the header row limit to 1000 characters
        $header_row = fgetcsv($handle, 1000, ",");

        // Find column index if it the input name exists in the header 
        $category_name_column_index = array_search($form_fields['category_name'], $header_row, true);
        $category_description_column_index = array_search($form_fields['category_description'], $header_row, true);

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

            if (isset($data[$category_name_column_index])) {
                $category_name = $data[$category_name_column_index];
                $category_description = $data[$category_description_column_index];
                create_category($category_name, $category_description, $taxonomy_name);
            }
        }

        fclose($handle);
        echo "<p>Categories CSV data processed successfully!</p>";
    } else {
        echo "<p>Error opening CSV file.</p>";
    }
}
function create_category($category_name, $category_description, $taxonomy_name)
{
    // Check if the category already exists then return
    $existing_category = get_term_by('name', $category_name, $taxonomy_name);
    if ($existing_category instanceof WP_Term) {
        echo "<p>Category '{$category_name}' already exists with ID '{$existing_category->term_id}";
        return;
    }

    // If the category doesn't exist, create it
    $category_created = wp_insert_term(
        $category_name,
        $taxonomy_name,
        array(
            'description' => $category_description // Category description
        )
    );

    if (!is_wp_error($category_created)) {
        echo "<p>Category '{$category_name}' created successfully with description '{$category_description}' in '{$taxonomy_name}'.</p>";
        return;
    } else {
        echo "<p>Error creating category '{$category_name}': " . $category_created->get_error_message() . "</p>";
        return;
    }
}

