import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestRegressor
from sklearn.preprocessing import LabelEncoder
from sklearn.metrics import mean_absolute_error, r2_score
import joblib
import json
import sys
import os

class PricePredictionModel:
    """
    Random Forest model untuk prediksi harga bengkel las
    """
    
    def __init__(self):
        self.model = None
        self.label_encoders = {}
        self.feature_names = []
        self.model_path = os.path.join(os.path.dirname(__file__), 'models', 'price_model.pkl')
        self.encoders_path = os.path.join(os.path.dirname(__file__), 'models', 'label_encoders.pkl')
        
    def prepare_data(self, df):
        """
        Prepare and clean data for training
        """
        # Kolom kategorikal yang perlu di-encode
        categorical_columns = ['jenis_proyek', 'material', 'kompleksitas']
        
        # Encode categorical variables
        for col in categorical_columns:
            if col in df.columns:
                if col not in self.label_encoders:
                    self.label_encoders[col] = LabelEncoder()
                    df[f'{col}_encoded'] = self.label_encoders[col].fit_transform(df[col])
                else:
                    df[f'{col}_encoded'] = self.label_encoders[col].transform(df[col])
        
        return df
    
    def train(self, data_path):
        """
        Train Random Forest model dengan dataset
        """
        # Load data
        if data_path.endswith('.xlsx'):
            df = pd.read_excel(data_path)
        elif data_path.endswith('.csv'):
            df = pd.read_csv(data_path)
        else:
            raise ValueError("Format file harus .xlsx atau .csv")
        
        # Prepare data
        df = self.prepare_data(df)
        
        # Define features (adjust sesuai dengan kolom dataset Anda)
        feature_columns = [
            'jenis_proyek_encoded', 
            'panjang', 
            'lebar', 
            'tinggi',
            'material_encoded',
            'kompleksitas_encoded'
        ]
        
        # Filter only available columns
        available_features = [col for col in feature_columns if col in df.columns]
        self.feature_names = available_features
        
        X = df[available_features]
        y = df['harga_total']  # Adjust nama kolom sesuai dataset
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.2, random_state=42
        )
        
        # Train model
        self.model = RandomForestRegressor(
            n_estimators=100,
            max_depth=15,
            min_samples_split=5,
            min_samples_leaf=2,
            random_state=42,
            n_jobs=-1
        )
        
        self.model.fit(X_train, y_train)
        
        # Evaluate
        y_pred = self.model.predict(X_test)
        mae = mean_absolute_error(y_test, y_pred)
        r2 = r2_score(y_test, y_pred)
        
        # Save model and encoders
        self.save_model()
        
        return {
            'mae': float(mae),
            'r2': float(r2),
            'n_samples': len(df),
            'features': self.feature_names
        }
    
    def predict(self, input_data):
        """
        Predict price based on input features
        
        Args:
            input_data (dict): Dictionary containing feature values
            
        Returns:
            float: Predicted price
        """
        if self.model is None:
            self.load_model()
        
        # Prepare input DataFrame
        df_input = pd.DataFrame([input_data])
        
        # Encode categorical variables
        for col, encoder in self.label_encoders.items():
            if col in df_input.columns:
                df_input[f'{col}_encoded'] = encoder.transform(df_input[col])
        
        # Select only the features used in training
        X = df_input[self.feature_names]
        
        # Predict
        prediction = self.model.predict(X)[0]
        
        return float(prediction)
    
    def save_model(self):
        """
        Save trained model and encoders
        """
        os.makedirs(os.path.dirname(self.model_path), exist_ok=True)
        joblib.dump(self.model, self.model_path)
        joblib.dump({
            'encoders': self.label_encoders,
            'features': self.feature_names
        }, self.encoders_path)
        
    def load_model(self):
        """
        Load trained model and encoders
        """
        if os.path.exists(self.model_path):
            self.model = joblib.load(self.model_path)
            encoder_data = joblib.load(self.encoders_path)
            self.label_encoders = encoder_data['encoders']
            self.feature_names = encoder_data['features']
        else:
            raise FileNotFoundError("Model file not found. Please train the model first.")


def main():
    """
    CLI interface for training and prediction
    """
    if len(sys.argv) < 2:
        print("Usage: python price_prediction.py [train|predict] [args...]")
        sys.exit(1)
    
    command = sys.argv[1]
    model = PricePredictionModel()
    
    if command == 'train':
        if len(sys.argv) < 3:
            print("Usage: python price_prediction.py train <data_path>")
            sys.exit(1)
        
        data_path = sys.argv[2]
        results = model.train(data_path)
        print(json.dumps(results, indent=2))
        
    elif command == 'predict':
        if len(sys.argv) < 3:
            print("Usage: python price_prediction.py predict <json_input>")
            sys.exit(1)
        
        input_json = sys.argv[2]
        input_data = json.loads(input_json)
        prediction = model.predict(input_data)
        print(json.dumps({'predicted_price': prediction}))
        
    else:
        print(f"Unknown command: {command}")
        sys.exit(1)


if __name__ == '__main__':
    main()
