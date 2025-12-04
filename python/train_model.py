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
    dataset_path = 'azkaljayalas_dataset.csv'
    print(f"Using default dataset: {dataset_path}")

# Load the dataset
print("Loading dataset...")
# Check file extension to determine how to read
if dataset_path.endswith('.csv'):
    df = pd.read_csv(dataset_path)
elif dataset_path.endswith('.xlsx') or dataset_path.endswith('.xls'):
    # Try common sheet names
    try:
        df = pd.read_excel(dataset_path, sheet_name='Data Order Bengkel')
    except:
        df = pd.read_excel(dataset_path, sheet_name='Dataset Transaksi')
else:
    raise ValueError("Unsupported file format. Please use .csv or .xlsx")

# Data cleaning
print("Cleaning data...")

# Print column names to check
print("Column names in dataset:")
for col in df.columns:
    print(f"- {col}")

# Standardize column names - handle both old and new format
column_mapping = {
    'ID Transaksi': 'order_id',
    'Produk': 'produk',
    'Jumlah Unit': 'jumlah_unit',
    'Jumlah Lubang': 'jumlah_lubang',
    'Ukuran (m²)': 'ukuran',
    'Jenis Material': 'jenis_material',
    'Ketebalan (mm)': 'ketebalan_material',
    'Finishing': 'finishing',
    'Kerumitan Desain': 'kerumitan_desain',
    'Metode Hitung': 'metode_hitung',
    'Harga Akhir (Rp)': 'harga_final',
    # Old format variations & Admin export format
    'Jumlah_Unit': 'jumlah_unit',
    'Jumlah_Lubang': 'jumlah_lubang',
    'Ukuran_m2': 'ukuran',  # From admin export
    'Jenis_Material': 'jenis_material',
    'Ketebalan_mm': 'ketebalan_material',
    'Kerumitan_Desain': 'kerumitan_desain',
    'Metode_Hitung': 'metode_hitung',
    'Harga_Akhir': 'harga_final',
    'Harga_Akhir_Rp': 'harga_final',  # From admin export (if with underscore)
    'Profile_Size': 'profile_size',
    'Upah_Tenaga_Ahli': 'upah_tenaga_ahli'
}

# Apply column mapping
df = df.rename(columns=column_mapping)

# Generate missing columns for new dataset
if 'profile_size' not in df.columns:
    # Default profile size based on material thickness
    df['profile_size'] = df['ketebalan_material'].apply(lambda x: '4x4' if x <= 1.0 else ('4x6' if x <= 1.5 else '4x8'))
    print("Generated 'profile_size' column based on material thickness")

if 'upah_tenaga_ahli' not in df.columns:
    # Calculate upah based on material type and area
    def calculate_upah(row):
        material = str(row.get('jenis_material', '')).lower()
        ukuran = float(row.get('ukuran', 0))
        
        if 'stainless' in material or 'stainlis' in material:
            return ukuran * 200000  # Rp 200k per m²
        else:
            return ukuran * 100000  # Rp 100k per m²
    
    df['upah_tenaga_ahli'] = df.apply(calculate_upah, axis=1)
    print("Generated 'upah_tenaga_ahli' column based on material and size")

# Check if target column exists after mapping
target_column = 'harga_final'
if target_column not in df.columns:
    print(f"ERROR: Target column '{target_column}' not found after mapping!")
    print(f"Available columns: {list(df.columns)}")
    sys.exit(1)

# Drop rows with missing target
df = df.dropna(subset=[target_column])

print(f"Dataset loaded with {len(df)} rows")

# Fill missing values in numeric columns with median
numeric_columns = ['jumlah_lubang', 'ukuran', 'ketebalan_material', 'upah_tenaga_ahli']
for col in numeric_columns:
    if col in df.columns:
        df[col] = df[col].fillna(df[col].median())

# Fill missing values in categorical columns with "UNKNOWN"
categorical_columns = ['produk', 'jenis_material', 'finishing', 'kerumitan_desain', 'metode_hitung', 'profile_size']
for col in categorical_columns:
    if col in df.columns:
        df[col] = df[col].fillna("UNKNOWN")

# Feature engineering
print("Engineering features...")

# ========== PRICE TABLE BASED FEATURES ==========
# 1. Material Base Price Lookup (from price table with YEAR-BASED pricing)
# Format: (produk, tahun, jenis_material) -> base price
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
    tahun = row.get('tahun', 2025)  # Default to latest year if missing
    if pd.isna(tahun):
        tahun = 2025
    
    # Try exact match first
    key = (row['produk'], int(tahun), row['jenis_material'])
    if key in material_base_price_map:
        return material_base_price_map[key]
    
    # Fallback: try nearest year (2019-2025 range)
    for year in [2025, 2024, 2023, 2022, 2021, 2020, 2019]:
        key = (row['produk'], year, row['jenis_material'])
        if key in material_base_price_map:
            return material_base_price_map[key]
    
    # Ultimate fallback
    return 500000

df['material_base_price'] = df.apply(get_material_base_price, axis=1)

# 2. Thickness Premium (from table)
thickness_premium_map = {0.8: 0, 1.0: 50000, 1.2: 100000}
df['thickness_premium'] = df['ketebalan_material'].map(thickness_premium_map).fillna(0)

# 3. Profile Size Premium
profile_premium_map = {
    '4x4': 0, '4x6': 50000, '4x8': 100000,
    '2x2': 50000, '1x3': 0, '1.5inch': 0, '2inch': 100000
}
df['profile_premium'] = df['profile_size'].map(profile_premium_map).fillna(0)

# 4. Finishing Premium
finishing_premium_map = {
    'Cat Dasar': 0, 'Cat Biasa': 0, 'Cat': 0,
    'Cat Duco': 150000, 'Tanpa Cat': 0
}
df['finishing_premium'] = df['finishing'].map(finishing_premium_map).fillna(0)

# 5. Complexity Premium
complexity_premium_map = {'Sederhana': 0, 'Menengah': 100000, 'Kompleks': 150000}
df['complexity_premium'] = df['kerumitan_desain'].map(complexity_premium_map).fillna(0)

# ========== DERIVED FEATURES ==========
df['total_area'] = df['jumlah_unit'] * df['ukuran']
df['total_lubang'] = df['jumlah_unit'] * df['jumlah_lubang']

# 7. Complexity Score (multi-factor)
df['kerumitan_numeric'] = df['kerumitan_desain'].map({'Sederhana': 1, 'Menengah': 2, 'Kompleks': 3}).fillna(1)
df['complexity_score'] = (df['kerumitan_numeric'] * df['ketebalan_material'] * 
                          (df['ukuran'] + 0.1) * (df['jumlah_lubang'] + 1))

# 8. Labor Intensity (holes per area)
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

# 12. Upah Ratio (normalized, not absolute)
df['upah_ratio'] = df['upah_tenaga_ahli'] / (df['total_area'] + 1)

# One-hot encoded features for Metode Hitung
df['is_per_lubang'] = (df['metode_hitung'].str.upper() == 'PER-LUBANG').astype(int)
df['is_per_m2'] = (df['metode_hitung'].str.upper() == 'PER-M2').astype(int)

# Thickness delta feature
df['thickness_delta'] = np.maximum(0, df['ketebalan_material'] - 0.8)

# Profile size encoding (4x4=1, 4x6=2, 4x8=3)
df['profile_numeric'] = df['profile_size'].map({'4x4': 1, '4x6': 2, '4x8': 3}).fillna(1)

# Material quality encoding (Hollow=1, Stainless=2)
df['material_quality'] = df['jenis_material'].map({'Hollow': 1, 'Stainless': 2}).fillna(1)

# Handling product-specific rules
df.loc[df['metode_hitung'].str.upper() == 'PER-LUBANG', 'total_area'] = 0
df.loc[df['metode_hitung'].str.upper() == 'PER-M2', 'total_lubang'] = 0

# Handle edge cases
# If Metode_Hitung is missing: infer from Produk (e.g., Teralis → Per Lubang, others → Per m²)
df.loc[(df['metode_hitung'] == "UNKNOWN") & (df['produk'] == 'Teralis'), 'metode_hitung'] = 'PER-LUBANG'
df.loc[(df['metode_hitung'] == "UNKNOWN") & (df['produk'] != 'Teralis'), 'metode_hitung'] = 'PER-M2'

# If Jumlah Lubang is missing for non-teralis products → fill with 0
df.loc[(df['jumlah_lubang'].isnull()) & (df['produk'] != 'Teralis'), 'jumlah_lubang'] = 0

# If ukuran is missing for per_lubang products → fill with 0
df.loc[(df['ukuran'].isnull()) & (df['metode_hitung'].str.upper() == 'PER-LUBANG'), 'ukuran'] = 0

# If Jumlah Unit missing → default = 1
df['jumlah_unit'] = df['jumlah_unit'].fillna(1)

# Prepare features and target
print("Preparing features and target...")
# SIMPLIFIED VERSION FOR THESIS - Only 15 core features with clear business justification
feature_columns = [
    # Original features (categorical) - 6 features
    'produk', 'jenis_material', 'finishing', 'kerumitan_desain', 'metode_hitung', 'profile_size',
    # Original features (numeric) - 4 features
    'jumlah_unit', 'jumlah_lubang', 'ukuran', 'ketebalan_material',
    # Price table based features (from bengkel price list) - 3 features
    'material_base_price', 'thickness_premium', 'profile_premium', 
    # Derived features (clear business logic) - 2 features
    'total_area', 'total_lubang'
]

# Check which columns actually exist
existing_feature_columns = [col for col in feature_columns if col in df.columns]
print(f"Existing feature columns ({len(existing_feature_columns)}): {existing_feature_columns}")

X = df[existing_feature_columns]
y = df['harga_final']

# Define preprocessing steps
print("Setting up preprocessing pipeline...")
# Numeric preprocessing
numeric_features = [
    'jumlah_unit', 'jumlah_lubang', 'ukuran', 'ketebalan_material',
    'material_base_price', 'thickness_premium', 'profile_premium', 
    'finishing_premium', 'complexity_premium',
    'total_area', 'total_lubang', 'complexity_score', 'labor_intensity',
    'cost_per_unit', 'material_thickness_interaction', 'area_complexity', 'upah_ratio',
    'thickness_delta', 'profile_numeric', 'material_quality', 
    'material_premium_index', 'kerumitan_numeric'
]
numeric_features = [col for col in numeric_features if col in X.columns]

# Categorical preprocessing
categorical_features = ['produk', 'jenis_material', 'finishing', 'metode_hitung', 'profile_size']
categorical_features = [col for col in categorical_features if col in X.columns]

# Ordinal preprocessing for Kerumitan Desain
ordinal_features = ['kerumitan_desain']
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
        ('ordinal', OrdinalEncoder(categories=[['UNKNOWN', 'Sederhana', 'Menengah', 'Kompleks']], 
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

# Split the data (500 training, 100 testing)
print("Splitting data...")
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=100, random_state=42)

print(f"Training set: {X_train.shape[0]} samples")
print(f"Testing set: {X_test.shape[0]} samples")

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
    "dataset_size": len(df),
    "train_size": len(X_train),
    "test_size": len(X_test),
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