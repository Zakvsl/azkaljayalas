#!/usr/bin/env python3
import sys
import json
import numpy as np
import pandas as pd
from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split, cross_val_score
from sklearn.metrics import mean_squared_error, r2_score, mean_absolute_error
from joblib import dump
from typing import Dict, Union

def train_model(data_path: str, model_path: str) -> Dict[str, Union[str, Dict[str, float]]]:
    """Train a Random Forest model for price prediction."""
    try:
        # Load training data
        df = pd.read_csv(data_path)
        
        # Prepare features and target
        X = df.drop('price', axis=1)
        y = df['price']
        
        # Split the data
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.2, random_state=42
        )
        
        # Initialize and train the model
        model = RandomForestRegressor(
            n_estimators=100,
            max_depth=10,
            min_samples_split=2,
            min_samples_leaf=1,
            random_state=42
        )
        model.fit(X_train, y_train)
        
        # Save the model
        dump(model, model_path)
        
        # Make predictions
        y_pred_train = model.predict(X_train)
        y_pred_test = model.predict(X_test)
        
        # Cross-validation scores
        cv_scores = cross_val_score(model, X, y, cv=5, scoring='r2')
        
        # Calculate various metrics
        metrics = {
            'train': {
                'r2': r2_score(y_train, y_pred_train),
                'mse': mean_squared_error(y_train, y_pred_train),
                'rmse': np.sqrt(mean_squared_error(y_train, y_pred_train)),
                'mae': mean_absolute_error(y_train, y_pred_train)
            },
            'test': {
                'r2': r2_score(y_test, y_pred_test),
                'mse': mean_squared_error(y_test, y_pred_test),
                'rmse': np.sqrt(mean_squared_error(y_test, y_pred_test)),
                'mae': mean_absolute_error(y_test, y_pred_test)
            },
            'cross_validation': {
                'mean_r2': cv_scores.mean(),
                'std_r2': cv_scores.std()
            },
            'feature_importance': dict(zip(X.columns, model.feature_importances_))
        }
        
        return {
            'status': 'success',
            'message': 'Model trained successfully',
            'metrics': metrics
        }
        
    except Exception as e:
        return {
            'status': 'error',
            'message': str(e)
        }

def main():
    """Main function to handle command-line usage."""
    if len(sys.argv) < 2:
        result = {
            'status': 'error',
            'message': 'Missing model path argument. Usage: train.py <model_path>'
        }
    else:
        data_path = 'storage/app/data/training_data.csv'
        model_path = sys.argv[1]
        result = train_model(data_path, model_path)
    
    print(json.dumps(result, indent=2))
    sys.exit(0 if result['status'] == 'success' else 1)

if __name__ == '__main__':
    main()