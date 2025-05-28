import csv
import random
import os
import re
import datetime
import glob

def extract_product_name(description):
    """Extract the product name from the description"""
    # Remove any numeric part at the end of the description
    # For example "YaleERGO 360 1500" -> "YaleERGO 360"
    name = re.sub(r'\s+\d+$', '', description)
    return name

def read_laadsheet(input_file):
    """Read and normalize a laadsheet CSV file"""
    with open(input_file, 'r', encoding='utf-8') as file:
        reader = csv.DictReader(file)
        laadsheet_data = list(reader)
    
    # Normalize column names (handle the space in YaleERGO column name)
    if any('Artikelnummer ' in row for row in laadsheet_data):
        laadsheet_data = [{k.strip(): v for k, v in row.items()} for row in laadsheet_data]
        for row in laadsheet_data:
            if 'Artikelnummer ' in row:
                row['Artikelnummer'] = row.pop('Artikelnummer ')
    
    # Ensure all required columns are present
    required_columns = ['Artikelnummer', 'Omschrijving', 'Capaciteit', 'Hijshoogte', 'Aantal parten']
    for row in laadsheet_data:
        for col in required_columns:
            if col not in row:
                raise ValueError(f"Missing required column '{col}' in the laadsheet: {input_file}")
    
    return laadsheet_data

def convert_single_laadsheet(laadsheet_data):
    """Convert a single laadsheet data to importsheet format"""
    # Extract unique values for each property
    unique_descriptions = list(set(row['Omschrijving'] for row in laadsheet_data))
    unique_capacities = list(set(str(row['Capaciteit']) for row in laadsheet_data))
    unique_heights = list(set(str(row['Hijshoogte']) for row in laadsheet_data))
    unique_parts = list(set(str(row['Aantal parten']) for row in laadsheet_data))
    
    # Generate a main product name from the first description
    if len(unique_descriptions) > 0:
        main_product_name = extract_product_name(unique_descriptions[0])
    else:
        main_product_name = "Product"
    
    # Create the import sheet data
    import_data = []
    
    # Generate a parent SKU based on the main product name
    parent_sku = re.sub(r'[^a-zA-Z0-9]', '', main_product_name).upper()
    
    # Add the parent product
    parent_product = {
        'ID': '',  # Empty ID, WooCommerce will generate it
        'Hoofd': '',  # Empty for parent
        'Type': 'variable',
        'SKU': parent_sku,  # Use generated SKU for parent
        'Naam': main_product_name,
        'Positie': 0,
        'Naam eigenschap 1': 'Omschrijving',
        'Waarde eigenschap 1': ', '.join(unique_descriptions),
        'Naam eigenschap 2': 'Capaciteit',
        'Waarde eigenschap 2': ', '.join(unique_capacities),
        'Naam eigenschap 3': 'Hijshoogte',
        'Waarde eigenschap 3': ', '.join(unique_heights),
        'Naam eigenschap 4': 'Aantal parten',
        'Waarde eigenschap 4': ', '.join(unique_parts),
        'Merken': '',  # User mentioned to ignore this for now
        'Categorieën': ''  # User mentioned to ignore this for now
    }
    import_data.append(parent_product)
    
    # Add the variations
    for i, row in enumerate(laadsheet_data):
        variation = {
            'ID': '',  # Empty ID, WooCommerce will generate it
            'Hoofd': parent_sku,  # Use parent SKU as reference instead of ID
            'Type': 'variation',
            'SKU': row['Artikelnummer'],
            'Naam': main_product_name,
            'Positie': i + 1,
            'Naam eigenschap 1': 'Omschrijving',
            'Waarde eigenschap 1': row['Omschrijving'],
            'Naam eigenschap 2': 'Capaciteit',
            'Waarde eigenschap 2': row['Capaciteit'],
            'Naam eigenschap 3': 'Hijshoogte',
            'Waarde eigenschap 3': row['Hijshoogte'],
            'Naam eigenschap 4': 'Aantal parten',
            'Waarde eigenschap 4': row['Aantal parten'],
            'Merken': '',  # Empty for variation
            'Categorieën': ''  # Empty for variation
        }
        import_data.append(variation)
    
    return import_data

def convert_multiple_laadsheets(input_files, output_file):
    """Convert multiple laadsheet CSV files to a single importsheet bundle"""
    # Separate lists for parent products and variations
    parent_products = []
    variations = []
    
    for file_path in input_files:
        try:
            print(f"Processing file: {file_path}")
            laadsheet_data = read_laadsheet(file_path)
            import_data = convert_single_laadsheet(laadsheet_data)
            
            # Separate parent products and variations
            parent_products.append(import_data[0])  # First item is always the parent
            variations.extend(import_data[1:])      # The rest are variations
            
            # Get the parent SKU for reporting
            parent_sku = import_data[0]['SKU']
            print(f"  Added {len(laadsheet_data)} variations with parent SKU: {parent_sku}")
            
        except Exception as e:
            print(f"Error processing file {file_path}: {str(e)}")
    
    # Combine parents and variations with parents at the top
    all_import_data = parent_products + variations
    
    # Write the combined import sheet data
    if all_import_data:
        fieldnames = [
            'ID', 'Hoofd', 'Type', 'SKU', 'Naam', 'Positie',
            'Naam eigenschap 1', 'Waarde eigenschap 1',
            'Naam eigenschap 2', 'Waarde eigenschap 2',
            'Naam eigenschap 3', 'Waarde eigenschap 3',
            'Naam eigenschap 4', 'Waarde eigenschap 4',
            'Merken', 'Categorieën'
        ]
        
        with open(output_file, 'w', newline='', encoding='utf-8') as file:
            writer = csv.DictWriter(file, fieldnames=fieldnames)
            writer.writeheader()
            writer.writerows(all_import_data)
        
        print(f"\nConversion complete. Added:")
        print(f"  - {len(parent_products)} parent products")
        print(f"  - {len(variations)} variations")
        print(f"  - {len(all_import_data)} total records into {output_file}")
        return output_file
    else:
        print("No data was processed. Output file was not created.")
        return None

def main():
    """Main function to handle command line arguments"""
    import argparse
    
    parser = argparse.ArgumentParser(description='Convert laadsheet CSV files to importsheet format')
    parser.add_argument('input_files', nargs='+', help='Paths to the input laadsheet CSV files or wildcard patterns')
    parser.add_argument('--output', '-o', help='Path to the output importsheet CSV file (default: combined_import.csv)')
    
    args = parser.parse_args()
    
    # Expand any wildcard patterns
    expanded_files = []
    for file_pattern in args.input_files:
        matching_files = glob.glob(file_pattern)
        if matching_files:
            expanded_files.extend(matching_files)
        else:
            expanded_files.append(file_pattern)  # Keep as is if no matches found
    
    # Remove any duplicates while preserving order
    input_files = []
    for f in expanded_files:
        if f not in input_files:
            input_files.append(f)
    
    if not input_files:
        print("No input files found!")
        return
    
    # Determine output filename
    output_file = args.output if args.output else "combined_import.csv"
    
    # Convert the files
    convert_multiple_laadsheets(input_files, output_file)

if __name__ == "__main__":
    main()