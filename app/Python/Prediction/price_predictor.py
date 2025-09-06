import numpy as np
import json
import sys

"""
Simple price prediction model for construction projects
This script takes input parameters and calculates the estimated price
"""

def calculate_price(product_id, material_id, finishing_id, kerumitan_id, ketebalan_id, width, height, length, quantity):
    """
    Calculate the estimated price based on input parameters
    
    Args:
        product_id: ID of the product
        material_id: ID of the material
        finishing_id: ID of the finishing
        kerumitan_id: ID of the complexity level
        ketebalan_id: ID of the thickness level
        width: Width in meters
        height: Height in meters
        length: Length in meters (optional)
        quantity: Quantity of items
        
    Returns:
        Estimated price
    """
    # In a real implementation, this would use a trained ML model
    # For now, we'll use a simple formula
    
    # Base price calculation (area * base_rate)
    area = width * height
    if length and length > 0:
        # If length is provided, calculate volume
        area = area * length
    
    # Assume we have base rates for each product type (would come from database)
    product_base_rates = {
        1: 500000,  # Base rate for product 1 per square meter
        2: 750000,  # Base rate for product 2 per square meter
        3: 1000000  # Base rate for product 3 per square meter
    }
    
    # Material multipliers (would come from database)
    material_multipliers = {
        1: 1.0,   # Standard material
        2: 1.5,   # Premium material
        3: 2.0    # Luxury material
    }
    
    # Finishing multipliers (would come from database)
    finishing_multipliers = {
        1: 1.0,   # Basic finishing
        2: 1.3,   # Standard finishing
        3: 1.6    # Premium finishing
    }
    
    # Complexity multipliers (would come from database)
    complexity_multipliers = {
        1: 1.0,   # Simple design
        2: 1.5,   # Moderate complexity
        3: 2.0    # High complexity
    }
    
    # Thickness multipliers (would come from database)
    thickness_multipliers = {
        1: 1.0,   # Thin
        2: 1.2,   # Medium
        3: 1.5    # Thick
    }
    
    # Get base rate for the product (default to first product if not found)
    base_rate = product_base_rates.get(product_id, product_base_rates[1])
    
    # Apply multipliers
    material_factor = material_multipliers.get(material_id, 1.0)
    finishing_factor = finishing_multipliers.get(finishing_id, 1.0)
    complexity_factor = complexity_multipliers.get(kerumitan_id, 1.0)
    thickness_factor = thickness_multipliers.get(ketebalan_id, 1.0)
    
    # Calculate price
    price = base_rate * area * material_factor * finishing_factor * complexity_factor * thickness_factor
    
    # Apply quantity
    total_price = price * quantity
    
    # Add some randomness to simulate real-world variation (±5%)
    variation = np.random.uniform(0.95, 1.05)
    total_price = total_price * variation
    
    return round(total_price, 2)


def main():
    """
    Main function to process input from command line and return price prediction
    """
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No input provided"}))
        sys.exit(1)
        
    try:
        # Parse input JSON
        input_data = json.loads(sys.argv[1])
        
        # Extract parameters
        product_id = int(input_data.get('product_id', 1))
        material_id = int(input_data.get('material_id', 1))
        finishing_id = int(input_data.get('finishing_id', 1))
        kerumitan_id = int(input_data.get('kerumitan_id', 1))
        ketebalan_id = int(input_data.get('ketebalan_id', 1))
        width = float(input_data.get('width', 1.0))
        height = float(input_data.get('height', 1.0))
        length = float(input_data.get('length', 0.0)) if input_data.get('length') else 0.0
        quantity = int(input_data.get('quantity', 1))
        
        # Calculate price
        price = calculate_price(
            product_id, material_id, finishing_id, kerumitan_id, ketebalan_id,
            width, height, length, quantity
        )
        
        # Return result as JSON
        result = {
            "success": True,
            "price": price,
            "currency": "IDR"
        }
        print(json.dumps(result))
        
    except Exception as e:
        print(json.dumps({"error": str(e)}))
        sys.exit(1)


if __name__ == "__main__":
    main()