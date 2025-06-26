import csv
import os
import glob

def read_and_clean_csv(input_file):
    """Read CSV and clean column names"""
    try:
        with open(input_file, 'r', encoding='utf-8') as file:
            reader = csv.DictReader(file)
            raw_data = list(reader)
        
        # Clean column names and data - remove leading/trailing spaces
        clean_data = []
        for row in raw_data:
            clean_row = {}
            for key, value in row.items():
                clean_key = key.strip() if key else key
                clean_value = value.strip() if isinstance(value, str) else value
                clean_row[clean_key] = clean_value
            clean_data.append(clean_row)
        
        return clean_data
        
    except FileNotFoundError:
        print(f"Error: File {input_file} not found")
        return None
    except Exception as e:
        print(f"Error reading CSV {input_file}: {e}")
        return None

def process_single_product(input_data, parent_sku):
    """Process data for a single product and return parent + variations"""
    
    if not input_data:
        return None, []
    
    # Auto-detect columns from first row (ignore empty column names)
    all_columns = list(input_data[0].keys())
    valid_columns = [col for col in all_columns if col and col.strip()]
    
    print(f"  Detected {len(valid_columns)} valid columns: {valid_columns}")
    
    # Get all unique attribute values for each valid column
    attributes = []
    for col in valid_columns:
        unique_values = list(set(str(row[col]) for row in input_data if row.get(col) and str(row[col]).strip()))
        attributes.append({
            'name': col,
            'values': [v for v in unique_values if v and v.strip()]  # Remove empty values
        })
    
    # Create main product (parent) - minimal, only attributes
    parent_product = {
        'ID': '',
        'Type': 'variable',
        'SKU': parent_sku,
        'Parent': '',
        'Regular price': '',  # Empty for variable products
    }
    
    # Add all attributes dynamically (no limit)
    for i, attr in enumerate(attributes, 1):
        parent_product[f'Attribute {i} name'] = attr['name']
        parent_product[f'Attribute {i} value(s)'] = ', '.join(sorted(attr['values']))
        parent_product[f'Attribute {i} visible'] = '1'
        parent_product[f'Attribute {i} global'] = '0'
    
    # Create variations - minimal, only attributes and €0 price
    variations = []
    for j, row in enumerate(input_data):
        variation = {
            'ID': '',
            'Type': 'variation',
            'SKU': f'{parent_sku}-{j+1:03d}',
            'Parent': parent_sku,
            'Regular price': '0',  # €0 price so it can be ordered
        }
        
        # Add all attributes dynamically (no limit)
        for i, attr in enumerate(attributes, 1):
            col_name = attr['name']
            variation[f'Attribute {i} name'] = col_name
            variation[f'Attribute {i} value(s)'] = str(row[col_name])
            variation[f'Attribute {i} visible'] = '1'
            variation[f'Attribute {i} global'] = '0'
        
        variations.append(variation)
    
    return parent_product, variations

def create_multiple_products_update(input_configs, output_file):
    """
    Create WooCommerce import file for multiple products
    input_configs: list of (csv_file, parent_sku) tuples
    """
    
    # Minimal headers - only what we need for attributes and variations
    base_headers = [
        'ID', 'Type', 'SKU', 'Parent', 'Regular price'
    ]
    
    # We'll determine max attributes needed after processing all files
    max_attributes = 0
    all_parents = []
    all_variations = []
    
    # First pass: process all products to find max attributes needed
    for csv_file, parent_sku in input_configs:
        print(f"Processing {csv_file} with SKU {parent_sku}")
        
        input_data = read_and_clean_csv(csv_file)
        if not input_data:
            continue
            
        parent_product, variations = process_single_product(input_data, parent_sku)
        if parent_product is None:
            continue
            
        all_parents.append(parent_product)
        all_variations.extend(variations)
        
        # Count attributes in this product
        attr_count = len([col for col in input_data[0].keys() if col and col.strip()])
        max_attributes = max(max_attributes, attr_count)
        
        print(f"  Added 1 parent + {len(variations)} variations ({attr_count} attributes)")
    
    # Build complete headers with all needed attribute columns
    headers = base_headers.copy()
    for i in range(1, max_attributes + 1):
        headers.extend([
            f'Attribute {i} name',
            f'Attribute {i} value(s)',
            f'Attribute {i} visible',
            f'Attribute {i} global'
        ])
    
    print(f"Total attributes needed: {max_attributes}")
    
    # Combine: all parents first, then all variations (like your old code)
    all_rows = all_parents + all_variations
    
    # Write output
    try:
        with open(output_file, 'w', newline='', encoding='utf-8') as file:
            writer = csv.DictWriter(file, fieldnames=headers)
            writer.writeheader()
            writer.writerows(all_rows)
        
        print(f"\nCreated combined update file: {output_file}")
        print(f"Total: {len(all_parents)} parents + {len(all_variations)} variations = {len(all_rows)} rows")
        
    except Exception as e:
        print(f"Error saving file: {e}")

def main():
    output_file = 'woocommerce_multiple_update.csv'
    
    print("=== WooCommerce Multiple Product Updater ===")
    print("This will update multiple variable products with attributes and variations")
    print()
    
    # Get input configurations
    input_configs = []
    
    while True:
        print(f"\n--- Product {len(input_configs) + 1} ---")
        
        # Get CSV file
        csv_file = input("Enter CSV file path (or press Enter to finish): ").strip()
        if not csv_file:
            break
            
        if not os.path.exists(csv_file):
            print(f"File {csv_file} not found. Try again.")
            continue
        
        # Get parent SKU
        parent_sku = input("Enter the parent product SKU: ").strip()
        if not parent_sku:
            print("SKU cannot be empty. Try again.")
            continue
        
        input_configs.append((csv_file, parent_sku))
        print(f"Added: {csv_file} -> {parent_sku}")
    
    if not input_configs:
        print("No products configured. Exiting.")
        return
    
    # Process all products
    create_multiple_products_update(input_configs, output_file)
    
    print(f"\n=== Import Instructions ===")
    print("1. Make sure all your products are set to 'Variable product' type")
    print("2. Make sure all products have their correct SKUs set")
    print("3. First import: CHECK 'Update existing products' (updates parents with attributes)")
    print("4. Second import: UNCHECK 'Update existing products' (creates variations with €0 price)")
    print("5. All variations will be orderable at €0 price")

if __name__ == "__main__":
    main()