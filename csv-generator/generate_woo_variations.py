import csv
import os

# Define the base product
product_name = "YaleERGO 360"
product_sku = "YALE360"

# Define the specific variations from your images
variations = [
    {"id": "#3643", "description": "YaleERGO360", "capacity": "9.000", "height": "6.0", "parts": "3"},
    {"id": "#3641", "description": "YaleERGO365", "capacity": "9.000", "height": "3.0", "parts": "3"},
    {"id": "#3633", "description": "YaleERGO 360 9000", "capacity": "9.000", "height": "1.5", "parts": "3"},
    {"id": "#3642", "description": "YaleERGO360", "capacity": "6.000", "height": "6.0", "parts": "2"},
    {"id": "#3640", "description": "YaleERGO364", "capacity": "6.000", "height": "3.0", "parts": "2"},
    {"id": "#3632", "description": "YaleERGO 360 6000", "capacity": "6.000", "height": "1.5", "parts": "2"},
    {"id": "#3639", "description": "YaleERGO363", "capacity": "3.000", "height": "6.0", "parts": "1"},
    {"id": "#3638", "description": "YaleERGO362", "capacity": "3.000", "height": "3.0", "parts": "1"},
    {"id": "#3631", "description": "YaleERGO 360 3000", "capacity": "3.000", "height": "1.5", "parts": "1"},
    {"id": "#3637", "description": "YaleERGO361", "capacity": "1500", "height": "6.0", "parts": "1"},
    {"id": "#3636", "description": "YaleERGO360", "capacity": "1500", "height": "3.0", "parts": "1"},
    {"id": "#3629", "description": "YaleERGO 360 1500", "capacity": "1500", "height": "1.5", "parts": "1"},
    {"id": "#3635", "description": "YaleERGO360", "capacity": "750", "height": "6.0", "parts": "1"},
    {"id": "#3634", "description": "YaleERGO360", "capacity": "750", "height": "3.0", "parts": "1"},
    {"id": "#3630", "description": "YaleERGO 360 750", "capacity": "750", "height": "1.5", "parts": "1"},
]

# Get unique attributes for the global attributes
unique_descriptions = sorted(set([v["description"] for v in variations]))
unique_capacities = sorted(set([v["capacity"] for v in variations]), key=lambda x: float(x.replace(".", "")))
unique_heights = sorted(set([v["height"] for v in variations]), key=lambda x: float(x.replace(".", "")))
unique_parts = sorted(set([v["parts"] for v in variations]))

# Define CSV headers for WooCommerce import
headers = [
    "Type", "SKU", "Name", "Published", "Is featured?", "Visibility in catalog", 
    "Short description", "Description", "Date sale price starts", "Date sale price ends", 
    "Tax status", "Tax class", "In stock?", "Stock", "Backorders allowed?", 
    "Sold individually?", "Weight (kg)", "Length (cm)", "Width (cm)", "Height (cm)", 
    "Allow customer reviews?", "Purchase note", "Sale price", "Regular price", 
    "Categories", "Tags", "Shipping class", "Images", "Download limit", 
    "Download expiry days", "Parent", "Grouped products", "Upsells", 
    "Cross-sells", "External URL", "Button text", "Position",
    "Attribute 1 name", "Attribute 1 value(s)", "Attribute 1 visible", "Attribute 1 global", "Attribute 1 default", "Attribute 1 used_for_variation",
    "Attribute 2 name", "Attribute 2 value(s)", "Attribute 2 visible", "Attribute 2 global", "Attribute 2 default", "Attribute 2 used_for_variation",
    "Attribute 3 name", "Attribute 3 value(s)", "Attribute 3 visible", "Attribute 3 global", "Attribute 3 default", "Attribute 3 used_for_variation",
    "Attribute 4 name", "Attribute 4 value(s)", "Attribute 4 visible", "Attribute 4 global", "Attribute 4 default", "Attribute 4 used_for_variation"
]

# Create the CSV file
with open('yale_ergo_360_products.csv', 'w', newline='', encoding='utf-8') as file:
    writer = csv.DictWriter(file, fieldnames=headers)
    writer.writeheader()
    
    # Add the parent variable product
    parent_data = {
        "Type": "variable",
        "SKU": product_sku,
        "Name": product_name,
        "Published": 1,
        "Is featured?": 0,
        "Visibility in catalog": "visible",
        "Short description": f"{product_name} lifting equipment",
        "Description": f"Full product description for {product_name}",
        "In stock?": 1,
        "Attribute 1 name": "Omschrijving",
        "Attribute 1 value(s)": " | ".join(unique_descriptions),
        "Attribute 1 visible": 1,
        "Attribute 1 global": 1,
        "Attribute 1 default": "",
        "Attribute 1 used_for_variation": 1,
        "Attribute 2 name": "Capaciteit",
        "Attribute 2 value(s)": " | ".join(unique_capacities),
        "Attribute 2 visible": 1,
        "Attribute 2 global": 1,
        "Attribute 2 default": "",
        "Attribute 2 used_for_variation": 1,
        "Attribute 3 name": "Hijshoogte",
        "Attribute 3 value(s)": " | ".join(unique_heights),
        "Attribute 3 visible": 1,
        "Attribute 3 global": 1,
        "Attribute 3 default": "",
        "Attribute 3 used_for_variation": 1,
        "Attribute 4 name": "Aantal parten",
        "Attribute 4 value(s)": " | ".join(unique_parts),
        "Attribute 4 visible": 1,
        "Attribute 4 global": 1,
        "Attribute 4 default": "",
        "Attribute 4 used_for_variation": 1,
    }
    writer.writerow(parent_data)
    
    # Add each variation
    for index, var in enumerate(variations):
        variation_data = {
            "Type": "variation",
            "SKU": f"{product_sku}-{index+1}",
            "Name": f"{var['description']} - {var['capacity']} - {var['height']} - {var['parts']}",
            "Published": 1,
            "Is featured?": 0,
            "Visibility in catalog": "visible",
            "In stock?": 1,
            "Regular price": "",  # Add your prices here
            "Parent": product_sku,
            "Attribute 1 name": "Omschrijving",
            "Attribute 1 value(s)": var["description"],
            "Attribute 2 name": "Capaciteit",
            "Attribute 2 value(s)": var["capacity"],
            "Attribute 3 name": "Hijshoogte",
            "Attribute 3 value(s)": var["height"],
            "Attribute 4 name": "Aantal parten",
            "Attribute 4 value(s)": var["parts"],
        }
        writer.writerow(variation_data)

print(f"CSV file has been created: {os.path.abspath('yale_ergo_360_products.csv')}")