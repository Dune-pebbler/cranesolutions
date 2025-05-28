import csv
import random
import os
import re
import datetime

def generate_unique_id():
    """Generate a unique numeric product ID for WooCommerce"""
    # Use a timestamp plus random number approach to ensure uniqueness
    # Format: prefix (99) + YYMMDDHHMM + 4-digit random number
    prefix = 99  # A prefix to identify laadsheet imported products
    timestamp = int(datetime.datetime.now().strftime("%y%m%d%H%M"))
    random_num = random.randint(1000, 9999)
    
    # Combine them into a single integer
    unique_id = int(f"{prefix}{timestamp}{random_num}")
    return unique_id

def extract_product_name(description):
    """Extract the product name from the description"""
    # Remove any numeric part at the end of the description
    # For example "YaleERGO 360 1500" -> "YaleERGO 360"
    name = re.sub(r'\s+\d+$', '', description)
    return name

def convert_laadsheet_to_importsheet(input_file, output_file):
    """Convert a laadsheet CSV to importsheet format"""
    # Read the laadsheet data
    with open(input_file, 'r', encoding='utf-8') as file:
        reader = csv.DictReader(file)
        laadsheet_data = list(reader)
    
    # Check if the file has the expected columns
    required_columns = ['Artikelnummer', 'Omschrijving', 'Capaciteit', 'Hijshoogte', 'Aantal parten']
    if 'Artikelnummer ' in laadsheet_data[0]:  # Handle the space in YaleERGO column name
        laadsheet_data = [{k.strip(): v for k, v in row.items()} for row in laadsheet_data]
        for row in laadsheet_data:
            if 'Artikelnummer ' in row:
                row['Artikelnummer'] = row.pop('Artikelnummer ')
    
    # Ensure all required columns are present
    for row in laadsheet_data:
        for col in required_columns:
            if col not in row:
                raise ValueError(f"Missing required column '{col}' in the laadsheet")
    
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
    
    # Add the parent product
    parent_id = generate_unique_id()
    parent_product = {
        'ID': parent_id,
        'Hoofd': '',  # Empty for parent
        'Type': 'variable',
        'SKU': '',  # Empty for parent
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
            'ID': '',  # Empty for variation
            'Hoofd': parent_id,
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
    
    # Write the import sheet data
    with open(output_file, 'w', newline='', encoding='utf-8') as file:
        fieldnames = [
            'ID', 'Hoofd', 'Type', 'SKU', 'Naam', 'Positie',
            'Naam eigenschap 1', 'Waarde eigenschap 1',
            'Naam eigenschap 2', 'Waarde eigenschap 2',
            'Naam eigenschap 3', 'Waarde eigenschap 3',
            'Naam eigenschap 4', 'Waarde eigenschap 4',
            'Merken', 'Categorieën'
        ]
        writer = csv.DictWriter(file, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(import_data)
    
    print(f"Conversion complete. Imported {len(laadsheet_data)} variations with parent ID: {parent_id}")
    return output_file

def main():
    """Main function to handle command line arguments"""
    import argparse
    parser = argparse.ArgumentParser(description='Convert a laadsheet CSV to importsheet format')
    parser.add_argument('input_file', help='Path to the input laadsheet CSV file')
    parser.add_argument('--output', '-o', help='Path to the output importsheet CSV file (default: output_[input_filename])')
    
    args = parser.parse_args()
    
    # Determine output filename
    if args.output:
        output_file = args.output
    else:
        input_basename = os.path.basename(args.input_file)
        output_file = f"output_{input_basename}"
    
    # Convert the file
    convert_laadsheet_to_importsheet(args.input_file, output_file)

if __name__ == "__main__":
    main()