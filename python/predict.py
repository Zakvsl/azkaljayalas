#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Single Prediction Script for Laravel Integration
Menerima input JSON dan mengembalikan prediksi harga
"""

import sys
import io
import json
import joblib
import pandas as pd
import numpy as np
from pathlib import Path

# Set UTF-8 encoding for stdout
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

def load_model():
    """Load trained model"""
    model_path = Path(__file__).parent / 'model_pipeline.joblib'
    if not model_path.exists():
        raise FileNotFoundError(f"Model not found at {model_path}")
    return joblib.load(model_path)

def prepare_input(data):
    """
    Prepare input to match trained model format
    Handles both Laravel format and normal format
    Column names: lowercase with underscores
    """
    # Check if Laravel format (project_type, material_type, dimensions)
    if 'project_type' in data:
        # Laravel format - convert to expected format
        dimensions = data.get('dimensions', {})
        area = dimensions.get('length', 0) * dimensions.get('width', 0)
        material = data.get('material_type', 'Hollow')
        profile = data.get('additional_features', {}).get('profile_size', '4x4')
        
        # Auto-calculate upah tenaga ahli (Stainless = 200k, Hollow = 100k)
        upah_rate = 200000 if 'stainless' in material.lower() else 100000
        upah = upah_rate * area
        
        renamed_data = {
            'produk': data.get('project_type', ''),
            'jumlah_unit': 1,
            'jumlah_lubang': data.get('additional_features', {}).get('holes', 0),
            'ukuran': area,
            'jenis_material': material,
            'ketebalan_material': dimensions.get('thickness', 0.8),
            'finishing': data.get('additional_features', {}).get('finishing', 'Cat Biasa'),
            'kerumitan_desain': data.get('additional_features', {}).get('complexity', 'Sederhana'),
            'metode_hitung': 'PER-M2' if area > 0 else 'PER-LUBANG',
            'profile_size': profile,
            'upah_tenaga_ahli': upah
        }
    else:
        # Normal format - handle both ukuran_m2 (area) and ukuran_m (length for Railing)
        material = data.get('jenis_material', data.get('Jenis_Material', 'Hollow'))
        produk = data.get('produk', data.get('Produk', ''))
        
        # Determine ukuran based on product type
        if produk == 'Railing':
            # Railing uses ukuran_m (length in meters)
            # Accept both ukuran_m and ukuran_m2 for Laravel compatibility
            area = data.get('ukuran_m', data.get('ukuran_m2', data.get('ukuran', 0)))
        else:
            # Others use ukuran_m2 (area in square meters)
            area = data.get('ukuran_m2', data.get('ukuran', 0))
        
        profile = data.get('profile_size', data.get('Profile_Size', '4x4'))
        
        # Auto-calculate upah if not provided (Hollow Stainless or Pipa Stainless = 200k, Hollow = 100k)
        upah_rate = 200000 if 'stainless' in material.lower() else 100000
        upah = data.get('upah_tenaga_ahli', data.get('Upah_Tenaga_Ahli', upah_rate * area))
        
        renamed_data = {
            'produk': produk,
            'jumlah_unit': data.get('jumlah_unit', data.get('Jumlah_Unit', 1)),
            'jumlah_lubang': data.get('jumlah_lubang', data.get('Jumlah_Lubang', 0)),
            'ukuran': area,
            'jenis_material': material,
            'ketebalan_material': data.get('ketebalan_mm', data.get('ketebalan_material', data.get('Ketebalan_mm', 0.8))),
            'finishing': data.get('finishing', data.get('Finishing', 'Cat Biasa')),
            'kerumitan_desain': data.get('kerumitan_desain', data.get('Kerumitan_Desain', 'Sederhana')),
            'metode_hitung': data.get('metode_hitung', data.get('Metode_Hitung', 'PER-M2')),
            'profile_size': profile,
            'upah_tenaga_ahli': upah
        }
    
    # Create DataFrame with single row
    df = pd.DataFrame([renamed_data])
    
    # Ensure numeric columns are float/int
    numeric_columns = ['jumlah_unit', 'jumlah_lubang', 'ukuran', 'ketebalan_material', 'upah_tenaga_ahli']
    for col in numeric_columns:
        if col in df.columns:
            df[col] = pd.to_numeric(df[col], errors='coerce').fillna(0)
    
    # Feature engineering (MUST match train_model.py exactly!)
    
    # ========== PRICE TABLE BASED FEATURES ==========
    # 1. Material Base Price Lookup (YEAR-BASED)
    material_base_price_map = {
        # Pagar
        ('Pagar', 2019, 'Hollow'): 500000, ('Pagar', 2019, 'Hollow Stainless'): 1000000, ('Pagar', 2019, 'Pipa Stainless'): 850000,
        ('Pagar', 2020, 'Hollow'): 500000, ('Pagar', 2020, 'Hollow Stainless'): 1050000, ('Pagar', 2020, 'Pipa Stainless'): 880000,
        ('Pagar', 2021, 'Hollow'): 500000, ('Pagar', 2021, 'Hollow Stainless'): 1100000, ('Pagar', 2021, 'Pipa Stainless'): 900000,
        ('Pagar', 2022, 'Hollow'): 530000, ('Pagar', 2022, 'Hollow Stainless'): 1130000, ('Pagar', 2022, 'Pipa Stainless'): 920000,
        ('Pagar', 2023, 'Hollow'): 550000, ('Pagar', 2023, 'Hollow Stainless'): 1150000, ('Pagar', 2023, 'Pipa Stainless'): 940000,
        ('Pagar', 2024, 'Hollow'): 550000, ('Pagar', 2024, 'Hollow Stainless'): 1180000, ('Pagar', 2024, 'Pipa Stainless'): 960000,
        ('Pagar', 2025, 'Hollow'): 600000, ('Pagar', 2025, 'Hollow Stainless'): 1200000, ('Pagar', 2025, 'Pipa Stainless'): 1000000,
        
        # Kanopi
        ('Kanopi', 2019, 'Hollow'): 350000, ('Kanopi', 2019, 'Hollow Stainless'): 650000, ('Kanopi', 2019, 'Pipa Stainless'): 600000,
        ('Kanopi', 2020, 'Hollow'): 350000, ('Kanopi', 2020, 'Hollow Stainless'): 700000, ('Kanopi', 2020, 'Pipa Stainless'): 630000,
        ('Kanopi', 2021, 'Hollow'): 350000, ('Kanopi', 2021, 'Hollow Stainless'): 750000, ('Kanopi', 2021, 'Pipa Stainless'): 660000,
        ('Kanopi', 2022, 'Hollow'): 350000, ('Kanopi', 2022, 'Hollow Stainless'): 780000, ('Kanopi', 2022, 'Pipa Stainless'): 680000,
        ('Kanopi', 2023, 'Hollow'): 420000, ('Kanopi', 2023, 'Hollow Stainless'): 800000, ('Kanopi', 2023, 'Pipa Stainless'): 700000,
        ('Kanopi', 2024, 'Hollow'): 450000, ('Kanopi', 2024, 'Hollow Stainless'): 800000, ('Kanopi', 2024, 'Pipa Stainless'): 700000,
        ('Kanopi', 2025, 'Hollow'): 450000, ('Kanopi', 2025, 'Hollow Stainless'): 800000, ('Kanopi', 2025, 'Pipa Stainless'): 700000,
        
        # Pintu Gerbang
        ('Pintu Gerbang', 2019, 'Hollow'): 550000, ('Pintu Gerbang', 2019, 'Hollow Stainless'): 1100000, ('Pintu Gerbang', 2019, 'Pipa Stainless'): 950000,
        ('Pintu Gerbang', 2020, 'Hollow'): 600000, ('Pintu Gerbang', 2020, 'Hollow Stainless'): 1150000, ('Pintu Gerbang', 2020, 'Pipa Stainless'): 1000000,
        ('Pintu Gerbang', 2021, 'Hollow'): 600000, ('Pintu Gerbang', 2021, 'Hollow Stainless'): 1200000, ('Pintu Gerbang', 2021, 'Pipa Stainless'): 1050000,
        ('Pintu Gerbang', 2022, 'Hollow'): 650000, ('Pintu Gerbang', 2022, 'Hollow Stainless'): 1250000, ('Pintu Gerbang', 2022, 'Pipa Stainless'): 1100000,
        ('Pintu Gerbang', 2023, 'Hollow'): 700000, ('Pintu Gerbang', 2023, 'Hollow Stainless'): 1300000, ('Pintu Gerbang', 2023, 'Pipa Stainless'): 1150000,
        ('Pintu Gerbang', 2024, 'Hollow'): 750000, ('Pintu Gerbang', 2024, 'Hollow Stainless'): 1420000, ('Pintu Gerbang', 2024, 'Pipa Stainless'): 1180000,
        ('Pintu Gerbang', 2025, 'Hollow'): 750000, ('Pintu Gerbang', 2025, 'Hollow Stainless'): 1500000, ('Pintu Gerbang', 2025, 'Pipa Stainless'): 1200000,
        
        # Teralis
        ('Teralis', 2019, 'Hollow'): 300000, ('Teralis', 2019, 'Hollow Stainless'): 650000, ('Teralis', 2019, 'Pipa Stainless'): 600000,
        ('Teralis', 2020, 'Hollow'): 300000, ('Teralis', 2020, 'Hollow Stainless'): 700000, ('Teralis', 2020, 'Pipa Stainless'): 620000,
        ('Teralis', 2021, 'Hollow'): 300000, ('Teralis', 2021, 'Hollow Stainless'): 750000, ('Teralis', 2021, 'Pipa Stainless'): 640000,
        ('Teralis', 2022, 'Hollow'): 300000, ('Teralis', 2022, 'Hollow Stainless'): 780000, ('Teralis', 2022, 'Pipa Stainless'): 660000,
        ('Teralis', 2023, 'Hollow'): 350000, ('Teralis', 2023, 'Hollow Stainless'): 800000, ('Teralis', 2023, 'Pipa Stainless'): 680000,
        ('Teralis', 2024, 'Hollow'): 350000, ('Teralis', 2024, 'Hollow Stainless'): 800000, ('Teralis', 2024, 'Pipa Stainless'): 700000,
        ('Teralis', 2025, 'Hollow'): 350000, ('Teralis', 2025, 'Hollow Stainless'): 800000, ('Teralis', 2025, 'Pipa Stainless'): 700000,
        
        # Railing
        ('Railing', 2019, 'Hollow'): 400000, ('Railing', 2019, 'Hollow Stainless'): 1000000, ('Railing', 2019, 'Pipa Stainless'): 850000,
        ('Railing', 2020, 'Hollow'): 400000, ('Railing', 2020, 'Hollow Stainless'): 1050000, ('Railing', 2020, 'Pipa Stainless'): 880000,
        ('Railing', 2021, 'Hollow'): 450000, ('Railing', 2021, 'Hollow Stainless'): 1100000, ('Railing', 2021, 'Pipa Stainless'): 900000,
        ('Railing', 2022, 'Hollow'): 450000, ('Railing', 2022, 'Hollow Stainless'): 1120000, ('Railing', 2022, 'Pipa Stainless'): 920000,
        ('Railing', 2023, 'Hollow'): 500000, ('Railing', 2023, 'Hollow Stainless'): 1150000, ('Railing', 2023, 'Pipa Stainless'): 950000,
        ('Railing', 2024, 'Hollow'): 500000, ('Railing', 2024, 'Hollow Stainless'): 1180000, ('Railing', 2024, 'Pipa Stainless'): 1000000,
        ('Railing', 2025, 'Hollow'): 500000, ('Railing', 2025, 'Hollow Stainless'): 1200000, ('Railing', 2025, 'Pipa Stainless'): 1000000,
        
        # Pintu Handerson
        ('Pintu Handerson', 2019, 'Hollow'): 750000, ('Pintu Handerson', 2019, 'Hollow Stainless'): 1300000, ('Pintu Handerson', 2019, 'Pipa Stainless'): 1200000,
        ('Pintu Handerson', 2020, 'Hollow'): 800000, ('Pintu Handerson', 2020, 'Hollow Stainless'): 1350000, ('Pintu Handerson', 2020, 'Pipa Stainless'): 1250000,
        ('Pintu Handerson', 2021, 'Hollow'): 800000, ('Pintu Handerson', 2021, 'Hollow Stainless'): 1420000, ('Pintu Handerson', 2021, 'Pipa Stainless'): 1300000,
        ('Pintu Handerson', 2022, 'Hollow'): 800000, ('Pintu Handerson', 2022, 'Hollow Stainless'): 1460000, ('Pintu Handerson', 2022, 'Pipa Stainless'): 1350000,
        ('Pintu Handerson', 2023, 'Hollow'): 850000, ('Pintu Handerson', 2023, 'Hollow Stainless'): 1500000, ('Pintu Handerson', 2023, 'Pipa Stainless'): 1400000,
        ('Pintu Handerson', 2024, 'Hollow'): 900000, ('Pintu Handerson', 2024, 'Hollow Stainless'): 1600000, ('Pintu Handerson', 2024, 'Pipa Stainless'): 1450000,
        ('Pintu Handerson', 2025, 'Hollow'): 900000, ('Pintu Handerson', 2025, 'Hollow Stainless'): 1700000, ('Pintu Handerson', 2025, 'Pipa Stainless'): 1500000,
    }
    
    def get_material_base_price(row):
        # Get tahun - handle if missing
        tahun = row.get('tahun', 2025)
        if pd.isna(tahun):
            tahun = 2025
        
        # Try exact match first
        key = (row['produk'], int(tahun), row['jenis_material'])
        if key in material_base_price_map:
            return material_base_price_map[key]
        
        # Fallback: try nearest year
        for year in [2025, 2024, 2023, 2022, 2021, 2020, 2019]:
            key = (row['produk'], year, row['jenis_material'])
            if key in material_base_price_map:
                return material_base_price_map[key]
        
        return 500000
    
    df['material_base_price'] = df.apply(get_material_base_price, axis=1)
    
    # 2. Thickness Premium
    thickness_premium_map = {0.8: 0, 1.0: 50000, 1.2: 100000}
    df['thickness_premium'] = df['ketebalan_material'].map(thickness_premium_map).fillna(0)
    
    # 3. Profile Size Premium
    profile_premium_map = {
        '3x6': 50000, '4x4': 0, '4x6': 50000, '4x8': 100000,
        '2x2': 0, '1x3': 0, '1.5inch': 0, '2inch': 100000
    }
    df['profile_premium'] = df['profile_size'].map(profile_premium_map).fillna(0)
    
    # 4. Finishing Premium
    finishing_premium_map = {
        'Cat Dasar': 0, 'Cat Biasa': 0, 'Cat': 0,
        'Cat Duco': 150000, 'Tanpa Cat': 0, 'Tanpa Finishing': 0,
        'Powder Coating': 100000
    }
    df['finishing_premium'] = df['finishing'].map(finishing_premium_map).fillna(0)
    
    # 5. Complexity Premium
    complexity_premium_map = {'Sederhana': 0, 'Menengah': 100000, 'Kompleks': 150000}
    df['complexity_premium'] = df['kerumitan_desain'].map(complexity_premium_map).fillna(0)
    
    # ========== DERIVED FEATURES ==========
    df['total_area'] = df['jumlah_unit'] * df['ukuran']
    df['total_lubang'] = df['jumlah_unit'] * df['jumlah_lubang']
    
    # 7. Complexity Score
    df['kerumitan_numeric'] = df['kerumitan_desain'].map({'Sederhana': 1, 'Menengah': 2, 'Kompleks': 3}).fillna(1)
    df['complexity_score'] = (df['kerumitan_numeric'] * df['ketebalan_material'] * 
                              (df['ukuran'] + 0.1) * (df['jumlah_lubang'] + 1))
    
    # 8. Labor Intensity
    df['labor_intensity'] = df['jumlah_lubang'] / (df['ukuran'] + 0.1)
    
    # 9. Cost Per Unit
    df['cost_per_unit'] = df['ukuran'] / df['jumlah_unit']
    
    # 10. Material-Thickness Interaction
    df['material_premium_index'] = df['jenis_material'].map({
        'Hollow': 1, 'Hollow Stainless': 2, 'Pipa Stainless': 2, 'Stainless': 2, 'Besi': 1
    }).fillna(1)
    df['material_thickness_interaction'] = df['material_premium_index'] * df['ketebalan_material']
    
    # 11. Area-Complexity Interaction
    df['area_complexity'] = df['total_area'] * df['kerumitan_numeric']
    
    # 12. Upah Ratio
    df['upah_ratio'] = df['upah_tenaga_ahli'] / (df['total_area'] + 1)
    
    # Encoded features
    df['is_per_lubang'] = (df['metode_hitung'].str.upper() == 'PER-LUBANG').astype(int)
    df['is_per_m2'] = (df['metode_hitung'].str.upper() == 'PER-M2').astype(int)
    df['thickness_delta'] = np.maximum(0, df['ketebalan_material'] - 0.8)
    df['profile_numeric'] = df['profile_size'].map({'4x4': 1, '4x6': 2, '4x8': 3}).fillna(1)
    df['material_quality'] = df['jenis_material'].map({'Hollow': 1, 'Stainless': 2}).fillna(1)
    
    # Handle product-specific rules
    df.loc[df['metode_hitung'].str.upper() == 'PER-LUBANG', 'total_area'] = 0
    df.loc[df['metode_hitung'].str.upper() == 'PER-M2', 'total_lubang'] = 0
    
    # Ensure correct column order (MUST match training: 30 features - removed labor_rate_index!)
    expected_columns = [
        'produk', 'jenis_material', 'finishing', 'kerumitan_desain', 'metode_hitung', 'profile_size',
        'jumlah_unit', 'jumlah_lubang', 'ukuran', 'ketebalan_material',
        'material_base_price', 'thickness_premium', 'profile_premium', 
        'finishing_premium', 'complexity_premium',
        'total_area', 'total_lubang', 'complexity_score', 'labor_intensity',
        'cost_per_unit', 'material_thickness_interaction', 'area_complexity', 'upah_ratio',
        'is_per_lubang', 'is_per_m2', 'thickness_delta', 'profile_numeric', 
        'material_quality', 'material_premium_index', 'kerumitan_numeric'
    ]
    
    # Reorder columns
    df = df[expected_columns]
    
    return df

def predict(model, input_df):
    """Make prediction"""
    try:
        prediction = model.predict(input_df)[0]
        return float(prediction)
    except Exception as e:
        raise ValueError(f"Prediction error: {str(e)}")

def main():
    try:
        # Read input from command line argument
        if len(sys.argv) < 2:
            raise ValueError("No input provided. Usage: python predict_single.py <json_file_path>")
        
        # Check if argument is a file path or JSON string
        input_arg = sys.argv[1]
        
        # Try to read as file first
        if Path(input_arg).exists():
            with open(input_arg, 'r', encoding='utf-8-sig') as f:  # utf-8-sig handles BOM
                input_data = json.load(f)
        else:
            # Fallback: parse as JSON string (backward compatibility)
            input_data = json.loads(input_arg)
        
        # Load model
        model = load_model()
        
        # Prepare input
        input_df = prepare_input(input_data)
        
        # Make prediction
        predicted_price = predict(model, input_df)
        
        # Return result as JSON
        result = {
            "success": True,
            "predicted_price": round(predicted_price, 2),
            "input": input_data,
            "message": "Prediction successful"
        }
        
        print(json.dumps(result, ensure_ascii=False))
        sys.exit(0)
        
    except FileNotFoundError as e:
        error_result = {
            "success": False,
            "error": "Model file not found",
            "message": str(e)
        }
        print(json.dumps(error_result, ensure_ascii=False))
        sys.exit(1)
        
    except json.JSONDecodeError as e:
        error_result = {
            "success": False,
            "error": "Invalid JSON input",
            "message": str(e)
        }
        print(json.dumps(error_result, ensure_ascii=False))
        sys.exit(1)
        
    except Exception as e:
        error_result = {
            "success": False,
            "error": "Prediction failed",
            "message": str(e)
        }
        print(json.dumps(error_result, ensure_ascii=False))
        sys.exit(1)

if __name__ == "__main__":
    main()
