import pandas as pd

# Check sheet names in the Excel file
try:
    xl = pd.ExcelFile('dataset_bengkel_las2.xlsx')
    print("Available sheet names:")
    for sheet in xl.sheet_names:
        print(f"- {sheet}")
        
    # Try to read the first sheet
    df = pd.read_excel('dataset_bengkel_las2.xlsx', sheet_name=xl.sheet_names[0])
    print("\nDataset loaded successfully from first sheet!")
    print("Sheet name:", xl.sheet_names[0])
    print("Shape:", df.shape)
    print("\nColumns:")
    for col in df.columns:
        print(f"- {col}")
    print("\nFirst 5 rows:")
    print(df.head())
    print("\nData types:")
    print(df.dtypes)
    
    # Check for any missing values
    print("\nMissing values:")
    print(df.isnull().sum())
except Exception as e:
    print(f"Error reading dataset: {e}")