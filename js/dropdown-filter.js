jQuery(document).ready(function($) {
    // Get references to our elements
    const variationDropdowns = $('.variations-only select');
    const variationTable = $('.variations-table table');
    const variationRows = variationTable.find('tbody tr');
    const resetButton = $('.reset-filters-btn');
    const tableHeaders = variationTable.find('thead th');
    
    // Dynamically build column indexes based on the table headers
    const columnIndexes = {};
    
    // Get the table headers and build the mapping dynamically
    tableHeaders.each(function(index) {
        const headerText = $(this).text().trim();
        // Skip the first column (SKU) and the last column (Add to cart)
        if (index > 0 && headerText !== 'Toevoegen aan') {
            columnIndexes[headerText] = index;
        }
    });
    
    console.log("Dynamically generated column indexes:", columnIndexes);
    
    // Counter for showing how many results are visible
    let visibleCount = variationRows.length;
    let totalCount = variationRows.length;
    
    // Sorting state
    let currentSortColumn = null;
    let currentSortDirection = 'asc';
    
    // Function to update the counter
    function updateCounter() {
        // Show/hide the reset button based on whether filters are applied
        if (visibleCount < totalCount) {
            resetButton.show();
        } else {
            resetButton.hide();
        }
    }
    
    // Initial setup - show all rows to start
    variationRows.show();
    resetButton.hide(); // Hide reset button initially
    
    // Function to filter the table based on dropdown selections
    function filterVariationTable() {
        // Get all current filter values
        const filterValues = {};
        
        variationDropdowns.each(function() {
            const $dropdown = $(this);
            // Updated selector for the new HTML structure
            const attributeName = $dropdown.closest('.variation-selector-item').find('label').text().trim();
            
            // Get the selected option's text rather than its value
            const selectedValue = $dropdown.val();
            
            // Only proceed if something is selected
            if (selectedValue && selectedValue !== '' && !selectedValue.includes('Kies een optie')) {
                // Get the text of the selected option instead of its value
                const selectedText = $dropdown.find('option:selected').text().trim();
                filterValues[attributeName] = selectedText;
                
                // Log for debugging
                console.log("Selected attribute:", attributeName, "Selected text:", selectedText);
            }
        });
        
        // If no filters are applied, show all rows
        if (Object.keys(filterValues).length === 0) {
            variationRows.show();
            visibleCount = totalCount;
            updateCounter();
            return;
        }
        
        // Reset visible count
        visibleCount = 0;
        
        // Loop through each table row
        variationRows.each(function() {
            const $row = $(this);
            let showRow = true;
            
            // Check each filter against this row
            for (const [attributeName, selectedText] of Object.entries(filterValues)) {
                // Get the column index for this attribute
                const columnIndex = columnIndexes[attributeName];
                if (columnIndex === undefined) continue;
                
                // Get the text directly from the table cell
                const cellText = $row.find('td').eq(columnIndex).text().trim();
                console.log("Cell Text", cellText, "Selected text:", selectedText);
                
                // Compare the cell text with the option text
                if (cellText !== selectedText) {
                    showRow = false;
                    break;
                }
            }
            
            // Show or hide the row based on our filter results
            if (showRow) {
                $row.show();
                visibleCount++;
            } else {
                $row.hide();
            }
        });
        
        // Update the counter display
        updateCounter();
        
        // If no results are visible, show a "no results" message
        if (visibleCount === 0) {
            if ($('.no-matching-variations').length === 0) {
                variationTable.after('<div class="no-matching-variations">Geen varianten gevonden met de geselecteerde filters.</div>');
            } else {
                $('.no-matching-variations').show();
            }
        } else {
            $('.no-matching-variations').hide();
        }
    }
    
    // Function to reset all filters
    function resetFilters() {
        // Reset all dropdowns to their default option
        variationDropdowns.each(function() {
            $(this).val('').trigger('change');
        });
        
        // Show all rows
        variationRows.show();
        visibleCount = totalCount;
        
        // Hide no results message
        $('.no-matching-variations').hide();
        
        // Update counter
        updateCounter();
    }
    
    // Helper function to extract numeric value from a string
    function extractNumber(value) {
        // Extract only digits and decimal points
        const numericValue = value.replace(/[^0-9.]/g, '');
        return numericValue === '' ? 0 : parseFloat(numericValue);
    }
    
    // Function to determine if a value is numeric
    function isNumeric(value) {
        // Check if the value contains any digits
        return /\d/.test(value);
    }
    
    // Function to sort the table
    function sortTable(columnIndex) {
        const tbody = variationTable.find('tbody');
        const rows = tbody.find('tr').toArray();
        
        // Toggle sort direction if clicking the same column
        if (currentSortColumn === columnIndex) {
            currentSortDirection = currentSortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            currentSortColumn = columnIndex;
            currentSortDirection = 'asc';
        }
        
        // Update table headers to show sort direction
        tableHeaders.removeClass('sort-asc sort-desc');
        tableHeaders.eq(columnIndex).addClass('sort-' + currentSortDirection);
        
        // Sort the rows
        rows.sort(function(a, b) {
            // Get text from the cells we're sorting
            const aValue = $(a).find('td').eq(columnIndex).text().trim();
            const bValue = $(b).find('td').eq(columnIndex).text().trim();
            
            // Check if values contain numbers
            const aHasNumbers = isNumeric(aValue);
            const bHasNumbers = isNumeric(bValue);
            
            let comparison = 0;
            
            // If both values have numbers, use numeric sorting
            if (aHasNumbers && bHasNumbers) {
                const aNum = extractNumber(aValue);
                const bNum = extractNumber(bValue);
                comparison = aNum - bNum;
            } else {
                // Otherwise use alphabetical sorting
                comparison = aValue.localeCompare(bValue);
            }
            

            return currentSortDirection === 'desc' ? -comparison : comparison;
        });
        

        tbody.append(rows);
    }
    
    // Make table headers clickable for sorting
    tableHeaders.css('cursor', 'pointer');
    tableHeaders.on('click', function() {
        const columnIndex = $(this).index();
        // Don't sort the "Add to cart" column
        if ($(this).text().trim() !== 'Toevoegen aan') {
            sortTable(columnIndex);
        }
    });
    
    // Add some basic styles for sort indicators
    $('<style>')
        .text(`
            .variations-table th.sort-asc::after {
                content: " ▲";
                font-size: 0.8em;
            }
            .variations-table th.sort-desc::after {
                content: " ▼";
                font-size: 0.8em;
            }
        `)
        .appendTo('head');
    
    // Add change event listeners to each dropdown
    variationDropdowns.on('change', filterVariationTable);
    
    // Add click event listener to reset button
    resetButton.on('click', resetFilters);
    
    // Filter initially in case there are default values
    filterVariationTable();
});