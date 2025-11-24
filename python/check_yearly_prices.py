import pandas as pd

df = pd.read_csv('dataset_bengkel_fixx.csv')

print("=== HARGA RATA-RATA PER M² UNTUK SETIAP PRODUK-MATERIAL PER TAHUN ===\n")

# Group by produk, material, tahun
for produk in df['produk'].unique():
    for material in df['jenis_material'].unique():
        subset = df[(df['produk'] == produk) & (df['jenis_material'] == material)]
        if len(subset) == 0:
            continue
            
        print(f"\n{produk} - {material}:")
        print("-" * 50)
        
        yearly_prices = {}
        for year in sorted(subset['tahun'].unique()):
            year_data = subset[subset['tahun'] == year]
            avg_price_per_m2 = (year_data['harga_final'] / year_data['ukuran']).mean()
            yearly_prices[year] = avg_price_per_m2
            print(f"  {year}: Rp {avg_price_per_m2:>12,.0f}/m² (n={len(year_data):>3})")
        
        # Calculate year-over-year changes
        years = sorted(yearly_prices.keys())
        if len(years) > 1:
            print("\n  Perubahan tahun ke tahun:")
            for i in range(1, len(years)):
                prev_year = years[i-1]
                curr_year = years[i]
                change = yearly_prices[curr_year] - yearly_prices[prev_year]
                change_pct = (change / yearly_prices[prev_year]) * 100
                print(f"    {prev_year}→{curr_year}: {change:+,.0f} ({change_pct:+.1f}%)")
