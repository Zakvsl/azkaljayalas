from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import pandas as pd
import numpy as np
import joblib
import json
from typing import List, Optional

# Create FastAPI app
app = FastAPI(
    title="Welding Price Prediction API",
    description="API for predicting welding workshop prices using a trained Random Forest model",
    version="1.0.0"
)

# Load the trained model pipeline
try:
    model_pipeline = joblib.load("model_pipeline.joblib")
    print("Model loaded successfully!")
except Exception as e:
    model_pipeline = None
    print(f"Error loading model: {e}")

# Load metrics
try:
    with open("metrics.json", "r") as f:
        metrics = json.load(f)
    mae_estimate = metrics.get("test_mae", 500000)  # Default if not found
except Exception as e:
    mae_estimate = 500000  # Default MAE estimate
    print(f"Error loading metrics: {e}")

# Define the input data model
class PricePredictionInput(BaseModel):
    Produk: str
    Jumlah_Unit: int
    Jumlah_Lubang: Optional[float] = None
    Ukuran_m2: Optional[float] = None
    Jenis_Material: str
    Ketebalan_mm: float
    Finishing: str
    Kerumitan_Desain: str
    Metode_Hitung: str

# Define the output data model
class PricePredictionOutput(BaseModel):
    predicted_price: int
    mae_estimate: float
    explanation: dict

# Health check endpoint
# @app.get("/health")
# def health_check():
#     """Health check endpoint to verify the API is running"""
#     return {"status": "healthy", "model_loaded": model_pipeline is not None}

# Prediction endpoint
@app.post("/predict", response_model=PricePredictionOutput)
def predict_price(input_data: PricePredictionInput):
    """Predict the price of a welding workshop product"""
    if model_pipeline is None:
        raise HTTPException(status_code=500, detail="Model not loaded")
    
    try:
        # Convert input data to DataFrame
        data_dict = {
            'Produk': [input_data.Produk],
            'Jumlah Unit': [input_data.Jumlah_Unit],
            'Jumlah Lubang': [input_data.Jumlah_Lubang if input_data.Jumlah_Lubang is not None else np.nan],
            'Ukuran_m2': [input_data.Ukuran_m2 if input_data.Ukuran_m2 is not None else np.nan],
            'Jenis Material': [input_data.Jenis_Material],
            'Ketebalan_mm': [input_data.Ketebalan_mm],
            'Finishing': [input_data.Finishing],
            'Kerumitan Desain': [input_data.Kerumitan_Desain],
            'Metode Hitung': [input_data.Metode_Hitung]
        }
        
        df = pd.DataFrame(data_dict)
        
        # Feature engineering (same as in training)
        df['total_area'] = df['Jumlah Unit'] * df['Ukuran_m2']
        df['total_lubang'] = df['Jumlah Unit'] * df['Jumlah Lubang']
        df['is_per_lubang'] = (df['Metode Hitung'] == 'Per Lubang').astype(int)
        df['is_per_m2'] = (df['Metode Hitung'] == 'Per m²').astype(int)
        df['thickness_delta'] = np.maximum(0, df['Ketebalan_mm'] - 0.8)
        
        # Handle product-specific rules
        df.loc[df['Metode Hitung'] == 'Per Lubang', 'total_area'] = 0
        df.loc[df['Metode Hitung'] == 'Per m²', 'total_lubang'] = 0
        
        # Handle edge cases
        df.loc[(df['Metode Hitung'].isnull()) & (df['Produk'] == 'Teralis'), 'Metode Hitung'] = 'Per Lubang'
        df.loc[(df['Metode Hitung'].isnull()) & (df['Produk'] != 'Teralis'), 'Metode Hitung'] = 'Per m²'
        
        df.loc[(df['Jumlah Lubang'].isnull()) & (df['Produk'] != 'Teralis'), 'Jumlah Lubang'] = 0
        df.loc[(df['Ukuran_m2'].isnull()) & (df['Metode Hitung'] == 'Per Lubang'), 'Ukuran_m2'] = 0
        df['Jumlah Unit'] = df['Jumlah Unit'].fillna(1)
        
        # Select features (same as in training)
        feature_columns = [
            'Produk', 'Jumlah Unit', 'Jumlah Lubang', 'Ukuran_m2', 'Jenis Material', 
            'Ketebalan_mm', 'Finishing', 'Kerumitan Desain', 'Metode Hitung',
            'total_area', 'total_lubang', 'is_per_lubang', 'is_per_m2', 'thickness_delta'
        ]
        
        # Ensure all feature columns exist
        for col in feature_columns:
            if col not in df.columns:
                df[col] = 0  # Default value for missing columns
        
        X = df[feature_columns]
        
        # Make prediction
        prediction = model_pipeline.predict(X)[0]
        
        # Get top feature importances
        try:
            feature_names = model_pipeline.named_steps['preprocessor'].get_feature_names_out()
            importances = model_pipeline.named_steps['model'].feature_importances_
            feature_importance_dict = dict(zip(feature_names, importances))
            sorted_features = sorted(feature_importance_dict.items(), key=lambda x: x[1], reverse=True)
            
            # Top 3 features
            top_features = [[feature, float(importance)] for feature, importance in sorted_features[:3]]
            explanation = {"top_features": top_features}
        except Exception as e:
            explanation = {"top_features": [["error", 0.0]]}
            print(f"Error computing feature importances: {e}")
        
        # Return prediction result
        return PricePredictionOutput(
            predicted_price=int(prediction),
            mae_estimate=mae_estimate,
            explanation=explanation
        )
    except Exception as e:
        raise HTTPException(status_code=400, detail=f"Prediction error: {str(e)}")

# Root endpoint
@app.get("/")
def read_root():
    """Root endpoint with API information"""
    return {
        "message": "Welding Price Prediction API", 
        "docs": "/docs", 
        "predict_endpoint": "/predict"
    }

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000)