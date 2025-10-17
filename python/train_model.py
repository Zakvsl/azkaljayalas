import pandas as pd
import numpy as np
from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split, cross_val_score
from sklearn.preprocessing import OneHotEncoder, OrdinalEncoder
from sklearn.compose import ColumnTransformer
from sklearn.pipeline import Pipeline
from sklearn.impute import SimpleImputer
from sklearn.metrics import mean_absolute_error, mean_squared_error, r2_score
import joblib
import json
import sys
import warnings
warnings.filterwarnings('ignore')

# Get dataset path from command line argument or use default
if len(sys.argv) > 1:
    dataset_path = sys.argv[1]
    print(f"Using dataset from: {dataset_path}")
else:
    dataset_path = 'dataset_bengkel_las2.xlsx'
    print("Using default dataset: dataset_bengkel_las2.xlsx")

# Load the dataset
print("Loading dataset...")
# Check file extension to determine how to read
if dataset_path.endswith('.csv'):
    df = pd.read_csv(dataset_path)
elif dataset_path.endswith('.xlsx') or dataset_path.endswith('.xls'):
    df = pd.read_excel(dataset_path, sheet_name='Dataset Transaksi')
else:
    raise ValueError("Unsupported file format. Please use .csv or .xlsx")

# Data cleaning
print("Cleaning data...")

# Print column names to check
print("Column names in dataset:")
for col in df.columns:
    print(f"- {col}")

# Standardize column names - handle both Excel format and CSV export format
column_mapping = {
    # Excel format (old)
    'Ukuran (m²)': 'Ukuran_m2',
    'Ketebalan (mm)': 'Ketebalan_mm',
    'Harga Akhir (Rp)': 'Harga_Akhir_Rp',
    # CSV export format (new) - already has underscores but need to match exactly
    'Jumlah_Unit': 'Jumlah Unit',
    'Jumlah_Lubang': 'Jumlah Lubang',
    'Jenis_Material': 'Jenis Material',
    'Ketebalan_mm': 'Ketebalan_mm',
    'Kerumitan_Desain': 'Kerumitan Desain',
    'Metode_Hitung': 'Metode Hitung',
    'Harga_Akhir': 'Harga_Akhir_Rp'
}

# Apply column mapping
df = df.rename(columns=column_mapping)

# Drop rows with missing target
target_column = 'Harga_Akhir_Rp' if 'Harga_Akhir_Rp' in df.columns else 'Harga Akhir (Rp)'
df = df.dropna(subset=[target_column])

# Ensure target column is named consistently
if target_column != 'Harga_Akhir_Rp':
    df = df.rename(columns={target_column: 'Harga_Akhir_Rp'})

print(f"Dataset loaded with {len(df)} rows")

# Fill missing values in numeric columns with median
numeric_columns = ['Jumlah Lubang', 'Ukuran_m2', 'Ketebalan_mm']
for col in numeric_columns:
    if col in df.columns:
        df[col] = df[col].fillna(df[col].median())

# Fill missing values in categorical columns with "UNKNOWN"
categorical_columns = ['Produk', 'Jenis Material', 'Finishing', 'Kerumitan Desain', 'Metode Hitung']
for col in categorical_columns:
    if col in df.columns:
        df[col] = df[col].fillna("UNKNOWN")

# Feature engineering
print("Engineering features...")
# Derived features
df['total_area'] = df['Jumlah Unit'] * df['Ukuran_m2']
df['total_lubang'] = df['Jumlah Unit'] * df['Jumlah Lubang']

# One-hot encoded features for Metode Hitung
df['is_per_lubang'] = (df['Metode Hitung'] == 'Per Lubang').astype(int)
df['is_per_m2'] = (df['Metode Hitung'] == 'Per m²').astype(int)

# Thickness delta feature
df['thickness_delta'] = np.maximum(0, df['Ketebalan_mm'] - 0.8)

# Handling product-specific rules
df.loc[df['Metode Hitung'] == 'Per Lubang', 'total_area'] = 0
df.loc[df['Metode Hitung'] == 'Per m²', 'total_lubang'] = 0

# Handle edge cases
# If Metode_Hitung is missing: infer from Produk (e.g., Teralis → Per Lubang, others → Per m²)
df.loc[(df['Metode Hitung'] == "UNKNOWN") & (df['Produk'] == 'Teralis'), 'Metode Hitung'] = 'Per Lubang'
df.loc[(df['Metode Hitung'] == "UNKNOWN") & (df['Produk'] != 'Teralis'), 'Metode Hitung'] = 'Per m²'

# If Jumlah Lubang is missing for non-teralis products → fill with 0
df.loc[(df['Jumlah Lubang'].isnull()) & (df['Produk'] != 'Teralis'), 'Jumlah Lubang'] = 0

# If Ukuran_m2 is missing for per_lubang products → fill with 0
df.loc[(df['Ukuran_m2'].isnull()) & (df['Metode Hitung'] == 'Per Lubang'), 'Ukuran_m2'] = 0

# If Jumlah Unit missing → default = 1
df['Jumlah Unit'] = df['Jumlah Unit'].fillna(1)

# Prepare features and target
print("Preparing features and target...")
feature_columns = [
    'Produk', 'Jumlah Unit', 'Jumlah Lubang', 'Ukuran_m2', 'Jenis Material', 
    'Ketebalan_mm', 'Finishing', 'Kerumitan Desain', 'Metode Hitung',
    'total_area', 'total_lubang', 'is_per_lubang', 'is_per_m2', 'thickness_delta'
]

# Check which columns actually exist
existing_feature_columns = [col for col in feature_columns if col in df.columns]
print(f"Existing feature columns: {existing_feature_columns}")

X = df[existing_feature_columns]
y = df['Harga_Akhir_Rp']

# Define preprocessing steps
print("Setting up preprocessing pipeline...")
# Numeric preprocessing
numeric_features = ['Jumlah Unit', 'Jumlah Lubang', 'Ukuran_m2', 'Ketebalan_mm', 
                   'total_area', 'total_lubang', 'thickness_delta']
numeric_features = [col for col in numeric_features if col in X.columns]

# Categorical preprocessing
categorical_features = ['Produk', 'Jenis Material', 'Finishing', 'Metode Hitung']
categorical_features = [col for col in categorical_features if col in X.columns]

# Ordinal preprocessing for Kerumitan Desain
ordinal_features = ['Kerumitan Desain']
ordinal_features = [col for col in ordinal_features if col in X.columns]

# Create transformers based on what features we actually have
transformers = []

if numeric_features:
    numeric_transformer = Pipeline(steps=[
        ('imputer', SimpleImputer(strategy='median'))
    ])
    transformers.append(('num', numeric_transformer, numeric_features))

if categorical_features:
    categorical_transformer = Pipeline(steps=[
        ('imputer', SimpleImputer(strategy='constant', fill_value='UNKNOWN')),
        ('onehot', OneHotEncoder(sparse_output=False, handle_unknown='ignore'))
    ])
    transformers.append(('cat', categorical_transformer, categorical_features))

if ordinal_features:
    ordinal_transformer = Pipeline(steps=[
        ('imputer', SimpleImputer(strategy='constant', fill_value='UNKNOWN')),
        ('ordinal', OrdinalEncoder(categories=[['UNKNOWN', 'Simple', 'Medium', 'Complex']], 
                                  handle_unknown='use_encoded_value', unknown_value=-1))
    ])
    transformers.append(('ord', ordinal_transformer, ordinal_features))

# Combine preprocessing steps
preprocessor = ColumnTransformer(transformers=transformers, remainder='passthrough')

# Create full pipeline with model
print("Creating model pipeline...")
model = RandomForestRegressor(n_estimators=200, random_state=42, n_jobs=-1)
pipeline = Pipeline(steps=[
    ('preprocessor', preprocessor),
    ('model', model)
])

# Split the data
print("Splitting data...")
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Train the model
print("Training model...")
pipeline.fit(X_train, y_train)

# Cross-validation
print("Performing cross-validation...")
cv_mae_scores = cross_val_score(pipeline, X_train, y_train, cv=5, scoring='neg_mean_absolute_error')
cv_r2_scores = cross_val_score(pipeline, X_train, y_train, cv=5, scoring='r2')

# Predictions on test set
print("Evaluating model...")
y_pred = pipeline.predict(X_test)

# Calculate metrics
mae = mean_absolute_error(y_test, y_pred)
rmse = np.sqrt(mean_squared_error(y_test, y_pred))
r2 = r2_score(y_test, y_pred)

# Display results
print("\n=== MODEL EVALUATION RESULTS ===")
print(f"Cross-validation MAE: {-cv_mae_scores.mean():.2f} (+/- {cv_mae_scores.std() * 2:.2f})")
print(f"Cross-validation R²: {cv_r2_scores.mean():.4f} (+/- {cv_r2_scores.std() * 2:.4f})")
print(f"Test MAE: {mae:.2f}")
print(f"Test RMSE: {rmse:.2f}")
print(f"Test R²: {r2:.4f}")

# Save metrics
metrics = {
    "cv_mae": float(-cv_mae_scores.mean()),
    "cv_mae_std": float(cv_mae_scores.std() * 2),
    "cv_r2": float(cv_r2_scores.mean()),
    "cv_r2_std": float(cv_r2_scores.std() * 2),
    "test_mae": float(mae),
    "test_rmse": float(rmse),
    "test_r2": float(r2)
}

with open('metrics.json', 'w') as f:
    json.dump(metrics, f, indent=2)

print("\nMetrics saved to metrics.json")

# Feature importance
print("\nComputing feature importance...")
try:
    # Get feature names after preprocessing
    feature_names = pipeline.named_steps['preprocessor'].get_feature_names_out()
    
    # Get feature importances
    importances = pipeline.named_steps['model'].feature_importances_
    feature_importance_dict = dict(zip(feature_names, importances))
    sorted_features = sorted(feature_importance_dict.items(), key=lambda x: x[1], reverse=True)

    print("\n=== TOP 10 FEATURE IMPORTANCES ===")
    for feature, importance in sorted_features[:10]:
        print(f"{feature}: {importance:.4f}")
        
    # Save feature importances
    with open('feature_importances.json', 'w') as f:
        json.dump(dict(sorted_features), f, indent=2)
    print("\nFeature importances saved to feature_importances.json")
except Exception as e:
    print(f"Could not compute feature importances: {e}")

# Save the model pipeline
print("\nSaving model pipeline...")
joblib.dump(pipeline, 'model_pipeline.joblib')
print("Model pipeline saved to model_pipeline.joblib")

print("\n=== TRAINING COMPLETE ===")