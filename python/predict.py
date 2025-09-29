#!/usr/bin/env python3
import sys
import json
import joblib
import numpy as np
import pandas as pd
from typing import Dict, Union, List
import os

class PricePredictor:
    def __init__(self, model_path: str):
        """Initialize the predictor with a trained model."""
        self.model_path = model_path
        
        try:
            # Load model
            self.model = joblib.load(model_path)
            
            # Load label encoders
            script_dir = os.path.dirname(os.path.abspath(__file__))
            encoders_path = os.path.join(script_dir, '..', 'storage', 'app', 'data', 'label_encoders.json')
            with open(encoders_path, 'r') as f:
                self.encoders = json.load(f)
                
        except Exception as e:
            raise ValueError(f"Failed to load model or encoders: {str(e)}")
            
    def _preprocess_input(self, data: Dict) -> pd.DataFrame:
        """Convert input data into features the model can use."""
        try:
            # Create a DataFrame with one row
            df = pd.DataFrame([{
                'project_type': data['project_type'],
                'material_type': data['material_type'],
                'length': float(data['dimensions']['length']),
                'width': float(data['dimensions']['width']),
                'thickness': float(data['dimensions']['thickness']),
                'area': float(data['dimensions']['length']) * float(data['dimensions']['width'])
            }])
            
            # Add additional features columns
            features = ['painting', 'welding', 'installation', 'polishing', 'design']
            for feature in features:
                df[feature] = int(feature in data.get('additional_features', []))
            
            # Apply label encoding using the same mapping as training
            try:
                df['project_type'] = df['project_type'].map(self.encoders['project_type']['mappings'])
                df['material_type'] = df['material_type'].map(self.encoders['material_type']['mappings'])
            except KeyError as e:
                raise ValueError(f"Invalid value for {str(e)}. Allowed values for "
                              f"project_type: {list(self.encoders['project_type']['mappings'].keys())}, "
                              f"material_type: {list(self.encoders['material_type']['mappings'].keys())}")
            
            if df.isna().any().any():
                raise ValueError("Invalid categorical value provided")
                
            return df
        except Exception as e:
            raise ValueError(f"Input preprocessing failed: {str(e)}")
    
    def predict(self, data: Dict) -> Dict[str, Union[float, str]]:
        """Predict the price based on input data."""
        try:
            # Preprocess input
            features = self._preprocess_input(data)
            
            # Make prediction
            prediction = self.model.predict(features)[0]
            prediction = max(prediction, 0)  # Ensure non-negative price
            
            return {
                'status': 'success',
                'estimated_price': prediction,
                'formatted_price': f"₱{prediction:,.2f}",
                'confidence': 0.95  # You can implement confidence calculation based on your needs
            }
        except Exception as e:
            return {
                'status': 'error',
                'message': str(e)
            }
    
    def get_valid_values(self) -> Dict[str, List[str]]:
        """Get lists of valid values for categorical inputs."""
        return {
            'project_types': list(self.encoders['project_type']['mappings'].keys()),
            'material_types': list(self.encoders['material_type']['mappings'].keys())
        }

def main():
    """Main function to handle command-line usage."""
    try:
        if '--get-valid-values' in sys.argv:
            if len(sys.argv) < 2:
                print(json.dumps({
                    'status': 'error',
                    'message': 'Missing model path. Usage: predict.py --get-valid-values <model_path>'
                }))
                sys.exit(1)
                
            # Find model path (it's either right after --get-valid-values or the last argument)
            model_path_idx = sys.argv.index('--get-valid-values') + 1
            model_path = sys.argv[model_path_idx] if model_path_idx < len(sys.argv) else sys.argv[-1]
            
            predictor = PricePredictor(model_path)
            result = {
                'status': 'success',
                **predictor.get_valid_values()
            }
        else:
            if len(sys.argv) < 3:
                print(json.dumps({
                    'status': 'error',
                    'message': 'Missing required arguments. Usage: predict.py <json_input> <model_path>'
                }))
                sys.exit(1)
                
            input_data = json.loads(sys.argv[1])
            model_path = sys.argv[2]
            
            predictor = PricePredictor(model_path)
            result = predictor.predict(input_data)
        
        print(json.dumps(result, indent=2))
        sys.exit(0 if result.get('status') == 'success' else 1)
        
    except Exception as e:
        print(json.dumps({
            'status': 'error',
            'message': str(e)
        }, indent=2))
        sys.exit(1)

if __name__ == '__main__':
    main()