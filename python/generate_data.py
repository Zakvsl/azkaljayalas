#!/usr/bin/env python3
import pandas as pd
import numpy as np
from typing import List, Dict, Tuple
import json
from sklearn.preprocessing import LabelEncoder

def generate_training_data(num_samples: int = 1000) -> Tuple[pd.DataFrame, Dict]:
    """Generate synthetic training data for price estimation."""
    np.random.seed(42)  # For reproducibility
    
    # Define possible values
    project_types = ['canopy', 'fence', 'gate', 'railing', 'stairs', 'truss']
    material_types = ['stainless_steel', 'mild_steel', 'galvanized_steel', 'aluminum']
    additional_features = ['painting', 'welding', 'installation', 'polishing', 'design']
    
    # Base prices per unit area (per square meter)
    base_prices = {
        'stainless_steel': {'min': 800, 'max': 1200},
        'mild_steel': {'min': 400, 'max': 600},
        'galvanized_steel': {'min': 500, 'max': 800},
        'aluminum': {'min': 600, 'max': 1000}
    }
    
    # Project type multipliers
    project_multipliers = {
        'canopy': {'min': 1.2, 'max': 1.5},
        'fence': {'min': 0.8, 'max': 1.2},
        'gate': {'min': 1.0, 'max': 1.3},
        'railing': {'min': 0.9, 'max': 1.2},
        'stairs': {'min': 1.3, 'max': 1.6},
        'truss': {'min': 1.4, 'max': 1.8}
    }
    
    # Generate random data
    data = []
    for _ in range(num_samples):
        # Select random types
        project_type = np.random.choice(project_types)
        material_type = np.random.choice(material_types)
        
        # Generate dimensions (in meters)
        length = np.random.uniform(1, 10)  # 1-10 meters
        width = np.random.uniform(1, 5)   # 1-5 meters
        thickness = np.random.uniform(0.001, 0.005)  # 1-5 mm
        
        # Calculate area
        area = length * width
        
        # Select random additional features (0-5 features)
        num_features = np.random.randint(0, len(additional_features) + 1)
        selected_features = np.random.choice(additional_features, num_features, replace=False)
        
        # Calculate base price
        base_price = np.random.uniform(
            base_prices[material_type]['min'],
            base_prices[material_type]['max']
        )
        
        # Apply project multiplier
        multiplier = np.random.uniform(
            project_multipliers[project_type]['min'],
            project_multipliers[project_type]['max']
        )
        
        # Calculate additional costs for features
        feature_cost = len(selected_features) * np.random.uniform(100, 300)
        
        # Calculate final price with some random variation
        price = (base_price * area * multiplier + feature_cost) * \
                np.random.uniform(0.95, 1.05)  # Add 5% random variation
        
        # Create row
        row = {
            'project_type': project_type,
            'material_type': material_type,
            'length': length,
            'width': width,
            'thickness': thickness,
            'area': area
        }
        
        # Add feature columns
        for feature in additional_features:
            row[feature] = 1 if feature in selected_features else 0
        
        row['price'] = price
        
        data.append(row)
    
    # Convert to DataFrame
    df = pd.DataFrame(data)
    
    # Create label encoders for categorical columns
    project_encoder = LabelEncoder()
    material_encoder = LabelEncoder()
    
    # Encode categorical columns
    df['project_type'] = project_encoder.fit_transform(df['project_type'])
    df['material_type'] = material_encoder.fit_transform(df['material_type'])
    
    # Save encoders to JSON for later use
    encoders = {
        'project_type': {
            'classes': project_encoder.classes_.tolist(),
            'mappings': {k: int(v) for k, v in dict(zip(project_encoder.classes_, project_encoder.transform(project_encoder.classes_))).items()}
        },
        'material_type': {
            'classes': material_encoder.classes_.tolist(),
            'mappings': {k: int(v) for k, v in dict(zip(material_encoder.classes_, material_encoder.transform(material_encoder.classes_))).items()}
        }
    }
    
    return df, encoders

def main():
    """Generate training data and save to CSV."""
    try:
        import os
        
        # Generate data
        print("Generating training data...")
        df, encoders = generate_training_data(1000)
        
        # Get absolute path
        script_dir = os.path.dirname(os.path.abspath(__file__))
        storage_dir = os.path.join(script_dir, '..', 'storage', 'app', 'data')
        os.makedirs(storage_dir, exist_ok=True)
        
        # Save to CSV
        data_path = os.path.join(storage_dir, 'training_data.csv')
        df.to_csv(data_path, index=False)
        
        # Save encoders
        encoders_path = os.path.join(storage_dir, 'label_encoders.json')
        with open(encoders_path, 'w') as f:
            json.dump(encoders, f, indent=2)
            
        print(f"Data saved to {data_path}")
        
        # Print summary statistics
        print("\nSummary Statistics:")
        print(json.dumps({
            'samples': len(df),
            'features': len(df.columns) - 1,  # Excluding price column
            'price_stats': {
                'min': float(df['price'].min()),
                'max': float(df['price'].max()),
                'mean': float(df['price'].mean()),
                'median': float(df['price'].median()),
                'std': float(df['price'].std())
            }
        }, indent=2))
        
    except Exception as e:
        print(json.dumps({
            'status': 'error',
            'message': str(e)
        }))

if __name__ == '__main__':
    main()