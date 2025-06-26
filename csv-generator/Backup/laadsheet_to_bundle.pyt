import csv
import random
import os
import re
import datetime

def generate_unique_id():
    """Generate a unique numeric product ID for WooCommerce"""
    prefix = 99
    timestamp = int(datetime.datetime.now().strftime("%y%m%d%H%M"))
    random_num = random.randint(1000, 9999)
    unique_id = int(f"{prefix}{timestamp}{random_num}")
    return unique_id

def extract_product_name(description):
    """Extract the product name from the description"""
    name = re.sub(r'\s+\d+$', '', description)
    return name

def clean_column_names(data):
    """Clean column names by removing extra spaces"""
    cleaned_data = []
    for row in data:
        cleaned_row = {k.strip(): v for k, v in row.items()}
        cleaned_data.append(cleaned_row)
    return cleaned_data

def detect_csv_structure(data):
    """Automatically detect CSV structure and column mappings"""
    if not data:
        raise ValueError("No data found in the input file")
    
    columns = list(data[0].keys())
    print(f"Detected columns: {columns}")
    
    # Find SKU column (usually contains "Artikelnummer" or "Type")
    sku_column = None
    for col in columns:
        if 'artikelnummer' in col.lower() or col.lower() == 'type':
            sku_column = col
            break
    
    if not sku_column:
        raise ValueError(f"Could not find SKU column. Available columns: {columns}")
    
    # Find description column (usually "Omschrijving" or similar)
    description_column = None
    for col in columns:
        if 'omschrijving' in col.lower():
            description_column = col
            break
    
    # If no description column, use the first text-heavy column or SKU column
    if not description_column:
        description_column = sku_column
    
    # All other columns become properties (excluding SKU and description if different)
    property_columns = []
    for col in columns:
        if col != sku_column and col != description_column:
            property_columns.append(col)
    
    # Always include description as a property if it exists and is different from SKU
    if description_column != sku_column:
        property_columns.insert(0, description_column)
    
    result = {
        'sku_column': sku_column,
        'description_column': description_column, 
        'property_columns': property_columns
    }
    
    print(f"Auto-detected structure:")
    print(f"  SKU column: {sku_column}")
    print(f"  Description column: {description_column}")
    print(f"  Property columns: {property_columns}")
    
    return result

def convert_laadsheet_to_importsheet(input_file, output_file):
    """Convert a laadsheet CSV to importsheet format"""
    
    # Read the input data
    with open(input_file, 'r', encoding='utf-8') as file:
        reader = csv.DictReader(file)
        raw_data = list(reader)
    
    if not raw_data:
        raise ValueError("Input file is empty")
    
    # Clean column names and detect structure
    data = clean_column_names(raw_data)
    structure = detect_csv_structure(data)
    
    # Extract unique values for each property
    unique_values = {}
    for col in structure['property_columns']:
        unique_vals = list(set(str(row[col]).strip() for row in data if row[col] and str(row[col]).strip()))
        unique_values[col] = unique_vals
    
    # Generate main product name from description
    desc_col = structure['description_column']
    descriptions = [row[desc_col] for row in data if row[desc_col]]
    if descriptions:
        main_product_name = extract_product_name(descriptions[0])
    else:
        main_product_name = "Product"
    
    # Create import data
    import_data = []
    
    # Add parent product
    parent_id = generate_unique_id()
    parent_product = {
        'ID': parent_id,
        'Hoofd': '',
        'Type': 'variable',
        'SKU': '',
        'Naam': main_product_name,
        'Positie': 0,
        'Merken': '',
        'Categorieën': ''
    }
    
    # Add properties to parent
    for i, col in enumerate(structure['property_columns'], 1):
        parent_product[f'Naam eigenschap {i}'] = col
        parent_product[f'Waarde eigenschap {i}'] = ', '.join(unique_values[col])
    
    import_data.append(parent_product)
    
    # Add variations
    sku_col = structure['sku_column']
    for i, row in enumerate(data):
        variation = {
            'ID': '',
            'Hoofd': parent_id,
            'Type': 'variation',
            'SKU': str(row[sku_col]).strip(),
            'Naam': main_product_name,
            'Positie': i + 1,
            'Merken': '',
            'Categorieën': ''
        }
        
        # Add properties to variation
        for j, col in enumerate(structure['property_columns'], 1):
            variation[f'Naam eigenschap {j}'] = col
            variation[f'Waarde eigenschap {j}'] = str(row[col]).strip()
        
        import_data.append(variation)
    
    # Write output file
    max_props = len(structure['property_columns'])
    fieldnames = [
        'ID', 'Hoofd', 'Type', 'SKU', 'Naam', 'Positie'
    ]
    
    # Add property fields
    for i in range(1, max_props + 1):
        fieldnames.extend([f'Naam eigenschap {i}', f'Waarde eigenschap {i}'])
    
    fieldnames.extend(['Merken', 'Categorieën'])
    
    with open(output_file, 'w', newline='', encoding='utf-8') as file:
        writer = csv.DictWriter(file, fieldnames=fieldnames)
        writer.writeheader()
        writer.writerows(import_data)
    
    print(f"\nConversion complete!")
    print(f"- Input: {len(data)} variations")
    print(f"- Output: {output_file}")  
    print(f"- Parent ID: {parent_id}")
    print(f"- Properties: {structure['property_columns']}")
    
    return output_file

def main():
    """Main function"""
    import argparse
    parser = argparse.ArgumentParser(description='Convert laadsheet CSV to importsheet format')
    parser.add_argument('input_file', help='Input CSV file path')
    parser.add_argument('--output', '-o', help='Output CSV file path')
    
    args = parser.parse_args()
    
    # Determine output filename
    if args.output:
        output_file = args.output
    else:
        input_basename = os.path.basename(args.input_file)
        name, ext = os.path.splitext(input_basename)
        output_file = f"output_{name}{ext}"
    
    try:
        convert_laadsheet_to_importsheet(args.input_file, output_file)
    except Exception as e:
        print(f"Error: {e}")
        return 1

if __name__ == "__main__":
    main()