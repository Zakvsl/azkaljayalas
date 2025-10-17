#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Single Prediction Script for Laravel Integration
Menerima input JSON dan mengembalikan prediksi harga
"""

import sys
import json
import joblib
import pandas as pd
import numpy as np
from pathlib import Path

def load_model():
    """Load trained model"""
    model_path = Path(__file__).parent / 'model_pipeline.joblib'
    if not model_path.exists():
        raise FileNotFoundError(f"Model not found at {model_path}")
    return joblib.load(model_path)

def prepare_input(data):
    """
    Prepare input data for prediction
    Expected format:
    {
        "Produk": "Pagar",
        "Jumlah_Unit": 1,
        "Jumlah_Lubang": 0,
        "Ukuran_m2": 10.5,
        "Jenis_Material": "Hollow",
        "Ketebalan_mm": 2.0,
        "Finishing": "Cat",
        "Kerumitan_Desain": "Sederhana",
        "Metode_Hitung": "Per m²"
    }
    """
    # Rename keys to match training data format (with spaces)
    renamed_data = {
        'Produk': data.get('Produk', data.get('produk', '')),
        'Jumlah Unit': data.get('Jumlah_Unit', data.get('jumlah_unit', 1)),
        'Jumlah Lubang': data.get('Jumlah_Lubang', data.get('jumlah_lubang', 0)),
        'Ukuran_m2': data.get('Ukuran_m2', data.get('ukuran_m2', 0)),
        'Jenis Material': data.get('Jenis_Material', data.get('jenis_material', '')),
        'Ketebalan_mm': data.get('Ketebalan_mm', data.get('ketebalan_mm', 0)),
        'Finishing': data.get('Finishing', data.get('finishing', '')),
        'Kerumitan Desain': data.get('Kerumitan_Desain', data.get('kerumitan_desain', '')),
        'Metode Hitung': data.get('Metode_Hitung', data.get('metode_hitung', ''))
    }
    
    # Create DataFrame with single row
    df = pd.DataFrame([renamed_data])
    
    # Ensure numeric columns are float/int
    numeric_columns = ['Jumlah Unit', 'Jumlah Lubang', 'Ukuran_m2', 'Ketebalan_mm']
    for col in numeric_columns:
        if col in df.columns:
            df[col] = pd.to_numeric(df[col], errors='coerce').fillna(0)
    
    # Feature engineering (same as training)
    df['total_area'] = df['Jumlah Unit'] * df['Ukuran_m2']
    df['total_lubang'] = df['Jumlah Unit'] * df['Jumlah Lubang']
    df['is_per_lubang'] = (df['Metode Hitung'] == 'Per Lubang').astype(int)
    df['is_per_m2'] = (df['Metode Hitung'] == 'Per m²').astype(int)
    df['thickness_delta'] = np.maximum(0, df['Ketebalan_mm'] - 0.8)
    
    # Handle product-specific rules
    df.loc[df['Metode Hitung'] == 'Per Lubang', 'total_area'] = 0
    df.loc[df['Metode Hitung'] == 'Per m²', 'total_lubang'] = 0
    
    # Ensure correct column order (must match training data)
    expected_columns = [
        'Produk', 'Jumlah Unit', 'Jumlah Lubang', 'Ukuran_m2',
        'Jenis Material', 'Ketebalan_mm', 'Finishing',
        'Kerumitan Desain', 'Metode Hitung',
        'total_area', 'total_lubang', 'is_per_lubang', 'is_per_m2', 'thickness_delta'
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
