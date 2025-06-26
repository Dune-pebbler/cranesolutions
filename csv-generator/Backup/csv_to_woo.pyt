import csv
import os

def create_woocommerce_update(input_file, output_file, parent_sku, preserve_name=True):
    """
    Create WooCommerce import file to update attributes and variations only.
    Assumes the main product is already set to variable type.
    """
    
    # Read input CSV and clean column names
    try:
        with open(input_file, 'r', encoding='utf-8') as file:
            reader = csv.DictReader(file)
            raw_data = list(reader)
        
        # Clean column names and data - remove leading/trailing spaces
        input_data = []
        for row in raw_data:
            clean_row = {}
            for key, value in row.items():
                clean_key = key.strip() if key else key
                clean_value = value.strip() if isinstance(value, str) else value
                clean_row[clean_key] = clean_value
            input_data.append(clean_row)
        
        print(f"Loaded {len(input_data)} variations from {input_file}")
        
    except FileNotFoundError:
        print(f"Error: File {input_file} not found")
        return
    except Exception as e:
        print(f"Error reading CSV: {e}")
        return
    
    # WooCommerce headers (only include fields we want to update)
    if preserve_name:
        headers = [
            'ID', 'Type', 'SKU', 'Parent', 'Regular price', 'In stock?', 'Manage stock?',
            'Attribute 1 name', 'Attribute 1 value(s)', 'Attribute 1 visible', 'Attribute 1 global',
            'Attribute 2 name', 'Attribute 2 value(s)', 'Attribute 2 visible', 'Attribute 2 global',
            'Attribute 3 name', 'Attribute 3 value(s)', 'Attribute 3 visible', 'Attribute 3 global',
            'Attribute 4 name', 'Attribute 4 value(s)', 'Attribute 4 visible', 'Attribute 4 global'
        ]
    else:
        headers = [
            'ID', 'Type', 'SKU', 'Name', 'Parent', 'Regular price', 'In stock?', 'Manage stock?',
            'Attribute 1 name', 'Attribute 1 value(s)', 'Attribute 1 visible', 'Attribute 1 global',
            'Attribute 2 name', 'Attribute 2 value(s)', 'Attribute 2 visible', 'Attribute 2 global',
            'Attribute 3 name', 'Attribute 3 value(s)', 'Attribute 3 visible', 'Attribute 3 global',
            'Attribute 4 name', 'Attribute 4 value(s)', 'Attribute 4 visible', 'Attribute 4 global'
        ]
    
    rows = []
    
    # Get all unique attribute values (column names are now cleaned)
    artikel_numbers = list(set(str(row['Artikelnummer']) for row in input_data if row.get('Artikelnummer')))
    capaciteiten = list(set(str(row['Capaciteit']) for row in input_data if row.get('Capaciteit')))
    hijshoogtes = list(set(str(row['Hijshoogte']) for row in input_data if row.get('Hijshoogte')))
    aantal_parten = list(set(str(row['Aantal parten']) for row in input_data if row.get('Aantal parten')))
    
    # Update main product with all attribute values using SKU
    if preserve_name:
        main_product = {
            'ID': '',
            'Type': 'variable',
            'SKU': parent_sku,
            'Parent': '',
            'Regular price': '',
            'In stock?': '',
            'Manage stock?': '',
            'Attribute 1 name': 'Artikelnummer',
            'Attribute 1 value(s)': ', '.join(sorted(artikel_numbers)),
            'Attribute 1 visible': '1',
            'Attribute 1 global': '0',
            'Attribute 2 name': 'Capaciteit',
            'Attribute 2 value(s)': ', '.join(sorted(capaciteiten)),
            'Attribute 2 visible': '1',
            'Attribute 2 global': '0',
            'Attribute 3 name': 'Hijshoogte',
            'Attribute 3 value(s)': ', '.join(sorted(hijshoogtes)),
            'Attribute 3 visible': '1',
            'Attribute 3 global': '0',
            'Attribute 4 name': 'Aantal parten',
            'Attribute 4 value(s)': ', '.join(sorted(aantal_parten)),
            'Attribute 4 visible': '1',
            'Attribute 4 global': '0'
        }
    else:
        main_product = {
            'ID': '',
            'Type': 'variable',
            'SKU': parent_sku,
            'Name': 'Yale ERGO Series',
            'Parent': '',
            'Regular price': '',
            'In stock?': '',
            'Manage stock?': '',
            'Attribute 1 name': 'Artikelnummer',
            'Attribute 1 value(s)': ', '.join(sorted(artikel_numbers)),
            'Attribute 1 visible': '1',
            'Attribute 1 global': '0',
            'Attribute 2 name': 'Capaciteit',
            'Attribute 2 value(s)': ', '.join(sorted(capaciteiten)),
            'Attribute 2 visible': '1',
            'Attribute 2 global': '0',
            'Attribute 3 name': 'Hijshoogte',
            'Attribute 3 value(s)': ', '.join(sorted(hijshoogtes)),
            'Attribute 3 visible': '1',
            'Attribute 3 global': '0',
            'Attribute 4 name': 'Aantal parten',
            'Attribute 4 value(s)': ', '.join(sorted(aantal_parten)),
            'Attribute 4 visible': '1',
            'Attribute 4 global': '0'
        }
    
    rows.append(main_product)
    
    # Create variations
    for row in input_data:
        if preserve_name:
            variation = {
                'ID': '',
                'Type': 'variation',
                'SKU': f'YALE-ERGO-{row["Artikelnummer"]}',
                'Parent': parent_sku,
                'Regular price': '0',
                'In stock?': '1',
                'Manage stock?': '0',
                'Attribute 1 name': 'Artikelnummer',
                'Attribute 1 value(s)': str(row['Artikelnummer']),
                'Attribute 1 visible': '1',
                'Attribute 1 global': '0',
                'Attribute 2 name': 'Capaciteit',
                'Attribute 2 value(s)': str(row['Capaciteit']),
                'Attribute 2 visible': '1',
                'Attribute 2 global': '0',
                'Attribute 3 name': 'Hijshoogte',
                'Attribute 3 value(s)': str(row['Hijshoogte']),
                'Attribute 3 visible': '1',
                'Attribute 3 global': '0',
                'Attribute 4 name': 'Aantal parten',
                'Attribute 4 value(s)': str(row['Aantal parten']),
                'Attribute 4 visible': '1',
                'Attribute 4 global': '0'
            }
        else:
            variation = {
                'ID': '',
                'Type': 'variation',
                'SKU': f'YALE-ERGO-{row["Artikelnummer"]}',
                'Name': f'Yale ERGO - {row["Omschrijving"]}',
                'Parent': parent_sku,
                'Regular price': '0',
                'In stock?': '1',
                'Manage stock?': '0',
                'Attribute 1 name': 'Artikelnummer',
                'Attribute 1 value(s)': str(row['Artikelnummer']),
                'Attribute 1 visible': '1',
                'Attribute 1 global': '0',
                'Attribute 2 name': 'Capaciteit',
                'Attribute 2 value(s)': str(row['Capaciteit']),
                'Attribute 2 visible': '1',
                'Attribute 2 global': '0',
                'Attribute 3 name': 'Hijshoogte',
                'Attribute 3 value(s)': str(row['Hijshoogte']),
                'Attribute 3 visible': '1',
                'Attribute 3 global': '0',
                'Attribute 4 name': 'Aantal parten',
                'Attribute 4 value(s)': str(row['Aantal parten']),
                'Attribute 4 visible': '1',
                'Attribute 4 global': '0'
            }
        
        rows.append(variation)
    
    # Write output
    try:
        with open(output_file, 'w', newline='', encoding='utf-8') as file:
            writer = csv.DictWriter(file, fieldnames=headers)
            writer.writeheader()
            writer.writerows(rows)
        
        print(f"Created update file: {output_file}")
        print(f"Will update product with SKU {parent_sku} and create {len(input_data)} variations")
        
    except Exception as e:
        print(f"Error saving file: {e}")

def main():
    input_file = 'YaleERGO.csv'
    output_file = 'woocommerce_update.csv'
    
    if not os.path.exists(input_file):
        print(f"Input file '{input_file}' not found.")
        return
    
    print("Do you want to preserve the existing product name? (y/n):")
    preserve_name = input().lower().strip() == 'y'
    
    print("Enter the SKU of your variable product:")
    try:
        parent_sku = input().strip()
        if not parent_sku:
            print("SKU cannot be empty")
            return
    except:
        print("Invalid SKU")
        return
    
    create_woocommerce_update(input_file, output_file, parent_sku, preserve_name)
    
    print("\nIMPORTANT:")
    print("1. Make sure your product is already set to 'Variable product' type")
    print("2. Give your product a unique SKU in WooCommerce first")
    print("3. Upload the CSV file via WooCommerce > Products > Import")
    print("4. CHECK 'Update existing products' - we're using SKU to match products")
    print("5. The main product will be updated, variations will be created with €0 price")

if __name__ == "__main__":
    main()